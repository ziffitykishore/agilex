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

namespace RocketWeb\ShoppingFeeds\Model\Taxonomy;

/**
 * Factory class for \RocketWeb\ShoppingFeeds\Model\Taxonomy\Type\*
 */
class ProviderFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config
     */
    protected $feedTypesConfig;

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
     * @param \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config $feedTypesConfig
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config $feedTypesConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->feedTypesConfig = $feedTypesConfig;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param \RocketWeb\ShoppingFeeds\Model\Feed $feed
     * @return \RocketWeb\ShoppingFeeds\Model\Taxonomy\ProviderInterface
     */
    public function create(\RocketWeb\ShoppingFeeds\Model\Feed $feed, $singleton = true)
    {
        try {
            $feedType = $feed->getType();

            if (!isset($this->_instances[$feedType]) || !$singleton) {

                $className = $this->feedTypesConfig->getTaxonomyProvider($feedType);

                if (!class_exists($className)) {
                    $className = 'RocketWeb\ShoppingFeeds\Model\Taxonomy\Type\Generic';
                }

                $object = $this->_objectManager->create($className, [
                    'feed'    => $feed
                ]);
                if ($singleton) {
                    $this->_instances[$feedType] = $object;
                } else {
                    return $object;
                }
            } else {
                $this->_instances[$feedType]
                    ->unsetData()
                    ->setFeed($feed);
            }

            return $this->_instances[$feedType];
        } catch (\Exception $e) {
            return false;
        }
    }
}
