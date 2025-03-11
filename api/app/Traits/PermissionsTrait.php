<?php

namespace App\Traits;

use Illuminate\Http\Request;

use App\Models\Permission;
use App\Http\Resources\PermissionResource;

trait PermissionsTrait {
    public function getPermissions(Request $request) {
        $search = $request->input("search");
        $limit = $request->input("limit", 25);
        $page = $request->input("page", 1);
        $permissions = Permission::where("name", "!=", "none")
            ->where("name", "ilike", "%" . $search . "%")
            ->paginates($limit, $page);

        $permissions["data"] = PermissionResource::collection($permissions["data"]);

        return $permissions;
    }
}
