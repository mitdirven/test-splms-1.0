<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

use App\Models\Log;

use App\Http\Resources\LogResource;

class LogController extends Controller {
    protected $DS = DIRECTORY_SEPARATOR;

    public function getLevels() {
        return trail()::$levels;
    }

    public function getLogs(Request $request, $year, $month = 0, $day = 0) {
        $year = $year == "null" ? Carbon::now()->format("Y") : $year;
        $hasMonth = $month && ($month > 0 && $month <= 12);
        $hasDay = $day && $day > 0;
        $dateStr = $hasMonth ? ($hasDay ? $year . "-" . $month . "-" . $day : $year . "-" . $month . "-01") : $year . "-07-01";

        $result = [];
        $sql = "";

        $count = 0;
        if ($hasDay) {
            $page = $request->input("page", 1);
            $limit = $request->input("limit", 100);
            $offset = $limit * ($page - 1);
            $orderBy = $request->input("orderBy", "time");
            $order = $request->input("order", "desc");
            $levelFilter = $request->input("levels", null);

            $grph = Log::whereDate("created_at", $dateStr)->select("level")->selectRaw("count(*) as total")->groupBy("level")->get();

            $summary = $this->summarize($grph);

            $logs = Log::whereDate("created_at", $dateStr)
                ->when(!!$levelFilter, function ($query) use ($levelFilter) {
                    $query->whereIn("level", $levelFilter);
                })
                ->when($orderBy == "time", function ($query) use ($order) {
                    $query->orderBy("created_at", $order);
                })
                ->when($orderBy == "level", function ($query) use ($order) {
                    $query->orderBy("level", $order);
                })
                ->when($orderBy == "module", function ($query) use ($order) {
                    $query->orderBy("module", $order);
                })
                ->offset($offset)
                ->limit($limit);

            $result = LogResource::collection($logs->get());
            $count = Log::whereDate("created_at", $dateStr)
                ->when($levelFilter, function ($query) use ($levelFilter) {
                    $query->whereIn("level", $levelFilter);
                })
                ->selectRaw("count(*) as count")
                ->first()->count;
            return response([
                "data" => $result,
                "count" => $count,
                "summary" => $summary,
            ]);
        } else {
            $date = Carbon::parse($dateStr);
            $start = ($hasMonth ? ($hasDay ? $date->startOfDay() : $date->startOfMonth()) : $date->startOfYear())->format("Y-m-d H:i:s");
            $end = ($hasMonth ? ($hasDay ? $date->endOfDay() : $date->endOfMonth()) : $date->endOfYear())->format("Y-m-d H:i:s");
            $sqlDateStr = "";
            $RawSqls = [];
            $sqlDateStr = $hasMonth ? "YYYY-MM-dd" : "YYYY-MM";
            $RawSqls = [
                "select" => "TO_CHAR(created_at::timestamp, '$sqlDateStr') as date",
                "group" => "TO_CHAR(created_at::timestamp, '$sqlDateStr')",
            ];

            $logs = Log::select("level", \DB::raw($RawSqls["select"]), \DB::raw("count(*) as total"))
                ->whereBetween("created_at", [$start, $end])
                ->groupBy("level", \DB::raw($RawSqls["group"]))
                ->orderBy("date", "desc")
                ->get();
            $result = $logs->groupBy("date")->map(function ($item, $key) {
                return $item->groupBy("level")->map(function ($items, $keys) {
                    return $items->first()->total;
                });
            });
        }

        return response([
            "data" => $result,
        ]);
    }

    public function summarize($grph) {
        $summary = [];
        $total = 0;

        foreach (trail()::$levels as $keys => $values) {
            $tmp = $grph->filter(function ($value, $key) use ($keys) {
                return $keys == $value["level"];
            });
            if ($tmp->first()) {
                $summary[] = [
                    "total" => $tmp->first()->total,
                    "prct" => 0,
                    "level" => $values,
                ];
                $total += $tmp->first()->total;
            } else {
                $summary[] = [
                    "total" => 0,
                    "prct" => 0,
                    "level" => $values,
                ];
            }
        }
        $summary = collect($summary)->map(function ($item, $key) use ($total) {
            return [
                "total" => $item["total"],
                "prct" => $total > 0 ? (float) number_format(($item["total"] / $total) * 100, 2) : 0,
                "level" => $item["level"],
            ];
        });
        return $summary->prepend([
            "total" => $total,
            "prct" => 100,
            "level" => "total",
        ]);
    }

    public function clearLogFile() {
        $path = storage_path("logs" . $this->DS . "laravel.log");
        File::delete($path);
        return response(["message" => "Log file deleted successfully!"]);
    }

    public function getSystemLog() {
        $path = storage_path("logs" . $this->DS . "laravel.log");
        if (File::exists($path)) {
            $size = File::size($path);
            if ($size > 52428800) {
                // 50MB
                return response(
                    [
                        "size" => $size,
                        "message" => "Log file is too large to preview, please download the file instead!",
                    ],
                    422
                );
            }
            $content = File::get($path);
            $mime = File::mimeType($path);
            $headers = ["Content-Type" => $mime, "Content-Length" => $size];
            return response($content)->header("Content-Type", $mime)->header("Content-Length", $size);
        }
        return response("");
    }

    public function downloadLogFile() {
        $path = storage_path("logs" . $this->DS . "laravel.log");
        if (!File::exists($path)) {
            return response(
                [
                    "message" => "Log file not found!",
                ],
                422
            );
        }
        return response()->download($path);
    }
}
