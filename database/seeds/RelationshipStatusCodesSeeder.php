<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RelationshipStatusCodesSeeder extends Seeder
{
	private $data = [
		['code' => 'R', 'status' => 'requested'],
		['code' => 'A', 'status' => 'accepted'],
        ['code' => 'D', 'status' => 'declined'],
		['code' => 'B', 'status' => 'blocked']
	];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$now = now();

    	data_fill($this->data, '*.created_at', $now);
    	data_fill($this->data, '*.updated_at', $now);

        DB::table('relationship_status_codes')->insert($this->data);
    }
}
