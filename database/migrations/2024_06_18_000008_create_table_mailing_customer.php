<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailing_customer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_mailing_index')->nullable();
            $table->foreign('id_mailing_index')->references('id')->on('mailing_index')->onDelete('cascade');
            $table->unsignedBigInteger('id_website_customer')->nullable();
            $table->foreign('id_website_customer')->references('id')->on('website_customer')->onDelete('cascade');
            $table->boolean('sending');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailing_customer', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_mailing_index');
            $table->dropConstrainedForeignId('id_website_customer');
        });
        Schema::dropIfExists('mailing_customer');
    }
};