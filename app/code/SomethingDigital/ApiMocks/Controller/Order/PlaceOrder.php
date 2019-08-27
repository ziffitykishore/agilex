<?php

namespace SomethingDigital\ApiMocks\Controller\Order;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\App\Action\Context;
 
class PlaceOrder extends \Magento\Framework\App\Action\Action
{
    /** @var JsonFactory */
    protected $jsonFactory;

    /**
     * AbstractController constructor.
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
    }

    public function execute() 
    {
        $resultJson = $this->jsonFactory->create();

        $request = $this->getRequest();
        if (!$request->isPost()) {
            $resultJson->setHttpResponseCode(Exception::HTTP_METHOD_NOT_ALLOWED);
            $resultJson->setData(['error' => __('Request not POST')]);
            return $resultJson;
        }

        $postdata = $this->getRequest()->getPost();
        $failure = $this->checkDataInvalid($postdata);
        if (!empty($failure)) {
            $resultJson->setData(['error' => $failure]);
        } else {
            $resultJson->setData($this->getResult());
        }

        return $resultJson;
    }

    protected function checkDataInvalid($postdata) {
    	$errors = [];
        if (empty($postdata['ShipServiceCode'])) {
            $errors['ShipServiceCode'] = 'ShipServiceCode is empty';
        }
        if (empty($postdata['Date'])) {
            $errors['Date'] = 'Date is empty';
        }
        if (empty($postdata['Total'])) {
            $errors['Total'] = 'Total is empty';
        }
        if (empty($postdata['ShipFee'])) {
            $errors['ShipFee'] = 'ShipFee is empty';
        }
        if (empty($postdata['ShipTo'])) {
            $errors['ShipTo'] = 'ShipTo is empty';
        }
        if (empty($postdata['Customer'])) {
            $errors['Customer'] = 'Customer is empty';
        }
        if (empty($postdata['LineItems'])) {
            $errors['LineItems'] = 'LineItems is empty';
        }
    	return $errors;
    }

    protected function getResult()
    {
        $result = [
            'order' => [
                'orderId' => rand(2000, 30000)
            ]
        ];

        return $result;
    }
}
