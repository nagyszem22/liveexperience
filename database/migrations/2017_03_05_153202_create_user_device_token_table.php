<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDeviceTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_device_token', function (Blueprint $table) {
            /* User device tokens ids */
            $table->increments('id');

            /* Users Device tokens relations */
            $table->integer('user_id')->unsigned()->index();
            $table->integer('device_token_id')->unsigned()->index();

            /* User device tokens logging */
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
        Schema::drop('user_device_token');
    }
}
