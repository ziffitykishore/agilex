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
use Magento\Framework\UrlInterface;

/**
 * Class FeedActions
 */
class FeedActions extends Column
{
    /** Url path */
    const FEED_URL_PATH_TEST = 'shoppingfeeds/feed/test';
    const FEED_URL_PATH_GENERATE = 'shoppingfeeds/feed/generate';
    const FEED_URL_PATH_VIEWLOG = 'shoppingfeeds/feed/viewlog';
    const FEED_URL_PATH_EDIT = 'shoppingfeeds/feed/edit';

    /** 
     * @var UrlInterface 
     */
    protected $urlBuilder;
    
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
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
                    $item[$name] = '<a href="' . $this->urlBuilder->getUrl(self::FEED_URL_PATH_GENERATE, ['id' => $item['id']]). '">'. __('Run Now'). '</a>'
                        . ' / <a href="' . $this->urlBuilder->getUrl(self::FEED_URL_PATH_EDIT, ['id' => $item['id']]). '">'. __('Configure'). '</a>'
                        . ' <br /><a popup="1" href="'. $this->urlBuilder->getUrl(self::FEED_URL_PATH_TEST, ['id' => $item['id']]). '" onclick="window.open(this.href,\'feed_test\',\'width=1010,height=700,resizable=1,scrollbars=1\');return false;">'. __('Test Feed'). '</a>'
                        . ' / <a popup="1" href="'. $this->urlBuilder->getUrl(self::FEED_URL_PATH_VIEWLOG, ['id' => $item['id']]). '" onclick="window.open(this.href,\'feed_logs\',\'width=835,height=700,resizable=1,scrollbars=1\');return false;">'. __('View Log'). '</a>';
                }
            }
        }
        
        return $dataSource;
    }
}
