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
        Schema::create('related_through_pivot_models', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('related_through_pivot_models');
    }
};
