<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kyc_records', function (Blueprint $table) {
            if (!Schema::hasColumn('kyc_records', 'zip_path')) {
                $table->string('zip_path')->nullable()->after('document_path');
            }
            if (!Schema::hasColumn('kyc_records', 'ref_no')) {
                $table->string('ref_no')->nullable()->after('zip_path');
            }
            if (!Schema::hasColumn('kyc_records', 'kyc_status')) {
                $table->string('kyc_status')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kyc_records', function (Blueprint $table) {
            $table->dropColumn(['zip_path', 'ref_no', 'kyc_status']);
        });
    }
};
