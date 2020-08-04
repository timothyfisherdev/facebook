<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_relationships', function (Blueprint $table) {
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('addressee_id');
            $table->timestamps();

            $table->foreign('requester_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            $table->foreign('addressee_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Composite key
            $table->primary(['requester_id', 'addressee_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_relationships');
    }
}
