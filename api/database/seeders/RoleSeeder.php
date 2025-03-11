<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $roles = [
            [
                "name" => config("mitd.superman"),
                "protected" => 1,
                "color" => "#FFD700",
                "level" => 0,
                "description" => 'A person with "Full/Unrestricted" access to admin features.',
                "permissions" => [],
            ],
            [
                "name" => "Moderator",
                "protected" => 1,
                "color" => "#1ABC9C",
                "level" => 1,
                "description" => "A person with access to some admin features.",
                "permissions" => [
                    "users_add",
                    "users_list",
                    "users_edit-profile",
                    "users_edit-account",
                    "users_edit-permission",
                    "users_change-status",

                    "roles_list",
                    "roles_add",
                    "roles_edit",
                    "roles_delete",
                ],
            ],
            [
                "name" => "User",
                "protected" => 1,
                "color" => "#D3D3D3",
                "level" => 2,
                "description" => "A person with limited access to system features.",
                "permissions" => ["self_change-password", "self_change-avatar", "self_update-profile", "self_update-account"],
            ],
        ];

        foreach ($roles as $role) {
            $roleName = Role::create([
                "name" => $role["name"],
                "protected" => $role["protected"],
                "description" => $role["description"],
                "color" => $role["color"],
                "level" => $role["level"],
                "guard_name" => "sanctum",
            ]);
            $roleName->syncPermissions($role["permissions"]);
        }
    }
}
