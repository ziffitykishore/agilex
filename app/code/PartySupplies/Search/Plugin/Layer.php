<?php

namespace PartySupplies\Search\Plugin;

class Layer
{
    protected $_request;

    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->_request = $request;
    }

    public function beforePrepareProductCollection(\Magento\Catalog\Model\Layer $subject, $collection)
    {
        $search = $this->_request->getParam('q');
        if ($search) {
            $collection->addAttributeToFilter([
                ['attribute' => 'name', 'like' => "%$search%"],
                ['attribute' => 'description', 'like' => "%$search%"],
                ['attribute' => 'sku', 'like' => "%$search%"]
            ]);
        }

        return [$collection];
    }
}
