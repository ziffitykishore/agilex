<?php

namespace Ziffity\Banners\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $imageModel = $objectManager->get('Ziffity\Banners\Model\ImageFactory');
        
        $image = $imageModel->create();
        
        $collection = $image->getCollection()->getFirstItem();
        var_dump($collection->getImageUrl());
        exit;
        foreach ($collection as $item)
        {
            var_dump('banners/images/image'.$item->getData('image'));
            var_dump($item->getImageUrl());
        }
        
    }    
}
