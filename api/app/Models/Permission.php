<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Veelasky\LaravelHashId\Eloquent\HashableId;
use Spatie\Permission\PermissionRegistrar;

use App\Traits\PaginatesTrait;

class Permission extends SpatiePermission {
    use HasFactory, HashableId, PaginatesTrait;

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
