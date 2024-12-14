<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class BusinessException extends Exception
{
    protected $stringCode;

    public function __construct(array $codeResponse, $tips = '', Throwable $previous = null)
    {
        list($code, $message) = $codeResponse;
        $message = !empty($tips) ? $tips : $message;
        parent::__construct($message, 0, $previous);
        $this->stringCode = $code;
    }

    public function getStringCode()
    {
        return $this->stringCode;
    }

}
