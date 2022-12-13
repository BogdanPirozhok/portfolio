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
        Schema::create('cabinet_sites', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cabinet_id');
            $table->foreign('cabinet_id',)
                ->on('cabinets')
                ->references('id')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug');

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamp('deleting_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cabinet_sites');
    }
};
