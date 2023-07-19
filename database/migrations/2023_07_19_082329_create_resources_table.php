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

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->integer('ore')->unsigned();
            $table->integer('fuel')->unsigned();
            $table->integer('energy')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->constrained()
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
        Schema::dropIfExists('resources');
    }
};
