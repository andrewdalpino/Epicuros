<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class SigningKeyNotFoundException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Signing key not found.');
    }
}
