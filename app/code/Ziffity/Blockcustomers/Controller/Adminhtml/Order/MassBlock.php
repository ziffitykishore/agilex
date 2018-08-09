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
class MassBlock extends \Magento\Backend\App\Action
{
    protected $customerFactory;

    protected $blockcustomersFactory;
    
    public function __construct(
        Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Ziffity\Blockcustomers\Model\BlockcustomersFactory $blockcustomersFactory
    )
    {
        $this->_customerFactory = $customerFactory;
        $this->_blockcustomersFactory = $blockcustomersFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $ids = $this->getRequest()->getParam("id");
        $modelFactory = $this->_customerFactory->create();
        $customerModel = $this->_blockcustomersFactory->create();
        if(empty($ids)){
           
            $this->messageManager->addError(__('Please select customer(s)'));
        }
        else{
            try{
                $data = [];
                foreach ($ids as $id) {
                     $model = $modelFactory->load($id);
                     $data['email'] = $model->getEmail();
                     $firstname = $model->getFirstname();
                     $lastname = $model->getLastname();
                     $data['name'] = $firstname." ".$lastname;       
                     $customerModel->setData($data);
                     $customerModel->save();
                }
                 
                 $this->messageManager->addSuccess(__('The %1 customer(s) have been blocked!',count($ids)));
            } catch (Exception $ex) {
                 $this->messageManager->addError($ex->getMessage());
            }
        }
       
        $this->_redirect('*/*/');
    }

}
