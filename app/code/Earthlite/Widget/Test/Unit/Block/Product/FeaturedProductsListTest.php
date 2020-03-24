<?php
/**
 * copy wight
 */
declare(strict_types=1);

namespace Earthlite\Widget\Test\Unit\Block\Product;

use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Catalog\Helper\Image;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Earthlite\Widget\Block\Product\FeaturedProductsList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\Config;

/**
 * Description of FeaturedProductsListTest
 *
 */
class FeaturedProductsListTest extends TestCase
{
    /**
     * @var FeaturedProductsListBlock 
     */
    private $block;
    
    /**
     * @var Context|MockObject
     */
    private $contextMock;
    
    /**
     * @var ProductCollectionFactory|MockObject
     */
    private $productCollectionFactoryMock;
    
    /**
     * @var Status|MockObject
     */
    private $productStatusMock;
    
    /**
     * @var Visibility|MockObject
     */
    private $productVisibilityMock;
    
    /**
     * @var ProductRepositoryInterfaceFactory|MockObject
     */
    private $productRepositoryInterfaceFactoryMock;
    
    /**
     * @var Image|MockObject
     */
    private $imageHelperMock;
    
    /**
     *
     * @var StoreManagerInterface
     */
    private $storeManagerMock;

    /**
     * @inhertiDoc
     */
    public function setUp()
    {      
        $this->productCollectionFactoryMock = $this->createMock(ProductCollectionFactory::class);
        $this->productStatusMock = $this->createMock(Status::class);
        $this->productVisibilityMock = $this->createMock(Visibility::class);
        $this->productRepositoryInterfaceFactoryMock = $this->createMock(ProductRepositoryInterfaceFactory::class);
        $this->imageHelperMock = $this->createMock(Image::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->catalogConfigMock = $this->getMockBuilder(\Magento\Catalog\Model\Config::class)
            ->setMethods(['getProductAttributes'])
            ->disableOriginalConstructor()
            ->getMock();

        
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->setMethods(
                [
                    'getEventManager', 'getScopeConfig', 'getLayout',
                    'getRequest', 'getCacheState', 'getCatalogConfig',
                    'getLocaleDate','getImageHelper','getStoreManager'
                ]
            )
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->getMock();
        

        $this->contextMock->expects($this->any())
            ->method('getCatalogConfig')
            ->willReturn($this->catalogConfigMock);
        
        $this->contextMock->expects($this->any())
            ->method('getImageHelper')
            ->willReturn($this->imageHelperMock);
        
        $this->contextMock->expects($this->any())
            ->method('getStoreManager')
            ->willReturn($this->storeManagerMock);
        
        $objectManager = new ObjectManager($this);
        $this->block = $objectManager->getObject(
            FeaturedProductsList::class,
            [
                'context' => $this->contextMock,
                'productCollectionFactory' => $this->productCollectionFactoryMock,
                'productStatus' => $this->productStatusMock,
                'productVisibility' => $this->productVisibilityMock,
                'productRepositoryInterfaceFactory' => $this->productRepositoryInterfaceFactoryMock
            ]);
    }
    
    public function testGetFeaturedProducts()
    {
        $this->catalogConfigMock->expects($this->any())->method('getProductAttributes')
            ->willReturn([]);        
        $this->productCollectionMock = $this->getMockBuilder(ProductCollection::class)
                ->setMethods(
                [
                    'setVisibility', 'addMinimalPrice', 'addFinalPrice',
                    'addTaxPercents', 'addAttributeToSelect', 'addUrlRewrite',
                    'addStoreFilter', 'addAttributeToSort', 'setPageSize',
                    'setCurPage', 'addAttributeToFilter','distinct'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->productCollectionMock->expects($this->once())->method('setVisibility')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())->method('addMinimalPrice')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())->method('addFinalPrice')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())->method('addTaxPercents')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->any())->method('addAttributeToSelect')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())->method('addUrlRewrite')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())->method('addStoreFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())->method('addAttributeToSort')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->any())->method('addAttributeToFilter')
            ->willReturnSelf();
        $this->productCollectionMock->expects($this->once())->method('distinct')
            ->willReturnSelf();
        $this->productCollectionFactoryMock->expects($this->any())
             ->method('create')
             ->willReturn($this->productCollectionMock);        
        $this->productVisibilityMock->expects($this->any())
             ->method('getVisibleInCatalogIds')
             ->willReturn([2,4]);                        
        $this->productCollectionMock->expects($this->any())->method('setVisibility')
                ->with($this->productVisibilityMock->getVisibleInCatalogIds())
                ->willReturn($this->productCollectionMock);
                
        $this->assertEquals(
            $this->productCollectionMock,
            $this->block->getFeaturedProducts()
        );
    }

    /**
     * @param string $imageUrl
     * @param string $image
     * @dataProvider dataProviderPaths
     */
    public function testGetImageUrl($imageUrl, $image)
    {
        $this->storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()->getMock();
        $this->storeMock->expects($this->any())->method('getBaseUrl')->willReturn('http://earthlite.com/');
        $this->imageHelperMock->expects($this->any())
             ->method('getDefaultPlaceholderUrl')
             ->willReturn('http://earthlite.com/media/catalog/product/placeholder/default/frame.png');
        $this->storeManagerMock->expects($this->any())->method('getStore')->willReturn($this->storeMock);
        
        $this->assertEquals(
            $imageUrl,
            $this->block->getImageUrl($image)
        );
        
    }
    
    /**
     * 
     * @return array
     */
    public function dataProviderPaths():array
    {
        return [
            ['http://earthlite.com/catalog/product/2/3/23sample.png', '/2/3/23sample.png'],
            ['http://earthlite.com/media/catalog/product/placeholder/default/frame.png', ''],
        ];
    }
}
