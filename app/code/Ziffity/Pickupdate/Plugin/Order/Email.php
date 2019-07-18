<?php

namespace Ziffity\Pickupdate\Plugin\Order;

class Email
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;


    /**
     * Email constructor.
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry         $coreRegistry
     * @param \Ziffity\Pickupdate\Helper\Data    $pickupHelper
     * @param \Psr\Log\LoggerInterface            $logger
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $coreRegistry,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->coreRegistry = $coreRegistry;
        $this->pickupHelper = $pickupHelper;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Block\Items\AbstractItems $subject
     * @param string                                   $result  HTML
     *
     * @return string
     */
    public function afterToHtml(\Magento\Sales\Block\Items\AbstractItems $subject, $result)
    {
        $addToResult = '';

        if ($subject->getOrder() && $subject->getOrder()->getId()) {
            $pickupDateFields = '';
            if ($subject instanceof \Magento\Sales\Block\Order\Email\Invoice\Items) {
                $pickupDateFields = $this->pickupHelper->whatShow('invoice_email', 'include');
            } elseif ($subject instanceof \Magento\Sales\Block\Order\Email\Shipment\Items) {
                $pickupDateFields = $this->pickupHelper->whatShow('shipment_email', 'include');
            } elseif ($subject instanceof \Magento\Sales\Block\Order\Email\Items) {
                $pickupDateFields = $this->pickupHelper->whatShow('order_email', 'include');
            }
            if ($pickupDateFields) {
                try {
                    $addToResult = $subject->getLayout()
                        ->createBlock(
                            'Ziffity\Pickupdate\Block\Sales\Order\Email\Pickupdate',
                            'pickupdate_info',
                            [
                                'data' => [
                                    'order_id' => $subject->getOrder()->getId(),
                                    'fields'   => $pickupDateFields,
                                    'address'  => $subject->getOrder()->getStoreAddress()
                                ]
                            ]
                        )
                        ->toHtml();

                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->logger->error($e->getLogMessage());
                }
            }
        }

        return $addToResult . $result;
    }
}
