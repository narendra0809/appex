<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_records', function (Blueprint $table) {
            $table->id();
            $table->string('pan', 10)->unique();
            $table->string('name')->nullable();
            $table->string('dob'); // DD/MM/YYYY format
            $table->string('father_name')->nullable();
            $table->text('address')->nullable();
            $table->string('pincode')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('status')->default('pending'); // pending, verified, not_found
            $table->text('kyc_json')->nullable(); // Store raw KYC JSON
            $table->dateTime('verified_at')->nullable();
            $table->string('document_path')->nullable(); // Path to uploaded documents
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('pan');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_records');
    }
};
