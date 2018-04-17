<?php

namespace SomethingDigital\BryantPark\Helper;
 
class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_imageFactory;
    protected $_mediaDirectory;
    protected $_filesystem;

    public function __construct(            
        \Magento\Framework\Filesystem $filesystem,         
        \Magento\Framework\Image\AdapterFactory $imageFactory         
    ) {
        $this->_filesystem = $filesystem;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;
    }

    /**
     * First check this file on FS
     *
     * @param string $filename
     * @return bool
     */
    protected function _fileExists($filename)
    {
        return $this->_mediaDirectory->isFile($filename) && strpos($filename, '.svg') === false;
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
     * Create Image Object
     *
     * @param string $filename
     * @param string $folder
     * @return object
     */
    protected function _getImageObj($image, $folder) 
    {
        if ($this->_fileExists($folder . $image)) {
            $absolutePath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath(). $folder . $image;
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
