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
        Schema::table('transactions', function (Blueprint $table) {

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('statement_id')->nullable()->constrained('statements')->onDelete('cascade');
            $table->longText('details')->nullable();
            $table->string('currency')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_user_id_foreign');
            $table->dropForeign('transactions_statement_id_foreign');
            $table->dropColumn('user_id');
            $table->dropColumn('statement_id');
            $table->dropColumn('details');
            $table->dropColumn('currency');
        });
    }
};
