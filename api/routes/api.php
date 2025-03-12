<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\VerificationController;

use App\Http\Controllers\AddressController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LogController;

use App\Http\Controllers\TestController;

use App\Http\Controllers\OfficeController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\DocumentTypeController;
use App\Models\Record;

#region Teams Management
Route::get('/offices', [OfficeController::class, 'list']);
#endregion

#region Records Management
Route::prefix("records")->group(function () {
    Route::get("{record}", [RecordController::class, "show"])->name("records.show");
    Route::get('', [RecordController::class, 'list'])->name("records.list");
    Route::post('', [RecordController::class, 'create'])->name("records.create");
    Route::patch("restore/{record}", [RecordController::class, "restore"])->name("records.restore");
    Route::patch("{record}", [RecordController::class, "update"])->name("records.update");
    Route::delete("{record}", [RecordController::class, "destroy"])->name("records.destroy");
});

Route::prefix("document_types")->group(function () {
    Route::get('', [DocumentTypeController::class, 'list']);
});
#endregion

Route::middleware(["auth:sanctum", "throttle:90,1", "isActive"])->group(function () {
    Route::middleware(["verified", "SPAOnly"])->group(function () {
        #region Permissions Management
        Route::prefix("permissions")->group(function () {
            Route::get("", [PermissionsController::class, "list"])->name("permissions.list");
            Route::post("", [PermissionsController::class, "store"])->name("permissions.store");
            Route::patch("{permission}", [PermissionsController::class, "update"])->name("permissions.update");
            Route::delete("{permission}", [PermissionsController::class, "destroy"])->name("permissions.destroy");
        });
        #endregion

        #region Roles Management
        Route::prefix("roles")->group(function () {
            Route::get("", [RolesController::class, "list"])->name("roles.list");
            Route::get("permissions", [RolesController::class, "getPermissions"])->name("roles.get_permissions");
            Route::post("", [RolesController::class, "store"])->name("roles.store");
            Route::patch("{role}", [RolesController::class, "update"])->name("roles.update");
            Route::delete("{role}", [RolesController::class, "destroy"])->name("roles.destroy");
        });
        #endregion

        #region Users Management
        Route::get("/users", [UsersController::class, "getUsers"]);
        Route::prefix("user")->group(function () {
            Route::get("roles", [UsersController::class, "getRoles"]);
            Route::get("genders", [UsersController::class, "getGenders"])->name("users.genders");
            Route::post("", [UsersController::class, "addUser"]);

            Route::patch("profile/{user}", [UsersController::class, "updateProfile"])->name("users.profile.update");
            Route::patch("username/{user}", [UsersController::class, "updateUsername"])->name("users.username.update");
            Route::patch("email/{user}", [UsersController::class, "updateEmail"])->name("users.email.update");
            Route::patch("verify/{user}", [UsersController::class, "verifyEmail"])->name("users.email.verify");
            Route::patch("password/{user}", [UsersController::class, "updatePassword"])->name("users.password.update");
            Route::delete("toggle/{user}", [UsersController::class, "toggleUser"]);

            Route::prefix("permissions")->group(function () {
                Route::get("", [UsersController::class, "getPermissions"]);
                Route::patch("{user}", [UsersController::class, "updatePermissions"]);
            });

            Route::patch("address-primary/{user}/{address}", [UsersController::class, "setMainAddress"])->name("users.address.primary");
            Route::prefix("address")->group(function () {
                Route::post("{user}", [UsersController::class, "addAddress"])->name("users.address.create");
                Route::patch("{user}/{address}", [UsersController::class, "updateAddress"])->name("users.address.update");
                Route::delete("{user}/{address}", [UsersController::class, "deleteAddress"])->name("users.address.delete");
            });

            Route::prefix("avatar")->group(function () {
                Route::post("{user}", [UsersController::class, "addAvatar"]);
                Route::patch("{user}/{image}", [UsersController::class, "changeAvatar"]);
                Route::delete("{user}/{image}", [UsersController::class, "deleteAvatar"]);
            });
        });
        #endregion

        #region Profile
        Route::prefix("auth")->group(function () {
            Route::get("profile", [AuthenticationController::class, "getPermissions"])->name("auth.profile.show");
            Route::patch("email", [ProfileController::class, "updateEmail"])->name("auth.email.update");
            Route::patch("username", [ProfileController::class, "updateUsername"])->name("auth.username.update");
            Route::patch("password", [ProfileController::class, "updatePassword"])->name("auth.password.update");
            Route::patch("profile", [ProfileController::class, "updateProfile"])->name("auth.profile.update");

            Route::patch("address-primary/{address}", [ProfileController::class, "setMainAddress"])->name("auth.address.primary");
            Route::prefix("address")->group(function () {
                // Route::get("", [ProfileController::class, "getAddresses"])->name("auth.address.list");
                Route::post("", [ProfileController::class, "addAddress"])->name("auth.address.create");
                Route::patch("{address}", [ProfileController::class, "updateAddress"])->name("auth.address.update");
                Route::delete("{address}", [ProfileController::class, "deleteAddress"])->name("auth.address.delete");
            });

            Route::prefix("avatars")->group(function () {
                // Route::get("", [ProfileController::class, "getAvatars"])->name("auth.avatars.list");
                Route::post("", [ProfileController::class, "addAvatar"])->name("auth.avatars.create");
                Route::patch("{image}", [ProfileController::class, "changeAvatar"])->name("auth.avatars.update");
                Route::delete("{image}", [ProfileController::class, "deleteAvatar"])->name("auth.avatars.delete");
            });
        });
        #endregion

        #region Logs
        Route::get("/logsy/{year}/{month?}/{day?}", [LogController::class, "getLogs"]);
        Route::get("/log/levels", [LogController::class, "getLevels"]);
        Route::get("/log/system", [LogController::class, "getSystemLog"]);
        Route::get("/log/system/dl", [LogController::class, "downloadLogFile"]);
        Route::delete("/log/system", [LogController::class, "clearLogFile"]);
        #endregion

        Route::get("/img/pri/{image}/{size?}", [ImageController::class, "imageDisplay"])->name("image.display.private");

        #region tests
        Route::post("/test/upload", [TestController::class, "upload"]);
        #endregion
    });

    Route::prefix("auth")->group(function () {
        Route::patch("profile-update", [ProfileController::class, "updateProfile"])->name("auth.profile.update.forced");
        Route::post("logout", [AuthenticationController::class, "logout"]);
        Route::get("permissions", [AuthenticationController::class, "getPermissions"])->name("auth.permissions");

        Route::prefix("email")->group(function () {
            route::post("resend", [VerificationController::class, "resend"]);
            Route::get("verify/{id}/{hash}", [VerificationController::class, "verify"])->name("verification.verify");
        });
    });
});

