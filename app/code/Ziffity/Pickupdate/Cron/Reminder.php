<?php

namespace Ziffity\Pickupdate\Cron;

use Magento\Framework\App\ResourceConnection;

class Reminder
{

    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate\Collection
     */
    protected $pickupdateCollection;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $order;
    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    public function __construct(
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate\Collection $pickupdateCollection,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\ResourceModel\Order $order,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {

        $this->pickupdateCollection = $pickupdateCollection;
        $this->orderFactory = $orderFactory;
        $this->order = $order;
        $this->pickupHelper = $pickupHelper;
        $this->date = $date;
        $this->transportBuilder = $transportBuilder;
    }

    public function execute()
    {
        $collection = $this->pickupdateCollection;
        $collection->addFieldToFilter('reminder', 0);
        $collection->getSelect()->where('`date` <> \'0000-00-00\'');

        if (0 < $collection->getSize()) {
            foreach ($collection as $pickupDate) {
                $order = $this->orderFactory->create();
                $this->order->load($order, $pickupDate->getOrderId());
                $storeId = $order->getStoreId();
                if ($this->pickupHelper->getDefaultScopeValue('reminder/enabled_reminder')) {
                    // 60 min. * 60 sec. = 3600 sec.
                    $now = $this->date->date('U') + 3600 * $this->pickupHelper->getDefaultScopeValue('general/offset');
                    $timeBefore = 3600 * $this->pickupHelper->getDefaultScopeValue('reminder/time_before', $storeId);
                    $threshold = $this->date->timestamp($pickupDate->getDate()) - $timeBefore;
                    if ($now >= $threshold) {
                        // send email
                        $emails = $this->pickupHelper->getDefaultScopeValue('reminder/recipient_email');
                        $emails = explode(',', $emails);

                        if (!empty($emails)) {
                            $templateId = $this->pickupHelper->getDefaultScopeValue('reminder/email_template');
                            $templateId = $templateId ? $templateId : 'pickupdate_reminder_email_template';

                            foreach ($emails as $email) {
                                $email = trim($email);
                                $vars = [
                                    'pickup' => $pickupDate,
                                    'order'    => $order,
                                ];
                                $this->transportBuilder
                                    ->setTemplateIdentifier($templateId)
                                    ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                                    ->setTemplateVars($vars)
                                    ->setFrom($this->pickupHelper->getDefaultScopeValue('reminder/reminder_sender'))
                                    ->addTo($email);

                                $transport = $this->transportBuilder->getTransport();
                                $transport->sendMessage();

                            }
                            $pickupDate->setReminder(1);
                            $pickupDate->save();
                        }
                    }
                }
            }
        }
    }
}