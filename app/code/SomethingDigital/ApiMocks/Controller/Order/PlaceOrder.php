<?php

namespace SomethingDigital\ApiMocks\Controller\Order;

use Magento\Framework\Controller\Result\JsonFactory;
 
class PlaceOrder
{
    /** @var JsonFactory */
    protected $jsonFactory;

    /**
     * AbstractController constructor.
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        JsonFactory $jsonFactory
    ) {
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