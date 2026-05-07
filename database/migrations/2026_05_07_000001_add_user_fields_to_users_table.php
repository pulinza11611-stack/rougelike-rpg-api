<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'user_id')) {
                $table->string('user_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'user_name')) {
                $table->string('user_name')->nullable()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'user_name')) {
                $table->dropColumn('user_name');
            }
            if (Schema::hasColumn('users', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
