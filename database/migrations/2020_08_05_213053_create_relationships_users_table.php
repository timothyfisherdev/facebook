<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelationshipsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relationships_users', function (Blueprint $table) {
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('addressee_id');
            $table->char('status_code', 1);
            $table->timestamps();

            $table->foreign('requester_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            $table->foreign('addressee_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('status_code')
                ->references('code')
                ->on('relationship_status_codes')
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
        Schema::dropIfExists('relationships_users');
    }
}
