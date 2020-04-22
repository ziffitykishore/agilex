<?php
declare(strict_types=1);

namespace Earthlite\Category\Block\Adminhtml\Category\Helper\Form\Gallery;

use Magento\Framework\App\ObjectManager;
use Magento\Backend\Block\Media\Uploader;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Backend\Block\DataProviders\ImageUploadConfig as ImageUploadConfigDataProvider;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Backend\Block\Widget;

/**
 * Block for gallery content.
 * 
 * class Content
 */
class Content extends Widget
{
    const UPLOADER = 'uploader';
    
    /**
     *
     * @var string
     */
    protected $_template = 'Earthlite_Category::gallery.phtml';

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $_mediaConfig;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @var ImageUploadConfigDataProvider
     */
    private $imageUploadConfigDataProvider;

    /**
     * @var Database
     */
    private $fileStorageDatabase;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param array $data
     * @param ImageUploadConfigDataProvider $imageUploadConfigDataProvider
     * @param Database $fileStorageDatabase
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        array $data = [],
        ImageUploadConfigDataProvider $imageUploadConfigDataProvider = null,
        Database $fileStorageDatabase = null
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_mediaConfig = $mediaConfig;
        parent::__construct($context, $data);
        $this->imageUploadConfigDataProvider = $imageUploadConfigDataProvider
            ?: ObjectManager::getInstance()->get(ImageUploadConfigDataProvider::class);
        $this->fileStorageDatabase = $fileStorageDatabase
            ?: ObjectManager::getInstance()->get(Database::class);
    }

    /**
     * Prepare layout.
     *
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            self::UPLOADER,
            Uploader::class,
            ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
        );

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->addSessionParam()->getUrl('category/category_gallery/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );
        return parent::_prepareLayout();
    }
    
    /**
     * 
     * @return Uploader
     */
    public function getUploader()
    {
        return $this->getChildBlock(self::UPLOADER);
    }

    /**
     * 
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml(self::UPLOADER);
    }
    
    /**
     * 
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * 
     * @return string
     */
    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            __('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }
    
    /**
     * 
     * @return array
     */
    public function getImagesArray()
    {
        return [];
    }
    
    /**
     * 
     * @return string
     */
    public function getImagesJson()
    {        
        $value = $this->getElement()->getImages();
        if (is_array($value) &&
            array_key_exists('images', $value) &&
            is_array($value['images']) &&
            count($value['images'])
        ) {
            $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $images = $this->sortImagesByPosition($value['images']);
            foreach ($images as &$image) {

                $image['url'] = $this->_mediaConfig->getMediaUrl($image['file']);
                if ($this->fileStorageDatabase->checkDbUsage() &&
                    !$mediaDir->isFile($this->_mediaConfig->getMediaPath($image['file']))
                ) {
                    $this->fileStorageDatabase->saveFileToFilesystem(
                        $this->_mediaConfig->getMediaPath($image['file'])
                    );
                }
                try {
                    $fileHandler = $mediaDir->stat($this->_mediaConfig->getMediaPath($image['file']));
                    $image['size'] = $fileHandler['size'];
                } catch (FileSystemException $e) {
                    $image['url'] = $this->getImageHelper()->getDefaultPlaceholderUrl('small_image');
                    $image['size'] = 0;
                    $this->_logger->info($e);
                }
            }
            return $this->_jsonEncoder->encode($images);
        }
        return '[]';
    }
    
    /**
     * 
     * @param type $images
     * @return type
     */
    private function sortImagesByPosition($images)
    {
        if (is_array($images)) {
            usort($images, function ($imageA, $imageB) {
                return ($imageA['position'] < $imageB['position']) ? -1 : 1;
            });
        }
        return $images;
    }
    
    /**
     * Returns image helper object.
     *
     * @return \Magento\Catalog\Helper\Image
     */
    private function getImageHelper()
    {
        if ($this->imageHelper === null) {
            $this->imageHelper = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Catalog\Helper\Image::class);
        }
        return $this->imageHelper;
    }
}