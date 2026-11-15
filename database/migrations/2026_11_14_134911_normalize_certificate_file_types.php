<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Align existing certificate file types with their stored extensions.
     */
    public function up(): void
    {
        DB::table('certificates')
            ->where('file_path', 'like', '%.pdf')
            ->update(['file_type' => 'pdf']);

        DB::table('certificates')
            ->where(function ($query): void {
                foreach (['jpg', 'jpeg', 'png', 'webp', 'avif'] as $extension) {
                    $query->orWhere('file_path', 'like', '%.'.$extension);
                }
            })
            ->update(['file_type' => 'image']);
    }

    /**
     * No data restoration is required for this normalization.
     */
    public function down(): void
    {
        //
    }
};
