<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Unauthorized.');
    }
}
