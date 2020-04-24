<?php
declare(strict_types=1);

namespace Earthlite\Category\Test\Unit\Plugin\Controller\Adminhtml\Category;

use Magento\Catalog\Controller\Adminhtml\Category\Save;
use Earthlite\Category\Model\CategoryGalleryFactory;
use Earthlite\Category\Model\ResourceModel\CategoryGalleryFactory as CategoryGalleryResourceModelFactory;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\Uploader as FileUploader;
use Magento\Framework\Controller\ResultInterface;
use Earthlite\Category\Plugin\Controller\Adminhtml\Category\SavePlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\RequestInterface;
use Earthlite\Category\Model\ResourceModel\CategoryGallery as CategoryGalleryResourceModel;
use Earthlite\Category\Model\CategoryGallery;

/**
 * Description of SavePluginTest
 *
 */
class SavePluginTest extends TestCase
{
    /**
     * @var SavePluginTest 
     */
    private $plugin;
    
    /**
     *
     * @var CategoryGalleryFactory|MockObject 
     */
    private $categoryGalleryFactoryMock;
    
    /**
     *
     * @var CategoryGalleryResourceModelFactory|MockObject 
     */
    private $categoryGalleryResourceModelFactoryMock;
    
    /**
     *
     * @var Config|MockObject 
     */
    private $mediaConfigMock;
    
    /**
     *
     * @var Filesystem|MockObject 
     */
    private $fileSystemMock;
    
    /**
     *
     * @var Database|MockObject 
     */
    private $fileStorageDbMock;
    
    /**
     * @inhertiDoc
     */ 
    public function setUp()
    {
        $this->categoryGalleryFactoryMock = $this->createMock(CategoryGalleryFactory::class);
        $this->categoryGalleryResourceModelFactoryMock = $this->createMock(CategoryGalleryResourceModelFactory::class);
        $this->mediaConfigMock = $this->createMock(Config::class);
        $this->fileSystemMock = $this->createMock(Filesystem::class);
        $this->fileStorageDbMock = $this->createMock(Database::class);
        $this->requestMock = $this->getMockForAbstractClass(
            \Magento\Framework\App\RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getParam', 'getPost', 'getPostValue']
        );
        
        $objectManager = new ObjectManager($this);
        $this->plugin = $objectManager->getObject(
            SavePlugin::class,
            [
                'categoryGalleryFactory' => $this->categoryGalleryFactoryMock,
                'categoryGalleryResourceModelFactory' => $this->categoryGalleryResourceModelFactoryMock,
                'mediaConfig' => $this->mediaConfigMock,
                'filesystem' => $this->fileSystemMock,
                'fileStorageDb' => $this->fileStorageDbMock,
            ]
        );        
    }
    
    /**
     * 
     * @param array $images
     * @dataProvider dataProviderImages
     */
    public function testAfterExecute(array $images)
    {
        $saveMock = $this->getMockBuilder(Save::class)            
                ->disableOriginalConstructor()
                ->getMock();
        $mediaGallery['images'] = $images;
        $photos['media_gallery'] = $mediaGallery;
        $postData = [
            'general-data',
            'photo' => $photos,
            'entity_id' => 1
        ];
        
        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postData);
        $saveMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);
        $resultMock = $this->getMockBuilder(ResultInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $categoryGalleryModelMock = $this->getMockBuilder(CategoryGallery::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->categoryGalleryFactoryMock->expects($this->any())
             ->method('create')
             ->willReturn($categoryGalleryModelMock);
        
        $categoryGalleryResourceModelMock = $this->getMockBuilder(CategoryGalleryResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->categoryGalleryResourceModelFactoryMock->expects($this->any())
             ->method('create')
             ->willReturn($categoryGalleryResourceModelMock);
        
        $categoryGalleryResourceModelMock->expects($this->any())
            ->method('load')
            ->with($categoryGalleryModelMock,1)
            ->willReturn($categoryGalleryModelMock);
        
        $this->assertEquals(
            $resultMock,
            $this->plugin->afterExecute($saveMock,$resultMock)
        );
        
    }
    
    /**
     * 
     * @return array
     */
    public function dataProviderImages():  array
    {
        return [
            [
                [
                    [
                        'value_id' => 1,
                        'position' => 1,
                        'file' => '/s/p/spa-table_6_4.jpg',
                        'label' => 'test',
                        'disabled' => 0,
                        'media_type' => 'image',
                        'removed' => 1
                    ],
                    [
                        'value_id' => 2,
                        'position' => 2,
                        'file' => '/s/p/spa-table_5_4.jpg',
                        'label' => 'test1',
                        'disabled' => 0,
                        'media_type' => 'image',
                        'removed' => 0
                    ]
                ]
            ],
            [
                [
                    [
                        'value_id' => 1,
                        'position' => 1,
                        'file' => '/s/p/spa-table_6_4.jpg',
                        'label' => 'test',
                        'disabled' => 0,
                        'media_type' => 'image',
                        'removed' => 0
                    ]
                ]
            ]
        ];
    }
       
}
