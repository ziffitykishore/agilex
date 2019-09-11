<?php

namespace Creatuity\Nav\Model\Task\EntityIncrementer;

class EntityIncrementer
{
    protected $incrementScale;
    protected $incrementStep;
    protected $startIndex;
    protected $currentIndex;

    public function __construct(
        $incrementScale,
        $startIndex = 1,
        $incrementStep = 1
    ) {
        $this->incrementScale = $incrementScale;
        $this->incrementStep = $incrementStep;
        $this->currentIndex = $this->startIndex = $startIndex;
    }

    public function get()
    {
        $value = $this->currentIndex * $this->incrementScale;
        $this->incrementRange();
        return $value;
    }

    protected function incrementRange()
    {
        $this->currentIndex += $this->incrementStep;
    }
}
