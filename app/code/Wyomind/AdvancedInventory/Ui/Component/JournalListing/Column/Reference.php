<?php

/**
 * Copyright © 2017 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Ui\Component\JournalListing\Column;

class Reference extends \Magento\Ui\Component\Listing\Columns\Column
{

    protected $_helperCore;
    protected $_posModel;
    protected $_productModel;
    protected $_orderModel;
    protected $_storeManager;
    protected $_helperData;

    /**
     * 
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
            \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
            \Wyomind\Core\Helper\Data $helperCore,
            \Wyomind\PointOfSale\Model\PointOfSale $posModel,
            \Magento\Catalog\Model\Product $productModel,
            \Magento\Sales\Model\Order $orderModel,
            \Magento\Store\Model\StoreManager $_storeManager,
            \Wyomind\AdvancedInventory\Helper\Data $helperData,
            array $components = [],
            array $data = []
    )
    {
        $this->_helperCore = $helperCore;
        $this->_posModel = $posModel;
        $this->_productModel = $productModel;
        $this->_orderModel = $orderModel;
        $this->_storeManager = $_storeManager;
        $this->_helperData = $helperData;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $title = [];
                foreach (explode(",", $item[$this->getData('name')]) as $ref) {
                    $ref = explode("#", $ref);
                    switch ($ref[0]) {
                        case "S":
                            $store = $this->_storeManager->getStore($ref[1]);
                            $group = $this->_storeManager->getGroup($store->getGroupId());
                            $website = $this->_storeManager->getWebsite($store->getWebsiteId());
                            $title[] = $website->getName() . " > " . $group->getName() . " > " . $store->getName();
                            break;
                        case "O":
                            $data = $this->_orderModel->load($ref[1])->getIncrementId();
                            $title[] = "Order #" . $data;
                            break;
                        case "P":
                            $data = $this->_productModel->load($ref[1])->getSku();
                            $title[] = "Sku : " . $data;
                            break;
                        case "W":
                            $data = $this->_posModel->load($ref[1])->getName();
                            $title[] = "WH/POS : " . $data;
                            break;
                    }
                }
                $item[$this->getData('name')] = "<span title=\"" . implode("\n", $title) . "\">" . ($item[$this->getData('name')]) . "</span>";
            }
        }

        return $dataSource;
    }

}
