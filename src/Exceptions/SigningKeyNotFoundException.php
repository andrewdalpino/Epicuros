<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class KeyNotFoundException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Key not found.');
    }
}
