<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickOrder
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\QuickOrder\Controller\Items;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\QuickOrder\Helper\Data;
use Mageplaza\QuickOrder\Helper\Item as QodItemHelper;

/**
 * Class Changeoption
 * @package Mageplaza\QuickOrder\Controller\Items
 */
class Changeoption extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var QodItemHelper
     */
    protected $_itemhelper;

    /**
     * @var StoreManagerInterface
     */
    protected $_storemanager;

    /**
     * @var JsonHelper
     */
    protected $_jsonHelper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var PricingHelper
     */
    protected $_priceHelper;

    /**
     * Changeoption constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helperData
     * @param StoreManagerInterface $storeManager
     * @param JsonHelper $jsonHelper
     * @param QodItemHelper $itemhelper
     * @param PriceCurrencyInterface $priceCurrency
     * @param PricingHelper $priceHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helperData,
        StoreManagerInterface $storeManager,
        JsonHelper $jsonHelper,
        QodItemHelper $itemhelper,
        PriceCurrencyInterface $priceCurrency,
        PricingHelper $priceHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helperData = $helperData;
        $this->_storemanager = $storeManager;
        $this->_jsonHelper = $jsonHelper;
        $this->_itemhelper = $itemhelper;
        $this->priceCurrency = $priceCurrency;
        $this->_priceHelper = $priceHelper;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $store = $this->_helperData->getStore();
        $optionIds = $this->getRequest()->getParam('optionIds');
        $product_id = $this->getRequest()->getParam('product_id');

        foreach ($optionIds as $optionId) {
            $str = explode(':', $optionId);
            $productAttributeId[$str[0]] = $str[1];
        }

        $product_children_simple = $this->_itemhelper->getchidrenSimpleProudctByAttribute(
            $productAttributeId,
            $product_id
        );
        $sku_child = $product_children_simple->getSku();

        $qtyStock = $this->_itemhelper->getProductQtyStock($sku_child, $product_id);
        $imageURL = $this->_itemhelper->getProductImageUrl($sku_child, $product_id, $store);
        $price = $this->priceCurrency->round($this->_priceHelper->currencyByStore(
            $product_children_simple->getPrice(),
            $store,
            false,
            false
        ));

        return $this->getResponse()->representJson($this->_jsonHelper->jsonEncode([
            'qtyStock'  => $qtyStock,
            'price'     => $price,
            'imageURL'  => $imageURL,
            'sku_child' => $sku_child
        ]));
    }
}
