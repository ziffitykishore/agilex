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

// @codingStandardsIgnoreFile

namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Feed\Source\Directory;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class AvailableCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    /** @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Directory\AvailableCurrencies */
    protected $sourceAvailableCurrencies;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
        $storeManager = $this->getMock('\Magento\Store\Model\StoreManagerInterface', [], [], '', false);

        /** @var \Magento\Directory\Model\CurrencyFactory $currencyFactory */
        $currencyFactory = $this->getMock('\Magento\Directory\Model\CurrencyFactory', ['create'], [], '', false);

        /** @var \Magento\Framework\Locale\Bundle\CurrencyBundle $currencyBundle */
        $currencyBundle = $this->getMock('\Magento\Framework\Locale\Bundle\CurrencyBundle', [], [], '', false);

        /** @var \Magento\Directory\Model\Currency $currency */
        $currency = $this->getMock('\Magento\Directory\Model\Currency', [], [], '', false);

        /** @var \Magento\Framework\Locale\Resolver $localeResolver */
        $localeResolver = $this->getMock('\Magento\Framework\Locale\Resolver', [], [], '', false);

        $store->expects($this->once())
            ->method('getAvailableCurrencyCodes')
            ->will($this->returnValue(['USD', 'PLN']));

        $storeManager->expects($this->exactly(2))
            ->method('getStore')
            ->will($this->returnValue($store));

        $currency->expects($this->once())
            ->method('getCurrencyRates')
            ->will($this->returnValue(['USD' => 1.0, 'PLN' => 4.0]));

        $currencyBundle->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValue([
                'Currencies' => [
                    'USD' => [1 => 'US Dollar'],
                    'PLN' => [1 => 'Polish Zloty']
                ]]
            ));

        $currencyFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($currency));

        $this->sourceAvailableCurrencies = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Feed\Source\Directory\AvailableCurrencies',
            [
                'storeManager' => $storeManager,
                'currencyFactory' => $currencyFactory,
                'currencyBundle' => $currencyBundle,
                'localeResolver' => $localeResolver
            ]
        );
    }

    public function testToOptionArray()
    {
        $this->assertEquals([
            ['value' => 'USD', 'label' => 'US Dollar'],
            ['value' => 'PLN', 'label' => 'Polish Zloty'],
        ], $this->sourceAvailableCurrencies->toOptionArray());
    }

    public function testToOptionArrayWithOptionsAlreadySet()
    {
        $this->sourceAvailableCurrencies->toOptionArray();
        $this->assertEquals([
            ['value' => 'USD', 'label' => 'US Dollar'],
            ['value' => 'PLN', 'label' => 'Polish Zloty'],
        ], $this->sourceAvailableCurrencies->toOptionArray());
    }
}
