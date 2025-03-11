<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\Auth\LoginRequest;

use App\Models\User;

trait AuthTrait {
    protected function AttemptLogin(LoginRequest $request, User $user, string $field, $fromFrontend = true) {
        $message = null;
        $disabled = false;

        $attempt = $fromFrontend ? $this->cookieLogin($request, $field) : $this->tokenLogin($request, $field, $user);
        if (!$attempt && $user->disabled_at !== null) {
            $message = config("mitd.auth.error.disabled");
            $disabled = true;
            if ($user->disabled_at === null) {
                $locked = $this->UserLoginFailed($user);
                $message = config($locked ? "mitd.auth.error.locked" : "mitd.auth.error.invalid");
                $disabled = $locked;
            }
        }

        return [
            "message" => $message,
            "disabled" => $disabled,
        ];
    }

    protected function tokenLogin(LoginRequest $request, string $field, User $user) {
        return Hash::check($request->input("password"), $user->password);
    }

    protected function cookieLogin(LoginRequest $request, string $field) {
        return Auth::attempt(
            [
                $field => strtolower($request->input("email")),
                "password" => $request->input("password"),
            ],
            $request->input("remember", false)
        );
    }

    protected function UserLoginFailed(User $user) {
        if (!!$user && $user->disabled_at == null) {
            $user->fails++;
            $max = config("mitd.auth.max_attempts");
            if (!is_null($max) && $max > 0 && $user->fails >= (int) $max) {
                $user->disabled_at = now();
            }
            $user->save();
        }
        return $user->disabled_at !== null;
    }
}
