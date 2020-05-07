<?php
declare(strict_types = 1);
namespace Earthlite\Checkout\Model\Checkout;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Checkout\Block\Checkout\LayoutProcessor;

/**
 * class LayoutProcessorPlugin
 */
class LayoutProcessorPlugin 
{

    protected $arrayManager;

    /**
     * Constructor
     *     
     * @param array $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        LayoutProcessor $subject, 
        array $jsLayout
    ) {
        $jsLayout = $this->getShippingFormFields($jsLayout, 2);
        $jsLayout = $this->getBillingFormFields($jsLayout, 2);
        return $jsLayout;
    }

    /**
     * 
     * @param type $jsLayout
     * @param type $numStreetLines
     * @return boolean
     */
    public function getShippingFormFields($jsLayout, $numStreetLines) {
        // Street Label
//        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
//                ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['label'] = '';
//        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
//                ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['required'] = false;
//        // Street Line 0
//        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
//                ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['label'] = __('Address 1');
//        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
//                ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['validation'] = ['required-entry' => true, "min_text_len‌​gth" => 1, "max_text_length" => 255];
        // Street Line 1
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][1]['visible'] = false;
        
        return $jsLayout;
    }
    
    /**
     * 
     * @param type $jsLayout
     * @param type $numStreetLines
     * @return int
     */
    public function getBillingFormFields($jsLayout, $numStreetLines) {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']
                        ['billing-step']['children']['payment']['children']
                        ['payments-list'])) {

            $paymentForms = $jsLayout['components']['checkout']['children']['steps']['children']
                    ['billing-step']['children']['payment']['children']
                    ['payments-list']['children'];

            foreach ($paymentForms as $paymentMethodForm => $paymentMethodValue) {

                $paymentMethodCode = str_replace('-form', '', $paymentMethodForm);

                if (!isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                                ['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form'])) {
                    continue;
                }
                // Street Label
//                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
//                        ['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form']
//                        ['children']['form-fields']['children']['street']['label'] = '';
//                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
//                        ['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form']
//                        ['children']['form-fields']['children']['street']['required'] = false;
//                // Street Line 0
//                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
//                        ['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form']
//                        ['children']['form-fields']['children']['street']['children'][0]['label'] = __('Address 1');
                // Street Line 1
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                        ['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form']
                        ['children']['form-fields']['children']['street']['children'][1]['visible'] = false;
                
            }
        }
        return $jsLayout;
    }

}
