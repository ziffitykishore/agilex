<?php

namespace RocketWeb\ShoppingFeedsGoogle\Test\Unit\Block\Product\View\Configurable;
use RocketWeb\ShoppingFeedsGoogle\Block\Product\View\Configurable\Remarketing as Remarketing;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;


class RemarketingTest extends \PHPUnit_Framework_TestCase
{
    const GOOGLE_CONV_ID = 'testing123_google_abc';

    /** @var \RocketWeb\ShoppingFeedsGoogle\Block\Product\View\Configurable\Remarketing */
    protected $remarketingBlock;

    /** @var  \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $productMock;

    /** @var  \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfigMock;

    /** @var  \Magento\Catalog\Block\Product\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $contextMock;

    /** @var  \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registryMock;

    /** @var  \Magento\ConfigurableProduct\Model\Product\Type\Configurable|\PHPUnit_Framework_MockObject_MockObject */
    protected $productTypeConfigFactoryMock;

    /** @var  array */
    protected $assocProductsMock = [];


    protected function setUp()
    {
        $objectHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->setupProductMock();
        $this->setupAssocProductsMock();
        $this->setupScopeConfigMock();
        $this->setupRegistryMock();
        $this->setupProductTypeConfigFactoryMock();
        $this->setupContextMock();

        $this->remarketingBlock = $objectHelper->getObject(
            'RocketWeb\ShoppingFeedsGoogle\Block\Product\View\Configurable\Remarketing',
            [
                'context'                   => $this->contextMock,
                'configurableProductType'   => $this->productTypeConfigFactoryMock
            ]
        );
    }

    public function setupProductMock()
    {
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $typeInstance = $this->getMock('Magento\Catalog\Model\Product\Type\AbstractType', [], [], '', false);

        $this->productMock->expects($this->any())
            ->method('getTypeInstance')
            ->will($this->returnValue($typeInstance));

        $typeInstance->expects($this->any())
            ->method('getStoreFilter')
            ->will($this->returnSelf());

        $this->productMock->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE));
    }

    protected function getIdsAndSkus()
    {
        return [
            '123' => 'abc',
            '222' => 'mnp',
            '321' => 'xyz'
        ];
    }

    public function setupAssocProductsMock()
    {
        foreach ($this->getIdsAndSkus() as $id => $sku) {
            $mock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);

            $mock->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($id));

            $mock->expects($this->any())
                ->method('getSku')
                ->will($this->returnValue($sku));

            $this->assocProductsMock[] = $mock;
        }
    }

    protected function setupScopeConfigMock()
    {
        $this->scopeConfigMock = $this->getMockBuilder('Magento\Framework\App\Config')
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMock();

        $valueMap = [
            [Remarketing::GOOGLE_CONVERSION_ID_PATH, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null, self::GOOGLE_CONV_ID],
            [Remarketing::REMARKETING_ENABLED_PATH, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null, '1']
        ];

        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->will($this->returnValueMap($valueMap));
    }

    public function setupRegistryMock()
    {
        $this->registryMock = $this->getMockBuilder('\Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->setMethods(['registry'])
            ->getMock();

        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with('product')
            ->will($this->returnValue($this->productMock));
    }

    protected function setupContextMock()
    {
        $this->contextMock = $this->getMockBuilder('\Magento\Catalog\Block\Product\Context')
            ->disableOriginalConstructor()
            ->setMethods(['getRegistry', 'getScopeConfig'])
            ->getMock();

        $this->contextMock->expects($this->any())
            ->method('getRegistry')
            ->will($this->returnValue($this->registryMock));

        $this->contextMock->expects($this->any())
            ->method('getScopeConfig')
            ->will($this->returnValue($this->scopeConfigMock));
    }

    public function setupProductTypeConfigFactoryMock()
    {
        $this->productTypeConfigFactoryMock = $this->getMockBuilder('\Magento\ConfigurableProduct\Model\Product\Type\Configurable')
            ->disableOriginalConstructor()
            ->setMethods(['getUsedProducts'])
            ->getMock();

        $this->productTypeConfigFactoryMock->expects($this->any())
            ->method('getUsedProducts')
            ->with($this->productMock, null)
            ->will($this->returnValue($this->assocProductsMock));
    }

    public function testIsEnabled()
    {
        $result = $this->remarketingBlock->isEnabled();
        $this->assertTrue($result);
    }

    public function testGoogleConversionId()
    {
        $result = $this->remarketingBlock->getGoogleConversionId();
        $this->assertEquals(self::GOOGLE_CONV_ID, $result);
    }

    public function testGetProducts()
    {
        $result = $this->remarketingBlock->getProducts();
        $resultSkus = [];

        foreach ($this->assocProductsMock as $assocProduct) {
            $resultSkus[$assocProduct->getId()] = $assocProduct->getSku();
        }

        $this->assertEquals($result, $this->assocProductsMock);
        $this->assertEquals($this->getIdsAndSkus(), $resultSkus);
    }
}