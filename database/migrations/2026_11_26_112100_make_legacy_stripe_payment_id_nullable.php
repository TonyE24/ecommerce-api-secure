<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('payments', 'stripe_payment_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('stripe_payment_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // The legacy column is retained for compatibility.
    }
};
