<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;

class AuthorizationErrorException extends Exception
{
    private $e;

    public function __construct(AuthorizationException $e)
    {
    	$this->e = $e;

    	parent::__construct($e->getMessage());
    }

    public function render($request)
    {
    	return response()->json([
            'errors' => [
                'status' => '401',
                'title' => 'Authorization Error',
                'detail' => 'Your request was not authorized.'
            ]
        ], 401);
    }
}
