<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceGoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_good', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('stockimage');
            $table->integer('strainname');
            $table->integer('asset_type_id');
            $table->integer('upc_fk');
            $table->string('batch_fk');
            $table->string('coa');
            $table->integer('coafile_fk');
            $table->string('um');
            $table->integer('weight');
            $table->integer('qtyonhand');
            $table->date('bestbefore');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_good');
    }
}
