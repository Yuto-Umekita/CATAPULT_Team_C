<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('memos', function (Blueprint $table) {
            $table->foreignId('group_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('groups')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('memos', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
};
