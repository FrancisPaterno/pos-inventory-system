<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_headers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('delivery_receipt_no', 30)->unique();
            $table->string('description', 500)->nullable();
            $table->date('date_received');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('supplier_id');
            $table->double('total');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
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
        Schema::dropIfExists('stock_headers');
    }
}
