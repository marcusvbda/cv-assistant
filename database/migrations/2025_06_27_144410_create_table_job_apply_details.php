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
        Schema::create('job_apply_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_description_analysis_id');
            $table->foreign('job_description_analysis_id')->references('id')->on('job_description_analyses')->onDelete('cascade');
            $table->longText('resume');
            $table->longText('cover_letter');
            $table->integer('percentage_fit')->default(0);
            $table->longText('comment');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_apply_details');
    }
};
