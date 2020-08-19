<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_relationships', function (Blueprint $table) {
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

            $table->primary(['requester_id', 'addressee_id']);
        });

        if (! app()->runningUnitTests()) {
            // Check constraint: https://dba.stackexchange.com/a/273480/213570
            // User cannot have a relationship with themselves
            \DB::statement('alter table `users_relationships` add check (requester_id != addressee_id)');

            // Index expression: https://superuser.com/a/1491438
            // User relationship can only appear once in the table
            \DB::statement('alter table `users_relationships` 
                add unique index `unique_users_relationships`
                ((least(requester_id,addressee_id)), (greatest(requester_id,addressee_id)))'
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_relationships');
    }
}
