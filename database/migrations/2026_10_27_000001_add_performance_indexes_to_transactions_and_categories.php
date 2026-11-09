<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'transaction_date']);
            $table->index(['user_id', 'type']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['user_id', 'type']);
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'transaction_date']);
            $table->dropIndex(['user_id', 'type']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'type']);
        });
    }
};
