<?php

namespace Creatuity\Nav\Model\Task\Data\Generator;

use Creatuity\Nav\Model\Data\Mapping\UnaryMapping;
use Creatuity\Nav\Model\Task\EntityIncrementer\EntityIncrementer;

class LineNumberDataGenerator
{
    protected $mapping;
    protected $entityIncrementer;

    public function __construct(
        UnaryMapping $mapping,
        EntityIncrementer $entityIncrementer
    ) {
        $this->mapping = $mapping;
        $this->entityIncrementer = $entityIncrementer;
    }

    public function generate()
    {
        return [
            $this->mapping->get() => $this->entityIncrementer->get(),
        ];
    }
}
