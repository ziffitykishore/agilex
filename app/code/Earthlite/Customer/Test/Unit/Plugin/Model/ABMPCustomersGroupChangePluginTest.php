<?php
/**
 * copy wight
 */
declare(strict_types=1);

namespace Earthlite\Customer\Test\Unit\Plugin\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Api\AttributeInterface;
use Magento\Customer\Model\GroupFactory as CustomerGroupModelFacotry;
use Magento\Customer\Model\ResourceModel\GroupFactory as CustomerGroupResourceModelFactory;
use Earthlite\Customer\Plugin\Model\ABMPCustomersGroupChangePlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Customer\Model\ResourceModel\Group as ResGroup;
use Magento\Customer\Model\Group;


/**
 * Description of ABMPCustomersGroupChangePluginTest
 *
 */
class ABMPCustomersGroupChangePluginTest extends TestCase
{
    /**
     * @var ABMPCustomersGroupChangePlugin 
     */
    private $plugin;
    
    /**
     * @var CustomerGroupModelFacotry|MockObject
     */
    private $customerGroupModelFactoryMock;
    
    /**
     * @var CustomerGroupResourceModelFactory|MockObject
     */
    private $customerGroupResourceModelFactoryMock;


    /**
     * @inhertiDoc
     */
    public function setUp()
    {
        $this->customerRepositoryMock = $this->createMock(CustomerRepository::class);
        $this->attributeInterfaceMock = $this->createMock(AttributeInterface::class);
        $this->customerInterfaceMock = $this->createMock(CustomerInterface::class);
        $this->customerGroupModelFactoryMock = $this->createMock(CustomerGroupModelFacotry::class);
        $this->customerGroupResourceModelFactoryMock = $this->createMock(CustomerGroupResourceModelFactory::class);

        $objectManager = new ObjectManager($this);
        $this->plugin = $objectManager->getObject(
            ABMPCustomersGroupChangePlugin::class,
            [
                'customerGroupModelFactory' => $this->customerGroupModelFactoryMock,
                'customerGroupResourceModelFactory' => $this->customerGroupResourceModelFactoryMock,
            ]
        );
    }
    
    /**
     * 
     * @param int $groupId
     * @dataProvider dataProviderGroupIds
     */
    public function testBeforeSave(string $groupId)
    {
        $this->attributeInterfaceMock->expects($this->any())
             ->method('getValue')
             ->willReturn($groupId);
        $this->customerInterfaceMock->expects($this->any())
            ->method('getCustomAttribute')
            ->willReturn($this->attributeInterfaceMock);
        
        $customerGroupModelMock = $this->getMockBuilder(Group::class)
            ->disableOriginalConstructor()
            ->getMock();
        $customerGroupModelMock->expects($this->any())
             ->method('getId')
             ->willReturn(4);
        
        $this->customerGroupModelFactoryMock->expects($this->any())
             ->method('create')
             ->willReturn($customerGroupModelMock);
                
        $customerGroupResourceModelMock = $this->getMockBuilder(ResGroup::class)
            ->disableOriginalConstructor()
            ->getMock();        
        
        $customerGroupResourceModelMock->expects($this->any())
            ->method('load')
            ->with($customerGroupModelMock,'ABMP','customer_group_code')
            ->willReturn($customerGroupModelMock);
        
        $this->customerGroupResourceModelFactoryMock->expects($this->any())
             ->method('create')
             ->willReturn($customerGroupResourceModelMock);
        
        $this->customerInterfaceMock->expects($this->any())
            ->method('setGroupId')
            ->with(4);
        
        $this->assertEquals(
            [$this->customerInterfaceMock, ''],
            $this->plugin->beforeSave(
                $this->customerRepositoryMock,
                $this->customerInterfaceMock
            )
        );

    }
    
    /**
     * 
     * @return array
     */
    public function dataProviderGroupIds():  array
    {
           return [
               [1],
               ['']
           ];
 
    }
}
