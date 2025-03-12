<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DocumentTypeResource;

class RecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hashid' => $this->hashid,
            'control_number' => $this->control_number,
            'title' => $this->title,
            'subject' => $this->subject,
            'document_type' => new DocumentTypeResource($this->whenLoaded('documentType')),
            'user' => new UserResource($this->whenLoaded('user')),
            'files' => FileResource::collection($this->whenLoaded('files')), // Include files
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
