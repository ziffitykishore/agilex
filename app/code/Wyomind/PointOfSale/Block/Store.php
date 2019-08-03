<?php
/**
 * Copyright (c) 2019. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace Wyomind\PointOfSale\Block;

class Store extends \Magento\Framework\View\Element\Template
{


    /**
     * @var null
     */
    private $store = null;
    /**
     * @var int|mixed
     */
    private $storeId = 0;

    /**
     * @var null|\Wyomind\PointOfSale\Helper\Data
     */
    protected $_posHelper = null;
    /**
     * @var \Magento\Directory\Model\Region
     */
    protected $_regionModel = null;
    /**
     * @var \Magento\Framework\Locale\ListsInterface|null
     */
    protected $_localLists = null;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider|null
     */
    protected $_filterProvider = null;
    /**
     * @var null|\Wyomind\PointOfSale\Model\ResourceModel\Attributes\Collection
     */
    protected $_attributesCollection = null;

    /**
     * @var \Magento\Directory\Model\Country|null
     */
    protected $_countryModel = null;

    /**
     * Store constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Wyomind\PointOfSale\Model\PointOfSale $posModel
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Wyomind\PointOfSale\Model\PointOfSale $posModel,
        \Wyomind\PointOfSale\Helper\Data $posHelper,
        \Magento\Directory\Model\Region $regionModel,
        \Magento\Directory\Model\Country $countryModel,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Wyomind\PointOfSale\Model\ResourceModel\Attributes\Collection $attributesCollection,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_posHelper = $posHelper;
        $this->_regionModel = $regionModel;
        $this->_countryModel = $countryModel;
        $this->_localLists = $localeLists;
        $this->_filterProvider = $filterProvider;
        $this->storeId = $this->getRequest()->getParam('store');
        $this->store = $posModel->getPlaceById($this->storeId);
        $this->_attributesCollection = $attributesCollection;

    }


    public function getStoreName()
    {
        return $this->store->getName();
    }

    public function getStoreLatitude()
    {
        return $this->store->getLatitude();
    }

    public function getStoreLongitude()
    {
        return $this->store->getLongitude();
    }

    public function getStoreGoogleRequest()
    {
        $fullAddress = $this->store->getAddressLine1();
        if ($this->store->getAddressLine2()) {
            $fullAddress .= "," . $this->store->getAddressLine2();
        }
        $fullAddress .= "," . $this->store->getCity();
        if ($this->store->getCountryCode()) {
            $fullAddress .= "," . $this->_countryModel->loadByCode($this->store->getCountryCode())->getName();
        }
        return $fullAddress;
    }

    public function getStoreDescription()
    {
        $html = "<b>" . $this->store->getName() . "</b><br/><br/>";
        $html .= $this->store->getAddressLine1() . "<br/>";
        if ($this->store->getAddressLine2()) {
            $html .= $this->store->getAddressLine2() . "<br/>";
        }
        $html .= $this->store->getPostalCode() . ", ";
        $html .= $this->store->getCity() . "<br/>";
        return $html;
    }


    public function getContent()
    {
        $this->store = $this->store->load($this->storeId);

        $pattern = $this->store->getStorePageContent();

        // common {{placeholders}}

        $replace = [];
        $replace['image'] = $this->_posHelper->getImage($this->store->getImage(), 150, 150, true, "float:right");

        $replace['name'] = $this->store->getName();
        $replace['code'] = $this->store->getStoreCode();

        $replace['address_1'] = $this->store->getAddressLine1();
        $replace['address_2'] = $this->store->getAddressLine2();
        $replace['zipcode'] = $this->store->getPostalCode();
        $replace['city'] = $this->store->getCity();

        if ($this->store->getState()) {
            $replace['state'] = $this->_regionModel->loadByCode($this->store->getState(), $this->store->getCountryCode())->getName();
        } else {
            $replace['state'] = null;
        }
        $replace['country'] = $this->_localLists->getCountryTranslation($this->store->getCountryCode());
        $replace['phone'] = $this->store->getMainPhone();
        $replace['email'] = $this->store->getEmail();
        $replace['description'] = $this->store->getDescription();

        $replace['hours'] = $this->_posHelper->getHours($this->store->getHours());
        $replace['days_off'] = $this->store->getDaysOff() ? __('Days off') . '<br/>' . $this->store->getDaysOff() : null;

        $replace['google_map'] = '<div id="map_canvas_pointofsale" style="min-width:50%;min-height:400px;"></div>';

        $replace['link'] = '<a target="blank" href="/' . $this->store->getStorePageUrlKey() . '.html">' . $this->store->getName() . "</a>";

        $search = [
            '{{image}}', '{{name}}', '{{code}}', '{{address_1}}', '{{address_2}}', '{{zipcode}}', '{{city}}', '{{state}}',
            '{{country}}', '{{phone}}', '{{email}}', '{{description}}', '{{hours}}', '{{days_off}}', '{{google_map}}',
            '{{link}}'
        ];

        // additional attributes placeholders
        foreach ($this->_attributesCollection as $attribute) {
            if ($attribute->getType() == \Wyomind\PointOfSale\Helper\Data::TEXT || $attribute->getType() == \Wyomind\PointOfSale\Helper\Data::TEXTAREA) {
                $replace[$attribute->getCode()] = htmlentities($this->store->getData($attribute->getCode()));
            } elseif ($attribute->getType() == \Wyomind\PointOfSale\Helper\Data::WYSIWYG) {
                $replace[$attribute->getCode()] = $this->_filterProvider->getBlockFilter()
                    ->setStoreId($this->_storeManager->getStore()->getId())
                    ->filter($this->store->getData($attribute->getCode()));
            }
            $search[] = '{{' . $attribute->getCode() . '}}';
        }


        $pattern = str_replace($search, $replace, $pattern);

        // widgets/variables/blocks....
        $pattern = $this->_filterProvider->getBlockFilter()
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->filter($pattern);

        return $pattern;
    }

}
