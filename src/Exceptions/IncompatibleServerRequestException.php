<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class IncompatibleServerRequestException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('I cannot understand the server request object.');
    }
}
