<?php


namespace Ziffity\Pickupdate\Controller\Guest;

use Ziffity\Pickupdate\Model\PickupdateRepository;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PickupdateRepository
     */
    private $pickupdateRepository;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    private $pickupHelper;

    /**
     * @var \Magento\Sales\Helper\Guest
     */
    private $guestHelper;

    /**
     * Edit constructor.
     *
     * @param Context                          $context
     * @param PickupdateRepository           $pickupdateRepository
     * @param Registry                         $coreRegistry
     * @param PageFactory                      $resultPageFactory
     * @param \Ziffity\Pickupdate\Helper\Data $pickupHelper
     * @param \Magento\Sales\Helper\Guest      $guestHelper
     */
    public function __construct(
        Context $context,
        PickupdateRepository $pickupdateRepository,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Magento\Sales\Helper\Guest $guestHelper
    ) {
        parent::__construct($context);
        $this->pickupdateRepository = $pickupdateRepository;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->pickupHelper = $pickupHelper;
        $this->guestHelper = $guestHelper;
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            return $this->_forward('noroute');
        }

        /* load order to register by params from session for guest */
        $result = $this->guestHelper->loadValidOrder($this->getRequest());
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }
        try {
            $pickupdate = $this->pickupdateRepository->getByOrder($orderId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->_redirect('sales/guest/view');
        }

        if (!$pickupdate->isCanEditOnFront()) {
            return $this->_redirect('sales/guest/view');
        }

        $this->coreRegistry->register('current_ziffity_pickupdate', $pickupdate);
        $order = $this->coreRegistry->registry('current_order');

        $title = __('Edit Pickup Date For The Order #%1', $order->getIncrementId());
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend($title);
        $this->guestHelper->getBreadcrumbs($resultPage);
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb(
            'cms_page',
            [
                'label' => __('Order Information'),
                'title' => __('Order Information'),
                'link'  => $this->_url->getUrl('sales/guest/view')
            ]
        );
        $breadcrumbs->addCrumb(
            'pickup_date',
            ['label' => __('Pickup Date'), 'title' => $title]
        );

        return $resultPage;
    }
}
