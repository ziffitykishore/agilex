<?php
namespace Travers\CategoryTable\Model\Entity\Attribute\Source;

class CustomOption extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
   public function getAllOptions()
   {
       if (!$this->_options) {
           $this->_options = [
               ['label' => __('Table'), 'value' => 'table'],
               ['label' => __('List'), 'value' => 'list']
            
           ];
       }
       return $this->_options;
   }

   /**
         * Get options in "key-value" format
         *
         * @return array
         */
        public function toArray()
        {
            return [
                'table' => __('Table'),
                'list' => __('List'),
                ];
        }
}