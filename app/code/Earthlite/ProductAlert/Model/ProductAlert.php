<?php

namespace Earthlite\ProductAlert\Model;

use Magento\ProductAlert\Model\Observer;

class ProductAlert extends Observer
{
    /**
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\ProductAlert\Model\ResourceModel\Price\CollectionFactory $priceColFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
     * @param \Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory $stockColFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\ProductAlert\Model\EmailFactory $emailFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\ProductAlert\Model\ProductSalability|null $productSalability
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\ProductAlert\Model\ResourceModel\Price\CollectionFactory $priceColFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory $stockColFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\ProductAlert\Model\EmailFactory $emailFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\ProductAlert\Model\ProductSalability $productSalability = null
    ) {
        parent::__construct(
            $catalogData,
            $scopeConfig,
            $storeManager,
            $priceColFactory,
            $customerRepository,
            $productRepository,
            $dateFactory,
            $stockColFactory,
            $transportBuilder,
            $emailFactory,
            $inlineTranslation,
            $productSalability
        );
    }

    /**
     * Process stock emails
     *
     * @param \Magento\ProductAlert\Model\Email $email
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _processStock(\Magento\ProductAlert\Model\Email $email)
    {
        $email->setType('stock');

        foreach ($this->_getWebsites() as $website) {
            /* @var $website \Magento\Store\Model\Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }
            if (!$this->_scopeConfig->getValue(
                self::XML_PATH_STOCK_ALLOW,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )
            ) {
                continue;
            }
            try {
                $collection = $this->_stockColFactory->create()->addWebsiteFilter(
                    $website->getId()
                )->addStatusFilter(
                    0
                )->setCustomerOrder();
            } catch (\Exception $e) {
                $this->_errors[] = $e->getMessage();
                throw $e;
            }

            $previousCustomer = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                $this->setAlertStoreId($alert, $email);
                try {
                    # Check whether the subscriber is already registered user
                    if ($alert->getCustomerId() != 0) {
                        if (!$previousCustomer || $previousCustomer->getId() != $alert->getCustomerId()) {
                            $customer = $this->customerRepository->getById($alert->getCustomerId());
                            if ($previousCustomer) {
                                $email->send();
                            }
                            if (!$customer) {
                                continue;
                            }
                            $previousCustomer = $customer;
                            $email->clean();
                            $email->setCustomerData($customer);
                        } else {
                            $customer = $previousCustomer;
                        }
                    }

                    $product = $this->productRepository->getById(
                        $alert->getProductId(),
                        false,
                        $website->getDefaultStore()->getId()
                    );

                    if ($alert->getCustomerId() == 0) {
                        $product->setCustomerGroupId(0);
                        $email->clean();
                    } else {
                        $product->setCustomerGroupId($customer->getGroupId());
                    }

                    if ($this->productSalability->isSalable($product, $website)) {
                        $email->addStockProduct($product);

                        if ($alert->getCustomerId() == 0) {
                            $email->sendEmailToGuest($alert->getEmail(), $alert->getStoreId());
                        }

                        $alert->setSendDate($this->_dateFactory->create()->gmtDate());
                        $alert->setSendCount($alert->getSendCount() + 1);
                        $alert->setStatus(1);
                        $alert->save();
                    }
                } catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                    throw $e;
                }
            }

            if ($previousCustomer) {
                try {
                    $email->send();
                } catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                    throw $e;
                }
            }
        }

        return $this;
    }

    /**
     * Set alert store id.
     *
     * @param \Magento\ProductAlert\Model\Price|\Magento\ProductAlert\Model\Stock $alert
     * @param Email $email
     * @return ProductAlert
     */
    private function setAlertStoreId($alert, \Magento\ProductAlert\Model\Email $email)
    {
        $alertStoreId = $alert->getStoreId();
        if ($alertStoreId) {
            $email->setStoreId((int)$alertStoreId);
        }

        return $this;
    }
}
