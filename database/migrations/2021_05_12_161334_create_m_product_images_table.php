<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('m_products')
                ->onDelete('cascade');
            $table->string('image');
            $table->bigInteger('rank')->default(0)->comment('Values to show hierachy');
            $table->integer('is_enabled')->default(0)->comment('0: Enabled, 1: Not Enabled');
            $table->softDeletes();
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
        Schema::dropIfExists('m_product_images');
    }
}
