<?php
declare(strict_types = 1);
namespace Earthlite\BackOrders\Model;

use Magento\Sales\Model\Order;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Directory\Model\Currency;

class OrderItems
{
    const CONFIG_MODULE_PATH = 'earthlite_backorders';
    /**
     *
     * @var productFactory
     */
    protected $productFactory;
    
    /**
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var ProductRepositoryInterfaceFactory
     */
    protected $productRepositoryInterface;

    protected $currency;

    public function __construct(
        ProductFactory $productFactory,
        ScopeConfigInterface $scopeConfig,
        ProductRepositoryInterfaceFactory $productRepositoryInterface,
        Currency $currency
    ) {
        $this->productFactory = $productFactory;
        $this->scopeConfig = $scopeConfig;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->currency = $currency;
    }

    /**
     * 
     * @param Order $order
     * @return array
     */
    public function getDelayedItems(Order $order)
    {
        $delayedProductDetails = [];
        foreach ($order->getAllVisibleItems() as $item) 
        {
            $productModel = $this->productFactory->create();
            if ($item->getQtyOrdered() != $item->getQtyShipped() && $productModel->getIdBySku($item->getSku())) {
                $productRepository = $this->productRepositoryInterface->create();
                $productRepository->cleanCache();
                $productDetails = $productRepository->get($item->getSku());
                if($productDetails->getTypeId() != 'virtual') {
                    $unShippedQty = $item->getQtyOrdered()-$item->getQtyShipped();
                    $price = $this->currency->format($item->getPrice(), array(), false, false);
                    $delayedProductDetails[] = ['name' => $item->getName(), 'sku' => $item->getSku(), 'qty' => $unShippedQty, 'price' => $price];
                }
            }
        }
        
        return $delayedProductDetails;
    }

    /**
     * 
     * @return string
     */
    public function getStorename()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * 
     * @return string
     */
    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            ScopeInterface::SCOPE_STORE
        );
    }


    public function isEnabled($storeId = null)
    {        
        return $this->getConfigGeneral('enabled', $storeId);
    }

    public function getConfigGeneral($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/general' . $code, $storeId);
    }


    public function getConfigValue($fullPath, $storeId)
    {        
        return $this->scopeConfig->getValue($fullPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }  
}
