<?php

namespace Ziffity\PickupdateOR\Plugin;

class RestApi {

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;

    public function __construct(
    \Ziffity\Pickupdate\Helper\Data $pickupHelper
    ) {
        $this->pickupHelper = $pickupHelper;
    }

    public function beforeDispatch(
    \Magento\Webapi\Controller\Rest $subject, \Magento\Framework\App\RequestInterface $request
    ) {
        $restUrl = $request->getPathInfo();
        if (
                $this->urlEndsWith($restUrl, 'estimate-shipping-methods') || 
                $this->urlEndsWith($restUrl, 'estimate-shipping-methods-by-address-id')
           ) {
            $params = json_decode($request->getContent(), true);
            $this->pickupHelper->setPickupDataToSession($params['data']);
        }
    }

    private function urlEndsWith($url, $endString) {
        $len = strlen($endString);
        if ($len === 0) {
            return false;
        }
        return (substr($url, -$len) === $endString);
    }
}
