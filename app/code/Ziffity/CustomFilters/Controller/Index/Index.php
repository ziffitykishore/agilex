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
     * @param \Magento\Catalog\Model\ProductFactory $productModelFactory
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
        \Magento\Catalog\Model\ProductFactory $productFactory
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
                if ($row > 0){
                    $nameData = isset($data[$getNameKey]) ? $data[$getNameKey] : '';
                    $skuData = isset($data[$getNameKey]) ? $data[$getSkuKey] : '';
                    $this->processName($nameData, $skuData);
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
            //$this->processGradeData($data, $sku);
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
            ($result) ? $this->storeData('coin_year', $result, $sku) : '';
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
        if($gradeData){      
            $this->storeData('coin_grade', $gradeData, $sku);
        }
        return $result;
    }

    /**
     * Store data to attribute option and map it to the product
     * 
     * @param String $attributeCode
     * @param String $label
     * @param String $sku
     */
    private function storeData($attributeCode, $label, $sku) {
        // save the option data to  the corresponding attribute
        $this->processOptionData($attributeCode, $label, $sku);
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
        if (strlen($label) < 1) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Label for %1 must not be empty.', $attributeCode)
            );
        }

        // Get existing option id
        $optionId = $this->getOptionId($attributeCode, $label);

        if (!$optionId) { // If no, add it.            

            /** @var \Magento\Eav\Model\Entity\Attribute\OptionLabel $optionLabel */
            $optionLabel = $this->optionLabelFactory->create();
            
            // Create option label for Default config
            $optionLabel->setStoreId(0);
            $optionLabel->setLabel($label);
            
            // Create option label for CSN Store
            $optionLabel1 = $this->optionLabelFactory->create();
            $optionLabel1->setStoreId(1);
            $optionLabel1->setLabel($label);
            
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
            $optionId = $this->getOptionId($attributeCode, $label, true);
        }
        $this->processProductAttribute($sku, $optionId);
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
                $string = substr($name, $position, 4);
                $existNumber = $this->extractNumberFromString($string);
                $result[] = ($existNumber) ? $string : '';
            }
        }
        return $result;
    }
    
    /**
     * Extract number data from product name
     * 
     * @param String $string
     * @return int
     */
    private function extractNumberFromString($string) {
        preg_match_all('!\d+!', $string, $matches);
        $matches = array_filter($matches);
        return count($matches);
    }
}
