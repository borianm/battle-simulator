<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArmyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('army', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('units');
            $table->integer('alive_units');
            $table->float('damage_received')->default(0.0);
            $table->integer('attack_strategy_id')->index();
            $table->integer('game_id')->index();
            $table->integer('status')->default(0)->index();
            $table->integer('turn_made')->default(0)->index();
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
        Schema::dropIfExists('army');
    }
}
