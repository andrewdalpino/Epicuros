<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class ServerUnauthorizedException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Could not verify the client signature.');
    }
}
