<?php

namespace AndrewDalpino\Epicuros;

use AndrewDalpino\Epicuros\Exceptions\VerifyingKeyNotFoundException;
use AndrewDalpino\Epicuros\Exceptions\KeyRepositoryIsImmutableException;
use ArrayAccess;

class VerifyingKeyRepository implements ArrayAccess
{
    /**
     * @var  array  $keys
     */
    protected $keys = [
        //
    ];

    /**
     * Constructor.
     *
     * @param  array  $keys
     * @return void
     */
    public function __construct(array $keys = [])
    {
        $this->keys = $keys;
    }

    /**
     * Fetch a key from the repository.
     *
     * @param  mixed  $keyId
     * @throws KeyNotFoundException
     * @return string
     */
    public function fetch(string $keyId)
    {
        $key = $this->keys[$keyId] ?? null;

        if (is_file($key)) {
            $key = file_get_contents($key);
        }

        if ($key === null) {
            throw new KeyNotFoundException();
        }

        return $key;
    }

    /**
     * @param  string  $offset
     * @param  mixed  $value
     * @throws KeyRepositoryIsImmutableException
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        throw new KeyRepositoryIsImmutableException();
    }

    /**
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->keys[$offset]);
    }

    /**
     * @param  string  $offset
     * @throws KeyRepositoryIsImmutableException
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new KeyRepositoryIsImmutableException();
    }

    /**
     * @param  string  $offset
     * @return string
     */
    public function offsetGet($offset)
    {
        return $this->fetch($offset);
    }
}
