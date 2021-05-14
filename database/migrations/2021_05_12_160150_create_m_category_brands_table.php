<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMCategoryBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_category_brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')
                ->constrained('m_product_categories')
                ->onDelete('cascade');
            $table->foreignId('product_brand_id')
            ->constrained('m_product_brands')
            ->onDelete('cascade');
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
        Schema::dropIfExists('m_category_brands');
    }
}
