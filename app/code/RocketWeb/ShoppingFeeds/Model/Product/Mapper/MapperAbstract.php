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

namespace RocketWeb\ShoppingFeeds\Model\Product\Mapper;

/**
 * Abstract class, defining main final methods
 *
 * Class MapperAbstract
 * @package RocketWeb\ShoppingFeeds\Model\Product\Mapper
 */
abstract class MapperAbstract implements MapperInterface
{

    /**
     * Holds the adapter array
     * 
     * @var array
     */
    protected $adapter = array();

    /**
     * Holds mapper configurations
     *
     * @var array
     */
    protected $configuration = array();

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Logger
     */
    protected $logger;

    /**
     * MapperAbstract constructor.
     *
     * @param \RocketWeb\ShoppingFeeds\Model\Logger $logger
     */
    public function __construct(\RocketWeb\ShoppingFeeds\Model\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract
     */
    final public function getAdapter()
    {
        return $this->adapter[count($this->adapter) - 1];
    }

    /**
     * @param \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $adapter
     * @return \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperAbstract
     */
    final public function addAdapter($adapter)
    {
        array_push($this->adapter, $adapter);
        return $this;
    }

    /**
     * @return \RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Simple
     */
    final public function popAdapter()
    {
        return array_pop($this->adapter);
    }

    /**
     * Getter for mapper configuration
     *
     * @param string $path
     * @return bool
     */
    final public function getConfiguration($path)
    {
        return isset($this->configuration[$path]) ? $this->configuration[$path] : false;
    }

    /**
     * Setter for mapper configuration
     *
     * @param string $path
     * @param mixed $value
     *
     * @return $this
     */
    final public function setConfiguration($path, $value)
    {
        $this->configuration[$path] = $value;
        return $this;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    final public function hasConfiguration($path)
    {
        return array_key_exists($path, $this->configuration);
    }

    /**
     * @return MapperAbstract
     */
    final public function resetConfiguration()
    {
        $this->configuration = array();
        return $this;
    }

    /**
     * @param $cell
     * @return boolean
     */
    public function filter($cell)
    {
        return false;
    }
}