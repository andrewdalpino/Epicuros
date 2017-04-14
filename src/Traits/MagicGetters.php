<?php

namespace AndrewDalpino\Epicuros\Traits;

use Illuminate\Support\Str;

trait MagicGetters
{
    /**
     * Magic getters.
     *
     * @param  string  $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        return $this->getAttribute($attribute);
    }

    /**
     * Attribute accessor.
     *
     * @param  string  $attribute
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        if ($this->hasAccessor($attribute)) {
            $accessor = $this->accessorMethod($attribute);

            return call_user_func([$this, $accessor]);
        }
    }

    /**
     * Determine if an attribute has an accessor.
     *
     * @param  string  $attribute
     * @return boolean
     */
    public function hasAccessor($attribute)
    {
        $accessor = $this->accessorMethod($attribute);

        return (method_exists($this, $accessor) && is_callable([$this, $accessor]));
    }

    /**
     * Format accessor method for an attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function accessorMethod($attribute)
    {
        return 'get' . Str::studly($attribute);
    }
}
