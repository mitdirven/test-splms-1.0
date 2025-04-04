<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; //use when auth is enabled

use Carbon\Carbon;

use App\Models\Record;
use App\Models\File;

use App\Http\Requests\Record\CreateRecordRequest;
use App\Http\Requests\Record\UpdateRecordRequest;
use App\Http\Resources\RecordResource;

class RecordController extends Controller
{
    public function show($hashid)
    {
        $record = Record::with(['documentType', 'user', 'files'])
            ->where('hashid', $hashid)
            ->firstOrFail();

        return response()->json(new RecordResource($record));
    }


    public function list(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search_term = $request->input('search_term', '');
        $records = Record::with(['documentType', 'user', 'files'])
            ->generalSearch($search_term)
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'total' => $records->total(),
            'page' => $records->currentPage(),
            'data' => RecordResource::collection($records),
        ]);
    }

    public function archive(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search_term = $request->input('search_term', '');

        $records = Record::onlyTrashed()
            ->with(['documentType', 'user', 'files'])
            ->generalSearch($search_term)
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'total' => $records->total(),
            'page' => $records->currentPage(),
            'data' => RecordResource::collection($records)
        ]);
    }

    public function create(CreateRecordRequest $request)
    {
        $fields = $request->validated();

        $hashid = Str::random(8);

        $date = Carbon::now();
        $month = $date->format('m');
        $year = $date->format('Y');

        $lastRecord = Record::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastRecord ? ((int)substr($lastRecord->control_number, -6)) + 1 : 1;
        $controlNumber = sprintf("SPARK-%s-%s-%06d", $month, $year, $nextNumber);

        $documentTypeId = $fields['document_type']['id'];

        // $userId = Auth::id(); //use when auth is enabled

        $record = Record::create([
            'hashid' => $hashid,
            'control_number' => $controlNumber,
            'title' => $fields['title'],
            'subject' => $fields['subject'],
            'document_type_id' => $documentTypeId,
            'user_id' => $fields['user_id'],
            // 'user_id' => $userId, //use when auth is enabled
        ]);

        if (isset($fields['files'])) {
            foreach ($fields['files'] as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $mimeType = $file->getMimeType();
                $size = $file->getSize();

                $newFileName = Str::uuid() . "." . $extension;

                $path = $file->storeAs('files/records', $newFileName);

                File::create([
                    'filable_id' => $record->id,
                    'filable_type' => Record::class,
                    'name' => $newFileName,
                    'old_name' => $originalName,
                    'file_name' => $newFileName,
                    'mime' => $mimeType,
                    'ext' => $extension,
                    'size' => $size,
                    'path' => $path,
                ]);
            }
        }

        return response()->json([
            'data' => $record->load('files'),
        ], 201);
    }

    public function update(UpdateRecordRequest $request, $hashid)
    {
        $fields = $request->validated();

        $record = Record::where('hashid', $hashid)->firstOrFail();

        $record->update([
            'title' => $fields['title'],
            'subject' => $fields['subject'],
            'document_type_id' => $fields['document_type_id'],
            'user_id' => $fields['user_id'],
        ]);

        $uploadedFileNames = [];
        if (isset($fields['files'])) {
            $existingFiles = File::where('filable_id', $record->id)
                ->where('filable_type', Record::class)
                ->get();

            foreach ($fields['files'] as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $mimeType = $file->getMimeType();
                $size = $file->getSize();
                $newFileName = Str::uuid() . "." . $extension;
                $path = $file->storeAs('files/records', $newFileName);

                $uploadedFileNames[] = $originalName;

                $fileExists = $existingFiles->contains(function ($existingFile) use ($originalName, $size, $extension) {
                    return $existingFile->old_name === $originalName &&
                        $existingFile->size === $size &&
                        $existingFile->ext === $extension;
                });

                if (!$fileExists) {
                    File::create([
                        'filable_id' => $record->id,
                        'filable_type' => Record::class,
                        'name' => $newFileName,
                        'old_name' => $originalName,
                        'file_name' => $newFileName,
                        'mime' => $mimeType,
                        'ext' => $extension,
                        'size' => $size,
                        'path' => $path,
                    ]);
                }

                foreach ($existingFiles as $existingFile) {
                    if (!in_array($existingFile->old_name, $uploadedFileNames)) {
                        Storage::delete($existingFile->path); // Delete file from storage
                        $existingFile->delete(); // Remove from database
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Record updated successfully',
            'data' => $record->load('files'),
        ]);
    }


    public function destroy($hashid)
    {
        $record = Record::where('hashid', $hashid)->firstOrFail();

        foreach ($record->files as $file) {
            Storage::delete('storage/' . $file->file_path);
            $file->delete();
        }

        $record->delete();

        return response()->json(['message' => 'Record and associated files deleted successfully.'], 200);
    }

    public function restore($hashid)
    {
        $record = Record::withTrashed()->where('hashid', $hashid)->firstOrFail();
        $record->restore();

        $record->files()->withTrashed()->restore();

        return response()->json(['message' => 'Record and files restored successfully.'], 200);
    }
}
