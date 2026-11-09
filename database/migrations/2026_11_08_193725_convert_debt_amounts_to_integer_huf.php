<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('debts')->update(['amount' => DB::raw('ROUND(amount)')]);
        DB::table('repayments')->update(['amount' => DB::raw('ROUND(amount)')]);

        Schema::table('debts', function (Blueprint $table): void {
            $table->bigInteger('amount')->change();
        });
        Schema::table('repayments', function (Blueprint $table): void {
            $table->bigInteger('amount')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table): void {
            $table->decimal('amount', 12, 2)->change();
        });
        Schema::table('repayments', function (Blueprint $table): void {
            $table->decimal('amount', 12, 2)->change();
        });
    }
};
