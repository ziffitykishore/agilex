<?php

namespace Ziffity\AjaxLogin\Helper\Wishlist;

use Magento\Wishlist\Controller\WishlistProviderInterface;


class Data extends \Magento\Wishlist\Helper\Data
{
    /**
     * Currently logged in customer
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $_currentCustomer;

    /**
     * Customer Wishlist instance
     *
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_wishlist;

    /**
     * Wishlist Product Items Collection
     *
     * @var \Magento\Wishlist\Model\ResourceModel\Item\Collection
     */
    protected $_productCollection;

    /**
     * Wishlist Items Collection
     *
     * @var \Magento\Wishlist\Model\ResourceModel\Item\Collection
     */
    protected $_wishlistItemCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    
    /**
     * @var \Ziffity\AjaxLogin\Helper\Data $blockHelper 
     */
    protected $blockHelper;
    
    /**
     * @param \Magento\Framework\App\Helper\Context           $context
     * @param \Magento\Framework\Registry                     $coreRegistry
     * @param \Magento\Customer\Model\Session                 $customerSession
     * @param \Magento\Wishlist\Model\WishlistFactory         $wishlistFactory
     * @param \Magento\Store\Model\StoreManagerInterface      $storeManager
     * @param \Magento\Framework\Data\Helper\PostHelper       $postDataHelper
     * @param \Magento\Customer\Helper\View                   $customerViewHelper
     * @param WishlistProviderInterface                       $wishlistProvider
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Ziffity\AjaxLogin\Helper\Data                  $blockHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Customer\Helper\View $customerViewHelper,
        WishlistProviderInterface $wishlistProvider,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Ziffity\AjaxLogin\Helper\Data $blockHelper
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_storeManager = $storeManager;
        $this->_postDataHelper = $postDataHelper;
        $this->_customerViewHelper = $customerViewHelper;
        $this->wishlistProvider = $wishlistProvider;
        $this->productRepository = $productRepository;
        $this->blockHelper = $blockHelper;
        parent::__construct(
            $context,
            $coreRegistry,
            $customerSession,
            $wishlistFactory,
            $storeManager,
            $postDataHelper,
            $customerViewHelper,
            $wishlistProvider,
            $productRepository
        );
    }
    
    public function isCustomerLogIn()
    {
        return $this->blockHelper->customerIsAlreadyLoggedIn();
    }
    
}
