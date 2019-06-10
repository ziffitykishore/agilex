<?php

namespace SomethingDigital\BryantPark\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as HelperContext;

class Image extends AbstractHelper
{
    protected $_imageFactory;
    protected $_mediaDirectory;
    protected $_filesystem;
    protected $_storeManager;

    public function __construct(
        HelperContext $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_filesystem = $filesystem;
        $this->_mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * First check this file on FS
     *
     * @param string $filename
     * @return bool
     */
    protected function isImageFile($filename)
    {
        return $this->_mediaDirectory->isFile($filename) && substr($filename, -4) !== '.svg';
    }

    /**
     * Get Image's Height
     *
     * @param string $filename
     * @param string $folder
     * @return integer
     */
    public function getHeight($image, $folder)
    {
        $imageObj = $this->_getImageObj($image, $folder);

        if ($imageObj === NULL) {
            return 0;
        }

        return $imageObj->getOriginalHeight();
    }

    /**
     * Get Image's Width
     *
     * @param string $filename
     * @param string $folder
     * @return integer
     */
    public function getWidth($image, $folder)
    {
        $imageObj = $this->_getImageObj($image, $folder);

        if ($imageObj === NULL) {
            return 0;
        }

        return $imageObj->getOriginalWidth();
    }

    /**
     * Get Image's ratio
     *
     * @param string $filename
     * @param string $folder
     * @return integer
     */
    public function getRatio($image, $folder)
    {
        $imageObj = $this->_getImageObj($image, $folder);

        if ($imageObj === NULL) {
            return 0;
        }

        $width = $imageObj->getOriginalWidth();
        $height = $imageObj->getOriginalHeight();

        if ($width && $height) {
            $ratio = ($height / $width);
        } else {
            $ratio = 1;
        }

        return $ratio;
    }

     /**
     * Resize an Image
     *
     * @param string $filename
     * @param int $width
     * @param int $height
     * @param string $pathPrefix
     * @return string
     */
    public function resize($image, $width = 40, $height = 22, $pathPrefix = 'gene-cms')
    {
        $urlPrefix = $pathPrefix . '/sd-resized/'. $width;
        $srcFile = $pathPrefix . $image;
        $destFile = $urlPrefix . $image;

        if (!$this->isImageFile($destFile) && $this->isImageFile($srcFile)) {
            // Ok. Looks like we don't have a resized image. Let's create one!
            $absolutePath = $this->_mediaDirectory->getAbsolutePath($srcFile);
            $destination = $this->_mediaDirectory->getAbsolutePath($destFile);

            $imageResize = $this->_imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(true);
            $imageResize->resize($width, $height);
            $imageResize->quality(50);

            $imageResize->save($destination);
        }

        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA). $destFile;
    }

    /**
     * Provide a transparent Gif in Base64 format.
     * Mostly used with lazysizes blur-up
     *
     * @return string
     */
    public function getBase64TransparentGif()
    {
        return 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
    }

    /**
     * Create Image Object
     *
     * @param string $filename
     * @param string $folder
     * @return object
     */
    protected function _getImageObj($image, $folder)
    {
        if ($this->isImageFile($folder . $image)) {
            $absolutePath = $this->_mediaDirectory->getAbsolutePath(). $folder . $image;
        } else {
            return null;
        }

        //create image factory...
        $imageObj = $this->_imageFactory->create();
        $imageObj->open($absolutePath);
        $imageObj->refreshImageDimensions();

        return $imageObj;
    }
}
