<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create('pivot_models', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('test_model_id');
            $table->integer('related_through_pivot_model_id');
            $table->string('location')->nullable();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_models');
    }
};
