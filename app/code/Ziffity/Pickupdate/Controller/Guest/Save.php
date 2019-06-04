<?php

namespace Ziffity\Pickupdate\Controller\Guest;

use Ziffity\Pickupdate\Model\PickupdateRepository;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends \Ziffity\Pickupdate\Controller\Pickupdate\Save
{
    /**
     * @var \Magento\Sales\Helper\Guest
     */
    private $guestHelper;

    public function __construct(
        Context $context,
        PickupdateRepository $pickupdateRepository,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magento\Sales\Controller\AbstractController\OrderViewAuthorization $orderAuthorization,
        OrderRepository $orderRepository,
        LoggerInterface $logInterface,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Sales\Helper\Guest $guestHelper
    ) {
        parent::__construct(
            $context,
            $pickupdateRepository,
            $coreRegistry,
            $resultPageFactory,
            $orderAuthorization,
            $orderRepository,
            $logInterface,
            $pickupHelper,
            $date,
            $transportBuilder
        );
        $this->guestHelper = $guestHelper;
    }

    /**
     * Get Redirect to Order View. For Guest
     *
     * @param int $orderId
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function getRedirect($orderId)
    {
        return $this->_redirect('sales/guest/view');
    }

    /**
     * @param int $orderId
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws NoSuchEntityException
     */
    protected function getOrder($orderId)
    {
        $result = $this->guestHelper->loadValidOrder($this->getRequest());
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            throw new NoSuchEntityException();
        }
        return $this->coreRegistry->registry('current_order');
    }
}
