<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('service')->nullable()->after('plan');
            $table->string('bank')->nullable()->after('service');
            $table->text('remark')->nullable()->after('bank');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['service', 'bank', 'remark']);
        });
    }
};
