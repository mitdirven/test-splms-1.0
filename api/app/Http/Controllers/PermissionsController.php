<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Http\Resources\PermissionResource;

use App\Http\Requests\Permission\ListPermissionRequest;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;

use App\Traits\PermissionsTrait;

class PermissionsController extends Controller {
    use PermissionsTrait;

    public function list(ListPermissionRequest $request) {
        return $this->getPermissions($request);
    }

    public function store(StorePermissionRequest $request) {
        $validated = $request->validated();
        $permission = Permission::create([
            "name" => $validated["name"],
            "description" => $validated["description"] ?? null,
        ]);

        return [
            "data" => new PermissionResource($permission),
            "message" => "Permission added successfully",
        ];
    }

    public function update(UpdatePermissionRequest $request, Permission $permission) {
        $validated = $request->validated();
        $permission->update([
            "name" => $validated["name"],
            "description" => $validated["description"],
        ]);

        return [
            "data" => new PermissionResource($permission),
            "message" => "Permission updated successfully",
        ];
    }

    public function destroy(ListPermissionRequest $request, Permission $permission) {
        $permission->users()->detach();
        $permission->delete();

        return [
            "message" => "Permission \"$permission->name\" deleted successfully",
        ];
    }
}
