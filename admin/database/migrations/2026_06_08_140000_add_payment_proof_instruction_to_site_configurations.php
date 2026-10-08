<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_configurations', function (Blueprint $table) {
            if (! Schema::hasColumn('site_configurations', 'payment_proof_instruction')) {
                $table->text('payment_proof_instruction')->nullable()->after('whatsapp_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_configurations', function (Blueprint $table) {
            if (Schema::hasColumn('site_configurations', 'payment_proof_instruction')) {
                $table->dropColumn('payment_proof_instruction');
            }
        });
    }
};
