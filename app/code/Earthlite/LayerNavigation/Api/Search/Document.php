<?php

namespace Earthlite\LayerNavigation\Api\Search;

use Magento\Framework\Api\Search\Document as SourceDocument;

class Document extends SourceDocument
{
    /**     
     *
     * @param string $fieldName
     *
     * @return \Magento\Framework\Api\AttributeInterface
     */
    public function getField($fieldName)
    {
        return $this->getCustomAttribute($fieldName);
    }
}
