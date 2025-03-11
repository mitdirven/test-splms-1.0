<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Record;
use App\Models\File;
use App\Http\Requests\Record\CreateRecordRequest;
use App\Http\Requests\FileUploadRequest;
use App\Http\Resources\RecordResource;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\FilesTrait;
use App\MITD\FileUpload;

class RecordController extends Controller
{
    use FilesTrait;

    public function show(Record $record)
    {
        $record = Record::with(['documentType', 'user'])->find($record->id);

        return response()->json(new RecordResource($record));
    }

    public function list(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search_term = $request->input('search_term', '');
        $records = Record::with(['documentType', 'user'])
            ->generalSearch($search_term)
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'total' => $records->total(),
            'page' => $records->currentPage(),
            'data' => RecordResource::collection($records),
        ]);
    }

    public function create(CreateRecordRequest $request)
    {
        dd($request);
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

        $record = Record::create([
            'hashid' => $hashid,
            'control_number' => $controlNumber,
            'title' => $fields['title'],
            'subject' => $fields['subject'],
            'document_type_id' => $fields['document_type_id'],
            'user_id' => $fields['user_id'],
        ]);

        $uid = $fields['uid'] ?? null;
        $location = "";
        $fileRule = "required|file";

        if (!$uid) {
            return FileUpload::prepareUpload($fields["name"], $fields["rename"], $fields["size"], $location);
        } else {
            $next = FileUpload::getNextPart($uid);
            if ($next["part"] == 1) {
                $request->validate(["file" => $fileRule]);
            }
            $received = FileUpload::receivePart($request->file("file"), $uid);
            if (!!$received["raw"]) {
                $received["file"] = File::create($received["raw"]);
            }
            return $received;
        }

        return response()->json([
            'data' => new RecordResource($record),
            'message' => 'Record created successfully with file upload.',
        ], 201);
    }
}
