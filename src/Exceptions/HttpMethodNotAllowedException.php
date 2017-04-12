<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class HttpMethodNotAllowedException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('HTTP method not allowed.');
    }
}
