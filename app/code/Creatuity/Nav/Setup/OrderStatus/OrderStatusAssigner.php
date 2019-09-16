<?php

namespace Creatuity\Nav\Setup\OrderStatus;

use Magento\Sales\Model\Order\StatusFactory;

class OrderStatusAssigner
{
    protected $statusCode;
    protected $statusLabel;
    protected $assignedState;
    protected $statusFactory;

    public function __construct(
        $statusCode,
        $statusLabel,
        $assignedState,
        StatusFactory $statusFactory
    ) {
        $this->statusCode = $statusCode;
        $this->statusLabel = $statusLabel;
        $this->assignedState = $assignedState;
        $this->statusFactory = $statusFactory;
    }

    public function assign()
    {
        $status = $this->statusFactory->create()->load($this->statusCode);
        if ($status->getStatus()) {
            return;
        }

        $this->statusFactory->create()
            ->addData([
                'status'       => $this->statusCode,
                'label'        => $this->statusLabel,
                'store_labels' => [],
            ])
            ->save()
            ->assignState($this->assignedState, true)
        ;
    }
}
