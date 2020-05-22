<?php
namespace Magedelight\Megamenu\Model\Category\Attribute\Source;


class Width extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {

        if (!$this->_options) {
            $this->_options = array(
                array('value'=>0,'label'=>'Full Widht'),
                array('value'=>1,'label'=>'Auto'),
                array('value'=>2,'label'=>'Custom'),
            );
        }

        return $this->_options;
    }
}
