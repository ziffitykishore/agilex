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

namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Feed\Source\Shipping;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class AvailableMethodsTest extends \PHPUnit_Framework_TestCase
{
    /** @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Shipping\AvailableMethods */
    protected $sourceAvailableMethods;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** 
     * @var \Magento\Shipping\Model\Config 
     */
    protected $shippingMethodConfig;

    /** 
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\OfflineShipping\Model\Carrier\Flatrate
     */
    protected $carrierFlatrate;

    /**
     * @var \Magento\OfflineShipping\Model\Carrier\Tablerate
     */
    protected $carrierTablerate;

    /**
     * @var \Magento\OfflineShipping\Model\Carrier\Freeshipping
     */
    protected $carrierFreeshiping;

    /**
     * @var \Magento\Fedex\Model\Carrier
     */
    protected $carrierFedex;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        /** @var \RocketWeb\ShoppingFeeds\Model\Feed $feed */
        $feed = $this->getMock('\RocketWeb\ShoppingFeeds\Model\Feed', [], [], '', false);

        /** @var \Magento\Framework\Registry $registry */
        $registry = $this->getMock('\Magento\Framework\Registry', [], [], '', false);

        /** @var \Magento\Shipping\Model\Config $this->shippingMethodConfig */
        $this->shippingMethodConfig = $this->getMock('\Magento\Shipping\Model\Config', [], [], '', false);
 
        /** @var \Magento\Framework\App\Config\ScopeConfigInterface $this->scopeConfig */
        $this->scopeConfig = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);

        /** @var \Magento\OfflineShipping\Model\Carrier\Flatrate $this->carrierFlatrate */
        $this->carrierFlatrate = $this->getMock('\Magento\OfflineShipping\Model\Carrier\Flatrate', [], [], '', false);

        /** @var \Magento\OfflineShipping\Model\Carrier\Tablerate $this->carrierTablerate */
        $this->carrierTablerate = $this->getMock('\Magento\OfflineShipping\Model\Carrier\Tablerate', [], [], '', false);

        /** @var \Magento\OfflineShipping\Model\Carrier\Freeshipping $this->carrierFreeshiping */
        $this->carrierFreeshiping = $this->getMock('\Magento\OfflineShipping\Model\Carrier\Freeshipping', [], [], '', false);

        /** @var \Magento\Fedex\Model\Carrier $this->carrierFedex */
        $this->carrierFedex = $this->getMock('\Magento\Fedex\Model\Carrier', [], [], '', false);

        $feed->expects($this->exactly(1))
            ->method('getConfig')
            ->with('shipping_carrier_realtime', [])
            ->will($this->returnValue(['ups','usps','fedex','dhl','dhlint']));

        $registry->expects($this->once())
            ->method('registry')
            ->with('feed')
            ->will($this->returnValue($feed));

        $this->shippingMethodConfig->expects($this->once())
            ->method('getActiveCarriers')
            ->will($this->returnValue([$this->carrierFlatrate, $this->carrierTablerate, $this->carrierFreeshiping, $this->carrierFedex]));

        $this->sourceAvailableMethods = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Feed\Source\Shipping\AvailableMethods',
            [
                'registry' => $registry,
                'shippingMethodConfig' => $this->shippingMethodConfig,
                'scopeConfig' => $this->scopeConfig,
            ]
        );
    }

    protected function getExpectedCarrierFlatrate()
    {
        return [
            'label' => 'Flat Rate', 'value' => [
                0 => ['value' => 'flatrate_flatrate', 'label' => '[flatrate] Fixed'],
            ],
        ];
    }
    
    protected function getExpectedCarrierTablerate()
    {
        return [
            'label' => 'Best Way', 'value' => [
                0 => ['value' => 'tablerate_bestway', 'label' => '[tablerate] Table Rate'],
            ],
        ];
    }

    protected function getExpectedCarrierFreeshipping()
    {
        return [
            'label' => 'Free Shipping', 'value' => [
                0 => ['value' => 'freeshipping_freeshipping', 'label' => '[freeshipping] Free'],
            ],
        ];
    }

    protected function prepareFlatrateMock($isActive = true, $hasMethods = true)
    {
        $this->carrierFlatrate->expects($this->once())
            ->method('getCarrierCode')
            ->will($this->returnValue('flatrate'));
        $this->carrierFlatrate->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue($isActive));

        if ($isActive && $hasMethods) {
            $this->carrierFlatrate->expects($this->once())
                ->method('getAllowedMethods')
                ->will($this->returnValue(['flatrate' => 'Fixed']));
        }
    }

    protected function prepareTablerateMock($isActive = true, $hasMethods = true)
    {
        $this->carrierTablerate->expects($this->once())
            ->method('getCarrierCode')
            ->will($this->returnValue('tablerate'));
        $this->carrierTablerate->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue($isActive));

        if ($isActive && $hasMethods) {
            $this->carrierTablerate->expects($this->once())
                ->method('getAllowedMethods')
                ->will($this->returnValue(['bestway' => 'Table Rate']));
        }
    }

    protected function prepareFreeshippingMock($isActive = true, $hasMethods = true)
    {
        $this->carrierFreeshiping->expects($this->once())
            ->method('getCarrierCode')
            ->will($this->returnValue('freeshipping'));
        $this->carrierFreeshiping->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue($isActive));

        if ($isActive && $hasMethods) {
            $this->carrierFreeshiping->expects($this->once())
                ->method('getAllowedMethods')
                ->will($this->returnValue(['freeshipping' => 'Free']));
        }
    }

    protected function prepareFedexMock($isActive = true, $hasMethods = true)
    {
        $this->carrierFedex->expects($this->once())
            ->method('getCarrierCode')
            ->will($this->returnValue('fedex'));
    }

    public function testToOptionArray()
    {
        $this->prepareFlatrateMock(true);
        $this->prepareTablerateMock(true);
        $this->prepareFreeshippingMock(true);
        $this->prepareFedexMock(true);

        $this->scopeConfig->expects($this->exactly(3))
            ->method('getValue')
            ->withConsecutive(
                ['carriers/flatrate/title', 'store', null],
                ['carriers/tablerate/title', 'store', null],
                ['carriers/freeshipping/title', 'store', null]
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue('Flat Rate'),
                $this->returnValue('Best Way'),
                $this->returnValue('Free Shipping')
            );

        $this->assertEquals([
            ['value' => '', 'label' => ''],
            'flatrate' => $this->getExpectedCarrierFlatrate(),
            'tablerate' => $this->getExpectedCarrierTablerate(),
            'freeshipping' => $this->getExpectedCarrierFreeshipping(),
        ], $this->sourceAvailableMethods->toOptionArray());
    }

    public function testToOptionArrayCarrierNotActive()
    {
        $this->prepareFlatrateMock(true);
        $this->prepareTablerateMock(false);
        $this->prepareFreeshippingMock(true);
        $this->prepareFedexMock(true);

        $this->scopeConfig->expects($this->exactly(2))
            ->method('getValue')
            ->withConsecutive(
                ['carriers/flatrate/title', 'store', null],
                ['carriers/freeshipping/title', 'store', null]
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue('Flat Rate'),
                $this->returnValue('Free Shipping')
            );

        $this->assertEquals([
            ['value' => '', 'label' => ''],
            'flatrate' => $this->getExpectedCarrierFlatrate(),
            'freeshipping' => $this->getExpectedCarrierFreeshipping(),
        ], $this->sourceAvailableMethods->toOptionArray());
    }

    public function testToOptionArrayCarrierWithoutMethods()
    {
        $this->prepareFlatrateMock(true);
        $this->prepareTablerateMock(true, false);
        $this->prepareFreeshippingMock(true);
        $this->prepareFedexMock(true);

        $this->scopeConfig->expects($this->exactly(2))
            ->method('getValue')
            ->withConsecutive(
                ['carriers/flatrate/title', 'store', null],
                ['carriers/freeshipping/title', 'store', null]
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue('Flat Rate'),
                $this->returnValue('Free Shipping')
            );

        $this->assertEquals([
            ['value' => '', 'label' => ''],
            'flatrate' => $this->getExpectedCarrierFlatrate(),
            'freeshipping' => $this->getExpectedCarrierFreeshipping(),
        ], $this->sourceAvailableMethods->toOptionArray());
    }

    public function testToOptionArrayWithOptionsAlreadySet()
    {
        $this->prepareFlatrateMock(true);
        $this->prepareTablerateMock(true);
        $this->prepareFreeshippingMock(true);
        $this->prepareFedexMock(true);

        $this->scopeConfig->expects($this->exactly(3))
            ->method('getValue')
            ->withConsecutive(
                ['carriers/flatrate/title', 'store', null],
                ['carriers/tablerate/title', 'store', null],
                ['carriers/freeshipping/title', 'store', null]
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue('Flat Rate'),
                $this->returnValue('Best Way'),
                $this->returnValue('Free Shipping')
            );

        $this->sourceAvailableMethods->toOptionArray();
        $this->assertEquals([
            ['value' => '', 'label' => ''],
            'flatrate' => $this->getExpectedCarrierFlatrate(),
            'tablerate' => $this->getExpectedCarrierTablerate(),
            'freeshipping' => $this->getExpectedCarrierFreeshipping(),
        ], $this->sourceAvailableMethods->toOptionArray());
    }
}
