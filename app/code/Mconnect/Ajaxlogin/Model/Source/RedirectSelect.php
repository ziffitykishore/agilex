<?php
namespace Mconnect\Ajaxlogin\Model\Source;

class RedirectSelect 
{
    const SAMEPAGE = 'samepage';

    const CUSTOMER_DASHBOARD = 'customer/account/';
	
	
    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->options = [
            ['value' => self::SAMEPAGE, 'label' => __('Stay on same page')],
            ['value' => self::CUSTOMER_DASHBOARD, 'label' => __('Customer Dashboard')],
            
         
        ];
   
        return $this->options;
    }
}
