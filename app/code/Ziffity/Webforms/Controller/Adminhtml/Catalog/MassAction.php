<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Catalog;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Ui\Component\MassAction\Filter;
use Ziffity\Webforms\Api\CatalogRepositoryInterface;
use Ziffity\Webforms\Controller\Adminhtml\Catalog;
use Ziffity\Webforms\Model\Catalog as DataModel;
use Ziffity\Webforms\Model\ResourceModel\Catalog\CollectionFactory;

abstract class MassAction extends Catalog
{
    protected $filter;

    protected $collectionFactory;

    protected $dataRepository;

    protected $resultForwardFactory;

    protected $successMessage;

    protected $errorMessage;

    public function __construct(
        Filter $filter,
        Registry $registry,
        CatalogRepositoryInterface $dataRepository,
        PageFactory $resultPageFactory,
        Context $context,
        CollectionFactory $collectionFactory,
        ForwardFactory $resultForwardFactory,
        $successMessage,
        $errorMessage
    ) {
        $this->filter               = $filter;
        $this->dataRepository       = $dataRepository;
        $this->collectionFactory    = $collectionFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->successMessage       = $successMessage;
        $this->errorMessage         = $errorMessage;
        parent::__construct($registry, $dataRepository, $resultPageFactory, $resultForwardFactory, $context);
    }

    abstract protected function massAction(DataModel $data);

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $data) {
                $this->massAction($data);
            }
            $this->messageManager->addSuccessMessage(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('comments/catalog/index');
        return $redirectResult;
    }
}