/**
 * Unthrottled private routes
 */
Route::middleware(["auth:sanctum"])->group(function () {
    Route::middleware(["verified", "SPAOnly"])->group(function () {});
});
/**
 * Public routes
 */
Route::middleware(["api", "throttle:60,1"])->group(function () {
    Route::prefix("auth")->group(function () {
        Route::post("login", [AuthenticationController::class, "loginSpa"])
            ->name("auth.login")
            ->middleware(["SPAOnly"]);
    });

    Route::prefix("password")->group(function () {
        Route::post("forgot", [PasswordController::class, "forgot_password"])->name("password.email");
        Route::post("reset", [PasswordController::class, "reset_password"])->name("password.update");
    });

    #region Address
    Route::prefix("address")->group(function () {
        Route::get("initial/barangay/{code}", [AddressController::class, "getBarangayAddress"]);
        Route::get("initial/city/{code}", [AddressController::class, "getCityAddress"]);
        Route::get("regions", [AddressController::class, "Regions"]);
        Route::get("provinces/{regionCode}", [AddressController::class, "Provinces"]);
        Route::get("cities/{provinceCode}", [AddressController::class, "Cities"]);
        Route::get("barangays/{cityCode}", [AddressController::class, "Barangays"]);
        Route::get("types", [AddressController::class, "getAddressTypes"]);
    });
    #endregion
});

/**
 * Public Mobile Routes
 * prettier-ignore
 */
Route::middleware(["api", "throttle:60,1"])->prefix("v1.0")->group(function () {
    Route::post("gettoken", [AuthenticationController::class, "loginMobile"])->name("auth.login.mobile");
});

/**
 * Mobile Routes
 * prettier-ignore
 */
Route::middleware(["auth:sanctum", "throttle:60,1"])->prefix("v1.0")->group(function () {
    Route::middleware(["verified"])->group(function () {});
});
