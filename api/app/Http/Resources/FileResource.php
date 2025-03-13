<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'new_name' => $this->file_name,
            'old_name' => $this->old_name,
            // 'file_path' => asset('files/records' . $this->file_path),
            'file_path' => asset('storage/app/' . $this->path),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
