<?php

namespace Earthlite\ProductAlert\Model;

use Magento\ProductAlert\Model\Email;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\ProductAlert\Block\Email\AbstractEmail;
use Magento\ProductAlert\Block\Email\Price;
use Magento\ProductAlert\Block\Email\Stock;
use Magento\ProductAlert\Helper\Data;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;

class StockAlertEmail extends Email
{
    /**
     * @param Context $context
     * @param Registry $registry
     * @param Data $productAlertData
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param View $customerHelper
     * @param Emulation $appEmulation
     * @param TransportBuilder $transportBuilder
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $productAlertData,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        View $customerHelper,
        Emulation $appEmulation,
        TransportBuilder $transportBuilder,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $productAlertData,
            $scopeConfig,
            $storeManager,
            $customerRepository,
            $customerHelper,
            $appEmulation,
            $transportBuilder,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Send stock alert email to guest users.
     * 
     * @param string $email
     * @param int $storeId
     * @return bool
     */
    public function sendEmailToGuest($email, $storeId)
    {
        if ($this->_website === null || $email === null || !$this->isExistDefaultStore()) {
            return false;
        }

        $products = $this->getProducts();
        $templateConfigPath = $this->getTemplateConfigPath();
        if (!in_array($this->_type, ['price', 'stock']) || count($products) === 0 || !$templateConfigPath) {
            return false;
        }

        $store = $this->getStore($storeId);
        $this->_appEmulation->startEnvironmentEmulation($storeId);

        $block = $this->getBlock();
        $block->setStore($store)->reset();

        // Add products to the block
        foreach ($products as $product) {
            $product->setCustomerGroupId(0);
            $block->addProduct($product);
        }

        $templateId = $this->_scopeConfig->getValue(
            $templateConfigPath,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $alertGrid = $this->_appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$block, 'toHtml']
        );
        $this->_appEmulation->stopEnvironmentEmulation();

        $this->_transportBuilder->setTemplateIdentifier(
            $templateId
        )->setTemplateOptions(
            ['area' => Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            [
                'customerName' => false,
                'alertGrid' => $alertGrid,
            ]
        )->setFrom(
            $this->_scopeConfig->getValue(
                self::XML_PATH_EMAIL_IDENTITY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        )->addTo(
            $email
        )->getTransport()->sendMessage();

        return true;   
    }

    /**
     * Retrieve the store for the email
     *
     * @param int $storeId
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    private function getStore(int $storeId): StoreInterface
    {
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Retrieve the block for the email based on type
     *
     * @return Price|Stock
     * @throws LocalizedException
     */
    private function getBlock(): AbstractEmail
    {
        return $this->_type === 'price'
            ? $this->_getPriceBlock()
            : $this->_getStockBlock();
    }

    /**
     * Retrieve the products for the email based on type
     *
     * @return array
     */
    private function getProducts(): array
    {
        return $this->_type === 'price'
            ? $this->_priceProducts
            : $this->_stockProducts;
    }

    /**
     * Retrieve template config path based on type
     *
     * @return string
     */
    private function getTemplateConfigPath(): string
    {
        return $this->_type === 'price'
            ? self::XML_PATH_EMAIL_PRICE_TEMPLATE
            : self::XML_PATH_EMAIL_STOCK_TEMPLATE;
    }

    /**
     * Check if exists default store.
     *
     * @return bool
     */
    private function isExistDefaultStore(): bool
    {
        if (!$this->_website->getDefaultGroup() || !$this->_website->getDefaultGroup()->getDefaultStore()) {
            return false;
        }
        return true;
    }
}
