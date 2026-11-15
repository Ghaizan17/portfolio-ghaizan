<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('description')->nullable();
            $table->longText('problem_statement')->nullable();
            $table->longText('objectives')->nullable();
            $table->string('role')->nullable();
            $table->text('team_collaboration')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('cover_path')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('github_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'published_at']);
            $table->index(['featured', 'sort_order']);
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
