<?php

namespace Ziffity\LocationSelector\Controller\Session;

class Store extends \Magento\Framework\App\Action\Action
{
    
    protected $resultJsonFactory;
    
    protected $coreSession;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $jsonFactory;
        $this->coreSession = $coreSession;
    }
    
    
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        
        if(isset($data['store_location'])){
            $this->setValue($data['store_location']);
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData(array("status" => true, "message" => "Location Saved", 'data' => $this->getValue()));
        }else {
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData(array("status" => false, "message" => "Location Not Saved"));
        }
        
        return $resultJson;
    }    
    
    
    public function setValue($value)
    {
        $this->coreSession->start();
        $this->coreSession->setStoreLocation($value);
    }

    public function getValue()
    {
        $this->coreSession->start();
        return $this->coreSession->getStoreLocation();
    }

    public function unSetValue()
    {
        $this->coreSession->start();
        return $this->coreSession->unsStoreLocation();
    }    
}
