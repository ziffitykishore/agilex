<?php

namespace Ziffity\Pickupdate\Plugin\Order;

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
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $ZiffityHelper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Info constructor.
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry         $coreRegistry
     * @param \Ziffity\Pickupdate\Helper\Data    $ZiffityHelper
     * @param \Psr\Log\LoggerInterface            $logger
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $coreRegistry,
        \Ziffity\Pickupdate\Helper\Data $ZiffityHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->coreRegistry = $coreRegistry;
        $this->ZiffityHelper = $ZiffityHelper;
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
                        'Ziffity\Pickupdate\Block\Sales\Order\Info\Pickupdate',
                        'Ziffitydate_info',
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
