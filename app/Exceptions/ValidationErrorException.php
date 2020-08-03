<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;

class ValidationErrorException extends Exception
{
	private $e;

	public function __construct(ValidationException $e = null)
	{
		$this->e = $e;

		parent::__construct($e->getMessage());
	}

    public function render($request)
    {
    	return response()->json([
            'errors' => [
                'status' => '422',
                'title' => 'Validation Error',
                'detail' => 'Your request is malformed or missing fields.',
                'meta' => $this->e->errors()
            ]
        ], 422);
    }
}
