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

namespace RocketWeb\ShoppingFeeds\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use RocketWeb\ShoppingFeeds\Model\FeedFactory;
use RocketWeb\ShoppingFeeds\Model\Feed\Source;

/**
 * Class Status
 */
class Status extends Column
{
    const MAP_STATUS_CLASS = [
        Source\Status::STATUS_COMPLETED     => 'grid-severity-notice',
        Source\Status::STATUS_DISABLED      => 'grid-severity-minor',
        Source\Status::STATUS_SCHEDULED     => 'grid-severity-minor',
        Source\Status::STATUS_PENDING       => 'grid-severity-major',
        Source\Status::STATUS_PROCESSING    => 'grid-severity-critical',
        Source\Status::STATUS_ERROR         => 'grid-severity-critical'
    ];

    /**
     * Feed status source model
     *
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Status
     */
    protected $sourceStatus;

    /**
     * Feed model factory
     *
     * @var \RocketWeb\ShoppingFeeds\Model\FeedFactory
     */
    protected $feedFactory;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\Source\Status $sourceStatus
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \RocketWeb\ShoppingFeeds\Model\Feed\Source\Status $sourceStatus,
        FeedFactory $feedFactory,
        array $components = [],
        array $data = []
    ) {
        $this->sourceStatus = $sourceStatus;
        $this->feedFactory = $feedFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param $status
     * @return string
     */
    protected function getClassByStatus($status)
    {
        $class = '';
        if (array_key_exists($status, self::MAP_STATUS_CLASS)) {
            $class = self::MAP_STATUS_CLASS[$status];
        }
        return $class;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $statuses = $this->sourceStatus->getOptionArray();

            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['id'])) {
                    $statusText = isset($statuses[$item[$name]]) ? $statuses[$item[$name]] : '';

                    if ($item[$name] == \RocketWeb\ShoppingFeeds\Model\Feed\Source\Status::STATUS_PROCESSING) {
                        $messages = $this->feedFactory->create()->setData($item)->getMessages();
                        $statusText = $messages['progress']. '%';
                    }

                    $statusClass = $this->getClassByStatus($item[$name]);
                    $item[$name] = sprintf('<span class="%s"><span>%s</span></span>', $statusClass, $statusText);
                }
            }
        }

        return $dataSource;
    }
}
