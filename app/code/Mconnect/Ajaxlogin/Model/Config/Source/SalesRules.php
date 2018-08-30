<?php

namespace Mconnect\Ajaxlogin\Model\Config\Source;

/**
 * Description of Coupons
 *
 * @author Daiva
 */
class SalesRules implements \Magento\Framework\Option\ArrayInterface
{
    protected $salesRulesFactory;

    public function __construct(
    \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $salesRulesFactory
    )
    {

        $this->salesRulesFactory = $salesRulesFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options    = [];
        $collection = $this->salesRulesFactory->create();
        
        foreach ($collection as $item) {
       
            $options[] = ['value' => $item->getRuleId(), 'label' => __($item->getName())];
        }
        return $options;
    }
}