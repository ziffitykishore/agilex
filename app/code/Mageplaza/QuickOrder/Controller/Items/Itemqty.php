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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Mageplaza\QuickOrder\Helper\Item as QodItemHelper;

/**
 * Class Itemqty
 * @package Mageplaza\QuickOrder\Controller\Items
 */
class Itemqty extends Action
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var QodItemHelper
     */
    protected $_itemHelper;

    /**
     * @var JsonHelper
     */
    protected $_jsonHelper;

    /**
     * Itemqty constructor.
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param QodItemHelper $itemHelper
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        QodItemHelper $itemHelper,
        JsonHelper $jsonHelper
    ) {
        $this->_productRepository = $productRepository;
        $this->_itemHelper = $itemHelper;
        $this->_jsonHelper = $jsonHelper;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $itemSku = $this->getRequest()->getParam('itemsku');
        $currentQty = $this->getRequest()->getParam('currentQty');

        if (!$itemSku) {
            return $this->getResponse()->setBody(false);
        }
        $productItem = $this->_productRepository->get($itemSku);
        $productId = $productItem->getId();
        $itemQty = $this->_itemHelper->getProductQtyStock($skuChild = '', $productId);

        if ($productItem->getTypeId() === 'downloadable') {
            return $this->getResponse()->representJson($this->_jsonHelper->jsonEncode([
                'stockQtyofItem' => $currentQty
            ]));
        }
        if ($currentQty > $itemQty) {
            $itemName = $productItem->getName();

            return $this->getResponse()->representJson($this->_jsonHelper->jsonEncode([
                'overStock'      => $itemName,
                'stockQtyofItem' => $itemQty
            ]));
        }

        return $this->getResponse()->representJson($this->_jsonHelper->jsonEncode(['stockQtyofItem' => $itemQty]));
    }
}