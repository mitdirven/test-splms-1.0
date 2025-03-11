<?php

namespace App\Traits;

use Illuminate\Http\Request;

use App\Http\Requests\FileUploadRequest;

use App\MITD\FileUpload;
use App\Models\File;

trait FilesTrait {
    protected function uploadFileRequest(FileUploadRequest $request, $location = "", $fileRule = "required|file") {
        $validated = $request->validated();
        $uid = $validated['uid'] ?? null;
        if(!$uid){
            return FileUpload::prepareUpload($validated["name"], $validated["rename"], $validated["size"], $location);
        } else {
            $next = FileUpload::getNextPart($uid);
            if($next["part"] == 1){
                $request->validate([ "file" => $fileRule ]);
            }
            $received = FileUpload::receivePart($request->file("file"), $uid);
            if (!!$received["raw"]) {
                $received["file"] = File::create($received["raw"]);
            }
            return $received;
        }
    }
}
