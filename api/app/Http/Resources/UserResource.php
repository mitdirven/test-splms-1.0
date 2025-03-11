<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $userID = Auth::id();
        $currentID = $this->id;
        $sameUser = $currentID == $userID;

        $permissions = $sameUser ? $this->getAllPermissions()->pluck("name") : PermissionResource::collection($this->getDirectPermissions());

        $roles = $this->roles
            ->map(function ($role) use ($sameUser) {
                $res = [
                    "name" => $role->name,
                    "color" => $role->color,
                ];
                if (!$sameUser) {
                    $res = array_merge($res, ["id" => $role->hash]);
                }
                return $res;
            })
            ->sortBy("name");

        return [
            "id" => $this->when(!$sameUser, $this->hash),
            "email" => $this->email,
            "username" => $this->username,
            "active" => !$this->disabled_at,
            "verified" => $this->email_verified_at,
            "profile" => new ProfileResource($this->profile),

            "roles" => $roles->toArray(),
            "permissions" => $permissions, // For own account (Profile)
        ];
    }
}
