<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->default('Your Name');
            $table->string('headline')->nullable();
            $table->string('class')->nullable();
            $table->string('vocational_competency')->nullable();
            $table->text('bio_short')->nullable();
            $table->longText('bio_long')->nullable();
            $table->text('personal_branding')->nullable();
            $table->text('career_goals')->nullable();
            $table->string('location')->nullable();
            $table->string('availability')->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
