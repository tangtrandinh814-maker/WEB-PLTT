<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Add image upload fields if not exists
            if (!Schema::hasColumn('articles', 'image_path')) {
                $table->string('image_path')->nullable()->after('image_url');
            }
            if (!Schema::hasColumn('articles', 'image_alt')) {
                $table->string('image_alt')->nullable()->after('image_path');
            }
            if (!Schema::hasColumn('articles', 'thumbnail_path')) {
                $table->string('thumbnail_path')->nullable()->after('image_alt');
            }
        });

        // Add image fields to categories
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'image_path')) {
                $table->string('image_path')->nullable()->after('icon');
            }
        });

        // Add logo path to sources
        Schema::table('sources', function (Blueprint $table) {
            if (!Schema::hasColumn('sources', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('logo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumnIfExists('image_path');
            $table->dropColumnIfExists('image_alt');
            $table->dropColumnIfExists('thumbnail_path');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumnIfExists('image_path');
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->dropColumnIfExists('logo_path');
        });
    }
};
