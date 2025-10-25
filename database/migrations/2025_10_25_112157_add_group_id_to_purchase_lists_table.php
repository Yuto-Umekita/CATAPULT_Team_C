<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_lists', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->nullable()->after('id');

            // （必要なら外部キー制約も付与）
            // $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_lists', function (Blueprint $table) {
            $table->dropColumn('group_id');
        });
    }
};
