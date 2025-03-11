<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $fillable = [
        'hashid',
        'control_number',
        'title',
        'subject',
        'document_type_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
