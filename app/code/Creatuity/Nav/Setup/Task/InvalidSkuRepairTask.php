<?php

namespace Creatuity\Nav\Setup\Task;

use Magento\Framework\DB\TransactionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Creatuity\Nav\Model\Task\TaskInterface;
use Creatuity\Nav\Model\Service\Service;

class InvalidSkuRepairTask implements TaskInterface
{
    protected $productRepository;
    protected $transactionFactory;
    protected $itemService;
    protected $skuRenameMap;
    protected $skuDisableMap;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        TransactionFactory $transactionFactory,
        Service $itemService,
        array $skuRenameMap,
        array $skuDisableMap
    )
    {
        $this->productRepository = $productRepository;
        $this->transactionFactory = $transactionFactory;
        $this->itemService = $itemService;
        $this->skuRenameMap = $skuRenameMap;
        $this->skuDisableMap = $skuDisableMap;
    }

    public function execute()
    {
        $transaction = $this->transactionFactory->create();

        foreach ($this->skuRenameMap as $fromSku => $toSku) {
            $product = $this->productRepository
                ->get($fromSku)
                ->setSku($toSku)
            ;
            $transaction->addObject($product);
        }

        foreach ($this->skuDisableMap as $sku) {
            $product = $this->productRepository
                ->get($sku)
                ->setStatus(Status::STATUS_DISABLED)
            ;
            $transaction->addObject($product);
        }

        $transaction->save();
    }
}
