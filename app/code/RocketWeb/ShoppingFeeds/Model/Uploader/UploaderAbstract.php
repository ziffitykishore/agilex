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

namespace RocketWeb\ShoppingFeeds\Model\Uploader;

/**
 * Class UploaderAbstract
 */
abstract class UploaderAbstract extends \Magento\Framework\DataObject
{
    /**
     * Feed Upload instance
     *
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Upload
     */
    protected $feedUpload;

    /**
     * UploaderAbstract constructor.
     * 
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\Upload $feedUpload
     * @param array $data
     */
    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Feed\Upload $feedUpload,
        array $data = []
    ) {
        $this->feedUpload = $feedUpload;
        parent::__construct($data);
    }

    /**
     * Uploads feed to remote server.
     *
     * @param string $filePath
     * @return bool
     */
    public function upload($filePath)
    {
        $fileName = basename($filePath);

        $connection  = $this->getConnection();
        return $connection->write($fileName, $filePath);
    }

    /**
     * Tries to create connection just to check if credentials work.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkConnection()
    {
        try {
            $connection  = $this->getConnection();
            return true;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Cannot validate connection for host="' . $this->feedUpload->getHost(). '". An error "' .
                    $e->getMessage() . '" occurred')
            );
        }
    }

    /**
     * Connect to a server using specified configuration
     *
     * @return \Magento\Framework\Filesystem\Io\IoInterface
     * @throws \InvalidArgumentException
     */
    public function getConnection()
    {
        $config = $this->feedUpload->getData();

        if (!isset(
                $config['host']
            ) || !isset(
                $config['username']
            ) || !isset(
                $config['password']
            ) || !isset(
                $config['path']
            )
        ) {
            throw new \InvalidArgumentException('Required config elements: host, username, password, path');
        }

        $this->connection->open($this->getConnectionConfiguration($config));

        if (strlen($config['path'])) {
            $this->connection->cd($config['path']);
        }

        return $this->connection;
    }

    /**
     * Provides connection configuration for connection class
     *
     * @param $config
     * @return array
     */
    abstract public function getConnectionConfiguration($config);
}