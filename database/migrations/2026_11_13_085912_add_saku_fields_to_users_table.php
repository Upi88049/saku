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
        Schema::table('users', function (Blueprint $table) {
            $table->string('plan_tier', 20)->default('starter')->after('email');
            $table->timestamp('plan_expires_at')->nullable()->after('plan_tier');
            $table->string('currency', 10)->default('IDR')->after('plan_expires_at');
            $table->unsignedTinyInteger('budget_start_day')->default(25)->after('currency');
            $table->text('payment_info')->nullable()->after('budget_start_day');
            $table->string('avatar')->nullable()->after('payment_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'plan_tier',
                'plan_expires_at',
                'currency',
                'budget_start_day',
                'payment_info',
                'avatar',
            ]);
        });
    }
};
