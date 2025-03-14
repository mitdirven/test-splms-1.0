<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Http\Resources\DocumentTypeResource;

class DocumentTypeController extends Controller
{
    public function list()
    {
        $documentTypes = DocumentType::select()->get();

        return DocumentTypeResource::collection($documentTypes);
    }
}
