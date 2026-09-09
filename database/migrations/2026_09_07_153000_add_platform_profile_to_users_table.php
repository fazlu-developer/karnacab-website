<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additive profile on website users (physical table web_users).
     * Links to Nest users later; does not duplicate customers/bookings tables.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('CUSTOMER')->after('email');
            $table->unsignedBigInteger('nest_user_id')->nullable()->after('role');
            $table->index('nest_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['nest_user_id']);
            $table->dropColumn(['role', 'nest_user_id']);
        });
    }
};
