<?php
namespace Cenpos\SimpleWebpay\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Index extends Action
{
	/**
	 * @var \Cenpos\SimpleWebpay\Model\Ui\ConfigProvider
	 */
    protected $_paymentMethod;

	/**
	 * @var \Magento\Backend\Model\Session\Quote
	 */
    protected $_quoteSession;

    /**
	 * @var \Magento\Backend\Model\Session\
	 */
    protected $_checkoutSession;

	/**
	 * Index constructor.
	 * @param Context $context
	 * @param \Cenpos\SimpleWebpay\Model\Ui\ConfigProvider $paymentMethod
	 * @param \Magento\Backend\Model\Session\Quote $quoteSession
	 */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Cenpos\SimpleWebpay\Model\Ui\ConfigProvider $paymentMethod,
        \Magento\Backend\Model\Session\Quote $quoteSession
    ) {
        parent::__construct($context);
        $this->_paymentMethod = $paymentMethod;
        $this->_quoteSession = $quoteSession;
    }

	/**
	 * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
	 */
    public function execute()
    {
        $ResponseSave = new \stdClass();
        try{

            //print_r($this->getRequest()->getParams());
            $operation = $this->getRequest()->getParam('op');

            if($operation == "save"){
                $paramsPayment = $this->getRequest()->getParam('payment');
                $this->_quoteSession->setRecurringData($paramsPayment); 
            }else{
                $this->_quoteSession->unsRecurringData(); 
            }

            $ResponseSave->Message = "Successfully";
            $ResponseSave->Result = 0;
            
        } catch (\Exception $ex) {
            $ResponseSave->Message = $ex->getMessage();
            $ResponseSave->Result = -1;
        }
        
        echo json_encode($ResponseSave);
    }
}