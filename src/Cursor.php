<?php

namespace AndrewDalpino\Epicuros;

use JsonSerializable;

class Cursor implements JsonSerializable
{
    const DEFAULT_LIMIT = 10;

    /**
     * The current location of the cursor.
     *
     * @var  int  $offset
     */
    protected $offset;

    /**
     * The location of the previous cursor.
     *
     * @var  int  $previous
     */
    protected $previous;

    /**
     * The maximum number of items to retrieve at a time.
     *
     * @var  int  $limit
     */
    protected $limit;

    /**
     * Constructor.
     *
     * @param  int  $offset
     * @param  int  $previous
     * @param  int  $limit
     * @return void
     */
    public function __construct(int $offset = null, int $previous = null, int $limit = null)
    {
        $this->offset = $offset ?? 0;
        $this->previous = $previous ?? null;
        $this->limit = $limit ?? self::DEFAULT_LIMIT;
    }

    /**
     * @return int
     */
    public function getOffset() : int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getPrevious() : int
    {
        return $this->previous;
    }

    /**
     * @return int
     */
    public function getLimit() : int
    {
        return $this->limit;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            'offset' => $this->getOffset(),
            'previous' => $this->getPrevious(),
            'limit' => $this->getLimit(),
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return json_encode($this);
    }
}
