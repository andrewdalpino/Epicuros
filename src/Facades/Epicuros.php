<?php

namespace AndrewDalPino\Epicuros\Facades;

use Illuminate\Support\Facades\Facade;

class Epicuros extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'epicuros';
    }
}
