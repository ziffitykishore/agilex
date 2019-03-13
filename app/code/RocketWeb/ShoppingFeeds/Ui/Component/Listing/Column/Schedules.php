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

/**
 * Class Schedules
 */
class Schedules extends Column
{
    const SCHEDULE_PARAGRAPH_CLASS = 'class="schedule"';

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
     * @param FeedFactory $feedFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FeedFactory $feedFactory,
        array $components = [],
        array $data = []
    ) {
        $this->feedFactory = $feedFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
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
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['id'])) {
                    $feed = $this->feedFactory->create()->setData($item);
                    $formattedSchedules = [];
                    foreach ($feed->getFormattedSchedules() as $schedule) {
                        $formattedSchedules[] = sprintf('<p %s>%s</p>', self::SCHEDULE_PARAGRAPH_CLASS, $schedule);
                    }
                    $item[$name] = implode($formattedSchedules);
                }
            }
        }

        return $dataSource;
    }
}
