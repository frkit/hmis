<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('specialization')->nullable()->after('designation');
            $table->string('qualification')->nullable()->after('specialization');
            $table->decimal('fee', 10, 2)->default(0)->after('qualification');
            $table->json('timings')->nullable()->after('fee');
        });

        // Drop the legacy doctors table after merging fields
        Schema::dropIfExists('doctors');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['specialization', 'qualification', 'fee', 'timings']);
        });

        // Recreate the doctors table if rolling back
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('specialization')->nullable();
            $table->string('qualification')->nullable();
            $table->decimal('fee', 10, 2)->default(0);
            $table->json('timings')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
