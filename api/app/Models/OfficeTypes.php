<?php

namespace App\Models;

class OfficeTypes extends AppModel {
    protected $fillable = [];

    /**
    * Get the attributes that should be cast.
    *
    * @return array<string, string>
    */
    protected function casts(): array {
        return [ ];
    }

    /**
    * Prepare a date for array / JSON serialization.
    */
    // protected function serializeDate(DateTimeInterface $date): string {
    //     return $date->format('Y-m-d');
    // }
    
}
