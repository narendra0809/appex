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
        Schema::create('bulk_kyc_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name')->nullable();
            $table->string('original_filename');
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->integer('total_records')->default(0);
            $table->integer('processed_records')->default(0);
            $table->integer('success_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->text('error_log')->nullable();
            $table->string('result_zip_path')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('bulk_kyc_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->string('pan');
            $table->string('dob');
            $table->string('status')->default('pending'); // pending, processing, success, failed
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('kyc_record_id')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
            
            $table->foreign('batch_id')->references('id')->on('bulk_kyc_batches')->onDelete('cascade');
            $table->foreign('kyc_record_id')->references('id')->on('kyc_records')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_kyc_records');
        Schema::dropIfExists('bulk_kyc_batches');
    }
};
