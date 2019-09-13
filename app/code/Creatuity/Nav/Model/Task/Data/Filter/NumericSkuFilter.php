<?php

namespace Creatuity\Nav\Model\Task\Data\Filter;

class NumericSkuFilter implements SkuFilterInterface
{
    protected $skus;

    public function __construct(array $skus)
    {
        $this->skus = $skus;
    }

    public function filter()
    {
        return array_filter($this->skus, 'is_numeric');
    }
}
