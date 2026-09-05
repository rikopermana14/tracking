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
        Schema::create('route_waypoints', function (Blueprint $table) {

    $table->id();

    $table->foreignId('route_id');

    $table->integer('sequence');

    $table->decimal('latitude',10,7);

    $table->decimal('longitude',10,7);

    $table->double('course')->nullable();

    $table->double('distance_nm')->nullable();

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
        //
    }
};
