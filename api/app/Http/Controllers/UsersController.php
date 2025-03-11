<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\User\ListUserRequest;
use App\Http\Requests\User\CreateUserRequest;

use App\Http\Requests\User\UpdateUserEmailRequest;
use App\Http\Requests\User\UpdateUserUsernameRequest;
use App\Http\Requests\User\UpdateUserPasswordRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Http\Requests\User\UpdateUserAddressRequest;
use App\Http\Requests\User\UpdateUserPermissionRequest;

use App\Http\Requests\FileUploadRequest;

use App\Traits\UserTrait;
use App\Traits\PermissionsTrait;

use App\Models\User;
use App\Models\Address;
use App\Models\Role;
use App\Models\Image;

use App\Http\Resources\UserResource;
use App\Http\Resources\PermissionResource;

class UsersController extends Controller implements HasMiddleware {
    use UserTrait, PermissionsTrait;

    public static function middleware(): array {
        return [
            new Middleware("permission:users_add|users_edit-permission", only: ["getRoles"]),
            new Middleware("permission:users_edit-permission", only: ["getPermissions"]),
        ];
    }

    public function getUsers(ListUserRequest $request) {
        $search = $request->input("search");
        $limit = $request->input("limit");

        $isSuperman = Auth::user()->hasRole(config("mitd.superman"));

        $users = User::paginates($limit, null, function ($query) use ($search, $isSuperman) {
            $query
                ->where("id", "!=", Auth::id())
                ->when(!$isSuperman, function ($q) {
                    $q->whereDoesntHave("roles", function ($qq) {
                        $qq->where("name", config("mitd.superman"));
                    });
                })
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where("username", "ilike", "%{$search}%")->orWhere(
                            "email",
                            "ilike",
                            "%{$search}%"
                        );
                    });
                });
        });

        $users["data"] = UserResource::collection($users["data"]);

        return $users;
    }

    public function addUser(CreateUserRequest $request) {
        $data = $request->validated();

        $isUserSuper = Auth::user()->hasRole(config("mitd.superman"));
        $roles = Role::whereIn("id", $data["roles"])->get();

        if (!$isUserSuper && $roles->where("name", config("mitd.superman"))->count() > 0) {
            return ["message" => "You are not allowed to create users with this role!"];
        }

        $user = User::create([
            "email" => $data["email"],
            "username" => strtolower($data["username"]),
            "password" => bcrypt($data["password"]),
            "email_verified_at" => now(),
        ]);

        $user->roles()->attach($data["roles"]);

        return ["data" => new UserResource($user), "message" => "User created successfully!"];
    }

    public function getRoles(Request $request) {
        $userRole = Auth::user()->roles->sortBy("level")->first();
        $withPermissions = $request->input("perms", false);
        $roles = Role::select("name", "id", "level", "color")
            ->when(!Auth::user()->hasRole(config("mitd.superman")), function ($q) {
                $q->where("name", "!=", config("mitd.superman"));
            })
            ->where("level", ">=", $userRole->level)
            ->with("permissions")
            ->get();

        return [
            "data" => $roles->map(function ($role) use ($withPermissions) {
                $perms = $withPermissions ? ["permissions" => PermissionResource::collection($role->permissions)] : [];
                return array_merge([ "color" => $role->color, "name" => $role->name, "id" => $role->hash ], $perms );
            }),
            "default" => $userRole->hash,
        ];
    }

    public function updateUsername(UpdateUserUsernameRequest $request, User $user) {
        $updated = $this->update_username($request->validated(), $user);
        return new UserResource($updated);
    }

    public function updateEmail(UpdateUserEmailRequest $request, User $user) {
        $updated = $this->update_email($request->validated(), $user);
        return new UserResource($updated);
    }

    public function verifyEmail(User $user) {
        Gate::authorize("updateAccount", $user);
        $user->update(["email_verified_at" => now()]);
        return new UserResource($user);
    }

    public function updatePassword(UpdateUserPasswordRequest $request, User $user) {
        $updated = $this->update_password($request->validated(), $user);
        return new UserResource($updated);
    }

    public function updateProfile(UpdateUserProfileRequest $request, User $user) {
        $updated = $this->update_profile($request->validated(), $user);
        return new UserResource($updated);
    }

    public function addAddress(UpdateUserAddressRequest $request, User $user) {
        $updated = $this->update_address($request->validated(), $user);
        return new UserResource($updated);
    }

    public function updateAddress(UpdateUserAddressRequest $request, User $user, Address $address) {
        $updated = $this->update_address($request->validated(), $user, $address);
        return new UserResource($updated);
    }

    public function setMainAddress(User $user, Address $address){
        Gate::authorize("updateProfile", $user);
        return new UserResource($this->set_main_address($user, $address));
    }

    public function deleteAddress(User $user, Address $address){
        Gate::authorize("updateProfile", $user);
        return new UserResource($this->delete_address($user, $address));
    }

    public function addAvatar(FileUploadRequest $request, User $user) {
        Gate::authorize("updateProfile", $user);
        $uploaded =  $this->add_avatar($request, $user);
        if($uploaded['status'] == 'complete'){
            return [
                ...$uploaded,
                "data" => new UserResource($user)
            ];
        }
        return $uploaded;
    }

    public function changeAvatar(User $user, Image $image) {
        Gate::authorize("updateProfile", $user);
        return new UserResource($this->change_avatar($user, $image));
    }

    public function deleteAvatar(User $user, Image $image) {
        Gate::authorize("updateProfile", $user);
        return new UserResource($this->delete_avatar($user, $image));
    }

    public function toggleUser(User $user) {
        Gate::authorize("toggleUsers", $user);
        $user->update(["disabled_at" => $user->disabled_at ? null : now()]);
        return new UserResource($user);
    }

    public function updatePermissions(UpdateUserPermissionRequest $request, User $user) {
        $validated = $request->validated();
        $user->syncRoles($validated["roles"]);

        if(Auth::user()->isSuperman()) {
            $user->syncPermissions($validated["permissions"]);
        }
        return new UserResource($user);
    }
}
