<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $users = [
            [
                "username" => "whykhamist",
                "email" => null,
                "password" => Hash::make("wasd1234"),
                "role" => "Admin",
            ],
            [
                "username" => "test",
                "email" => null,
                "password" => Hash::make("password123"),
                "role" => "Admin",
            ],
        ];
        foreach ($users as $user) {
            $new_user = User::create([
                "username" => $user["username"],
                "email" => $user["email"],
                "email_verified_at" => now(),
                "password" => $user["password"],
            ]);

            // $new_user->assignRole($user['role']);
            $role = Role::where("name", $user["role"])->first();
            DB::table("model_has_roles")->insert([
                "role_id" => $role->id,
                "model_type" => "App\Models\User",
                "model_id" => $new_user->id,
            ]);
        }
    }
}
