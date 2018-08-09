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
class massDelete extends \Magento\Backend\App\Action
{
    protected $blockcustomersFactory;
    
    public function __construct(
        Context $context,
        \Ziffity\Blockcustomers\Model\BlockcustomersFactory $blockcustomersFactory
    )
    {
        $this->_blockcustomersFactory = $blockcustomersFactory;
        parent::__construct($context);
    }
   
    public function execute()
    {
        
        $customerModel = $this->_blockcustomersFactory->create();
         $ids = $this->getRequest()->getParam('id');
		if (!is_array($ids) || empty($ids)) {
            $this->messageManager->addError(__('Please select customer(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $row = $customerModel->load($id);
                    $row->delete();
                    
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 customer(s) have been deleted.', count($ids))
                );
            }
            catch(\Exception $e){
                 $this->messageManager->addError($e->getMessage());
            }
                
            }
             $this->_redirect('*/*/blocklist');
        
    

}
}
