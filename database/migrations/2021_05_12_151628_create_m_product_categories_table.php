<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_product_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_id')->default(0)->comment('0: categories without parent');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('size_category_ids')->nullable();
            $table->integer('is_enabled')->default(0)->comment('0: Enabled, 1: Not Enabled');
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
        Schema::dropIfExists('m_product_categories');
    }
}
