<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stock_header_id');
            $table->unsignedBigInteger('item_id');
            $table->string('description', 500)->nullable();
            $table->integer('qty');
            $table->double('wholesale_price', 8, 2);
            $table->double('retail_price', 8, 2);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('stock_header_id')
                ->references('id')
                ->on('stock_headers')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_items');
    }
}
