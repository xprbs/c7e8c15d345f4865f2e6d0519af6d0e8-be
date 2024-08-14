<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->string('item_group');
            $table->string('uom');
            $table->string('type');
            $table->string('category');
            $table->text('description_before');
            $table->text('description_after');
            $table->string('sub_type');
            $table->string('dimension');
            $table->string('storage');
            $table->string('reservation');
            $table->string('item_model');
            $table->string('product_group');
            $table->string('findim');
            $table->string('status');
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
        Schema::dropIfExists('items');
    }
}
