<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('payments', 'stripe_payment_intent_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('stripe_payment_intent_id')->nullable()->unique()->after('order_id');
            });
        }

        if (Schema::hasColumn('payments', 'stripe_payment_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('stripe_payment_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'stripe_payment_intent_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropUnique(['stripe_payment_intent_id']);
                $table->dropColumn('stripe_payment_intent_id');
            });
        }
    }
};
