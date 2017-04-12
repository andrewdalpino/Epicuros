<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class InvalidSigningAlgorithmException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Invalid signing algorithm.');
    }
}
