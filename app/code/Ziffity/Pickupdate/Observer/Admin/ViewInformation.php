<?php

namespace Ziffity\Pickupdate\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

class ViewInformation implements ObserverInterface
{
    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    public function __construct(
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->pickupHelper = $pickupHelper;
        $this->coreRegistry = $coreRegistry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->pickupHelper->moduleEnabled()) {
            $elementName = $observer->getElementName();
            $transport = $observer->getTransport();
            $html = $transport->getOutput();
            $block = $observer->getLayout()->getBlock($elementName);

            if ('order_info' == $elementName)
            {
                $insert = $observer->getLayout()
                    ->createBlock('Ziffity\Pickupdate\Block\Adminhtml\Sales\Order\View\Pickupdate');

                if ($block->getParentBlock() instanceof \Magento\Sales\Block\Adminhtml\Order\View\Tab\Info)
                {
                    $pickupDate = $this->pickupHelper->whatShow('order_view');
                    $html = $this->addToHtml($pickupDate, $html, $insert);

                } elseif ($block->getParentBlock() instanceof \Magento\Sales\Block\Adminhtml\Order\Invoice\View\Form)
                {
                    $pickupDate = $this->pickupHelper->whatShow('invoice_view');
                    $this->coreRegistry->register('current_pickupdate_place', 'invoice');
                    $html = $this->addToHtml($pickupDate, $html, $insert);

                } elseif ($block->getParentBlock() instanceof \Magento\Shipping\Block\Adminhtml\View\Form)
                {
                    $pickupDate = $this->pickupHelper->whatShow('shipment_view');
                    $this->coreRegistry->register('current_pickupdate_place', 'shipment');
                    $html = $this->addToHtml($pickupDate, $html, $insert);
                }


            } elseif ('shipping_method' == $elementName)
            {
                if ($block->getParentBlock() instanceof \Magento\Sales\Block\Adminhtml\Order\Create\Data)
                {
                    $pickupDate = $this->pickupHelper->whatShow('order_create');
                    $insert = $observer->getLayout()
                        ->createBlock('Ziffity\Pickupdate\Block\Adminhtml\Sales\Order\Create\Pickupdate');

                    $html = $this->addToHtml($pickupDate, $html, $insert);
                }

            }

            $transport->setOutput($html);
        }
    }

    protected function addToHtml($pickupDate, $html, $insert, $where = 'view') {
        if (!empty($pickupDate)
            && false === strpos($html, 'BEGIN `Ziffity: Pickup Date`')
        ) {
            $html .= $insert->toHtml();
        }

        return $html;
    }
}
