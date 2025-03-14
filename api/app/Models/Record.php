<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
    use HasFactory, SoftDeletes;

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

    public function files()
    {
        return $this->morphMany(File::class, 'filable');
    }


    public function scopeGeneralSearch(Builder $query, ?string $search_term)
    {
        return $query->when($search_term, function ($general_search_query) use ($search_term) {
            $general_search_query->where(function ($q) use ($search_term) {
                $q->where('control_number', 'ilike', "%{$search_term}%")
                    ->orWhere('title', 'ilike', "%{$search_term}%")
                    ->orWhere('subject', 'ilike', "%{$search_term}%");
            });
        });
    }
}
