<?php

namespace AndrewDalpino\Epicuros\Exceptions;

use Exception;

class KeyRepositoryIsImmutableException extends Exception
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Cannot mutate the key repository once it has been instantiated.');
    }
}
