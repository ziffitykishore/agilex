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

namespace Mageplaza\QuickOrder\Block;

use Magento\Framework\DataObject;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\QuickOrder\Helper\Data;
use Mageplaza\QuickOrder\Helper\Item as ItemHelper;

/**
 * Class Dashboard
 * @package Mageplaza\QuickOrder\Block
 */
class Dashboard extends Template
{
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var FormatInterface
     */
    protected $localeFormat;

    /**
     * Dashboard constructor.
     * @param Context $context
     * @param Data $helperData
     * @param ItemHelper $itemHelper
     * @param FormatInterface $localeFormat
     */
    public function __construct(
        Context $context,
        Data $helperData,
        ItemHelper $itemHelper,
        FormatInterface $localeFormat
    ) {
        $this->_helperData = $helperData;
        $this->localeFormat = $localeFormat;

        parent::__construct($context);
    }

    /**
     * get url quick order suffix
     *
     * @return string
     */
    public function getPageTitle()
    {
        $storeId = $this->_helperData->getStoreId();

        return $this->_helperData->getPageTitle($storeId);
    }

    /**
     * get url quick order suffix
     *
     * @return string
     */
    public function getQuickOrderLabel()
    {
        $storeId = $this->_helperData->getStoreId();

        return $this->_helperData->getQuickOrderLabel($storeId);
    }

    /**
     * @return string
     */
    public function getQuickOrderConfig()
    {
        $data = new DataObject([
            'changeOption'  => $this->getUrl('quickorder/items/changeoption'),
            'buildItemUrl'  => $this->getUrl('quickorder/items/preitem/'),
            'downloadCsv'   => $this->getUrl('quickorder/index/downloadcsv'),
            'addCartAction' => $this->getUrl('quickorder/items/cartcheckout/'),
            'cartpage'      => $this->getUrl('checkout/cart/'),
            'checkoutStep'  => $this->getUrl('checkout/'),
            'itemqty'       => $this->getUrl('quickorder/items/itemqty'),
            'bundleitemqty' => $this->getUrl('quickorder/items/bundleitemqty'),
            'lazyload'      => $this->_assetRepo->getUrlWithParams('images/loader-1.gif', [
                '_secure' => $this->_request->isSecure()
            ]),
            'priceFormat'   => $this->localeFormat->getPriceFormat()
        ]);

        return Data::jsonEncode($data);
    }
}
