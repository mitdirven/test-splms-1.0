<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use Veelasky\LaravelHashId\Eloquent\HashableId;
use Spatie\Permission\PermissionRegistrar;

use App\Traits\PaginatesTrait;

class Role extends SpatieRole {
    use HasFactory, HashableId, PaginatesTrait;

    protected $casts = [
        "protected" => "boolean",
    ];

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany {
        return $this->morphedByMany(
            User::class,
            "model",
            config("permission.table_names.model_has_roles"),
            app(PermissionRegistrar::class)->pivotRole,
            config("permission.column_names.model_morph_key")
        );
    }
}
