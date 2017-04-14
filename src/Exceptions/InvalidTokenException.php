<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class InvalidTokenException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Invalid token.');
    }
}
