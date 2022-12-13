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
        Schema::create('agent_countries', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('agent_id')->index();
            $table->foreign('agent_id')
                ->references('id')
                ->on('agents')
                ->onDelete('CASCADE');

            $table->unsignedBigInteger('country_id')->index();
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('CASCADE');

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
        Schema::dropIfExists('agent_countries');
    }
};
