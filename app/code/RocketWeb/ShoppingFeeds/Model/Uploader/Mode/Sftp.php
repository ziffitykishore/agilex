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

namespace RocketWeb\ShoppingFeeds\Model\Uploader\Mode;

use \RocketWeb\ShoppingFeeds\Model\Uploader\UploaderAbstract;
use \RocketWeb\ShoppingFeeds\Model\Uploader\UploaderInterface;

/**
* Sftp Uploader
*
*/
class Sftp extends UploaderAbstract implements UploaderInterface
{
    const DEFAULT_TIMEOUT = 10;

    /**
     * @var \Magento\Framework\Filesystem\Io\Sftp
     */
    protected $connection;

    /**
     * Ftp constructor.
     *
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\Upload $feedUpload
     * @param \Magento\Framework\Filesystem\Io\Sftp $connection
     * @param array $data
     */
    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Feed\Upload $feedUpload,
        \Magento\Framework\Filesystem\Io\Sftp $connection,
        array $data = []
    ) {
        $this->connection = $connection;
        parent::__construct($feedUpload, $data);
    }

    /**
     * Provides connection configuration for connection class
     *
     * @param $config
     * @return array
     */
    public function getConnectionConfiguration($config)
    {
        return [
            'host'     => sprintf('%s:%s', $config['host'], $config['port']),
            'username' => $config['username'],
            'password' => $config['password'],
            'timeout'  => self::DEFAULT_TIMEOUT,
        ];
    }
}