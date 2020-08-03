<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    public function render()
    {
    	return response()->json([
	    	'errors' => [
	    		'status' => '404',
	    		'title' => 'Requested User Not Found',
	    		'detail' => 'Unable to find the requested user.'
	    	]
	    ], 404);
    }
}
