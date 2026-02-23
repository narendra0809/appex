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
       Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->date('payment_date')->nullable();
    $table->string('client_name');
    $table->string('mobile')->nullable();
    $table->string('email')->nullable();
    $table->string('father_name')->nullable();
    $table->string('pan_card')->nullable();
    $table->string('aadhaar_card')->nullable();
    $table->date('dob')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->decimal('gross_amount',10,2)->nullable();
    $table->decimal('net_amount',10,2)->nullable();
    $table->string('amount_type')->nullable();
    $table->string('segment')->nullable();
    $table->string('assigned_to')->nullable();
    $table->string('plan')->nullable();
    $table->date('service_start')->nullable();
    $table->date('service_end')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
