<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */

namespace RocketWeb\ShoppingFeeds\Model\Product\Adapter;

/**
 * Factory class for \RocketWeb\ShoppingFeeds\Model\Product\Adapter\*
 */
class AdapterFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instances = [];

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param \RocketWeb\ShoppingFeeds\Model\Feed $feed
     * @param \Magento\Catalog\Model\Product $product
     * @return \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterInterface
     */
    public function create(\Magento\Catalog\Model\Product $product, \RocketWeb\ShoppingFeeds\Model\Feed $feed, $singleton = true)
    {
        try {
            $productType = $product->getTypeId();

            if (!isset($this->_instances[$productType]) || !$singleton) {
                $className = sprintf('RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\%s', ucfirst($productType));
                if (!class_exists($className)) {
                    //We switch to Simple adapter
                    $className = sprintf('RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Simple');

                }
                $object = $this->_objectManager->create($className, [
                    'product' => $product,
                    'feed'    => $feed
                ]);
                if ($singleton) {
                    $this->_instances[$productType] = $object;
                } else {
                    return $object;
                }
            } else {
                $this->_instances[$productType]
                    ->unsetData()
                    ->setProduct($product)
                    ->setFeed($feed)
                    ->setAdapterData();
            }

            return $this->_instances[$productType];
        } catch (\Exception $e) {
            return false;
        }
    }
}
