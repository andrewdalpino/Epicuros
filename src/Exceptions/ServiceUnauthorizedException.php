<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class ServiceUnauthorizedException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Could not verify signature.');
    }
}
