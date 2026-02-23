<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('designation')->default('employee')->after('role');
            // JSON array of module keys e.g. ["registration","laboratory","hr"]
            $table->json('permissions')->nullable()->after('designation');
            $table->string('phone')->nullable()->after('permissions');
            $table->boolean('is_active')->default(true)->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['designation', 'permissions', 'phone', 'is_active']);
        });
    }
};
