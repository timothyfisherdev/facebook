<?php

use App\RelationshipStatusCode;
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
        RelationshipStatusCode::insert(
            collect($this->data)->fillTimestamps()->toArray()
        );
    }
}
