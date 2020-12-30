<?php

/**
 * Created by pp
 *
 * @project pp-dev-2-unirgy-ext
 */

namespace Unirgy\RapidFlowSales\Model\Profile;

use Magento\Catalog\Model\Product\Image;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteFactory;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Asset\MergeService;
use Unirgy\RapidFlow\Helper\Data as HelperData;
use Unirgy\RapidFlow\Model\Config;
use Unirgy\RapidFlow\Model\Io\File;
use Unirgy\RapidFlow\Model\Profile;
use Unirgy\RapidFlow\Model\Rule;
use Unirgy\RapidFlowSales\Helper\Data as RapidFlowSalesHelperData;
use Unirgy\RapidFlowSales\Model\Source;
use Unirgy\RapidFlow\Model\Profile\HistoryFactory;

class Sales extends Profile
{
    const OPTIONS_IMPORT_MISSING_CUSTOMER    = 'options/import/missing_customer';
    const OPTIONS_FAILED_CREATE_CUSTOMER     = 'options/import/failed_create_customer';
    const OPTIONS_IMPORT_MISSING_STORE       = 'options/import/missing_store';
    const OPTIONS_IMPORT_URF_ID_PREFIX       = 'options/import/urf_id_prefix';
    const OPTIONS_IMPORT_STORE_IDS           = 'options/store_ids';
    const OPTIONS_ROW_TYPES_CONFIG_PATH      = 'options/row_types';
    const OPTIONS_ROW_TYPES_JSON_CONFIG_PATH = 'options/row_types_json';
    /**
     * @var Source
     */
    protected $_modelSource;
    /**
     * @var RapidFlowSalesHelperData
     */
    protected $_helperData;
    protected $_currentEntityType;
    protected $_storeIdsFilter;
    protected $_filterByStore = [];

    public function __construct(
        HistoryFactory $historyFactory,
        Context $context,
        Registry $registry,
        MergeService $mergeService,
        IndexerRegistry $indexerRegistry,
        ResolverInterface $localeResolver,
        TimezoneInterface $localeDate,
        Config $rapidFlowConfig,
        HelperData $rapidFlowHelper,
        DirectoryList $directoryList,
        WriteFactory $directoryWrite,
        Rule $rapidFlowModelRule,
        Manager $cacheManager,
        Filesystem $magentoFilesystem,
        ScopeConfigInterface $scopeConfig,
        File $modelIoFile,
        Image $modelProductImage,
        Source $modelSource,
        RapidFlowSalesHelperData $helperData,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [])
    {
        $this->_modelSource = $modelSource;
        $this->_helperData  = $helperData;

        parent::__construct(
            $historyFactory,
            $context,
            $registry,
            $mergeService,
            $indexerRegistry,
            $localeResolver,
            $localeDate,
            $rapidFlowConfig,
            $rapidFlowHelper,
            $directoryList,
            $directoryWrite,
            $rapidFlowModelRule,
            $cacheManager,
            $magentoFilesystem,
            $scopeConfig,
            $modelIoFile,
            $modelProductImage,
            $resource,
            $resourceCollection,
            $data);
    }

    public function getCurrentEntityType()
    {
        return $this->_currentEntityType;
    }

    public function setCurrentEntityType($entityType)
    {
        $this->_currentEntityType = $entityType;

        return $this;
    }

    public function shouldFilterByStore($code)
    {
        if (array_key_exists($code, $this->_filterByStore)) {
            return $this->_filterByStore[$code];
        }

        if (!$this->getStoreIdsFilter()) {
            return false;
        }

        $columns = $this->getEntityColumns($code);

        $this->_filterByStore[$code] = in_array('store', $columns, true);

        return $this->_filterByStore[$code];
    }

