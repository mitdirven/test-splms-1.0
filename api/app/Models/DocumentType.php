<?php

namespace App\Models;

class DocumentType extends AppModel
{
    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    
    public function records()
    {
        return $this->hasMany(Record::class);
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    // protected function serializeDate(DateTimeInterface $date): string {
    //     return $date->format('Y-m-d');
    // }

}
