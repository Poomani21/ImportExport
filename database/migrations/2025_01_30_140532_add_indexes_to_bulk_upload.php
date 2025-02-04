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
        Schema::table('bulk_upload', function (Blueprint $table) {
            $table->index('gender');   // Index for faster filtering
            $table->index('pincode');  // Index for numeric search
            $table->index('city');     // Index for city-based searches
            $table->index('state');    // Index for state-based searches
            $table->index('country');  // Index for country-based searches
            $table->index('phone');  // Index for country-based searches
            $table->index('name');  // Index for country-based searches
            $table->index('description');  // Index for country-based searches
            $table->index('email');  // Index for country-based searches

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulk_upload', function (Blueprint $table) {
            $table->dropIndex(['gender']);
            $table->dropIndex(['pincode']);
            $table->dropIndex(['city']);
            $table->dropIndex(['state']);
            $table->dropIndex(['country']);
            $table->dropIndex(['description']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['email']);
        });
    }
};
