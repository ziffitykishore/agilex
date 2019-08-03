<?php

namespace Wyomind\PointOfSale\Block;

class PointOfSale extends \Magento\Framework\View\Element\Template
{

    protected $_pointofsaleModel = null;
    protected $_countryModel = null;
    protected $_posHelper = null;
    private $_places = null;
    private $_symbolFactory = null;
    private $_isPickupAtStore = false;
    private $_coreHelper = null;

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

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Wyomind\PointOfSale\Model\PointOfSale $pointofsaleModel,
        \Magento\Directory\Model\Country $countryModel,
        \Wyomind\PointOfSale\Helper\Data $helper,
        \Magento\Directory\Model\CurrencyFactory $symbolFactory,
        \Magento\Framework\HTTP\PhpEnvironment\Request $request,
        \Wyomind\Core\Helper\Data $coreHelper,
        \Magento\Directory\Model\Region $regionModel,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Wyomind\PointOfSale\Model\ResourceModel\Attributes\Collection $attributesCollection,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_pointofsaleModel = $pointofsaleModel;
        $this->_countryModel = $countryModel;
        $this->_posHelper = $helper;
        $this->_symbolFactory = $symbolFactory;
        $this->_coreHelper = $coreHelper;
        $this->_isPickupAtStore = strpos($request->getUriString(), "pickupatstore") !== FALSE;
        $this->_regionModel = $regionModel;
        $this->_countryModel = $countryModel;
        $this->_localLists = $localeLists;
        $this->_filterProvider = $filterProvider;
        $this->_attributesCollection = $attributesCollection;

    }

    public function getNbStoresToDisplay()
    {
        if ($this->getData('isPointOfSalePage') === true) {
            return 0;
        }
        return $this->_coreHelper->getStoreConfig("pointofsale/settings/display_x_first_pos", $this->_storeManager->getStore()->getStoreId());
    }

    public function getDisplayDistance() {
        return $this->_coreHelper->getStoreConfig("pointofsale/settings/display_distance", $this->_storeManager->getStore()->getStoreId());
    }

    public function getDisplayDuration() {
        return $this->_coreHelper->getStoreConfig("pointofsale/settings/display_duration", $this->_storeManager->getStore()->getStoreId());
    }

    public function getUnitSystem() {
        return $this->_coreHelper->getStoreConfig("pointofsale/settings/unit_system", $this->_storeManager->getStore()->getStoreId());
    }

    public function setPlaces($places)
    {
        $this->_places = $places;
    }

    public function isPickupAtStore()
    {
        return $this->_isPickupAtStore;
    }

    public function getCurrencySymbol()
    {
        return $this->_symbolFactory->create()->load($this->_storeManager->getStore()->getCurrentCurrency()->getCode())->getCurrencySymbol();
    }

    public function getPointofsale()
    {
        if ($this->_places !== null) {
            $collection = $this->_places;
        } else {
            $collection = $this->_pointofsaleModel->getPlacesByStoreId($this->_storeManager->getStore()->getStoreId(), true);
            $collection->setOrder("position", "ASC");
        }
        return $collection;
    }

    public function getCountries()
    {
        $collection = $this->_pointofsaleModel->getCountries($this->_storeManager->getStore()->getStoreId());
        $countries = [];
        foreach ($collection as $country) {
            if ($country->getCountryCode()) {
                $countryModel = $this->_countryModel->loadByCode($country->getCountryCode());
                $countryName = $countryModel->getName();
                $countries[] = [
                    'code' => $country->getCountryCode(),
                    'name' => $countryName,
                ];
            }
        }
        return $countries;
    }

    public function getStoreLocatorStoreDescription($place)
    {
        $pattern = $place->getStoreLocatorDescription();

        // common {{placeholders}}

        $replace = [];
        $replace['image'] = $this->_posHelper->getImage($place->getImage(), 150, 150, true, "");

        $replace['name'] = $place->getName();
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

        $replace['hours'] = $this->_posHelper->getHours($place->getHours());
        $replace['days_off'] = $place->getDaysOff() ? __('Days off') . '<br/>' . $place->getDaysOff() : null;

        $replace['link'] = '<a target="blank" href="/' . $place->getStorePageUrlKey() . '.html">' . $place->getName() . "</a>";

        $search = [
            '{{image}}', '{{name}}', '{{code}}', '{{address_1}}', '{{address_2}}', '{{zipcode}}', '{{city}}', '{{state}}',
            '{{country}}', '{{phone}}', '{{email}}', '{{description}}', '{{hours}}', '{{days_off}}',
            '{{link}}', "\n"
        ];

        $replace['br'] = "<br/>";

        // additional attributes placeholders
        foreach ($this->_attributesCollection as $attribute) {
            if ($attribute->getType() == \Wyomind\PointOfSale\Helper\Data::TEXT || $attribute->getType() == \Wyomind\PointOfSale\Helper\Data::TEXTAREA) {
                $replace[$attribute->getCode()] = htmlentities($place->getData($attribute->getCode()));
            } elseif ($attribute->getType() == \Wyomind\PointOfSale\Helper\Data::WYSIWYG) {
                $replace[$attribute->getCode()] = $this->_filterProvider->getBlockFilter()
                    ->setStoreId($this->_storeManager->getStore()->getId())
                    ->filter($place->getData($attribute->getCode()));
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

    public function getJsonData()
    {
        $i = 0;
        $data = [];
        foreach ($this->getPointofsale() as $place) {
            $fullAddress = $place->getAddressLine1();
            if ($place->getAddressLine_2()) {
                $fullAddress .= "," . $place->getAddressLine2();
            }
            $fullAddress .= "," . $place->getCity();
            if ($place->getCountryCode()) {
                $fullAddress .= "," . $this->_countryModel->loadByCode($place->getCountryCode())->getName();
            }
            if (!$place->getGoogleRequest()) {
                $request = $fullAddress;
            } else {
                $request = $place->getGoogleRequest();
            }

            $data[] = [
                "id" => $place->getPlaceId(),
                "title" => "<h4><b>" . $place->getName() . "</b></h4>",
                "links" => [
                    "directions" => "<a href=\"javascript:void(0);\" onclick=\"require(['pointofsale'], function(pointofsale) {pointofsale.getDirections(" . $i . ")});\">" . __("Get Directions") . "</a>",
                    "showOnMap" => "<a target=\"_blank\" href=\"//maps.google.com/maps?q=" . $request . "\">" . __("Show on Google Map") . "</a>"
                ],
                "name" => $place->getName(),
                "lat" => $place->getLatitude(),
                "lng" => $place->getlongitude(),
                "country" => $place->getCountryCode(),
                "duration" => ["text" => null, "value" => null],
                "distance" => ["text" => null, "value" => null]
            ];
            $i++;
        }
        return json_encode($data);
    }

    public function getPosHelper()
    {
        return $this->_posHelper;
    }

    public function getGoogleApiKey()
    {
        return $this->_posHelper->getGoogleApiKey();
    }

}
