<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Coin;

use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\Manager;
use Magento\Framework\Api\DataObjectHelper;
use Ziffity\Webforms\Api\CoinRepositoryInterface;
use Ziffity\Webforms\Api\Coin\CoinInterface;
use Ziffity\Webforms\Api\Coin\CoinInterfaceFactory;
use Ziffity\Webforms\Controller\Adminhtml\Coin;

class Save extends Coin
{
    protected $messageManager;

    protected $dataRepository;

    protected $dataFactory;

    protected $dataObjectHelper;

    public function __construct(
        Registry $registry,
        CoinRepositoryInterface $dataRepository,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Manager $messageManager,
        CoinInterfaceFactory $dataFactory,
        DataObjectHelper $dataObjectHelper,
        Context $context
    ) {
        $this->messageManager   = $messageManager;
        $this->dataFactory      = $dataFactory;
        $this->dataRepository   = $dataRepository;
        $this->dataObjectHelper  = $dataObjectHelper;
        parent::__construct($registry, $dataRepository, $resultPageFactory, $resultForwardFactory, $context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $id = $this->getRequest()->getParam('customer_id');
            if ($id) {
                $model = $this->dataRepository->getById($id);
            } else {
                unset($data['customer_id']);
                $model = $this->dataFactory->create();
            }

            try {
                $this->dataObjectHelper->populateWithArray($model, $data, CoinInterface::class);
                $this->dataRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved this data.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['customer_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['customer_id' => $this->getRequest()->getParam('customer_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
