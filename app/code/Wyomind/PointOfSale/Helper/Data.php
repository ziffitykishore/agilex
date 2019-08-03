<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\PointOfSale\Helper;

/**
 * Core general helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const TEXTAREA = 0;
    const WYSIWYG = 1;
    const TEXT = 2;

    protected $_coreHelper = null;
    protected $_regionModel = null;
    protected $_localLists = null;
    protected $_storeManager = null;
    protected $_imageAdapterFactory = null;
    protected $_coreDate = null;
    protected $_directoryList = null;
    protected $_file = null;
    protected $_registry = null;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Wyomind\Core\Helper\Data $coreHelper
     * @param \Magento\Directory\Model\Region $regionModel
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\Image\AdapterFactory $imageAdapterFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\File $file
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context, 
        \Wyomind\Core\Helper\Data $coreHelper, 
        \Magento\Directory\Model\Region $regionModel, 
        \Magento\Framework\Locale\ListsInterface $localeLists, 
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate, 
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface, 
        \Magento\Framework\Image\AdapterFactory $imageAdapterFactory, 
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList, 
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Registry $registry
    )
    {
        parent::__construct($context);
        $this->_coreHelper = $coreHelper;
        $this->_coreDate = $coreDate;
        $this->_regionModel = $regionModel;
        $this->_localLists = $localeLists;
        $this->_storeManager = $storeManagerInterface;
        $this->_imageAdapterFactory = $imageAdapterFactory;
        $this->_directoryList = $directoryList;
        $this->_file = $file;
        $this->_registry = $registry;
    }

    public function getImage($src, $xSize = 150, $ySize = 150, $keepRatio = true, $styles = "")
    {
        if ($src != "") {
            $path = $this->_getMediaDir() . DIRECTORY_SEPARATOR . $src;
            if ($this->_file->fileExists($path)) {
                $part = explode("/", $src);
                $basename = array_pop($part);

                $cachePath = $this->_getMediaDir() . DIRECTORY_SEPARATOR . "stores" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $basename;

                $image = new \Magento\Framework\Image($this->_imageAdapterFactory->create(), $path);
                $image->constrainOnly(false);
                $image->keepAspectRatio($keepRatio);

                $image->setImageBackgroundColor(0xFFFFFF);
                $image->keepTransparency(true);
                $image->resize($xSize, $ySize);
                $image->save($cachePath);
                $baseurl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, false);
                
                return "<img style='" . $styles . "' src='" . $baseurl . "stores/cache/" . $basename . "'/>";
            } else {
                return;
            }
        } else {
            return;
        }
    }

    public function getStoreDescription($place)
    {
        $pattern = $this->_coreHelper->getStoreConfig('pointofsale/settings/pattern');
        $replace = [];
        $replace['image'] = $this->getImage($place->getImage(), 150, 150, true, "float:right");

        $replace['name'] = $place->getStoreName();
        $replace['code'] = $place->getStoreCode();

        $replace['address_1'] = $place->getAddressLine1();
        $replace['address_2'] = $place->getAddressLine2();
        $replace['zipcode'] = $place->getPostalCode();
        $replace['city'] = $place->getCity();

        if ($place->getState()) {
            $replace['state'] = $this->_regionModel->loadByCode($place->getState(), $place->getCountryCode())->getName();
        } else {
            $replace['state'] = null;
        }
        $replace['country'] = $this->_localLists->getCountryTranslation($place->getCountryCode());
        $replace['phone'] = $place->getMainPhone();
        $replace['email'] = $place->getEmail();
        $replace['description'] = $place->getDescription();

        $replace['hours'] = $this->getHours($place->getHours());
        $replace['days_off'] = $place->getDaysOff() ? __('Days off') . '<br/>' . $place->getDaysOff() : null;


        $replace['link'] = '<a target="blank" href="/' . $place->getStorePageUrlKey() . '.html">' . $place->getName() . "</a>";

        $search = [
            '{{image}}', '{{name}}', '{{code}}', '{{address_1}}', '{{address_2}}', '{{zipcode}}', '{{city}}', '{{state}}',
            '{{country}}', '{{phone}}', '{{email}}', '{{description}}', '{{hours}}', '{{days_off}}', '{{link}}'
        ];

        return preg_replace('#(?:<br\s*/?>\s*?){2,}#', "<br>", nl2br(str_replace($search, $replace, $pattern)));
    }

    /**
     * @param $data
     * @return null|string
     */
    public function getHours($data)
    {
        $data = json_decode($data);
        $content = null;
        if ($data != null) {
            foreach ($data as $day => $hours) {
                $content .= __($day);
                $f = explode(':', $hours->from);
                $t = explode(':', $hours->to);
                $from = $f[0] * 60 * 60 + $f[1] * 60 + 1;
                $to = $t[0] * 60 * 60 + $t[1] * 60 + 1;
                $lfrom = 0;
                $lto = 0;
                if (isset($hours->lunch_from) && isset($hours->lunch_to)) {
                    $lf = explode(':',$hours->lunch_from);
                    $lt = explode(':',$hours->lunch_to);
                    $lfrom = $lf[0] * 60 * 60 + $lf[1] * 60 + 1;
                    $lto = $lt[0] * 60 * 60 + $lt[1] * 60 + 1;
                }
                
                $content .= ' ' 
                        . $this->_coreDate->gmtDate($this->_coreHelper->getStoreConfig("pointofsale/settings/time"), $from) 
                        . ($lfrom != 0 ? ' - '.date($this->_coreHelper->getStoreConfig("pointofsale/settings/time"), $lfrom) : '')
                        . ' - ' 
                        . ($lto != 0 ? date($this->_coreHelper->getStoreConfig("pointofsale/settings/time"), $lto).' - ' : '')
                        . date($this->_coreHelper->getStoreConfig("pointofsale/settings/time"), $to) 
                        . "<br>";
            }
        }
        return $content;
    }

    protected function _getMediaDir()
    {
        return $this->_directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    }

    public function getGoogleApiKey()
    {
        return $this->_coreHelper->getStoreConfig('pointofsale/settings/googleapi');
    }

    /**
     * Get the handling fee of the shipping method
     * @return float|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHandlingFee()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->_coreHelper->getStoreConfig('carriers/pickupatstore/handling_fee', $storeId);
    }

    public function getGoogleMapsAPIScript() {
        if (!$this->_registry->registry('GoogleMapsAPILoaded')) {
            $this->_registry->register('GoogleMapsAPILoaded', true);
            return '<script type="text/javascript" type="text/javascript" src="' . '/' . '/' . 'maps.googleapis.com/maps/api/js?sensor=false&v=3&key=' . $this->getGoogleApiKey() . '"></script>';
        } else {
            return "";
        }
    }
}