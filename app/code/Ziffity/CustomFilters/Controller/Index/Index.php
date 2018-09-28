<?php
/**
 * Read and process CSV file data
 */
namespace Ziffity\CustomFilters\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $csv;
        
    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var array
     */
    protected $attributeValues;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Source\TableFactory
     */
    protected $tableFactory;

    /**
     * @var \Magento\Eav\Api\AttributeOptionManagementInterface
     */
    protected $attributeOptionManagement;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory
     */
    protected $optionLabelFactory;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory
     */
    protected $optionFactory;
    
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productFactory;
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    
    /**
     * Data Constructor
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param \Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory
     * @param \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement
     * @param \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory
     * @param \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @return type
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
        \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreRepository $storeRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->csv = $csv;
        $this->_dir = $dir;
        $this->dateFilter = [];
        $this->gradeFilter = [];
        $this->attributeRepository = $attributeRepository;
        $this->tableFactory = $tableFactory;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->optionFactory = $optionFactory;
        $this->productFactory = $productFactory;
        $this->storeRepository = $storeRepository;
        $this->logger = $logger;
        
        return parent::__construct($context);
    }
        
    /**
     * Process CSV data
     * 
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute() {
        $varDirectory = $this->_dir->getPath('var');
        $filePath = $varDirectory."/custom_filter/";
        $files = scandir($filePath, SCANDIR_SORT_DESCENDING);
        $newest_file = $filePath.$files[0];            
        if (!isset($newest_file))  {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file to process.'));
        }
        $csvData = $this->csv->getData($newest_file);
        if ($csvData) {
            $getNameKey = array_search('name', $csvData[0]);
            $getSkuKey = array_search('sku', $csvData[0]);
            foreach ($csvData as $row => $data) {
                if ($row > 0) {
                    $nameData = isset($data[$getNameKey]) ? $data[$getNameKey] : '';
                    $skuData = isset($data[$getNameKey]) ? $data[$getSkuKey] : '';
                    try {
                        $this->processName($nameData, $skuData);
                        echo "<br />Line number ".($row+1)." has been processed with the SKU ".$skuData;
                    } catch (\Exception $e) {
                        $this->logger->critical($e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Process name to split year and grade
     * 
     * @param String $data
     * @return String
     */
    private function processName($data = null, $sku = null) {
        $result = '';
        if($data && $sku) {            
            $this->processYearData($data, $sku);
            $this->processGradeData($data, $sku);
        }
        return $result;
    }
    
    /**
     * Process name to segregate the year
     * 
     * @param String $data
     * @return String
     */
    private function processYearData($data = null, $sku = null) {
        $result = '';
        if($data) {
            $years = [];
            preg_match_all('/(\d{4,})/',$data, $years);
            
            switch(count($years[0])) {
                case 1:
                    $result = isset($years[0][0]) ? $years[0][0] : '';
                    break;
                case 2:
                    sort($years[0]);
                    $result = implode("-", $years[0]);
                    break;
                case 3:                    
                    sort($years[0]);
                    array_pop($years[0]);
                    $result = implode("-", $years[0]);
            }
            // save the option data to  the corresponding attribute
            ($result) ? $this->processOptionData('coin_year', $result, $sku) : '';
        }
        return $result;
    }
    
    /**
     * Process name to segregate the year
     * 
     * @param String $data
     * @return String
     */
    private function processGradeData($data = null, $sku = null) {       
        $result = '';
        $definedGrade = ['FR', 'AG', 'G', 'VG', 'F', 'VF', 'EF', 'AU', 'UNC', 'MS', 'PF', 'PR', 'EU', 'RP', 'SP'];
        $gradeData = $this->matchGradeString($definedGrade, $data);
        if(count($gradeData)>0){
            $this->processOptionData('coin_grade', $gradeData, $sku);
        }
        return $result;
    }

    /**
     * Link year attribute value with Product
     * 
     * @param String $sku
     * @param int $optionId
     */
    private function processProductAttribute($sku, $optionId) {
        $productFactory = $this->productFactory->create();
        $product = $productFactory->load($productFactory->getIdBySku($sku));
        $product->setCoinYear($optionId);
        $product->save();
    }
    
    /**
     * Get attribute by code.
     *
     * @param string $attributeCode
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     */
    public function getAttribute($attributeCode)
    {
        return $this->attributeRepository->get($attributeCode);
    }

    /**
     * Find or create a matching attribute option
     *
     * @param string $attributeCode Attribute the option should exist in
     * @param string $label Label to find or add
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processOptionData($attributeCode, $label, $sku)
    {
        $option = [];
        
        if (is_array($label)) {
            foreach ($label as $item) {
                if($item) {
                    $option[] = $this->getOptionData($attributeCode, $item);
                }
            }
        } else {
            $option[] = $this->getOptionData($attributeCode, $label);
        }       
        $optionId = implode(",", $option);
        $this->processProductAttribute($sku, $optionId);
        return $optionId;
    }
    
    /**
     * Get Option details
     * 
     * @param String $attributeCode
     * @param String $label
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOptionData($attributeCode, $label) {

        $labelData = trim($label);
        if (strlen($labelData) < 1) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Label for %1 must not be empty.', $attributeCode)
            );
        }

        // Get existing option id
        $optionId = $this->getOptionId($attributeCode, $labelData);

        if (!$optionId) { // Create option if mot exists  
            $optionId = $this->createOption($attributeCode, $labelData);
        }
        return $optionId;
    }

    /**
     * Find the ID of an option matching $label, if any.
     *
     * @param string $attributeCode Attribute code
     * @param string $label Label to find
     * @param bool $force If true, will fetch the options even if they're already cached.
     * @return int|false
     */
    public function getOptionId($attributeCode, $label, $force = false)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $this->getAttribute($attributeCode);

        // Build option array if necessary
        if ($force === true || !isset($this->attributeValues[ $attribute->getAttributeId() ])) {
            $this->attributeValues[ $attribute->getAttributeId() ] = [];

            // We have to generate a new sourceModel instance each time through to prevent it from
            // referencing its _options cache. No other way to get it to pick up newly-added values.

            /** @var \Magento\Eav\Model\Entity\Attribute\Source\Table $sourceModel */
            $sourceModel = $this->tableFactory->create();
            $sourceModel->setAttribute($attribute);

            foreach ($sourceModel->getAllOptions() as $option) {
                $this->attributeValues[ $attribute->getAttributeId() ][ $option['label'] ] = $option['value'];
            }
        }

        // Return option ID if exists
        if (isset($this->attributeValues[ $attribute->getAttributeId() ][ $label ])) {
            return $this->attributeValues[ $attribute->getAttributeId() ][ $label ];
        }

        // Return false if does not exist
        return false;
    }
    
    /**
     * Create new Option
     * 
     * @param String $attributeCode
     * @param String $label
     * @return int
     */
    private function createOption($attributeCode, $label) {

        /** @var \Magento\Eav\Model\Entity\Attribute\OptionLabel $optionLabel */
        $optionLabel = $this->optionLabelFactory->create();
        $optionLabel1 = $this->optionLabelFactory->create();
        
        $storeManagerDataList = $this->storeRepository->getList();
        foreach ($storeManagerDataList as $value) {
            $storeId = $value->getStoreId();
            switch ($storeId) {
                case 0:
                     // Create option label for Default Config
                    $optionLabel->setStoreId($storeId);
                    $optionLabel->setLabel($label);
                case 1:
                     // Create option label for CSN store
                    $optionLabel1->setStoreId(1);
                    $optionLabel1->setLabel($label);
            }
        }

        // Store data to attribute options
        $option = $this->optionFactory->create();
        $option->setLabel($optionLabel);
        $option->setStoreLabels([$optionLabel, $optionLabel1]);
        $option->setSortOrder(0);
        $option->setIsDefault(false);

        $this->attributeOptionManagement->add(
            \Magento\Catalog\Model\Product::ENTITY,
            $this->getAttribute($attributeCode)->getAttributeId(),
            $option
        );

        // Get the inserted ID. Should be returned from the installer, but it isn't.
        return $this->getOptionId($attributeCode, $label, true);
    }
    
    /**
     * Get grade string from product name
     * 
     * @param type $gradeList
     * @param type $name
     * @return boolean
     */
    private function matchGradeString($gradeList, $name) {
        $result = [];
        
        if(empty($gradeList)){
            return false;
        }
        foreach($gradeList as $grade) {
            $position = strpos($name, " ".$grade);
            if ($position > -1) {
                $string = substr($name, $position, 5);
                $existNumber = $this->extractNumberFromString($string);
                $result[] = ($existNumber) ? trim($string) : '';
            }
        }
        return array_filter($result);
    }
    
    /**
     * Extract number data from product name
     * 
     * @param type $string
     * @return int
     */
    private function extractNumberFromString($string) {
        $matches = [];
        preg_match_all('!\d+!', $string, $matches);
        $matches = array_filter($matches);
        return count($matches);
    }
}
