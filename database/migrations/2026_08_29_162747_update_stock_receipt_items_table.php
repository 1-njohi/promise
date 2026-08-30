<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stock_receipt_items', function (Blueprint $table) {
            // Drop the old foreign key
            $table->dropForeign(['catalogue_item_id']);
            $table->dropColumn('catalogue_item_id');

            // Add new foreign key to inventory_items
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('stock_receipt_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn('inventory_item_id');

            $table->foreignId('catalogue_item_id')->constrained()->onDelete('cascade');
        });
    }
};