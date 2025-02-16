<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CategoryException extends Exception
{
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], $this->getCode());
    }
}
