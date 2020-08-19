<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersRelationshipsStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_relationships_status', function (Blueprint $table) {
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('addressee_id');
            $table->unsignedBigInteger('specifier_id');
            $table->char('status_code', 1);
            $table->timestamps();

            $table->foreign(['requester_id', 'addressee_id'])
                ->references(['requester_id', 'addressee_id'])
                ->on('users_relationships');
            
            $table->foreign('specifier_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('status_code')
                ->references('code')
                ->on('relationship_status_codes')
                ->onDelete('cascade');
        });

        Schema::table('users_relationships_status', function (Blueprint $table) {
            $table->primary(['requester_id', 'addressee_id', 'created_at'], 'pk_users_relationships_status');
        });

        if (! app()->runningUnitTests()) {
            // Check constraint: https://dba.stackexchange.com/a/273480/213570
            // User cannot have a relationship with themselves
            \DB::statement('alter table `users_relationships_status` add check (requester_id != addressee_id)');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_relationships_status');
    }
}
