<?php

namespace Ziffity\Blockcustomers\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;

/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class Blockcustomers extends \Magento\Backend\App\Action
{

    protected $customerFactory;
    protected $blockcustomersFactory;

    public function __construct(
    Context $context, \Magento\Customer\Model\CustomerFactory $customerFactory, \Ziffity\Blockcustomers\Model\BlockcustomersFactory $blockcustomersFactory
    )
    {
        $this->_customerFactory = $customerFactory;
        $this->_blockcustomersFactory = $blockcustomersFactory;
        parent::__construct($context);
    }

    public function execute()
    {

        $modelFactory = $this->_customerFactory->create();
        $id = $this->getRequest()->getParam('id');
        $data = [];
        if (!empty($id)) {
            $customerModel = $modelFactory->load($id);
            $email = $customerModel->getEmail();
            try {
                $blockedcustomerModel = $this->_blockcustomersFactory->create();
                $customerData = $blockedcustomerModel->getCollection()->getData();
                $customerId = "";
                foreach ($customerData as $custData) {
                    if ($email === $custData['email']) {
                        $customerId = $custData['id'];
                    }
                }
                $custEmail = $blockedcustomerModel->load($customerId)->getEmail();
                if ($email == $custEmail) {
                    $this->messageManager->addError(__('The customer has been already blocked!'));
                } else {
                    $firstname = $modelFactory->getFirstname();
                    $lastname = $modelFactory->getLastname();
                    $data['name'] = $firstname . " " . $lastname;
                    $data['email'] = $email;
                    $blockedcustomerModel->setData($data);
                    $blockedcustomerModel->save();
                    $this->messageManager->addSuccess(__("The Customer has been blocked!"));
                }
            } catch (Exception $ex) {
                $this->messageManager->addError($ex->getMessage());
            }
        }
        $this->_redirect("*/*/");
    }

}
