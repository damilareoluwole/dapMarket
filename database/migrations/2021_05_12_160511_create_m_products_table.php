<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('price');
            $table->string('details');
            $table->string('description');
            $table->string('image')->nullable();
            $table->string('purchases')->nullable();
            $table->string('keyword')->nullable();
            $table->foreignId('category_id')
                ->constrained('m_product_categories')
                ->onDelete('cascade');
            $table->foreignId('size_id')
                ->constrained('m_product_sizes')
                ->onDelete('cascade');
            $table->foreignId('shop_id')
                ->constrained('m_shops')
                ->onDelete('cascade');
            $table->foreignId('brand_id')
                ->constrained('m_product_brands')
                ->onDelete('cascade');
            $table->foreignId('condition_id')
                ->constrained('m_product_conditions')
                ->onDelete('cascade');
            $table->bigInteger('rank')->default(0)->comment('Values to show hierachy');
            $table->integer('is_sponsored')->default(0)->comment('0: NOT Sponsored, 1: Sponsored');
            $table->integer('availability')->nullable()->comment('Null: Always Available, 0: NOT Available, 1: Available');
            $table->integer('is_free_shipping')->default(0)->comment('0: NOT free, 1: Free');
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
        Schema::dropIfExists('m_products');
    }
}
