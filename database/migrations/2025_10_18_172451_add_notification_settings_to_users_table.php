<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_low_stock')->default(true)->after('remember_token');
            $table->boolean('notify_recipe_updates')->default(false)->after('notify_low_stock');
            $table->boolean('notify_system')->default(true)->after('notify_recipe_updates');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notify_low_stock', 'notify_recipe_updates', 'notify_system']);
        });
    }
};
