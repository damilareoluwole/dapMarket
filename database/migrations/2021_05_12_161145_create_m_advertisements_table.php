<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMAdvertisementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('text1');
            $table->string('text2');
            $table->string('image');
            $table->integer('ad_type')->default(0)->comment('0: Product, 1: Shop, 2:url');
            $table->integer('is_enabled')->default(0)->comment('0: Enabled, 1: Not Enabled');
            $table->string('add_id_link')->comment('Product id, Shop id or url');
            $table->bigInteger('rank')->default(0)->comment('Values to show hierachy');
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
        Schema::dropIfExists('m_advertisements');
    }
}
