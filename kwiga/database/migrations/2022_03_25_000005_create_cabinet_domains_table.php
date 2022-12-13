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
        Schema::create('cabinet_domains', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('cabinet_site_id');
            $table->foreign('cabinet_site_id')
                ->on('cabinet_sites')
                ->references('id')
                ->cascadeOnDelete();

            $table->string('cloudflare_id');
            $table->string('hostname');

            $table->json('dns_records');

            $table->boolean('is_active')->default(false);

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
        Schema::dropIfExists('cabinet_domains');
    }
};
