<?php

namespace AndrewDalpino\Epicuros;

use JsonSerializable;

class Context implements JsonSerializable
{
    /**
     * Any additional claims.
     *
     * @var  array  $claims
     */
    protected $claims = [
        //
    ];

    /**
     * @param  array  $cliams
     * @return self
     */
    public static function build(array $claims = [])
    {
        return new self($claims);
    }

    /**
     * Constructor.
     *
     * @param  array  $claims
     * @return void
     */
    public function __construct(array $claims = [])
    {
        $this->claims = $claims;
    }

    /**
     * Include any custom claims.
     *
     * @param  array  $claims
     * @return self
     */
    public function addClaims(array $claims)
    {
        $claims = array_merge($this->claims, $claims);

        return new self($claims);
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return $this->claims;
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    /**
     * Magic getters.
     *
     * @param  string  $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        return $this->claims[$attribute] ?? null;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return json_encode($this);
    }
}
