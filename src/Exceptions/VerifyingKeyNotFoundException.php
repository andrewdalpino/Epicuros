<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class VerifyingKeyNotFoundException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Verifying key not found.');
    }
}
