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

namespace RocketWeb\ShoppingFeeds\Block\Adminhtml\Feed;

class Viewlog extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    protected $readFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->directoryList = $directoryList;
        $this->readFactory = $readFactory;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }
    
    /**
     * Before rendering html, but after trying to load cache
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $file = false;
        $feed = $this->registry->registry('feed');
        $mageRootPath = $this->directoryList->getRoot();

        $feedFolder = $mageRootPath . '/' . $feed->getConfig('general_feed_dir');
        $feedLogFile = sprintf($feed->getConfig('file_log'), $feed->getId());
        $directoryReader = $this->readFactory->create($feedFolder);

        if ($directoryReader->isExist($feedLogFile)) {
            try {
                $fileReader = $directoryReader->openFile($feedLogFile);
                $file = $fileReader->readAll();
            } catch (\Exception $e) {
                $this->setError(__('Log file cannot be read. Check permissions'));
            }
        } else {
            $this->setError(__('Log file was not found'));
        }
        if ($file && !$this->getError()) {
            $this->setFileLines(explode(PHP_EOL, $file));
        } else {
            $this->setError(__('Log file %1 contains no records', str_replace($mageRootPath, '', $feedLogFile)));
        }
        return $this;
    }
}
