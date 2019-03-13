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

namespace RocketWeb\ShoppingFeeds\Model\Product\Formatter;

/**
 * Abstract class, defining main final methods
 *
 * Class FormatterAbstract
 * @package RocketWeb\ShoppingFeeds\Model\Product\Formatter
 */
abstract class FormatterAbstract implements FormatterInterface
{
    /**
     * Holds the mapper object
     *
     * @var array
     */
    protected $adapter = array();

    /**
     * @return \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract
     */
    final public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract
     * @return \RocketWeb\ShoppingFeeds\Model\Product\Formatter\FormatterAbstract
     */
    final public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @param $var
     * @return mixed
     */
    public function run($var)
    {
        return $var;
    }
}