<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kyc_records', function (Blueprint $table) {
            if (!Schema::hasColumn('kyc_records', 'api_raw_response')) {
                $table->text('api_raw_response')->nullable()->after('kyc_json');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kyc_records', function (Blueprint $table) {
            $table->dropColumn('api_raw_response');
        });
    }
};
