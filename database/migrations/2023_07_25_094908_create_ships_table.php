<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('ships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ship_yard_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->unsignedInteger('construction_cost');
            $table->unsignedInteger('energy_consumption');
            $table->unsignedInteger('fuel_consumption');
            $table->unsignedInteger('attack');
            $table->unsignedInteger('defense');
            $table->boolean('claimed')->default(false);
            $table->dateTime('finished_at');
            $table->timestamps();

            $table->foreign('ship_yard_id')->references('id')->on('ship_yards')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ships');
    }
};
