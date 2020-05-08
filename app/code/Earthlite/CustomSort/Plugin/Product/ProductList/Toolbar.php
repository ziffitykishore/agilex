<?php
namespace Earthlite\CustomSort\Plugin\Product\ProductList;

 class Toolbar
 {
     /**
      * Plugin
      *
      * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
      * @param \Closure $proceed
      * @param \Magento\Framework\Data\Collection $collection
      * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
      */
     public function aroundSetCollection(
         \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
         \Closure $proceed,
         $collection
     ) {
         $currentOrder = $subject->getCurrentOrder();
         $result = $proceed($collection);

         switch ($currentOrder) {
                case 'position_asc':
                        $subject->getCollection()
                        ->setOrder('position', 'asc');
                        break;
                case 'position_desc':
                        $subject->getCollection()
                        ->setOrder('position', 'desc');
                        break;
                case 'name_asc':
                        $subject->getCollection()
                        ->setOrder('name', 'asc');
                        break;
                case 'name_desc':
                        $subject->getCollection()
                        ->setOrder('name', 'desc');
                        break;
                case 'price_asc':
                        $subject->getCollection()
                        ->setOrder('price', 'asc');
                        break;
                case 'price_desc':
                        $subject->getCollection()
                        ->setOrder('price', 'desc');
                        break;
                default:
                        $subject->getCollection()
                        ->setOrder($subject->getCurrentOrder(), $subject->getCurrentDirection());
                        break;
         }

         return $result;
     }
 }
