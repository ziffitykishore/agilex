<?php
namespace Ziffity\Filteredproducts\Helper;
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $productRepositoryFactory;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory
    )
    {
        $this->_productRepositoryFactory = $productRepositoryFactory;
        parent::__construct($context);
    }
    
    public function getProductDetails($productId){
        try{
            $product = $this->_productRepositoryFactory->create()->getById($productId);
//        $image = $this->image->init($product, 'product_base_image')
//                            ->setImageFile($product->getFile())->resize("100", "100")->getUrl();
//      echo $image;exit;  
 $image = $product->getData('image');
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
             $image = false;
        }
        return $image;
    }
    
}
