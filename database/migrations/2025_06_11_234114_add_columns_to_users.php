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
        Schema::table('users', function (Blueprint $table) {
            $table->longText('introduction')->nullable()->after("name");
            $table->string('position')->nullable()->after("name");
            $table->jsonb('ai_integration')->nullable()->after("name");
            $table->longText('linkedin')->nullable()->after("name");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('introduction');
            $table->dropColumn('position');
            $table->dropColumn('ai_integration');
            $table->dropColumn('linkedin');
        });
    }
};
