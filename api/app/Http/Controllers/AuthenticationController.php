<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

use App\Http\Requests\Auth\LoginRequest;

use App\Traits\AuthTrait;

use App\Models\User;

use App\Http\Resources\UserResource;

class AuthenticationController extends Controller {
    use AuthTrait;

    public function login(LoginRequest $request) {
        $validated = $request->validated();

        /**
         * $fromFrontend is used to determine if the request is from the first party SPA
         * if true, login will be done using cookies instead of tokens
         */
        $fromFrontend = EnsureFrontendRequestsAreStateful::fromFrontend($request);

        $field = filter_var($validated["email"], FILTER_VALIDATE_EMAIL) ? "email" : "username";
        $user = User::where($field, strtolower($validated["email"]))->first();
        if ($user === null) {
            return response(["message" => config("mitd.auth.error.invalid")], 401);
        }
        $result = $this->AttemptLogin($request, $user, $field, $fromFrontend);

        if ($result["message"] !== null) {
            return response(["disabled" => $result["disabled"], "message" => $result["message"]], 401);
        }
        $user->resetFailedLoginAttempts();
        $result = new UserResource($user);

        if ($fromFrontend) {
            $request->session()->regenerate();
        } else {
            $user
                ->tokens()
                ->where("name", $request->device_name)
                ->delete();
            $token = $user->createToken($validated["device_name"])->plainTextToken;
            $result->additional(["token" => $token]);
        }

        return $result;
    }

    public function loginSpa(LoginRequest $request) {
        $validated = $request->validated();
        $field = filter_var($validated["email"], FILTER_VALIDATE_EMAIL) ? "email" : "username";
        $user = User::where($field, strtolower($validated["email"]))->first();
        if ($user === null) {
            return response(["message" => config("mitd.auth.error.invalid")], 401);
        }
        $result = $this->AttemptLogin($request, $user, $field);
        if ($result["message"] !== null) {
            return response(["disabled" => $result["disabled"], "message" => $result["message"]], 401);
        }
        $user->resetFailedLoginAttempts();
        $request->session()->regenerate();

        return new UserResource($user);
    }

    public function loginMobile(LoginRequest $request) {
        $validated = $request->validated();
        $field = filter_var($validated["email"], FILTER_VALIDATE_EMAIL) ? "email" : "username";
        $user = User::where($field, strtolower($validated["email"]))->first();
        if ($user === null) {
            return response(["message" => config("mitd.auth.error.invalid")], 401);
        }
        $result = $this->AttemptLogin($request, $user, $field, false);

        if ($result["message"] !== null) {
            return response(["disabled" => $result["disabled"], "message" => $result["message"]], 401);
        }
        $user->resetFailedLoginAttempts();
        $user
            ->tokens()
            ->where("name", $request->device_name)
            ->delete();
        $token = $user->createToken($validated["device_name"])->plainTextToken;

        return (new UserResource($user))->additional(["token" => $token]);
    }

    public function logout(Request $request) {
        $fromFrontend = EnsureFrontendRequestsAreStateful::fromFrontend($request);
        $user = $request->user();
        if ($fromFrontend) {
            // Auth::logout();
            $user->unsetRelation("roles", "permissions");
            Session::flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } else {
            $user->currentAccessToken()->delete();
        }

        return response(null, 204);
    }

    public function getPermissions(Request $request) {
        $user = $request->user();
        return response([
            "data" => new UserResource($user),
        ]);
    }
}
