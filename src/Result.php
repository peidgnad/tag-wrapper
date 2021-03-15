<?php

namespace Peidgnad\TagWrapper;

class Result
{
    public $wrappedString;

    public $indexes;

    public $positions;

    public function __construct($wrappedString, $indexes, $positions)
    {
        $this->wrappedString = $wrappedString;
        $this->indexes = $indexes;
        $this->positions = $positions;
    }

    public function getWrapped()
    {
        return $this->wrappedString;
    }

    public function getIndexes()
    {
        return $this->indexes;
    }

    public function getPositions()
    {
        return $this->positions;
    }

    public function __toString()
    {
        return $this->wrappedString;
    }
}
