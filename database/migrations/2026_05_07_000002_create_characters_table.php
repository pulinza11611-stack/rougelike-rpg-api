<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('character_id');
            $table->string('character_name');
            $table->string('class');
            $table->integer('level')->default(0);
            $table->integer('exp')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->unique(['user_id', 'character_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
