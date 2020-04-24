<?php

declare(strict_types = 1);

namespace Earthlite\Category\Plugin\Controller\Adminhtml\Category;

use Magento\Catalog\Controller\Adminhtml\Category\Save;
use Earthlite\Category\Model\CategoryGalleryFactory;
use Earthlite\Category\Model\ResourceModel\CategoryGalleryFactory as CategoryGalleryResourceModelFactory;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\Uploader as FileUploader;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class SavePlugin
 */
class SavePlugin 
{
    /**
     *
     * @var CategoryGalleryFactory
     */
    protected $categoryGalleryFactory;

    /**
     *
     * @var CategoryGalleryResourceModelFactory 
     */
    protected $categoryGalleryResourceModelFactory;

    /**
     * @var Config
     */
    protected $mediaConfig;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var Database
     */
    protected $fileStorageDb;
    
    /**
     *
     * @var \Magento\Catalog\Controller\Adminhtml\Category\Save 
     */
    protected $categoryPostData;

    /**
     * SavePlugin Constructor
     * 
     * @param CategoryGalleryFactory $categoryGalleryFactory
     * @param CategoryGalleryResourceModelFactory $categoryGalleryResourceModelFactory
     * @param Config $mediaConfig
     * @param Filesystem $filesystem
     * @param Database $fileStorageDb
     */
    public function __construct(
        CategoryGalleryFactory $categoryGalleryFactory, 
        CategoryGalleryResourceModelFactory $categoryGalleryResourceModelFactory,
        Config $mediaConfig, 
        Filesystem $filesystem,
        Database $fileStorageDb
    ) {
        $this->categoryGalleryFactory = $categoryGalleryFactory;
        $this->categoryGalleryResourceModelFactory = $categoryGalleryResourceModelFactory;
        $this->mediaConfig = $mediaConfig;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->fileStorageDb = $fileStorageDb;
    }

    /**
     * Plugin to save the category media
     * 
     * @param Save $subject
     * @param \Magento\Framework\Controller\ResultInterface $result
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function afterExecute(Save $subject, ResultInterface $result) 
    {
        $this->categoryPostData = $subject->getRequest()->getPostValue();
        if (!empty($this->categoryPostData['photo'])) {
            $categoryMediaImages = $this->categoryPostData['photo']['media_gallery']['images'];
            $this->saveCatalogGallery($categoryMediaImages);
        }
        return $result;
    }

    /**
     * 
     * @param array $categoryMediaImages
     * @return void
     */
    protected function saveCatalogGallery(array $categoryMediaImages): void 
    {
        foreach ($categoryMediaImages as $categoryMediaImage) {
            if ($categoryMediaImage['removed'] == 1) {
                $this->removeImage($categoryMediaImage);
            } else {
                $this->addImage($categoryMediaImage);
            }
        }
    }

    /**
     * 
     * @param array $categoryMediaImage
     * @return void
     */
    protected function removeImage(array $categoryMediaImage): void 
    {
        $categoryGalleryModel = $this->getCatalogCategoryModel();
        $categoryGalleryResourceModel = $this->getCatalogCategoryResourceModel();
        $categoryGalleryResourceModel->load($categoryGalleryModel, $categoryMediaImage['value_id']);
        $categoryGalleryResourceModel->delete($categoryGalleryModel);
    }

    /**
     * 
     * @param array $categoryMediaImage
     * @return void
     */
    protected function addImage(array $categoryMediaImage): void 
    {
        $categoryGalleryModel = $this->getCatalogCategoryModel();
        if (!empty($categoryMediaImage['value_id'])) {
            $categoryGalleryModel->setValueId($categoryMediaImage['value_id']);
        } else {
            $newFile = $this->moveImageFromTmp($categoryMediaImage['file']);
            $categoryMediaImage['new_file'] = $newFile;
            $categoryMediaImage['file'] = $newFile;
        }
        $categoryGalleryModel->setCategoryId($this->categoryPostData['entity_id']);
        $categoryGalleryModel->setValue($categoryMediaImage['file']);
        $categoryGalleryModel->setMediaType($categoryMediaImage['media_type']);
        $categoryGalleryModel->setDisabled($categoryMediaImage['disabled']);
        $categoryGalleryModel->setPosition($categoryMediaImage['position']);
        $categoryGalleryModel->setLabel($categoryMediaImage['label']);
        $categoryGalleryResourceModel = $this->getCatalogCategoryResourceModel();
        $categoryGalleryResourceModel->save($categoryGalleryModel);
    }

    /**
     * 
     * @return \Earthlite\Category\Model\CategoryGalleryFactory
     */
    protected function getCatalogCategoryModel() 
    {
        return $this->categoryGalleryFactory->create();
    }

    /**
     * 
     * @return \Earthlite\Category\Model\ResourceModel\CategoryGalleryFactory
     */
    protected function getCatalogCategoryResourceModel() 
    {
        return $this->categoryGalleryResourceModelFactory->create();
    }

    /**
     * Move image from temporary directory to normal
     *
     * @param string $inputFile
     * @return string
     */
    protected function moveImageFromTmp(string $inputFile): string 
    {
        $file = $this->getFilenameFromTmp($this->getSafeFilename($inputFile));
        $destinationFile = $this->getUniqueFileName($file);

        if ($this->fileStorageDb->checkDbUsage()) {
            $this->fileStorageDb->renameFile(
                    $this->mediaConfig->getTmpMediaShortUrl($file), $this->mediaConfig->getMediaShortUrl($destinationFile)
            );

            $this->mediaDirectory->delete($this->mediaConfig->getTmpMediaPath($file));
            $this->mediaDirectory->delete($this->mediaConfig->getMediaPath($destinationFile));
        } else {
            $this->mediaDirectory->renameFile(
                    $this->mediaConfig->getTmpMediaPath($file), $this->mediaConfig->getMediaPath($destinationFile)
            );
        }

        return str_replace('\\', '/', $destinationFile);
    }

    /**
     * Check whether file to move exists. Getting unique name
     *
     * @param string $file
     * @param bool $forTmp
     * @return string
     */
    protected function getUniqueFileName($file, $forTmp = false) 
    {
        if ($this->fileStorageDb->checkDbUsage()) {
            $destFile = $this->fileStorageDb->getUniqueFilename(
                   $this->mediaConfig->getBaseMediaUrlAddition(), $file
            );
        } else {
            $destinationFile = $forTmp ? $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getTmpMediaPath($file)) : $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getMediaPath($file));
            // phpcs:disable Magento2.Functions.DiscouragedFunction
            $destFile = dirname($file) . '/' . FileUploader::getNewFileName($destinationFile);
        }

        return $destFile;
    }

    /**
     * Returns file name according to tmp name
     *
     * @param string $file
     * @return string
     */
    protected function getFilenameFromTmp($file) 
    {
        return strrpos($file, '.tmp') == strlen($file) - 4 ? substr($file, 0, strlen($file) - 4) : $file;
    }

    /**
     * Returns safe filename for posted image
     *
     * @param string $file
     * @return string
     */
    private function getSafeFilename($file) 
    {
        $file = DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
        return $this->mediaDirectory->getDriver()->getRealPathSafety($file);
    }

}
