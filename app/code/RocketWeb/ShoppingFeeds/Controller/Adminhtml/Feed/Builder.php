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

namespace RocketWeb\ShoppingFeeds\Controller\Adminhtml\Feed;

use Magento\Framework\App\RequestInterface;

class Builder
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\FeedFactory
     */
    protected $feedFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Model\Session $session
     */
    protected $session;

    /**
     * @param \RocketWeb\ShoppingFeeds\Model\FeedFactory $feedFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\Session $session
     */
    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\FeedFactory $feedFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Session $session
    ) {
        $this->feedFactory = $feedFactory;
        $this->logger = $logger;
        $this->registry = $registry;
        $this->session = $session;
    }

    /**
     * Build feed based on user request
     *
     * @param array
     * @return \RocketWeb\ShoppingFeeds\Model\Feed
     */
    public function build($formData)
    {
        $feedId = (int) isset($formData['id']) ? $formData['id'] : 0;
        /** @var $feed \RocketWeb\ShoppingFeeds\Model\Feed */
        $feed = $this->feedFactory->create();
        $feed->setStoreId((int) isset($formData['store_id']) ? $formData['store_id'] : 0);

        $typeId = isset($formData['type']) ? $formData['type'] : null;

        if (!$feedId && $typeId) {
            $feed->setType($typeId);
        }

        if ($feedId) {
            try {
                $feed->load($feedId);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        if (isset($formData['schedules'])) {
            $feed->setSchedules($formData['schedules']);
        } elseif ($feed->isObjectNew() && empty($feed->getSchedules())) {
            $feed->setSchedules([['id' => null, 'feed_id' => null, 'start_at' => 1, 'batch_mode' => 0, 'batch_limit' => '']]);
        }

        $this->registry->register('feed', $feed);

        $currentFeed = null;
        $sessionData = $this->session->getFeedData(true);
        if (!empty($sessionData)) {
            $currentFeed = $sessionData;
        }
        $this->registry->register('current_feed', $currentFeed);

        return $feed;
    }
}
