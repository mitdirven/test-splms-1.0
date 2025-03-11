<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Models\Record;

use App\Http\Requests\Record\CreateRecordRequest;
use App\Http\Resources\RecordResource;

class RecordController extends Controller
{

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
            'title' => $request->title,
            'subject' => $request->subject,
            'document_type_id' => $request->document_type_id,
            'user_id' => $request->user_id,
        ]);

        return response()->json([
            'data' => $record,
        ], 201);
    }
}
