<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('items', function (Blueprint $table) {
            // Adding the columns
            $table->integer('desk_id')->unsigned()->nullable();
            $table->integer('deskpart_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('items', function (Blueprint $table) {
            // Dropping the columns to revert the change
            $table->dropColumn(['desk_id', 'deskpart_id']);
        });
    }
};
