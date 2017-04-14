<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class NotIntendedAudienceException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Not intended audience.');
    }
}
