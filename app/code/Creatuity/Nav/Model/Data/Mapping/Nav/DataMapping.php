<?php

namespace Creatuity\Nav\Model\Data\Mapping\Nav;

use Creatuity\Nav\Model\Data\Mapping\BinaryMapping;
use Creatuity\Nav\Model\Data\Transform\Nav\DataTransformInterface;

class DataMapping
{
    protected $fieldMapping;
    protected $dataTransforms = [];

    public function __construct(BinaryMapping $fieldMapping, array $dataTransforms = [])
    {
        $this->fieldMapping = $fieldMapping;
        $this->dataTransforms = $dataTransforms;
    }

    public function addDataTransform(DataTransformInterface $dataTransform)
    {
        $this->dataTransforms[] = $dataTransform;
    }

    public function apply(array $data)
    {
        $fromField = $this->fieldMapping->getFrom();
        $value = isset($data[$fromField]) ? $data[$fromField] : null;

        foreach ($this->dataTransforms as $dataTransform) {
            $value = $dataTransform->transform($value);
        }

        return [
            $this->fieldMapping->getTo() => $value,
        ];
    }
}
