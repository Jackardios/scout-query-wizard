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
        Schema::create('test_models', static function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->boolean('is_visible')->default(true);
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('test_models');
    }
};
