<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedRequestException extends Exception
{
    public function render()
    {
    	return response()->json([
	    	'errors' => [
	    		'status' => '403',
	    		'title' => 'Unauthorized Request'
	    	]
	    ], 403);
    }
}
