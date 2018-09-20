<?php

namespace Magebees\Lazyload\Model\Config;

class LoadMode implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $loadmode_arr['0']='Do not load anything';
        $loadmode_arr['1']='Only load visible elements';
        $loadmode_arr['2']='Load also very near view elements';
        $loadmode_arr['3']='Load also not so near view elements';
            return $loadmode_arr ;
    }
}
