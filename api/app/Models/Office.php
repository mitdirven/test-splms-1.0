<?php

namespace App\Models;

use App\Models\OfficeTypes;

class Office extends AppModel
{
    protected $fillable = [
        'code',
        'type_id',
        'name',
        'address',
        'contact_no',
        'email',
    ];

    protected $hidden = [
        'id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function type()
    {
        return $this->belongsTo(OfficeTypes::class, 'type_id');
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    // protected function serializeDate(DateTimeInterface $date): string {
    //     return $date->format('Y-m-d');
    // }

}
