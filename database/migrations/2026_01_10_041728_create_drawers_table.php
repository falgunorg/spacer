<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrawersTable extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('drawers', function (Blueprint $table) {
            $table->id();
            $table->integer('cabinet_id')->unsigned();
            $table->string('title');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('drawers');
    }
}
