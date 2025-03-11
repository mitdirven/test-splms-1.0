<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->unique()->constrained();
            $table->foreignId("gender_id")->nullable()->constrained();
            $table->string("first_name")->nullable();
            $table->string("middle_name")->nullable();
            $table->string("last_name")->nullable();
            $table->string("suffix")->nullable();
            $table->string("nickname")->nullable();
            $table->date("birthdate")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table("profiles", function (Blueprint $table) {
            $table->dropForeign(["user_id"]);
            $table->dropForeign(["gender_id"]);
        });
        Schema::dropIfExists('profiles');
    }
};
