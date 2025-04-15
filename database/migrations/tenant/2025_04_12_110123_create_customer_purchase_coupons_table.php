<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerPurchaseCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_purchase_coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('configuration_purchase_coupon_id');
            $table->unsignedBigInteger('document_id');
            $table->string('document_number')->nullable();
            $table->string('customer_name');
            $table->string('customer_number');
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->decimal('document_amount', 10, 2);
            $table->date('expiration_date')->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('customer_purchase_coupons');
    }
}
