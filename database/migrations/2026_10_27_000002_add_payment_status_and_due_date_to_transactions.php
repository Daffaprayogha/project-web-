<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('status')->default('lunas')->after('type');
            $table->date('due_date')->nullable()->after('transaction_date');
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_date']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('transaction_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropColumn('transaction_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'due_date']);
            $table->dropColumn(['status', 'due_date']);
        });
    }
};