    public function getStoreIdsFilter()
    {
        if ($this->_storeIdsFilter === null) {
            $this->_storeIdsFilter = $this->getData('options/store_ids');
        }

        return $this->_storeIdsFilter;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getEntityColumns($type)
    {
        $config   = $this->getEntityConfig($type);
        $table    = $config['table'];
        $excluded = isset($config['excluded'])? $config['excluded']: [];
        $mapped   = isset($config['mapped']) ? $config['mapped']: [];

        $origColumns = array_keys($this->getResource()->getConnection()->describeTable($table));
        $columns     = [];
        foreach ($origColumns as $column) {
            if (in_array($column, $excluded, true)) {
                // column not to be exported
                continue;
            }
            if (array_key_exists($column, $mapped)) {
                // if column has to be mapped
                $mapping   = $mapped[$column];
                $columns[] = $mapping['to'];
            } else {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    public function getEntityConfig($type)
    {
        return $this->helper()->getConfigForSalesEntity($type);
    }

    /**
     * @return RapidFlowSalesHelperData
     */
    protected function helper()
    {
        return $this->_helperData;
    }

    public function getColumnsForRowType($code)
    {
        $result       = [];
        $rowTypesJson = $this->getData(self::OPTIONS_ROW_TYPES_JSON_CONFIG_PATH);
        if ($rowTypesJson) {
            /** @var Source $source */
            $source = $this->getSource();

            $rowTypesTree = $source->setProfile($this)
                                   ->parseTreeRowTypesStructure(json_decode($rowTypesJson, true));
            if (!$rowTypesTree) {
                return $result;
            }
            if (isset($rowTypesTree['*'])) {
                if ($rowTypesTree['*'] === true) {
                    return $result;
                }
                if ($rowTypesTree['*'] !== true && isset($rowTypesTree['*'][$code])) {
                    $result = $rowTypesTree['*'][$code];
                }
            }
        }

        if (!in_array('urf_id', $result, true)) {
            $result[] = 'urf_id';
        }

        return $result;
    }

    public function shouldFilterByDate($code)
    {
        $filteredByDate = $this->getData('options/date_filtered_types');

        if ($filteredByDate) {
            return in_array($code, $filteredByDate, true);
        }

        return false;
    }

    public function getFieldCodes($entity)
    {
        $attrCodesKey = sprintf('attribute_codes-%s', $entity);
        if (!$this->hasData($attrCodesKey)) {
            $this->_collectAttributeCodes($entity);
        }

        return $this->getData($attrCodesKey);
    }

    public function _collectAttributeCodes($entity)
    {
        $attrCodesKey    = sprintf('attribute_codes-%s', $entity);
        $sysAttrCodesKey = sprintf('system_attribute_codes-%s', $entity);
        $columns         = (array) $this->getColumns($entity);
        $sysAttrs        = $attrs = [];
        foreach ($columns as $f) {
            if (strpos($f['field'], '.') === false) {
                $attrs[] = $f['field'];
            } else {
                $sysAttrs[] = $f['field'];
            }
        }
        $attrs    = array_unique($attrs);
        $sysAttrs = array_unique($sysAttrs);
        $this->setData($attrCodesKey, $attrs);
        $this->setData($sysAttrCodesKey, $sysAttrs);

        return $this;
    }

    protected function _processColumnsPost()
    {
        if ($this->hasData('columns_post')) {
            $columns = [];
            foreach ($this->getData('columns_post') as $et => $aEt) {
                foreach ($aEt as $k => $a) {
                    foreach ($a as $i => $v) {
                        if ($v !== '') {
                            $columns[$et][$i][$k] = $v;
                        }
                    }
                }
            }
            $this->setData('columns', $columns);
        }
    }

    protected function _processPostData()
    {
        $rowTypesJson = $this->getData(self::OPTIONS_ROW_TYPES_JSON_CONFIG_PATH);
        if ($rowTypesJson) {
            /** @var Source $source */
            $source = $this->getSource();

            $rowTypesTree = $source->setProfile($this)
                                   ->parseTreeRowTypesStructure(json_decode($rowTypesJson, true));
            if (isset($rowTypesTree['*'])) {
                if ($rowTypesTree['*'] === true) {
                    // when * is set to true, then all entities should be exported
                    $this->unsetData(self::OPTIONS_ROW_TYPES_CONFIG_PATH);
                } else {
                    $data = $this->getData();
                    $rowTypes = array_keys($rowTypesTree['*']);
                    $data['options']['row_types'] = $rowTypes;
                    $this->setData($data);
                }
            }

        }
        parent::_processPostData();
    }

    /**
     * @return Source
     */
    public function getSource()
    {
        return $this->_modelSource;
    }
}
