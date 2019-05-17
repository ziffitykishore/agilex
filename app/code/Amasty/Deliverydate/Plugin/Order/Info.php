<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */


namespace Amasty\Deliverydate\Plugin\Order;

class Info
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
     * @var \Amasty\Deliverydate\Helper\Data
     */
    protected $deliveryHelper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Info constructor.
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry         $coreRegistry
     * @param \Amasty\Deliverydate\Helper\Data    $deliveryHelper
     * @param \Psr\Log\LoggerInterface            $logger
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\Deliverydate\Helper\Data $deliveryHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->coreRegistry = $coreRegistry;
        $this->deliveryHelper = $deliveryHelper;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Block\Items\AbstractItems $subject
     * @param string                                   $result
     *
     * @return string
     */
    public function afterToHtml(\Magento\Sales\Block\Items\AbstractItems $subject, $result)
    {
        $addToResult = '';

        if ($subject->getOrder() && $subject->getOrder()->getId()) {
            try {
                $addToResult = $subject->getLayout()
                    ->createBlock(
                        'Amasty\Deliverydate\Block\Sales\Order\Info\Deliverydate',
                        'deliverydate_info',
                        ['data' => ['order_id' => $subject->getOrder()->getId()]]
                    )
                    ->toHtml();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->logger->error($e->getLogMessage());
            }
        }

        return $addToResult . $result;
    }
}
