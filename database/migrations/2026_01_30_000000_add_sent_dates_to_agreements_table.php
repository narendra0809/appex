<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            $table->timestamp('agreement_sent_at')->nullable()->after('word_path');
            $table->timestamp('invoice_sent_at')->nullable()->after('agreement_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            $table->dropColumn(['agreement_sent_at', 'invoice_sent_at']);
        });
    }
};
