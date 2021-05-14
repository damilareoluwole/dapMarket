<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMFavouriteCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_favourite_collections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category_ids')->nullable();
            $table->string('size_ids')->nullable();
            $table->string('brand_ids')->nullable();
            $table->string('condition_ids')->nullable();
            $table->integer('min_price')->nullable();
            $table->integer('max_price')->nullable();
            $table->integer('free_shipping')->nullable();
            $table->integer('shop_id')->nullable();
            $table->bigInteger('rank')->default(0)->comment('Values to show hierachy');
            $table->integer('is_enabled')->default(0)->comment('0: Enabled, 1: Not Enabled');
            $table->string('keyword')->nullable()->comment('name, description, keyword ... Like user keyword');
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
        Schema::dropIfExists('m_favourite_collections');
    }
}
