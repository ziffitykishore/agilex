<?php
/**
 * Core Helper to use all needed methods
 */

namespace Ziffity\Promo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Store\Model\ScopeInterface;
use \Magento\CatalogInventory\Api\StockStateInterface;
use \Magento\CatalogInventory\Api\StockItemRepositoryInterface;

class Data extends AbstractHelper
{

    protected $_request;
    protected $_checkoutSession;
    protected $_productFactory;
    protected $_scopeConfig;
    protected $_messageManager;
    protected $stockStateInterface;
    protected $stockItemRepInterface;

    /**
     * Constructor
     * @param Context $context
     * 
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
         StockStateInterface $stockStateInterface,
         StockItemRepositoryInterface $stockItemRepInterface
    ) {
        $this->scopeConfig  = $context->getScopeConfig();
        $this->_request = $context->getRequest();
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_messageManager = $messageManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_productFactory = $productFactory;
        $this->stockStateInterface = $stockStateInterface;
        $this->stockItemRepInterface = $stockItemRepInterface;    
        parent::__construct($context);
    }

    public function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    public function getMessageInterface()
    {
        return $this->_messageManager;
    }
    
    // find qty 
    // (for the whole cart it is $rule->getDiscountQty()
    // for items it is (qty * (number of matched non-free items) / step)
    public function _getFreeItemsQty($rule, $quote)
    {
        $amount = max(1, $rule->getDiscountAmount());
        $qty = 0;
        if ('ampromo_cart' == $rule->getSimpleAction()){
            $qty = $amount;
        }
        else {
            $step = max(1, $rule->getDiscountStep());
            foreach ($quote->getItemsCollection() as $item) {
                if (!$item) 
                    continue;
                if ($item->getOptionByCode('ampromo_rule')) 
                    continue;                    
                if (!$rule->getActions()->validate($item)) {
                    continue;
                }
               $qty = $qty + $item->getQty();
            }
            $qty = floor($qty / $step) * $amount; 
            $max = $rule->getDiscountQty();
            if ($max){
                $qty = min($max, $qty);
            }
        }
        return $qty; 
    }
            
    public function _loadProduct($sku, $qty)
    {
        // don't add already removed items
        $arr = $this->_checkoutSession->getAmpromoDeletedItems();
        if (!is_array($arr)){
            $arr = array();
        }
        if (isset($arr[$sku])){
            if ($this->_request->getControllerName() == 'cart'){
                $message  = __('Your cart has deleted free items. <a href="%s">Restore them</a>?', $this->getUrl('ampromo/cart/restore'));
                $this->_showMessage($message, false, true);
            }
            return false;
        }

        // we have to load each product individually
        $product = $this->_productFactory->create();
        $product->load($product->getIdBySku($sku));
	    
        if (!$product->getId()){
            $this->_showMessage(__(
                'We apologise, but there is no promo item with the SKU `%s` in the catalog', $sku
            ));
            return false;
        }

	if (Status::STATUS_ENABLED != $product->getStatus()){
            $this->_showMessage(__(
                'We apologise, but promo item with the SKU `%s` is not available', $sku
            ));
	    return false;
	}

        $hasQty  = $this->stockStateInterface->checkQty($product->getId(), $qty);
        $inStock = $this->stockItemRepInterface->get($product->getId())->getIsInStock();

        if (!$inStock || !$hasQty){
            $this->_showMessage(__(
                'We apologise, but there are no %d item(s) with the SKU `%s` in the stock', $qty, $sku
            ));
            return false;
        }
        return $product;        
    }

    public function _addProductToQuote($quote, $product, $qty)
    {
        try {
            if ('multishipping' === $this->_request->getControllerName()){
                return false;
            }

            $product->addCustomOption('ampromo_rule', 1);
            $item  = $quote->getItemByProduct($product);
            if ($item) {  
                return false;       
            }

            $item = $quote->addProduct($product, $qty);
            // required custom options or configurable product
            if (!is_object($item)){ 
                throw new Exception($item);   
            }
            
            $item->setCustomPrice(0); 
            $item->setOriginalCustomPrice(0); 
            
            $prefix = $this->_scopeConfig->getValue('ampromo/general/prefix', ScopeInterface::SCOPE_STORE);
            if ($prefix){
                $item->setName($prefix . ' ' . $item->getName());
            }

            $customMessage = $this->_scopeConfig->getValue('ampromo/general/message', ScopeInterface::SCOPE_STORE);
            if ($customMessage){
                $item->setMessage($customMessage);
            }            
        }
        catch (Exception $e){
            $this->_showMessage(__(
                'We apologise, but there is an error while adding free items to the cart: %s', $e->getMessage()
            ));            
            return false;
        }
        return true;        
    }

    public function _showMessage($message, $isError = true, $showEachTime=false) 
    { 
        // show on cart page only
        $all = $this->_checkoutSession->getMessages(false)->toString();
        if (false !== strpos($all, $message))
            return;
            
        if ($isError && isset($_GET['debug'])){
            $this->messageManager->addError($message);
        }
        else {
            $arr = $this->_checkoutSession->getAmpromoMessages();
            if (!is_array($arr)){
                $arr = array();
            }
            if (!in_array($message, $arr) || $showEachTime){
                $this->messageManager->addNotice($message);
                $arr[] = $message;
                $this->_checkoutSession->setAmpromoMessages($arr);
            }
        }
    }
}
