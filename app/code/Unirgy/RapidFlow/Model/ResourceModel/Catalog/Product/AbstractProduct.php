<?php

namespace Unirgy\RapidFlow\Model\ResourceModel\Catalog\Product;

use Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes\Extend;
use Magento\Catalog\Model\Indexer\Product\Flat\Processor as FlatIndexer;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Staging\Model\VersionManager;
use Magento\Indexer\Model\Indexer as IndexerModelIndexer;
use Magento\Store\Model\ScopeInterface;
use Unirgy\RapidFlow\Helper\Data as HelperData;
use Unirgy\RapidFlow\Model\ResourceModel\Catalog\AbstractCatalog;
use Unirgy\SimpleLicense\Helper\ProtectedCode;
use Zend_Db;
use Unirgy\RapidFlow\Model\ResourceModel\ProductIndexerPrice;

abstract class AbstractProduct
    extends AbstractCatalog
{

    protected $_scopeConfig;


    protected $_productCollection;


    protected $_productModel;


    protected $_catalogHelper;


    protected $_productType;


    protected $_logger;


    protected $_modelStockStatus;


    protected $_productMediaConfig;


    protected $_catalogRuleCollection;


    protected $_rapidFlowCatalogrule;


    protected $_indexerRegistry;


    protected $_modelProductAction;


    protected $_catalogStockConfiguration;


    protected $_fullTextIndexer;


    protected $_catalogUrlHelper;


    protected $_insertAttrChunkSize;

    protected $_entityType = 'catalog_product';

    protected $_entityTypeId;

    protected $_tplAttrSet = [];

    protected $_categories = [];
    protected $_categoriesBySeqId = [];

    protected $_catEntity2Row = [];
    protected $_catRow2Entity = [];

    protected $_categoriesByNamePath = [];

    protected $_attributeSetFields = [];

    protected $_attrDepends = [];

    protected $_attrJoined = [];

    protected $_storeId;

    protected $_websiteStores = [];

    protected $_websitesByStore = [];

    protected $_storesByWebsite = [];

    protected $_fields = [];

    protected $_fieldsCodes = [];

    protected $_newDataTemplate = [];

    protected $_products = [];

    protected $_productIds = [];

    protected $_productIdsUpdated = [];

    protected $_valid = [];

    protected $_defaultUsed = [];

    protected $_skuLine = [];

    protected $_attrValueIds = [];

    protected $_attrValuesFetched = [];

    protected $_websiteScope = [];

    protected $_websiteScopeProducts = [];

    protected $_websiteScopeAttributes = [];

    protected $_insertEntity = [];

    protected $_updateEntity = [];

    protected $_changeAttr = [];

    protected $_insertAttr = []; // type/#=>row

    protected $_updateAttr = []; // type/vId=>value

    protected $_deleteAttr = []; // type/#=>vId

    protected $_changeWebsite = [];

    protected $_changeStock = [];

    protected $_changeCategoryProduct = [];

    protected $_mediaChanges = [];

    protected $_insertStock = [];

    protected $_updateStock = [];

    protected $_deleteStock = [];

    protected $_rtIdxFlatAttrCodes = [];

    protected $_realtimeIdx = [
        'cataloginventory_stock' => [],
        'catalog_product_attribute' => [],
        'catalog_product_price' => [],
        'catalog_url' => [],
        'catalogsearch_fulltext' => [],
        'catalog_category_product' => [],
        'catalog_product_flat' => [],
        'tag_summary' => [],
    ];

    protected $_newData = [];

    protected $_skuIdx;

    protected $_startLine;

    protected $_isLastPage = false;

    protected $_fieldAttributes = [
        'product.attribute_set' => 'attribute_set_id',
        'product.type' => 'type_id',
        'product.store' => 'store_id',
        'product.entity_id' => 'entity_id',
        'product.has_options' => 'has_options',
        'product.required_options' => 'required_options',
    ];

    protected $_autoCategory = null;

    protected $_saveAttributesMethod;

    protected $_configurableParentSku = [];

    private static $_licenseIsValid = [];

    protected $_modelProductImage;

    protected $_productFlatIndexState;

    protected $_productFlatIndexHelper;

    protected $_indexerConfig;

    const MAGENTO_ROOT_CAT_ID = 1;

    protected function _construct()
    {
        parent::_construct();

        $this->_scopeConfig = $this->_context->scopeConfig;
        $this->_productCollection = $this->_context->productCollection;
        $this->_productModel = $this->_context->productModel;
        $this->_catalogHelper = $this->_context->catalogHelper;
        $this->_productType = $this->_context->productType;
        $this->_logger = $this->_context->logger;
        $this->_modelStockStatus = $this->_context->modelStockStatus;
        $this->_productMediaConfig = $this->_context->productMediaConfig;
        $this->_catalogRuleCollection = $this->_context->catalogRuleCollection;
        $this->_rapidFlowCatalogrule = $this->_context->rapidFlowCatalogrule;
        $this->_indexerRegistry = $this->_context->indexerRegistry;
        $this->_modelProductAction = $this->_context->modelProductAction;
        $this->_catalogStockConfiguration = $this->_context->catalogStockConfiguration;
        $this->_fullTextIndexer = $this->_context->fullTextIndexer;

        $this->_productFlatIndexHelper = $this->_context->productFlatIndexHelper;
        $this->_productFlatIndexState = $this->_context->productFlatIndexState;
        $this->_modelProductImage = $this->_context->modelProductImage;
        $this->_indexerConfig = $this->_context->indexerConfig;
    }

    final public static function validateLicense($module)
    {
        if (!empty($_licenseIsValid[$module])) {
            return true;
        }
        $key = 'VN29643YBOFNSD86R2VOEWYEIF' . microtime(true);
        ProtectedCode::obfuscate($key);
        $hash = ProtectedCode::validateModuleLicense($module);
        if (sha1($key . $module) !== $hash) {
            throw new \Exception('Invalid response from validation method');
        }
        $_licenseIsValid[$module] = true;
        return true;
    }

    protected function _exportConfigurableParentSku()
    {
        if (!$this->_configurableParentSku) return $this;

        $parentIds = $this->_read->fetchPairs(
            $this->_read->select()
                ->from($this->_t('catalog_product_super_link'), ['product_id', 'group_concat(parent_id)'])
                ->where('product_id IN(?)', $this->_productSeqIds)
                ->group('product_id')
        );

        if (empty($parentIds)) return $this;

        $allPids = [];
        foreach ($parentIds as $cId => &$pIds) {
            $pIds = explode(',', $pIds);
            $allPids = array_merge($allPids, $pIds);
        }
        $parentSkus = $this->_read->fetchPairs(
            $this->_read->select()
                ->from($this->_t('catalog_product_entity'), [$this->_entityIdField, 'sku'])
                ->where($this->_entityIdField . ' IN (?)', array_values($allPids))
        );

        foreach ($parentIds as $cId => &$pIds) {
            foreach ($pIds as $dummy => &$_pId) {
                $_pId = !empty($parentSkus[$_pId]) ? $parentSkus[$_pId] : $_pId;
            }
        }

        foreach ($this->_products as $id => &$prod) {
            $seqId = $this->_productIdToSeq[$id];
            if (!empty($parentIds[$seqId])) {
                reset($parentIds[$seqId]);
                $separator = $this->_profile->getData('options/csv/multivalue_separator');
                if (!$separator) {
                    $separator = '; ';
                }
                if (!empty($this->_configurableParentSku['separator'])) {
                    $separator = $this->_configurableParentSku['separator'];
                }
                if (isset($this->_configurableParentSku['format']) && $this->_configurableParentSku['format'] == 'single') {
                    $prod[0]['product.configurable_parent_sku'] = current($parentIds[$seqId]);
                } else {
                    $prod[0]['product.configurable_parent_sku'] = implode($separator, $parentIds[$seqId]);
                }
            }
        }

        return $this;
    }

    protected function _exportProcessPrice()
    {
        self::validateLicense('Unirgy_RapidFlow');

        $profile = $this->_profile;
        $storeId = $profile->getStoreId();
        #$useMinimalPrice = true;#$profile->getData('options/export/use_minimal_price');
        $useMinimalPrice = empty($this->_fieldsCodes) || array_key_exists('price.minimal', $this->_fieldsCodes);
        $useMaximumPrice =
            (empty($this->_fieldsCodes) || array_key_exists('price.maximum', $this->_fieldsCodes));
//            && $this->_rapidFlowHelper->hasMageFeature('indexer_1.4');
        #$useFinalPrice = $profile->getData('options/export/use_final_price');
        $useFinalPrice = empty($this->_fieldsCodes) || array_key_exists('price.final', $this->_fieldsCodes);
        $addTax = $profile->getData('options/export/add_tax');
        $markup = (float)$profile->getData('options/export/markup');
        $markup /= 100;
        $loadProduct = $profile->getData('options/export/load_product');

        $p = false;

        if ($useMinimalPrice || $useFinalPrice || $useMaximumPrice || $addTax) {

            $collection = $this->_productCollection
                //->setStore($this->_storeModelStoreManagerInterface->getStore($profile->getStoreId()))
                ->setStore($storeId)
                ->addWebsiteFilter($this->_storeManager->getStore($storeId)->getWebsiteId())
                ->addAttributeToSelect('tax_class_id')
                ->addIdFilter(array_keys($this->_products));
            if ($useMinimalPrice) {
                $collection->addMinimalPrice();
            }
            if ($useFinalPrice || $useMaximumPrice) {
                $collection->addFinalPrice();
            }
            if ($addTax) {
                $collection->addTaxPercents();
            }
        }
        foreach ($this->_products as $id => &$prod) {
            if (($useMinimalPrice || $useFinalPrice || $addTax) && isset($collection)) {

                $p = $collection->getItemById($id);
                if (null === $p && $loadProduct) { // if product is not found then most likely price indexes are gone
                    $p = $this->_productModel->load($id);
                }
            }
            $price = 0;
            $sId = $storeId;
            if ($p && $useFinalPrice) {
                $finalPrice = $p->getCalculatedFinalPrice();
                if (!isset($finalPrice)) $finalPrice = $p->getFinalPrice();
            }
            if ($p && $useMinimalPrice) {
                $minPrice = $p->getMinimalPrice();
            }
            if ($p && $useMaximumPrice) {
                $maxPrice = $p->getMaxPrice();
            }
            if (isset($prod[$storeId]['price'])) {
                $price = $prod[$storeId]['price'];
            } elseif (isset($prod[0]['price'])) {
                $sId = 0;
                $price = $prod[0]['price'];
            }
            if ($p && $addTax) {
                $price *= 1 + $p->getTaxPercent() / 100;
                if (isset($finalPrice)) $finalPrice *= 1 + $p->getTaxPercent() / 100;
                if (isset($minPrice)) $minPrice *= 1 + $p->getTaxPercent() / 100;
                if (isset($maxPrice)) $maxPrice *= 1 + $p->getTaxPercent() / 100;
            }
            if ($markup) {
                $price *= 1 + $markup;
                if (isset($finalPrice)) $finalPrice *= 1 + $markup;
                if (isset($minPrice)) $minPrice *= 1 + $markup;
                if (isset($maxPrice)) $maxPrice *= 1 + $markup;
            }
            $prod[$sId]['price'] = $price;
            if (isset($finalPrice)) $prod[$sId]['price.final'] = $finalPrice;
            else $prod[$sId]['price.final'] = $price;
            if (isset($minPrice)) $prod[$sId]['price.minimal'] = $minPrice;
            else $prod[$sId]['price.minimal'] = $price;
            if (isset($maxPrice)) $prod[$sId]['price.maximum'] = $maxPrice;
            else $prod[$sId]['price.maximum'] = $price;
            unset($finalPrice);
            unset($minPrice);
            unset($maxPrice);
        }
        unset($prod);
    }

    protected function _importPrepareColumns()
    {
        self::validateLicense('Unirgy_RapidFlow');

        $profile = $this->_profile;
        $columns = (array)$profile->getColumns();
        $attrs = [];
        $dups = [];
        $alias = [];
        $this->_fields = [];
        $this->_newDataTemplate = [];
        $this->_fieldsCodes = [
            'url_key' => 0,
        ];
        foreach ($columns as $i => &$f) {
            if (!is_array($f)) {
                continue;
            }
            if (!empty($f['alias'])/* && strtolower(trim($f['alias']))!=$f['field']*/) {
                $aliasKey = strtolower(trim($f['alias']));
                if (!isset($alias[$aliasKey])) {
                    $alias[$aliasKey] = $f['field'];
                } elseif (!is_array($alias[$aliasKey]) && $alias[$aliasKey] != $f['field']) {
                    $alias[$aliasKey] = [$alias[$aliasKey], $f['field']];
                } elseif (!in_array($f['field'], $alias[$aliasKey])) {
                    $alias[$aliasKey][] = $f['field'];
                }
            }
            if (!in_array($f['field'], ['const.value', 'const.function']) && !empty($attrs[$f['field']])) {
                $dups[$f['field']] = $f['field'];
            }
            $this->_fields[$f['field']] = $f;
            $this->_fieldsCodes[$f['field']] = 0;

            if (isset($f['default']) && $f['default'] !== '') {
                if (!empty($f['default_multiselect'])) {
                    if (false !== strpos($f['field'], 'category.')
                        && !empty($f['separator'])
                    ) {
                        $f['default'] = explode($f['separator'], $f['default']);
                    } else {
                        $f['default'] = explode(',', $f['default']);
                    }
                }
                $this->_newDataTemplate[$f['field']] = $f['default'];
            }
        }
        unset($f);
        if ($dups) {
            throw new LocalizedException(__('Duplicate attributes: %1', join(', ', $dups)));
        }

        $k = 'product.websites';
        if (empty($this->_fields[$k]) && $this->_storeManager->isSingleStoreMode()) {
            $wId = $this->_storeManager->getDefaultStoreView()->getWebsiteId();
            $this->_fields[$k] = ['field' => $k, 'alias' => $k, 'default_multiselect' => true, 'default' => [$wId]];
            $this->_fieldsCodes[$k] = 0;
            $this->_newDataTemplate[$k] = [$wId];
        }

        $headers = $profile->ioRead();
        if (!$headers) {
            //no data
            $profile->ioClose();
            return;
        }
        $this->_fieldsIdx = [];
        foreach ($headers as $i => $f) {
            if ($f === '') {
                $this->_fieldsIdx[$i] = false;
                $profile->addValue('num_warnings');
                $profile->getLogger()->setLine(2)->setColumn($i + 1)
                    ->warning(__('Empty title, the column will be ignored'));
                continue;
            }
            $f = strtolower($f);
            $f = !empty($alias[$f]) ? $alias[$f] : $f;
            $this->_fieldsIdx[$i] = $f;
            foreach ((array)$f as $_ff) {
                $this->_fieldsCodes[$_ff] = $i;
            }
        }

        if (!isset($this->_fieldsCodes['sku'])) {
            //no sku field
            $profile->ioClose();
            throw new LocalizedException(__('Missing SKU column'));
        }
        $this->_skuIdx = $this->_fieldsCodes['sku'];
    }

    protected function _prepareAttributes($columns = null)
    {
        self::validateLicense('Unirgy_RapidFlow');

        // reset all attributes
        $this->_attributesById = [];
        $this->_attributesByCode = [];
        $this->_attributesByType = [];
        $storeId = $this->_profile->getStoreId();

        $removeFields = ['has_options', 'required_options', 'category_ids', 'minimal_price'];
        if ($this->_profile->getProfileType() === 'import') {
            $removeFields = array_merge($removeFields, ['created_at', 'updated_at']);
        }

        // collect data about all attributes used in profile
        $select = $this->_read->select()->from(['a' => $this->_t('eav_attribute')])
            ->where('entity_type_id=?', $this->_entityTypeId)
            ->where("frontend_input <> 'gallery' OR frontend_input IS NULL")
            ->where('attribute_code not in (?)', $removeFields);

        if ($columns) {
            $attrCodes = [];
            // special attributes
            foreach ($columns as $f) {
                if (strpos($f, '.') === false) {
                    $attrCodes[$f] = $f;
                }
                if (is_array($f) && isset($f['field']) && !empty($this->_attrDepends[$f['field']])) {
                    // added is array and isset checks, because in my tests $f is not an array and !empty emits a warning
                    foreach ((array)$this->_attrDepends[$f['field']] as $v) {
                        $attrCodes[$v] = $v;
                    }
                }
            }
            $attrCodes[] = 'url_key';
            array_unique($attrCodes);

            $select->where("is_required=1 or default_value<>'' or attribute_code in (?)", $attrCodes);
            if ($catalogAttrTable = $this->_t('catalog_eav_attribute')) {
                $select->join(['c' => $catalogAttrTable], 'c.attribute_id=a.attribute_id');
            }
        }
        $csAttrIds = array();
        $rows = $this->_read->fetchAll($select);
//        $prodTable = $this->_t("catalog/product");
        foreach ($rows as $r) {
            $a = [];
            if (!empty($r['apply_to'])) {
                foreach (explode(',', $r['apply_to']) as $t) {
                    $a[$t] = true;
                }
            }
            $r['apply_to'] = $a;
            if (in_array($r['attribute_code'], ['special_price','special_from_date','special_to_date'])) {
                $productTypes = $this->_productType->getOptionArray();
                if (empty($r['apply_to'])) {
                    $r['apply_to'] = $productTypes;
                }
                $__spApplyTo = $r['apply_to'];
                $r['apply_to'] = [];
                foreach ($__spApplyTo as $__spAt=>$__spAtTrue) {
                    if ($__spAt!='configurable') {
                        $r['apply_to'][$__spAt] = true;
                    }
                }
            }

            if (!empty($r['default_value']) && !isset($this->_newDataTemplate[$r['attribute_code']])) {
                $this->_newDataTemplate[$r['attribute_code']] = $r['default_value'];
            }

            if ($r['backend_model'] === 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend') {
                $r['frontend_input'] = 'multiselect';
            }

            $r['rtidx_eav'] = (
                    (!empty($r['is_filterable']) || !empty($r['is_filterable_in_search']) || !empty($r['is_visible_in_advanced_search']))
                    && ($r['backend_type'] == 'int' && $r['frontend_input'] == 'select'
                        || $r['backend_type'] == 'varchar' && $r['frontend_input'] == 'multiselect'
                        || $r['backend_type'] == 'decimal'
                    )
                ) || $r['attribute_code'] == 'price';

            $r['rtidx_price'] = in_array($r['attribute_code'],
                [
                    'price',
                    'special_price',
                    'special_from_date',
                    'special_to_date',
                    'tax_class_id',
                    'status',
                    'required_options'
                ]);
            $r['rtidx_tag'] = in_array($r['attribute_code'], ['visibility', 'status']);
            $r['rtidx_category'] = in_array($r['attribute_code'], ['visibility', 'status']);
            $r['rtidx_stock'] = in_array($r['attribute_code'], ['status']);
            $r['rtidx_search'] = !empty($r['is_searchable']);
            $r['rtidx_url'] = in_array($r['attribute_code'], ['url_key']);

            // if special source_model (options) fetch them from model class
            if (!empty($r['source_model']) && $r['source_model'] !== 'Magento\Eav\Model\Entity\Attribute\Source\Table') {
                $model = HelperData::om()->get($r['source_model']);
                if ($model && is_callable([$model, 'getAllOptions']) && ($options = $model->getAllOptions())) {
                    $csAttrIds[] = $r['attribute_id'];
                    $r['options'] = [];
                    foreach ($options as $o) {
                        if (is_array($o['value'])) {
                            foreach ($o['value'] as $o1) {
                                $r['options'][$o1['value']] = $o['label'] . ' - ' . $o1['label'];
                                $r['options_bytext'][strtolower(trim($o['label'] . ' - ' . $o1['label']))] = $o1['value'];
                            }
                            continue;
                        }
                        $r['options'][$o['value']] = $o['label'];
                        $r['options_bytext'][strtolower(trim($o['label']))] = $o['value'];
                    }
                }
            }
            if ($r['backend_model'] === 'Magento\Catalog\Model\Product\Attribute\Backend\Price') {
                if ($this->_catalogHelper->isPriceGlobal()) {
                    $r['is_global'] = EavAttribute::SCOPE_GLOBAL;
                } else {
                    $r['is_global'] = EavAttribute::SCOPE_WEBSITE;
                }
            }
            // save with different fetch methods
            $this->_attributesById[$r['attribute_id']] = $r;
            $this->_attributesByCode[$r['attribute_code']] =& $this->_attributesById[$r['attribute_id']];
            $aType = $this->getAttrType($r);
            $this->_attributesByType[$aType][$r['attribute_id']] =& $this->_attributesById[$r['attribute_id']];
        }
        // retrieve all options for regular eav source
//        $sql = $this->_read->quoteInto(
//            "SELECT o.attribute_id, o.option_id, v.value
//FROM {$this->_t('eav_attribute_option_value')} v
//INNER JOIN {$this->_t('eav_attribute_option')} o USING (option_id)
//WHERE v.store_id in (0, $storeId) AND o.attribute_id in (?) ORDER BY v.store_id DESC",
//            array_keys($this->_attributesById));

        $sql = $this->_read->select()
            ->from(['v' => $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE)], 'value')
            ->join(['o' => $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION)], 'v.option_id=o.option_id', ['attribute_id', 'option_id'])
            ->where('v.store_id IN (0, ?)', $storeId)->where('o.attribute_id IN (?)', array_keys($this->_attributesById))
            ->order('v.store_id DESC');
        $rows = $this->_read->fetchAll($sql);
        if ($rows) {
            foreach ($rows as $r) {
                if (in_array($r['attribute_id'], $csAttrIds)) continue;
                if (empty($this->_attributesById[$r['attribute_id']]['options'][$r['option_id']])) {
                    $this->_attributesById[$r['attribute_id']]['options'][$r['option_id']] = $r['value'];
                }
                $text = strtolower(trim($r['value']));
                if (empty($this->_attributesById[$r['attribute_id']]['options_bytext'][$text])) {
                    $this->_attributesById[$r['attribute_id']]['options_bytext'][$text] = $r['option_id'];
                }
            }
        }
    }

    protected function _getAttributeSetFields($attrSet)
    {
        if ($attrSet && !is_numeric($attrSet)) {
            $attrSet = $this->_attr('product.attribute_set', 'options_bytext', strtolower($attrSet));
        }
        if (!$attrSet) {
            throw new LocalizedException(__('Invalid attribute set'));
        }
        if (empty($this->_attributeSetFields[$attrSet])) {
//            $select = "SELECT a.attribute_code, a.attribute_id FROM {$this->_t('eav_entity_attribute')} ea
//                INNER JOIN {$this->_t('eav_attribute')} a on a.attribute_id=ea.attribute_id
//                WHERE attribute_set_id={$attrSet}";

            $select = $this->_write->select()->from(['ea' => $this->_t(self::TABLE_EAV_ENTITY_ATTRIBUTE)], [])
                ->join(['a' => $this->_t(self::TABLE_EAV_ATTRIBUTE)], 'a.attribute_id=ea.attribute_id',
                    ['attribute_code', 'attribute_id'])
                ->where('attribute_set_id=?', $attrSet);
            $this->_attributeSetFields[$attrSet] = $this->_write->fetchPairs($select);
        }
        return $this->_attributeSetFields[$attrSet];
    }

    protected function _prepareSystemAttributes()
    {
        // product.attribute_set_id
//        $select = "SELECT *
//FROM {$this->_t('eav_attribute_set')}
//WHERE entity_type_id='{$this->_entityTypeId}'
//ORDER BY sort_order, attribute_set_name";

        $select = $this->_write->select()->from($this->_t(self::TABLE_EAV_ATTRIBUTE_SET))
            ->where('entity_type_id=?', $this->_entityTypeId)->order(['sort_order', 'attribute_set_name']);
        $rows = $this->_read->fetchAll($select);
        $attr = [
            'options' => [],
            'options_bytext' => [],
            'frontend_label' => 'Attribute Set',
            'frontend_input' => 'select',
            'backend_type' => 'static',
            'force_field' => 'attribute_set_id',
            'is_required' => 1
        ];
        foreach ($rows as $r) {
            $attr['options'][$r['attribute_set_id']] = $r['attribute_set_name'];
            $attr['options_bytext'][strtolower($r['attribute_set_name'])] = $r['attribute_set_id'];
        }
        $this->_attributesByCode['product.attribute_set'] = $attr;

        // product.type_id
        $rows = $this->_productType->getOptionArray();
        $attr = [
            'options' => [],
            'options_bytext' => [],
            'frontend_label' => 'Product Type',
            'frontend_input' => 'select',
            'backend_type' => 'static',
            'force_field' => 'type_id',
            'is_required' => 1
        ];
        foreach ($rows as $k => $v) {
            $attr['options'][$k] = $k;
            $attr['options_bytext'][$k] = $k;
        }
        $this->_attributesByCode['product.type'] = $attr;

        // product.website_ids
        $rows = $this->_storeManager->getWebsites(true);
        $attr = [
            'options' => [],
            'options_bytext' => [],
            'frontend_label' => 'Websites',
            'frontend_input' => 'multiselect',
            'backend_type' => 'static',
        ];
        foreach ($rows as $k => $v) {
            if (!$k) {
                continue;
            }
            $attr['options'][$v->getId()] = $v->getCode();
            $attr['options_bytext'][strtolower($v->getCode())] = $v->getId();
        }
        $this->_attributesByCode['product.websites'] = $attr;

        // product.store_ids



        $this->_attributesByCode['category.ids'] = [
            'frontend_label' => 'Category ID(s)',
            'frontend_input' => 'multiselect'
        ];
        $this->_attributesByCode['category.path'] = [
            'frontend_label' => 'Category Path(s)',
            'frontend_input' => 'multiselect'
        ];
        $this->_attributesByCode['category.name'] = [
            'frontend_label' => 'Category Name(s)',
            'frontend_input' => 'multiselect'
        ];

        // stock.*
        $yesStr = __('Yes');
        $noStr = __('No');

        $pOptAttrs = ['has_options' => __('Has Options')];
        $pOptAttrs['required_options'] = __('Has Required Options');
        foreach ($pOptAttrs as $pOpt => $pOptLbl) {
            $this->_attributesByCode['product.' . $pOpt] = [
                'frontend_label' => $pOptLbl,
                'frontend_input' => 'select',
                'backend_type' => 'static',
                'force_field' => $pOpt,
                'options' => [
                    0 => $noStr,
                    1 => $yesStr,
                ],
                'options_bytext' => [
                    strtolower($noStr) => 0,
                    strtolower($yesStr) => 1,
                ],
            ];
        }

        $inStockStr = __('In Stock');
        $outOfStockStr = __('Out of Stock');
        $this->_attributesByCode['stock.is_in_stock'] = [
            'frontend_label' => __('Is In Stock'),
            'frontend_input' => 'select',
            'backend_type' => 'int',
            'options' => [
                0 => $outOfStockStr,
                1 => $inStockStr,
            ],
            'options_bytext' => [
                strtolower($noStr) => 0,
                strtolower($yesStr) => 1,
                strtolower($outOfStockStr) => 0,
                strtolower($inStockStr) => 1,
            ],
        ];
        $noBackOrdersStr = __('No Backorders');
        $allowQtyBelow0Str = __('Allow Qty Below 0');
        $allowQtyBelow0andNotifyStr = __('Allow Qty Below 0 and Notify Customer');
        $this->_attributesByCode['stock.backorders'] = [
            'frontend_label' => __('Backorders'),
            'frontend_input' => 'select',
            'backend_type' => 'int',
            'options' => [
                0 => $noBackOrdersStr,
                1 => $allowQtyBelow0Str,
                2 => $allowQtyBelow0andNotifyStr,
            ],
            'options_bytext' => [
                strtolower($noStr) => 0,
                strtolower($yesStr) => 1,
                strtolower($noBackOrdersStr) => 0,
                strtolower($allowQtyBelow0Str) => 1,
                strtolower($allowQtyBelow0andNotifyStr) => 2,
            ],
        ];
        $yesno = [
            'frontend_input' => 'select',
            'backend_type' => 'int',
            'options' => [0 => $noStr, 1 => $yesStr],
            'options_bytext' => ['' => 0, strtolower($noStr) => 0, strtolower($yesStr) => 1],
        ];
        $fields = [
            //'is_in_stock' => 'Is In Stock',
            'manage_stock' => __('Manage Stock'),
            'use_config_manage_stock' => __('Use Config for Managing Stock'),
            'is_qty_decimal' => __('Is quantity decimal'),
            'use_config_notify_stock_qty' => __('Use Config for Stock Qty Notifications'),
            'use_config_min_qty' => __('Use Config for Minimal Stock Qty'),
            //'backorders' => 'Backorders',
            'use_config_backorders' => __('Use Config for Backorders'),
            'use_config_min_sale_qty' => __('Use Config for Minimal Sale Qty'),
            'use_config_max_sale_qty' => __('Use Config for Maximum Sale Qty'),
            'stock_status_changed_auto' => __('Stock Status Changed Automatically'),
            //'stock_status_changed_automatically' => __('Stock Status Changed Automatically'),
            //'use_config_enable_qty_increments' => __('Use Config for Enable Qty Increments'),
            'use_config_enable_qty_inc' => __('Use Config for Enable Qty Increments'),
            'enable_qty_increments' => __('Enable Qty Increments'),
            'use_config_qty_increments' => __('Use Config for Qty Increments'),
        ];
        foreach ($fields as $k => $l) {
            $this->_attributesByCode['stock.' . $k] = $yesno + ['frontend_label' => $l];
        }

        $fields = [
            'qty' => __('Qty in Stock'),
            'min_qty' => __('Minimal Qty'),
            'min_sale_qty' => __('Minimal Sale Qty'),
            'max_sale_qty' => __('Maximum Sale Qty'),
            'notify_stock_qty' => __('Notify Stock Qty'),
            'qty_increments' => __('Qty Increments'),
        ];
        if (!isset($this->_profile) || $this->_profile->getProfileType() === 'import') {
            $fields['addqty'] = __('Increment/Decrement Qty in Stock');
        }
        foreach ($fields as $k => $l) {
            $this->_attributesByCode['stock.' . $k] = [
                'frontend_label' => $l,
                'backend_type' => 'decimal',
                'is_required' => $k === 'qty',
            ];
        }

        $fixedStr = __('Fixed');
        $dynamicStr = __('Dynamic');
        $fixedIdx = Extend::FIXED;
        $dynIdx = Extend::DYNAMIC;
        foreach (['price_type', 'weight_type', 'sku_type'] as $f) {
            #$this->_attributesByCode[$f]['backend_type'] = 'int';
            $this->_attributesByCode[$f]['frontend_input'] = 'select';
            $this->_attributesByCode[$f]['options'] = [$fixedIdx => $fixedStr, $dynIdx => $dynamicStr];
            $this->_attributesByCode[$f]['options_bytext'] = [
                '' => 0,
                strtolower($fixedStr) => $fixedIdx,
                strtolower($dynamicStr) => $dynIdx
            ];
        }
        $togetherStr = __('Together');
        $separatelyStr = __('Separately');
        $f = 'shipment_type';
        #$this->_attributesByCode[$f]['backend_type'] = 'int';
        $this->_attributesByCode[$f]['frontend_input'] = 'select';
        $this->_attributesByCode[$f]['options'] = [0 => $togetherStr, 1 => $separatelyStr];
        $this->_attributesByCode[$f]['options_bytext'] = [
            '' => 0,
            strtolower($togetherStr) => 0,
            strtolower($separatelyStr) => 1
        ];

        // backward compatibility with 1.3.x for 1.4
        $this->_attributesByCode['visibility']['options_bytext']['nowhere'] = 1;

        if (!isset($this->_profile) || $this->_profile->getProfileType() === 'export') {
            $this->_attributesByCode['product.entity_id'] = [
                'frontend_label' => 'Entity ID',
                'frontend_input' => 'text',
                'backend_type' => 'static',
                'force_field' => 'entity_id'
            ];
            if ($this->_entityIdField!='entity_id') {
                $this->_attributesByCode['product.'.$this->_entityIdField] = [
                    'frontend_label' => ucfirst($this->_entityIdField).' ID',
                    'frontend_input' => 'text',
                    'backend_type' => 'static',
                    'force_field' => $this->_entityIdField
                ];
            }

            $this->_attributesByCode['product.configurable_parent_sku'] = [
                'frontend_label' => 'Configurable Parent Sku',
                'frontend_input' => 'text',
                'backend_type' => 'text'
            ];

            $this->_attributesByCode['price.final'] = [
                'attribute_code' => 'price.final',
                'frontend_input' => 'text',
                'frontend_label' => __('Final Price'),
                'backend_type' => 'decimal'
            ];

            $this->_attributesByCode['price.minimal'] = [
                'attribute_code' => 'price.minimal',
                'frontend_input' => 'text',
                'frontend_label' => __('Minimal Price'),
                'backend_type' => 'decimal'
            ];

            $this->_attributesByCode['price.maximum'] = [
                'attribute_code' => 'price.maximum',
                'frontend_input' => 'text',
                'frontend_label' => __('Maximum Price'),
                'backend_type' => 'decimal'
            ];
        }

//        $this->setupProductFlatIdx();
    }

    protected function _importValidateColumns()
    {
        $profile = $this->_profile;
        $logger = $profile->getLogger();

        foreach ($this->_fieldsIdx as $i => $f) {
            if ($f === false) {
                continue;
            }
            $unknownField = false;
            if (is_array($f)) {
                $unknownField = true;
                $c = "";
                foreach ($f as $_f) {
                    $unknownField = $unknownField && !isset($this->_attributesByCode[$_f]);
                    if ($unknownField) {
                        $c .= ', ' . $_f;
                    }
                }
            } else if (!is_array($f) && !isset($this->_attributesByCode[$f])) {
                $c = $f;
                $unknownField = true;
            }
            if ($unknownField) {
                $profile->addValue('num_warnings');
                $logger->setLine(2)->setColumn($i + 1)
                    ->warning(__('Unknown field: %1, the column will be ignored', $c));
            }
        }
    }

    protected function _prepareCategories()
    {
        self::validateLicense('Unirgy_RapidFlow');

        $storeId = $this->_profile->getStoreId();

        $hasRowId = $this->_rapidFlowHelper->hasMageFeature(self::ROW_ID);
        $entityId = $this->_entityIdField;

        $rootStr = __('[ROOT]');
        $rootKeyStr = strtolower($rootStr);

        // load categories for the store specified in profile
        $rootCatSeqId = (int) $this->_getRootCatId();
        $rootCatId = (int) $rootCatSeqId;
        if ($hasRowId) {
            $rootCatId = (int) $this->_read->fetchOne(
                $this->_read->select()
                    ->from($this->_t('catalog_category_entity'), ['entity_id'])
                    ->where('entity_id=?', $rootCatSeqId)
                    ->where('created_in <= ?', $this->currentVersion->getId())
                    ->where('updated_in > ?', $this->currentVersion->getId())
            );
        }
        $rootPath = $rootCatId ? '1/' . $rootCatSeqId . '/' : '1/';

        $eav = $this->_eavModelConfig;

        $rootCatData = [
            'name' => $rootStr,
            'name_path' => $rootStr,
            'path' => '1/' . $rootCatSeqId,
            'parent_id' => 1,
        ];

        $categories = [
            $rootCatId => $rootCatData
        ];
        $categoriesBySeqId = [
            $rootCatSeqId => $rootCatData
        ];
        $allCategories = [1 => 1, $rootCatId => $rootCatId];

        $this->_attributesByCode['category.name']['options'][$rootCatId] = $rootStr;
        $this->_attributesByCode['category.name']['options_bytext'][$rootKeyStr] = $rootCatId;
        $this->_attributesByCode['category.path']['options'][$rootCatId] = $rootStr;
        $this->_attributesByCode['category.path']['options_bytext'][$rootKeyStr] = $rootCatId;

        $suffix = $this->_getPathSuffix($storeId);
        $suffixLen = strlen($suffix);

        $table = $this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY);
        // retrieve all categories and filter later in PHP
//        $rows = $this->_read->fetchAll("SELECT `{$entityId}`, `path`, `parent_id`, `position` FROM {$table}");
        $columns = [
            $entityId,
            'path',
            'parent_id',
            'position'
        ];
        if ($hasRowId) {
            $columns[] = 'entity_id';
        }
        $rows = $this->_read->fetchAll($this->_write->select()->from($table, $columns));

        $row2Entity = [$rootCatId=>$rootCatSeqId];
        $entity2Row = [$rootCatSeqId=>$rootCatId];

        if ($rows) {
            // create associated arrays
            $childrenMaxPos = [];
            foreach ($rows as $r) {
                $row2Entity[$r[$entityId]] = $r['entity_id'];
                $entity2Row[$r['entity_id']] = $r[$entityId];
            }
            foreach ($rows as $r) {
                $allCategories[$r[$entityId]] = $r[$entityId];
                $allCategoriesBySeqId[$r['entity_id']] = $r[$entityId];
                if (strpos($r['path'], $rootPath) === 0) {
                    $categories[$r[$entityId]] = $r;
                    $categoriesBySeqId[$r['entity_id']] = $r;
                    $childrenMaxPos[$r['parent_id']] = isset($childrenMaxPos[$r['parent_id']])
                        ? max($childrenMaxPos[$r['parent_id']], $r['position'])
                        : $r['position'];
                }

                if((int) $r[$entityId] === (int) $rootCatId){
                    $categories[$r[$entityId]]['entity_id'] = $r['entity_id'];
                    $categoriesBySeqId[$r['entity_id']]['entity_id'] = $r['entity_id'];
                }
            }
            $this->_attributesByCode['category.ids']['children_max_pos'] = $childrenMaxPos;
            // with PHP's copy on write there should not be memory waste with 2 references
            $this->_attributesByCode['category.ids']['options'] = $allCategories;
            $this->_attributesByCode['category.ids']['options_bytext'] = $allCategoriesBySeqId;

            foreach (['name', 'url_path', 'url_key'] as $k) {
                $attrId = $eav->getAttribute('catalog_category', $k)->getAttributeId();
                // fetch names for loaded categories
//                $select = "SELECT `{$entityId}`, `value` FROM `{$table}_varchar`
//                    WHERE `attribute_id`={$attrId} AND `store_id` IN (0, {$storeId}) AND `{$entityId}` IN (?)
//                    ORDER BY store_id DESC";
                $select = $this->_write->select()->from($table . '_varchar', [$entityId, 'value'])
                    ->where('`attribute_id`=?', $attrId)->where('`store_id` IN (?)', [0, $storeId])
                    ->where($entityId . ' IN (?)', array_keys($categories));

                $rows = $this->_read->fetchAll($select);
                foreach ($rows as $r) {
                    // load names for specific store OR default
                    if (empty($categories[$r[$entityId]][$k])) {
                        $categories[$r[$entityId]][$k] = $r['value'];
                    }
                    $__seqId = @$row2Entity[$r[$entityId]];
                    if ($__seqId && empty($categoriesBySeqId[$__seqId][$k])) {
                        $categoriesBySeqId[$__seqId][$k] = $r['value'];
                    }
                }
            }

            $delimiter = !empty($this->_fields['category.name']['delimiter']) ? $this->_fields['category.name']['delimiter'] : ' > ';
            // generate breadcrumbs for loaded categories
            foreach ($categories as $id => &$c) {
                $c['name_path'] = [];
                $key = [];
                $pathArr = explode('/', $c['path']);
                //$pathArr = array_slice($pathArr, 2);
                foreach ($pathArr as $i) {
                    if (!empty($entity2Row[$i])) {
                        $ascId = $entity2Row[$i];
                    } else {
                        $ascId = $i;
                    }
                    if ((int) $i === self::MAGENTO_ROOT_CAT_ID || (int) $ascId === $rootCatId) {
                        continue;
                    }
                    if (!empty($categories[$ascId]['name'])) {
                        $c['name_path'][] = $categories[$ascId]['name'];
                        $key[] = strtolower(trim($categories[$ascId]['name']));
                    }
                }
                if ($key) {
                    $this->_attributesByCode['category.name']['options'][$id] = implode($delimiter, $c['name_path']);
                    $this->_attributesByCode['category.name']['options_bytext'][implode('>', $key)] = $id;
                }
                if (!empty($c['url_path'])) {
                    $this->_attributesByCode['category.path']['options'][$id] = $c['url_path'];
                    $this->_attributesByCode['category.path']['options_bytext'][$c['url_path']] = $id;
                    if ($suffix) {
                        $additionalKey = substr($c['url_path'], -$suffixLen) === $suffix
                            ? substr($c['url_path'], 0, strlen($c['url_path']) - $suffixLen)
                            : $c['url_path'] . $suffix;
                        $this->_attributesByCode['category.path']['options_bytext'][$additionalKey] = $id;
                    }
                }
            }
            unset($c);
        }
        $this->_categories = $categories;
        $this->_categoriesBySeqId = $categoriesBySeqId;
        $this->_catEntity2Row = $entity2Row;
        $this->_catRow2Entity = $row2Entity;
    }

    public function catRowIdBySeqId($seqId)
    {
        return @$this->_catEntity2Row[$seqId];
    }
    public function catSeqIdByRowId($rowId)
    {
        return @$this->_catRow2Entity[$rowId];
    }

    protected function _prepareWebsites()
    {
        self::validateLicense('Unirgy_RapidFlow');

        foreach ($this->_storeManager->getStores() as $sId => $store) {
            $wId = $store->getWebsiteId();
            foreach ($this->_storeManager->getWebsite($wId)->getStores() as $wsId => $s) {
                $this->_websiteStores[$sId][] = $wsId;
            }
            $this->_storesByWebsite[$wId][$sId] = $store->toArray();
            $this->_websitesByStore[$sId][$wId] = $store->getWebsite()->toArray();
        }
    }

    protected function _importFetchOldData()
    {
        $this->_skus = [];
        $this->_skuSeq = [];
        $this->_products = [];
        $this->_productIds = [];
        $this->_productSeqIds = [];
        if (!$this->_newData) {
            return;
        }
        $attributeFields = array_flip($this->_fieldAttributes);

        $sqlSkus = $skus = [];
        foreach ($this->_newData as $sku => $p) {
            $skus[] = $sku;
            $sqlSkus[] = new \Zend_Db_Expr(
                (is_numeric($sku) ? "'".$sku."'" : $this->_write->quote($sku))
            );
        }

        // work around weird mysql 5.0.90(?) bug
        // SELECT `catalog_product_entity`.* FROM `catalog_product_entity` WHERE (sku IN ('', 1))
        // returns ALL records
        // retrieve product rows from database using skus from file
        $table = $this->_t('catalog_product_entity');
        $select = $this->_write->select()->from($table)->where('sku in (?)', $sqlSkus);
        if ($this->currentVersion && $this->currentVersion->getId()) {
            $select->setPart('disable_staging_preview', true);
            $select->where($table . '.created_in <= ?', $this->currentVersion->getId());
            $select->where($table . '.updated_in > ?', $this->currentVersion->getId());
        }
//        $select = $this->_write->select()->from($this->_t('catalog_product_entity'))->where('sku in ('.join(',', $skus).')', null, Select::TYPE_CONDITION);
        $productRows = $this->_write->fetchAll($select);
        unset($select);
        foreach ($productRows as $r) {
            $this->_skus[$r['sku']] = $r[$this->_entityIdField];
            $this->_skuSeq[$r['sku']] = $r['entity_id'];// it will be present in any case
            $r1 = [];
            foreach ($r as $k => $v) {
                if (!empty($attributeFields[$k])) {
                    $r1[$attributeFields[$k]] = $v;
                } else {
                    $r1[$k] = $v;
                }
            }
            $this->_products[$r[$this->_entityIdField]][0] = $r1;
            $this->_productIdToSeq[$r[$this->_entityIdField]] = $r['entity_id'];
        }
        $this->_productIds = array_keys($this->_products);
        $this->_productSeqIds = array_values($this->_skuSeq); // in case of EE 2.1+ these might differ from productIds
    }

    protected function _importResetPageData()
    {
        $this->_attrValueIds           = [];
        $this->_attrValuesFetched      = [];
        $this->_changeAttr             = [];
        $this->_changeCategoryProduct  = [];
        $this->_changeStock            = [];
        $this->_changeWebsite          = [];
        $this->_defaultUsed            = [];
        $this->_deleteAttr             = []; // type/#=>vId
        $this->_deleteStock            = [];
        $this->_insertAttr             = []; // type/#=>row
        $this->_insertEntity           = [];
        $this->_insertStock            = [];
        $this->_isLastPage             = false;
        $this->_mediaChanges           = [];
        $this->_newData                = [];
        $this->_productIdsUpdated      = [];
        $this->_products               = [];
        $this->_productSeqIds          = [];
        $this->_productIdToSeq         = [];
        $this->_productIds             = [];
        $this->_productIdsUpdated      = [];
        $this->_skuLine                = [];
        $this->_skus                   = [];
        $this->_updateAttr             = []; // type/vId=>value
        $this->_updateEntity           = [];
        $this->_updateStock            = [];
        $this->_valid                  = [];
        $this->_websiteScope           = [];
        $this->_websiteScopeAttributes = [];
        $this->_websiteScopeProducts   = [];
        foreach ($this->_realtimeIdx as &$idx) {
            $idx = [];
        }
    }

    protected function _fetchAttributeValues($storeId, $defaults = false, $productIds = null, $limitAttrIds = null, $force = false)
    {
        // do not fetch attributes of existing products when only creating new products
        if (!$force && $this->_profile->getData('options/import/actions') === 'create') {
            return;
        }
        if (!empty($this->_attrValuesFetched[$storeId])) {
            return;
        }
        if ($productIds === null) {
            $productIds = $this->_productIds;
        }
        $table = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);

        // retrieve attribute data by type (table)
        foreach ($this->_attributesByType as $type => $attrs) {
            // static attributes are already loaded with entity rows
            if ($type === 'static') {
                continue;
            }
            // no need to retrieve attributes used in filters
            foreach ($this->_attrJoined as $id) {
                unset($attrs[$id]);
            }
            if ($limitAttrIds && is_array($limitAttrIds)) {
                $oldAttrs = $attrs;
                foreach ($oldAttrs as $id => $a) {
                    if (!in_array($id, $limitAttrIds)) {
                        unset($attrs[$id]);
                    }
                }
            }
            $attrIds = array_keys($attrs);
            $entityId = $this->_entityIdField;
            // retrieve attribute data for this page
            $rows = $this->_read->fetchAll($this->_read->select()->from($table . '_' . $type)
                ->where("{$entityId} in (?)",
                    $productIds)// load values only for products in this page
                ->where('attribute_id in (?)', $attrIds)// load only attributes stored in this table
                ->where('store_id in (0, ?)', $storeId) // load both default and store specific records
            //->order('store_id', 'desc')) // first load store specific records
            );
            if (empty($rows)) {
                continue;
            }
            // retrieve store specific data AND default data
            foreach ($rows as $r) {
                $attrCode = $this->_attr($r['attribute_id'], 'attribute_code');
                if (empty($this->_products[$r[$entityId]][$r['store_id']][$attrCode])) {
                    // text multiselect values separated by commas
                    if ($this->_attr($r['attribute_id'], 'frontend_input') === 'multiselect') {
                        if ($r['value'] === '' || $r['value'] === null) {
                            $r['value'] = [];
                        } else {
                            $r['value'] = array_unique(explode(',', $r['value']));
                        }
                    }
                    // if value was not set before, just set it as plain value
                    $this->_products[$r[$entityId]][$r['store_id']][$attrCode] = $r['value'];
                } else {
                    if (!is_array($this->_products[$r[$entityId]][$r['store_id']][$attrCode])) {
                        if ($r['value'] !== $this->_products[$r[$entityId]][$r['store_id']][$attrCode]) {
                            // if value was set already, it is a multiselect, convert to array
                            $this->_products[$r[$entityId]][$r['store_id']][$attrCode] = [
                                $this->_products[$r[$entityId]][$r['store_id']][$attrCode],
                                $r['value'],
                            ];
                        }
                    } else {
                        if (!in_array($r['value'], $this->_products[$r[$entityId]][$r['store_id']][$attrCode])) {
                            // multiselect was already initialized, add to array
                            $this->_products[$r[$entityId]][$r['store_id']][$attrCode][] = $r['value'];
                        }
                    }
                }
                $this->_attrValueIds[$r[$entityId]][$r['store_id']][$attrCode] = $r['value_id'];
            }
            unset($rows);
        } // foreach ($this->_attributesByType as $type=>$attrs)

        if ($defaults) {
            $this->_attrValuesFetched[0] = true;
        }
        $this->_attrValuesFetched[$storeId] = true;
    }

    protected function _fetchWebsiteValues()
    {
        if ($this->_hasColumnsLike('product.websites')) {
            $productIds = $this->_productIds;
            if (!empty($this->_productSeqIds) && is_array($this->_productSeqIds)) {
                $productIds = array_flip($this->_productIdToSeq);
            }
//            $sql = $this->_read->quoteInto("SELECT * FROM {$this->_t('catalog_product_website')} WHERE website_id<>0 AND product_id IN (?)",
//                                           $this->_productIds);
            $sql = $this->_read->select()->from($this->_t(self::TABLE_CATALOG_PRODUCT_WEBSITE))->where('website_id<>0 AND product_id IN (?)', $this->_productSeqIds);// use sequence ids because they are used in EE, in CE they should match product id
            if ($rows = $this->_read->fetchAll($sql)) {
                foreach ($rows as $r) {
                    $pid = $productIds[$r['product_id']];// fetched product_id is actually seq id in Magento 2.1 EE
                    $this->_products[$pid][0]['product.websites'][] = $r['website_id'];
                }
            }
        }
    }

    protected function _fetchCategoryValues()
    {
        if ($this->_hasColumnsLike('category.')) {
//            $sql = $this->_read->quoteInto("SELECT * FROM {$this->_t('catalog_category_product')} WHERE product_id IN (?)",
//                                           $this->_productIds);
            $productIds = $this->_productIds;
            if (!empty($this->_productIdToSeq) && is_array($this->_productIdToSeq)) {
                // $this->_productIdToSeq contains mapping of sequence ids to entity IDs
                // it must be generated in any case
                $productIds = array_flip($this->_productIdToSeq);
            }
            $sql = $this->_read->select()->from(['main' => $this->_t(self::TABLE_CATALOG_CATEGORY_PRODUCT)])
                ->where('product_id IN (?)', $this->_productSeqIds);
            if($this->_rapidFlowHelper->hasMageFeature(self::ROW_ID)){
                // if sequence tables are used, the query must be updated
                $sql->join(['p'=>$this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY)],
                    'p.entity_id = main.product_id',
                    ['prid' => $this->_entityIdField]);
                $sql->join(['c'=>$this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY)],
                    'c.entity_id = main.category_id',
                    ['catid' => $this->_entityIdField]);
            }
            if ($rows = $this->_read->fetchAll($sql)) {
                foreach ($rows as $r) {
                    $pId = isset($r['prid'])? $r['prid']: $productIds[$r['product_id']];
                    //$pId = $productIds[$r['product_id']];
                    $catId = isset($r['catid'])? $r['catid']: $r['category_id'];
                    $this->_products[$pId][0]['category.ids'][]            = $catId;
                    $this->_products[$pId][0]['category.path'][]           = $catId;
                    $this->_products[$pId][0]['category.name'][]           = $catId;
                    $this->_products[$pId][0]['category.position'][$catId] = $r['position'];
                }
            }
        }
    }

    protected function _fetchStockValues()
    {
        if ($this->_hasColumnsLike('stock.')) {
//            $sql = $this->_read->quoteInto("SELECT * FROM {$this->_t('cataloginventory_stock_item')} WHERE product_id IN (?)",
//                                           $this->_productIds);
            $productIds = $this->_productIds;
            if (!empty($this->_productSeqIds) && is_array($this->_productSeqIds)) {
                $productIds = array_flip($this->_productIdToSeq);
            }
            $sql = $this->_write->select()->from($this->_t(self::TABLE_CATALOGINVENTORY_STOCK_ITEM))
                ->where('stock_id=1')
                ->where(' product_id IN (?)', $this->_productSeqIds);
            if ($rows = $this->_read->fetchAll($sql)) {
                foreach ($rows as $r) {
                    foreach ($r as $k => $v) {
                        if ($k !== 'item_id' && $k !== 'product_id' && $k !== 'stock_id') {
                            $pId = $productIds[$r['product_id']];
                            $this->_products[$pId][0]['stock.' . $k] = $v;
                        }
                    }
                }
            }
        }
        if ($this->_profile && $this->_profile->getData('options/export/configurable_qty_as_sum')
            && (empty($this->_fieldsCodes) || array_key_exists('stock.qty', $this->_fieldsCodes))
        ) {
            $confSumQty = $this->_calculateConfigurableSumQty();
            foreach ($confSumQty as $pId => $sumQty) {
                $this->_products[$pId][0]['stock.qty'] = $sumQty;
            }
        }
    }

    protected function _calculateConfigurableSumQty()
    {
        return $this->_read->fetchPairs(
            $this->_read->select()->from(['sp' => $this->_t('catalog_product_entity')], [])
                ->join(
                    ['psl' => $this->_t('catalog_product_super_link')],
                    "sp.{$this->_entityIdField}=psl.parent_id",
                    []
                )
                ->join(
                    ['csi' => $this->_t('cataloginventory_stock_item')],
                    'psl.product_id=csi.product_id',
                    []
                )
                ->where("sp.{$this->_entityIdField} in (?)", array_keys($this->_products))
                ->group("sp.{$this->_entityIdField}")
                ->columns(["sp.{$this->_entityIdField}", 'sum(IF(csi.qty>0, csi.qty, 0))'])
        );
    }

    protected function _importProcessNewData()
    {
        foreach ($this->_newData as $sku => $p) {
            // prepare url_key if missing
            if (empty($this->_skus[$sku]) || isset($p['name']) || isset($p['url_key'])) {
                $urlKey = null;
                if (!empty($p['url_key'])) {
                    $urlKey = $p['url_key'];
                } elseif (!empty($p['name']) && empty($this->_skus[$sku])) {
                    // if name is provided and it is new product, use name to get url-key
                    $urlKey = $p['name'];
                } elseif (!empty($this->_skus[$sku])) {
                    $pId = $this->_skus[$sku];
                    if (!empty($this->_products[$pId][0]['url_key'])) {
                        $urlKey = $this->_products[$pId][0]['url_key'];
                    } elseif (!empty($this->_products[$pId][0]['name'])) {
                        $urlKey = $this->_products[$pId][0]['name'];
                    }
                }
                $this->_newData[$sku]['url_key'] = $this->_createUrlKey($urlKey, $sku,
                    $this->_t('catalog_product_entity') . '_varchar');
            }

            if (!empty($p['category.name'])) {
                $delimiter = !empty($this->_fields['category.name']['delimiter']) ? $this->_fields['category.name']['delimiter'] : '>';
                foreach ($this->_newData[$sku]['category.name'] as $i => $v) {
                    $levels = explode($delimiter, $v);
                    $newArr = [];
                    foreach ($levels as $l) {
                        $newArr[] = trim($l);
                    }
                    $this->_newData[$sku]['category.name'][$i] = join($delimiter, $newArr);
                }
            }

            // do not overwrite attributes with default values for existing products
            if (!empty($this->_skus[$sku])) {
                foreach ($this->_defaultUsed[$sku] as $k => $v) {
                    unset($this->_newData[$sku][$k]);
                }
            }
        }
    }

    protected function _importCreateAttributeSet($name)
    {
        $attr = 'product.attribute_set';
        $name = trim($name);
        if (!empty($this->_attributesByCode[$attr]['options_bytext'][strtolower($name)])) {
            return;
        }
        $profile = $this->_profile;

        if (!$profile->getData('options/import/dryrun')) {
            $w = $this->_write;
            $gTable = $this->_t('eav_attribute_group');
            $eaTable = $this->_t('eav_entity_attribute');

            if (!$this->_tplAttrSet) {
                $tplId = (int)$profile->getData('options/import/create_attributeset_template');
                $this->_tplAttrSet = [
//                    'groups' => $w->fetchAll("select * from {$gTable} where attribute_set_id={$tplId}"),
                    'groups' => $w->fetchAll($this->_write->select()->from($gTable)->where('attribute_set_id=?', $tplId)),
//                    'attrs' => $w->fetchAll("select * from {$eaTable} where attribute_set_id={$tplId}"),
                    'attrs' => $w->fetchAll($this->_write->select()->from($eaTable)->where('attribute_set_id=?', $tplId)),
                ];
            }
            $this->_write->insert($this->_t('eav_attribute_set'), [
                'entity_type_id' => $this->_entityTypeId,
                'attribute_set_name' => $name,
            ]);
            $asId = $w->lastInsertId();
            foreach ($this->_tplAttrSet['groups'] as $g) {
                $g1 = $g;
                $g1['attribute_set_id'] = $asId;
                unset($g1['attribute_group_id']);
                $w->insert($gTable, $g1);
                $gId = $w->lastInsertId();
                foreach ($this->_tplAttrSet['attrs'] as $a) {
                    if ($a['attribute_group_id'] != $g['attribute_group_id']) {
                        continue;
                    }
                    unset($a['entity_attribute_id']);
                    $a['attribute_set_id'] = $asId;
                    $a['attribute_group_id'] = $gId;
                    $w->insert($eaTable, $a);
                }
            }
        } else {
            $asId = 0;
            foreach ($this->_attributesByCode[$attr]['options'] as $k => $v) {
                $asId = max($asId, $k);
            }
            $asId++;
        }

        $this->_attributesByCode[$attr]['options'][$asId] = $name;
        $this->_attributesByCode[$attr]['options_bytext'][strtolower($name)] = $asId;

        return $asId;
    }

    protected function _importCreateAttributeOption($attr, $name)
    {
        $aId = $attr['attribute_id'];
        $name = trim($name);
        if (!empty($this->_attributesById[$aId]['options_bytext'][strtolower($name)])) {
            return;
        }
        $profile = $this->_profile;

        if (!$profile->getData('options/import/dryrun')) {

            $this->_write->insert($this->_t('eav_attribute_option'), ['attribute_id' => $aId]);
            $oId = $this->_write->lastInsertId();

            $this->_write->insert($this->_t('eav_attribute_option_value'),
                ['option_id' => $oId, 'store_id' => 0, 'value' => $name]);
            $vId = $this->_write->lastInsertId();
        } else {
            if (!empty($this->_attributesById[$aId]['options'])) {
                $oId = 0;
                foreach ($this->_attributesById[$aId]['options'] as $k => $v) {
                    $oId = max($oId, $k);
                }
                $oId++;
            } else {
                $oId = 1;
            }
        }

        $this->_attributesById[$aId]['options'][$oId] = $name;
        $this->_attributesById[$aId]['options_bytext'][strtolower($name)] = $oId;

        return $oId;
    }

    protected function _importCreateCategory($name)
    {
        $profile = $this->_profile;
        $storeId = $this->_storeId;
        $attr = 'category.name';

        if (!$profile->getData('options/import/dryrun')) {
            if ($this->_autoCategory === null) {
//                $row = $this->_read->fetchRow("SELECT entity_type_id, default_attribute_set_id FROM {$this->_t('eav_entity_type')} WHERE entity_type_code='catalog_category'");
                $row = $this->_read->fetchRow($this->_write->select()
                    ->from($this->_t(self::TABLE_EAV_ENTITY_TYPE), ['entity_type_id', 'default_attribute_set_id'])
                    ->where('entity_type_code=?', 'catalog_category'));
                $eav = $this->_eavModelConfig;

                $this->_autoCategory = [
                    'type_id' => $row['entity_type_id'],
                    'attribute_set_id' => $row['default_attribute_set_id'],
                    'suffix' => '',//$this->_getPathSuffix($storeId),
                    'default' => [
                        'is_active' => $profile->getData('options/import/create_categories_active'),
                        'is_anchor' => $profile->getData('options/import/create_categories_anchor'),
                        'display_mode' => $profile->getData('options/import/create_categories_display'),
                        'include_in_menu' => $profile->getData('options/import/create_categories_menu'),
                        'name' => null,
                        'url_key' => null,
                        'url_path' => null,
                    ],
                ];
                foreach ($this->_autoCategory['default'] as $a => $v) {
                    $a1 = $eav->getAttribute('catalog_category', $a);
                    $this->_autoCategory['attr'][$a] = [
                        'type' => $this->getAttrType($a1->getData(), 'catalog/category'),
                        'id' => $a1->getId()
                    ];
                }
            }

            $delimiter = !empty($this->_fields[$attr]['delimiter']) ? $this->_fields[$attr]['delimiter'] : ' > ';

            $path = '1/' . $this->_getRootCatId();
            $parentId = $this->_getRootCatId();
            $namePathArr = [];
            $urlPathArr = [];
            $level = 1;
            $table = $this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY);
            $createdInPaths = [];
            $catNameArr = explode(trim($delimiter), $name);
            foreach ($catNameArr as $i => $catName) {
                $catName = trim($catName);
                $level++;
                $namePathArr[] = $catName;
                $namePath = implode($delimiter, $namePathArr);
                $namePathKey = strtolower(implode('>', $namePathArr));
                if (empty($this->_attributesByCode[$attr]['options_bytext'][$namePathKey])) {
                    if (!isset($this->_attributesByCode['category.ids']['children_max_pos'][$parentId])) {
                        $this->_attributesByCode['category.ids']['children_max_pos'][$parentId] = 0;
                    }
                    $data = [
                        'attribute_set_id' => $this->_autoCategory['attribute_set_id'],
                        'parent_id' => $parentId,
                        'created_at' => HelperData::now(),
                        'updated_at' => HelperData::now(),
                        'path' => $path,
                        'position' => ++$this->_attributesByCode['category.ids']['children_max_pos'][$parentId],
                        'level' => $level,
                        'children_count' => 0,
                    ];
                    if($this->_rapidFlowHelper->hasMageFeature(self::ROW_ID)){
                        $data['entity_id'] = $this->_getNextCategorySequence((bool) $this->_profile->getData('options/import/dryrun'));
                        $data['created_in'] = 1;
                        $data['updated_in'] = VersionManager::MAX_VERSION;
                    }
                    $this->_write->insert($table, $data);
                    $cId = $this->_write->lastInsertId();
                    $this->_updateCategories($cId);
                    $parentId = isset($data['created_in'], $data['entity_id']) ? $data['entity_id'] : $cId;// if EE, use sequence entity id for parent id and path
                    $createdInPaths[] = $path;
                    $path .= '/' . $parentId;
                    $this->_write->update($table, ['path' => $path], "{$this->_entityIdField}='{$cId}'");
                    $this->_rapidFlowHelper->addCategoryIdForRewriteUpdate($cId, $this->catSeqIdByRowId($cId));

                    $attrValues = $this->_autoCategory['default'];
                    $attrValues['name'] = $catName;
                    $attrValues['url_key'] = $this->_rapidFlowHelper->formatUrlKey($catName);
                    $urlPathArr[] = $attrValues['url_key'];
                    $urlPath = implode('/', $urlPathArr) . $this->_autoCategory['suffix'];
                    $attrValues['url_path'] = $urlPath;
                    foreach ($attrValues as $a => $v) {
                        $a1 = $this->_autoCategory['attr'][$a];
                        $this->_write->insert($table . '_' . $a1['type'], [
                            $this->_entityIdField => $cId,
                            'attribute_id' => $a1['id'],
                            'value' => $v,
                        ]);
                    }
                    $this->_attributesByCode[$attr]['options'][$cId] = $namePath;
                    $this->_attributesByCode[$attr]['options_bytext'][$namePathKey] = $cId;

                    $this->_attributesByCode['category.path']['options'][$cId] = $urlPath;
                    $this->_attributesByCode['category.path']['options_bytext'][$urlPath] = $cId;

                } else {
                    $parentId = $this->_attributesByCode[$attr]['options_bytext'][$namePathKey];
                    $pathId = $this->_categories[$parentId]['entity_id']; // this should exist in any case, and for EE it can be different than parentId
//                    $select = "SELECT `value` FROM {$table}_varchar
//                        WHERE {$this->_entityIdField}='{$parentId}' AND attribute_id='{$this->_autoCategory['attr']['url_key']['id']}'
//                        ORDER BY store_id";
                    $select = $this->_write->select()->from("{$table}_varchar", ['value'])
                        ->where($this->_entityIdField . '=?', $parentId)
                        ->where('attribute_id=?', $this->_autoCategory['attr']['url_key']['id']);
                    $urlPathArr[] = $this->_write->fetchOne($select);
                    //$this->_attributesByCode['category.path']['options'][$parentId]; // not really doing anything
                    $path .= '/' . $pathId;
                    $cId = $parentId;
                }
#var_dump($this->_attributesByCode[$attr]);
            }

            if (!empty($createdInPaths)) {
                $updateCountIds = [];
                foreach ($createdInPaths as $cInPath) {
                    foreach (explode('/', $cInPath) as $_cCountId) {
                        $cCountId = $this->catSeqIdByRowId($_cCountId);
                        if (empty($updateCountIds[$cCountId])) {
                            $updateCountIds[$cCountId] = 0;
                        }
                        $updateCountIds[$cCountId]++;
                    }
                }
                foreach ($updateCountIds as $uCountId => $cAddCount) {
                    $this->_write->query(
                        "UPDATE {$table} SET children_count=children_count+" . (int)$cAddCount
                        . " WHERE {$this->_entityIdField}=" . (int)$uCountId
                    );
                    $this->_rapidFlowHelper->addCategoryIdForRewriteUpdate($uCountId, $this->catSeqIdByRowId($uCountId));
                }
            }

        } else {
            if (!empty($this->_attributesByCode[$attr]['options'])) {
                $cId = 0;
                foreach ($this->_attributesByCode[$attr]['options'] as $k => $v) {
                    $cId = max($cId, $k);
                }
                $cId++;
            } else {
                $cId = 1;
            }
            $this->_attributesByCode[$attr]['options'][$cId] = $name;
            $this->_attributesByCode[$attr]['options_bytext'][strtolower($name)] = $cId;
        }

        return $cId;
    }

    protected function _importGenerateAttributeValues()
    {
        $profile = $this->_profile;
        $logger = $profile->getLogger();
        $storeId = $this->_storeId;

        $sameAsDefault = $profile->getData('options/import/store_value_same_as_default');

        // load old values for changed website scope attributes for comparison
        if (!empty($this->_websiteScope)) {
            $websiteProductIds = array_keys($this->_websiteScopeProducts);
            $websiteAttrIds = array_keys($this->_websiteScopeAttributes);
            foreach ($this->_websiteStores[$storeId] as $sId) {
                $this->_fetchAttributeValues($sId, false, $websiteProductIds, $websiteAttrIds);
            }
        }
        // generate attribute value actions
        foreach ($this->_changeAttr as $sku => $p) {
            //$logger->setLine($this->_skuLine[$sku]);
            $pId = $this->_skus[$sku];
            foreach ($p as $k => $v) {
                $attr = $this->_attr($k);
                if (!$attr) {
                    continue;
                }
                $aId = $attr['attribute_id'];
                $aType = $this->getAttrType($attr);
                // multiselect values
                if (is_array($v)) {
                    $v = join(',', $v);
                }
                // find which actions need to be performed
                $values = [];
                if (!empty($this->_products[$pId])) {
                    foreach ($this->_products[$pId] as $sId => $sValues) {
                        if (isset($sValues[$k])) {
                            $values[$sId] = $sValues[$k];
                        }
                    }
                }
                $sIds = !empty($this->_websiteScope[$sku][$aId]) ? $this->_websiteStores[$storeId] : [$storeId];
                $sActions = [];
                if ($v !== null && $v !== false && $v !== '') { // new value exists
                    if (!isset($this->_attrValueIds[$pId][0][$k])) {
                        // there is no default value for attribute.
                        if ($attr['is_required'] || $attr['is_global'] == 1) {
                            // if attribute is required or global scope, there must be a value
                            $sActions = [0 => 'I'];
                        }
                    }

//                    // no default value at all AND we are not operating on a store level, save only default value
//                    if (!isset($this->_attrValueIds[$pId][0][$k]) && $attr['is_required']) {
//                        $sActions = array(0=>'I');
//                    }
//                    // default is set and store is 0, then we're updating it
//                    elseif (isset($this->_attrValueIds[$pId][0][$k]) && !$storeId) {
//                        $sActions = array(0=>'U');
//                    }
//                    // attribute is global
//                    elseif ($attr['is_global']==1) {
//                        if(!isset($this->_attrValueIds[$pId][0][$k])) {
//                            $sActions = array(0 => 'I'); // if attribute is global and has no value for default, add the value
//                        } else {
//                            $sActions = array(0 => 'U'); // if there is value, update it
//                        }
//                    }
                    // updating not defaults, default value is set, check store values
                    foreach ($sIds as $sId) {
                        if ($attr['is_global'] == 1 && $sId != 0 && empty($sActions[0])) {
                            $sActions[0] = 'U';
                            if (isset($this->_attrValueIds[$pId][$sId][$k])) {
                                $sActions[$sId] = 'D'; // make sure to remove any store level value for attribute
                            }
                            // $this->_psrLogLoggerInterface->log($attr, null, 'rf.log', true);
                            continue; // global values should be updated for store 0 only
                        }
                        if (isset($this->_attrValueIds[$pId][$sId][$k])) {
                            if ($sId != 0 && isset($values[0]) && $v == $values[0] && $sameAsDefault === 'default') {
                                // if store id ($sId) is not 0 and new value is same as default, leave just default
                                $sActions[$sId] = 'D';
                            } else {
                                $sActions[$sId] = 'U';
                            }
                        } else {
                            $sActions[$sId] = 'I';
                        }
                    }
                } else { // new value is empty
                    // default value exists and updating defaults - delete default
                    // if product is global then remove for default values as well.
                    if (isset($this->_attrValueIds[$pId][0][$k]) && (!$storeId || $attr['is_global'] == 1)) {
                        $sActions = [0 => 'D'];
                    }
                    // attribute is global
//                    elseif ($attr['is_global']==1) {
//                        $sActions = array(0=>'D');
//                    }
                    // updating not defaults, delete store values
                    else if ($storeId) {
                        foreach ($sIds as $sId) {
                            if ($sId && isset($this->_attrValueIds[$pId][$sId][$k])) {
                                $sActions[$sId] = 'D';
                            }
                        }
                    }
                }
                $this->_rtIdxRegisterAttrChange($sku, $k, $v);
                // generate attribute value inserts/updates/deletes
                foreach ($sActions as $sId => $action) {
                    switch ($action) {
                        case 'I':
                            $this->_insertAttr[$aType][] = [
                                'attribute_id' => $aId,
                                'store_id' => $sId,
                                $this->_entityIdField => $pId,
                                'value' => $v,
                            ];
                            break;

                        case 'U':
                            $this->_updateAttr[$aType][$this->_attrValueIds[$pId][$sId][$k]] = $v;
                            break;

                        case 'D':
                            $this->_deleteAttr[$aType][] = $this->_attrValueIds[$pId][$sId][$k];
                            break;
                    }
                }
            }
        }
    }

    protected function _importSaveEntities()
    {
        $logger = $this->_profile->getLogger()->setColumn(0);

        $table = $this->_t('catalog_product_entity');
        // create new products
        foreach ($this->_insertEntity as $a) {
            $this->_write->insert($table, $a);
            $pId = $this->_write->lastInsertId();
            $this->_skus[$a['sku']] = $pId;
            $this->_skuSeq[$a['sku']] = isset($a['entity_id']) ? $a['entity_id'] : $pId;
            $this->_productIdsUpdated[$pId] = 1;
            $this->_rtIdxRegisterNewProduct($a['sku']);
            $logger->setLine($this->_skuLine[$a['sku']])->success(null, 1);
        }
        // update existing entity rows
        foreach ($this->_updateEntity as $pId => $a) {
            $this->_write->update($table, $a, $this->_entityIdField . '=' . $pId);
            $this->_productIdsUpdated[$pId] = 1;
        }
    }

    protected function _importSaveAttributeValues()
    {
//        $table = $this->_t('catalog_product_entity');
#echo "<xmp>"; print_r($this->_insertAttr); echo "</xmp>"; exit;
        foreach ($this->_insertAttr as $type => $attrs) {
            if ($type === 'static') {
                continue;
            }
            $table = $this->_tablesByType[$type];
            $rows = [];
            $i = 0;
            $j = 0;
            $sqlPrefix = "INSERT INTO `{$table}` ( `attribute_id`, `store_id`, `{$this->_entityIdField}`, `value`) VALUES ";

            foreach ($attrs as $a) {
                $sqlValue = "('{$a['attribute_id']}', '{$a['store_id']}', '{$a[$this->_entityIdField]}', ?)";
                $value = $type === 'varchar' ? substr($a['value'], 0, 255) : $a['value'];
                $sql = $this->_write->quoteInto($sqlValue, $value);
                if ($type === 'text' && strlen((string)$value) > 4000) {
                    try {
                        $this->_write->exec($sqlPrefix . $sql);
                    } catch (\Exception $e) {
                        $this->_logger->debug($sqlPrefix . $sql);
                        $this->_logger->debug($e->getTraceAsString());
                        $this->_profile->getLogger()->error($e->getMessage());
                    }
                } else {
                    $rows[] = $sql;
                }
            }
            $chunks = array_chunk($rows, $this->_insertAttrChunkSize);
            foreach ($chunks as $chunk) {
                try {
                    $this->_write->getConnection()->exec($sqlPrefix . join(',', $chunk));
                } catch (\Exception $e) {
                    $this->_logger->debug($sqlPrefix . join(',', $chunk));
                    $this->_logger->debug($e->getTraceAsString());
                    $this->_profile->getLogger()->error($e->getMessage());
                }
            }
        }
        foreach ($this->_updateAttr as $type => $attrs) {
            if ($type === 'static') {
                continue;
            }
            $table = $this->_tablesByType[$type];
            foreach ($attrs as $k => $v) {
                try {
                    $this->_write->update($table,
                        [
                            'value' => $type === 'varchar' ? substr($v, 0, 255) : $v,
                        ],
                        'value_id=' . $k);
                } catch (\Exception $e) {
                    $this->_logger->debug($table);
                    $this->_logger->debug(['value' => $type === 'varchar' ? substr($v, 0, 255) : $v,]);
                    $this->_logger->debug('value_id=' . $k);
                    $this->_logger->debug($e->getTraceAsString());
                    $this->_profile->getLogger()->error($e->getMessage());
                }
            }
        }
        foreach ($this->_deleteAttr as $type => $vIds) {
            if ($type === 'static') {
                continue;
            }
            $table = $this->_tablesByType[$type];
            try {
                $this->_write->delete($table, 'value_id in (' . join(',', $vIds) . ')');
            } catch (\Exception $e) {
                $this->_logger->debug($table);
                $this->_logger->debug('value_id in (' . join(',', $vIds) . ')');
                $this->_logger->debug($e->getTraceAsString());
                $this->_profile->getLogger()->error($e->getMessage());
            }
        }
    }

    protected function _importSaveWebsiteValues()
    {
        if ($this->_changeWebsite) {
            $table = $this->_t('catalog_product_website');
            $insert = [];
            $delete = [];
            foreach ($this->_changeWebsite as $sku => $actions) {
                if (empty($this->_skus[$sku])) {
                    continue; // product was not created
                }
                $pId = $this->_skus[$sku];
                if (isset($this->_skuSeq[$sku])) {
                    $pId = $this->_skuSeq[$sku];
                }
                foreach ($actions['I'] as $wId) {
                    $insert[] = "('$pId','$wId')";
                }
                foreach ($actions['D'] as $wId) {
                    $delete[] = "(`product_id`='$pId' and `website_id`='$wId')";
                }
                $this->_rtIdxRegisterWebsiteChange($sku, $actions);
            }
            if ($insert) {
                $this->_write->query("INSERT IGNORE INTO `{$table}` (`product_id`, `website_id`) VALUES " . implode(',',
                        $insert));
            }
            if ($delete) {
                $this->_write->query("DELETE FROM `{$table}` WHERE " . implode(" or ", $delete));
            }
        }
    }

    protected function _importSaveProductCategories()
    {
        if ($this->_changeCategoryProduct) {
            $table = $this->_t(self::TABLE_CATALOG_CATEGORY_PRODUCT);
            $insert = [];
            $delete = [];
            foreach ($this->_changeCategoryProduct as $sku => $actions) {
                if (empty($this->_skus[$sku])) {
                    continue; // product was not created
                }
                $pId = $this->_skus[$sku];
                if (isset($this->_skuSeq[$sku])) {
                    $pId = $this->_skuSeq[$sku];
                };
                foreach ($actions['I'] as $cId => $pos) {
                    $insert[] = "('$cId','$pId','$pos')";
                }
                foreach ($actions['D'] as $cId) {
                    $delete[] = "(`product_id`='$pId' and `category_id`='$cId')";
                }
                $this->_rtIdxRegisterCategoryChange($sku, $actions);
            }
            if ($insert) {
                $insertSql = "INSERT INTO `{$table}` (`category_id`, `product_id`, `position`) VALUES "
                    . join(',', $insert)
                    . 'ON DUPLICATE KEY UPDATE position=VALUES(position)';
                $this->_write->query($insertSql);
            }
            if ($delete) {
                $this->_write->query("DELETE FROM `{$table}` WHERE " . join(" or ", $delete));
            }
        }
    }

    protected function _importSaveStockValues()
    {
        if ($this->_changeStock) {
            $table = $this->_t('cataloginventory_stock_item');
            $siColumns = $this->_write->describeTable($table);
            foreach ($this->_changeStock as $sku => $_r) {
                if (empty($this->_skus[$sku])) {
                    continue; // product was not created
                }
                $r = [];
                foreach ($_r as $_rK => $_rV) {
                    if (array_key_exists($_rK, $siColumns)) {
                        $r[$_rK] = $_rV;
                    }
                }
                $pId = $this->_skus[$sku];
                $pIdIns = $pId;
                if (isset($this->_skuSeq[$sku])) {
                    $pIdIns = $this->_skuSeq[$sku];
                }
                if (empty($r) && (!isset($this->_products[$pId][0]['stock.is_in_stock']) || empty($_r['addqty']))) {
                    continue;
                }
                $this->_rtIdxRegisterStockChange($sku, $r);
                if (!isset($this->_products[$pId][0]['stock.is_in_stock'])) {
                    $r['stock_id'] = 1;
                    $r['product_id'] = $pIdIns;
                    $this->_write->insert($table, $r);
                } else {
                    if (empty($_r['qty']) && !empty($_r['addqty'])) {
                        $r['qty'] = $this->_products[$pId][0]['stock.qty'] + (float)$_r['addqty'];
                    }
                    $this->_write->update($table, $r, "stock_id=1 and product_id='$pIdIns'");
                }
            }
        }
    }

    protected function _afterImport()
    {

    }

    static protected $originalSqlMode = null;


    protected function _setSqlMode($mode = 'TRADITIONAL')
    {

        if (null === self::$originalSqlMode) {
            $sqlReadSQLMode = 'SELECT @@sql_mode';
            self::$originalSqlMode = $this->_read->fetchOne($sqlReadSQLMode);
        }
        return $this->_write->query('SET SESSION sql_mode=?', $mode);
    }


    protected function _restoreSqlMode()
    {
        if (null === self::$originalSqlMode) {
            return -1;
        }

        return $this->_write->query('SET SESSION sql_mode=?', self::$originalSqlMode);
    }

    protected function _importUpdateImageGallery()
    {
        if (!$this->_productIdsUpdated) {
            return;
        }
        $this->_setSqlMode('TRADITIONAL'); // had to set this in order to avoid mysql erring out because of only_full_group_by sql_mode

        $tMedia = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $tMediaValue = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);
        $tMediaRel = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_ENTITY);
        $tVarchar = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_VARCHAR);
        $tEavAttr = $this->_t(self::TABLE_EAV_ATTRIBUTE);

        $entityTypeId = $this->_entityTypeId;
        $storeId = $this->_storeId;
        $sqlReadMain = $this->_write->select()
            ->from(['v' => $tVarchar], [$this->_entityIdField, 'value'])
            ->join(['va' => $tEavAttr], "va.frontend_input='media_image' and va.attribute_id=v.attribute_id", null)
            ->join(['ga' => $tEavAttr], "ga.attribute_code='media_gallery'", ['attribute_id'])
            ->joinLeft(['gve' => $tMediaRel], "v.{$this->_entityIdField}=gve.{$this->_entityIdField}", [])
            ->joinLeft(['g' => $tMedia], 'v.value = g.value', ['current_value' => 'value', 'current_value_id' => 'value_id'])
            ->where("v.value<>'no_selection' AND v.value<>'' AND (g.value IS NULL OR gve.`value_id` IS NULL) AND va.entity_type_id=?", $entityTypeId)
            ->where('ga.entity_type_id=?', $entityTypeId)
            ->where("v.{$this->_entityIdField} IN (?)", array_keys($this->_productIdsUpdated))
            ->group(['v.' . $this->_entityIdField,'v.value']);

        $rows = $this->_read->fetchAll($sqlReadMain); // fetch all product images that are not present in gallery table
        $mediaRel = [];
        $mediaRelId = [];
        if ($rows) {
            $sqlWriteMedia = "INSERT INTO {$tMedia} (`attribute_id`, `value`) VALUES ";

            $insertMedia = [];
            foreach ($rows as $row) {
                if ($row['current_value'] === null) { // insert image only if not already inserted
                    $insertMedia[] = "('{$row['attribute_id']}','$row[value]')";
                }
                // regardless of if image is inserted for another product or not, there is no relation between image and product
                if (!empty($row['current_value_id'])) {
                    $mediaRelId[$row['current_value_id']][] = $row[$this->_entityIdField];
                } else {
                    $mediaRel[$row['value']][] = $row[$this->_entityIdField];
                }
            }
            if ($insertMedia) {
                $sqlWriteMedia .= implode(',', $insertMedia);
                $this->_write->query($sqlWriteMedia);
            }
        }

        if (!empty($mediaRel)) {
            $sqlReadMedia = $this->_write->select()->from(['g' => $tMedia])->where('g.value IN (?)',
                array_keys($mediaRel));
            $relRows = $this->_read->fetchAll($sqlReadMedia); // fetch all gallery images without entity to image entry
            if ($relRows) {
                foreach ($relRows as $relRow) {
                    $val = $relRow['value'];
                    if (!isset($mediaRel[$val])) {
                        continue; // no relations stored for this image
                    }
                    foreach ($mediaRel[$val] as $eId) {
                        $mediaRelId[$relRow['value_id']][] = $eId;
                    }
                }
            }
        }

        if (!empty($mediaRelId)) {
            $sqlWriteMediaRel = "INSERT INTO {$tMediaRel} (`value_id`, `{$this->_entityIdField}`) VALUES "; // gallery entity table
            $sqlWriteMediaVal = "INSERT INTO {$tMediaValue} (`value_id`, `store_id`, `{$this->_entityIdField}`) VALUES "; // gallery value table
            $insertMediaRel = [];
            $insertMediaVal = [];
            foreach ($mediaRelId as $vId => $entityIds) {
                foreach ($entityIds as $eId) {
                    $insertMediaRel[] = "({$vId},{$eId})";
                    $insertMediaVal[] = "({$vId},{$storeId},{$eId})";
                }
            }
            $sqlWriteMediaRel .= implode(',', $insertMediaRel);
            $this->_write->query($sqlWriteMediaRel);

            $sqlWriteMediaVal .= implode(',', $insertMediaVal);
            $this->_write->query($sqlWriteMediaVal);
        }

        if ($this->_deleteOldImage && !empty($this->_mediaChanges)) {

            $imagesToDelete = [];
            $delWhere = [];
            foreach ($this->_mediaChanges as $mc) {
                $imagesToDelete[] = $mc[1];
                $delWhere[] = implode(' AND ', [
                    $this->_write->quoteInto("`{$this->_entityIdField}`=?", $this->_skus[$mc[2]]),
                    $this->_write->quoteInto("`value_id` IN (SELECT `value_id` FROM {$tMedia} WHERE value=?)", $mc[1]),
                ]);
            }

            $delWhere = implode(' OR ', $delWhere);
//            $delWhere = "($delWhere) AND attribute_id={$this->_getGalleryAttrId()}";
            $this->_write->delete($tMediaRel, $delWhere);
            $this->_write->delete($tMediaValue, $delWhere);
            if (!$this->_deleteOldImageSkipUsageCheck) {
//                $delSql = "SELECT main.`value` FROM {$tMediaRel} AS entity
//JOIN {$tMedia} AS main ON entity.value_id = main.value_id WHERE main.`value` IN ({$this->_write->quote($imagesToDelete)})";
                $delSql = $this->_write->select()->from(['entity' => $tMediaRel])
                    ->join(['main' => $tMedia], 'entity.value_id = main.value_id')
                    ->where('main.value IN (?)', $imagesToDelete);
                $imgNoToDel = $this->_write->fetchCol($delSql);
                if (!empty($imgNoToDel)) {
                    $imagesToDelete = array_diff($imagesToDelete, $imgNoToDel);
                }
            }
            if (!empty($imagesToDelete)) {
                $directory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                foreach ($imagesToDelete as $_imageToDelete) {
                    $absoluteImgPath = $directory->getAbsolutePath($this->_productMediaConfig->getMediaPath($_imageToDelete));
                    @unlink($absoluteImgPath);
                }
            }
        }

        $this->_restoreSqlMode();
    }

    protected function _applyCatalogRules($pIds)
    {
        $rules = $this->_catalogRuleCollection->addIsActiveFilter();
        $crAppliedPids = [];

        foreach ($rules as $rule) {
            $rule->getResource()->updateRuleMultiProductData($rule, $pIds);
            if ($rule->getFromDate() <= HelperData::now(true)) {
                $matchPids = $rule->getMatchingMultiProductIds($pIds);
                foreach ($matchPids as $matchPid) {
                    $crAppliedPids[$matchPid] = true;
                }
            }
        }
        if (!empty($crAppliedPids)) {
            $this->_rapidFlowCatalogrule->applyAllByPids(array_keys($crAppliedPids));
        }
        return array_keys($crAppliedPids);
    }

    protected function _importRealtimeReindex()
    {
        if ($this->_profile->getData('options/import/reindex_type') === 'realtime') {
            $indexerRegistry = $this->_indexerRegistry;
            $indexerConfig = $this->_indexerConfig;
            $pAction = $this->_modelProductAction;


//            $pAction->setWebsiteIds([0]);
            $crAppliedPids = $this->_applyCatalogRules(array_values($this->_skus));
            if (!empty($crAppliedPids)) {
                foreach ($crAppliedPids as $craPid) {
                    $this->_realtimeIdx['catalog_product_price'][$craPid] = true;
                }
            }
            HelperData::om()->get(ProductIndexerPrice::class)->prepareWebsiteDateTable();
            if (!$this->_catalogStockConfiguration->isShowOutOfStock()) {
                foreach ([
                             'catalog_product_attribute',
                             'catalog_product_price',
                             'tag_summary',
                             'catalog_category_product'
                         ] as $idxKey
                ) {
                    $this->_realtimeIdx[$idxKey] = $this->_realtimeIdx[$idxKey] + $this->_realtimeIdx['cataloginventory_stock'];
                }
            }
            foreach ([
                         'cataloginventory_stock',
                         'catalog_product_attribute',
                         'catalog_product_price',
                         'tag_summary',
                         'catalog_category_product'
                     ] as $idxKey
            ) {
                if (empty($this->_realtimeIdx[$idxKey]) || !$indexerConfig->getIndexer($idxKey)) continue;
                $indexerRegistry->get($idxKey)->reindexList($this->_realtimeIdx[$idxKey]);
            }
            if (!empty($this->_realtimeIdx['catalogsearch_fulltext'])) {
                $exPids = [];
                if (!empty($this->_realtimeIdx['catalogsearch_fulltext']['full']['C'])) {
                    foreach ($this->_realtimeIdx['catalogsearch_fulltext']['full']['C'] as $wId => $_pIds) {
                        $pIds = array_keys($_pIds);
                        $exPids = array_unique(array_merge($exPids, $pIds));
                        foreach ($this->_storesByWebsite[$wId] as $sId => $sData) {
                            $this->_fullTextIndexer->rebuildStoreIndex($sId, $pIds);
                        }
                    }
                }
                if (!empty($this->_realtimeIdx['catalogsearch_fulltext']['website']['D'])) {
                    foreach ($this->_realtimeIdx['catalogsearch_fulltext']['website']['D'] as $wId => $_pIds) {
                        $pIds = array_keys($_pIds);
                        foreach ($this->_storesByWebsite[$wId] as $sId => $sData) {
//                            $this->_fullTextIndexer->cleanIndex($sId, $pIds);
                            // todo fix find way to drop indexes
                        }
                    }
                }
                if (!empty($this->_realtimeIdx['catalogsearch_fulltext']['website']['I'])) {
                    foreach ($this->_realtimeIdx['catalogsearch_fulltext']['website']['I'] as $wId => $_pIds) {
                        $pIds = array_keys($_pIds);
                        if ($pIds = array_diff($pIds, $exPids)) {
                            foreach ($this->_storesByWebsite[$wId] as $sId => $sData) {
                                $this->_fullTextIndexer->rebuildStoreIndex($sId, $pIds);
                            }
                        }
                    }
                }
            }
            /*
            if (!empty($this->_realtimeIdx['catalog_url'])) {
                $exPids = [];
                $urlModel = $this->_catalogUrlHelper;
                if (!empty($this->_realtimeIdx['catalog_url']['full']['C'])) {
                    foreach ($this->_realtimeIdx['catalog_url']['full']['C'] as $wId => $_pIds) {
                        $pIds = array_keys($_pIds);
                        $exPids = array_unique(array_merge($exPids, $pIds));
                        foreach ($this->_storesByWebsite[$wId] as $sId => $sData) {
                            foreach ($pIds as $pId) {
                                $urlModel->refreshProductRewrite($pId, $sId, $sData);
                            }
                        }
                    }
                }
                if (!empty($this->_realtimeIdx['catalog_url']['website']['I'])) {
                    foreach ($this->_realtimeIdx['catalog_url']['website']['I'] as $wId => $_pIds) {
                        $pIds = array_keys($_pIds);
                        if ($pIds = array_diff($pIds, $exPids)) {
                            foreach ($this->_storesByWebsite[$wId] as $sId => $sData) {
                                foreach ($pIds as $pId) {
                                    $urlModel->refreshProductRewrite($pId, $sId, $sData);
                                }
                            }
                        }
                    }
                }
                if (!empty($this->_realtimeIdx['catalog_url']['website']['D'])) {
                    foreach ($this->_realtimeIdx['catalog_url']['website']['D'] as $wId => $_pIds) {
                        $pIds = array_keys($_pIds);
                        foreach ($this->_storesByWebsite[$wId] as $sId => $sData) {
                            foreach ($pIds as $pId) {
                                $urlModel->refreshProductRewrite($pId, $sId, $sData);
                            }
                        }
                    }
                }
            }
            */
            if (!empty($this->_realtimeIdx['catalog_product_flat'])
                && $this->_scopeConfig->isSetFlag('catalog/frontend/flat_catalog_product')
            ) {
                $idxProdFlat = $indexerRegistry->get(FlatIndexer::INDEXER_ID);
                $exPids = [];
                if (!empty($this->_realtimeIdx['catalog_product_flat']['full']['C'])) {
                    foreach ($this->_realtimeIdx['catalog_product_flat']['full']['C'] as $wId => $_pIds) {
                        $pIds = array_keys($_pIds);
                        $exPids = array_unique(array_merge($exPids, $pIds));
                        $idxProdFlat->reindexList($pIds);
                    }
                }
                if (!empty($this->_realtimeIdx['catalog_product_flat']['status'])) {
                    foreach ($this->_realtimeIdx['catalog_product_flat']['status'] as $statusVal => $wData) {
                        if (!empty($wData['C'])) {
                            foreach ($wData['C'] as $wId => $_pIds) {
                                $pIds = array_keys($_pIds);
                                if ($pIds = array_diff($pIds, $exPids)) {
                                    $idxProdFlat->reindexList($pIds);
//                                    foreach ($this->_storesByWebsite[$wId] as $sId => $sData) {
//                                        $idxProdFlat->updateProductStatus($pIds, $statusVal, $sId);
//                                    }
                                }
                            }
                        }
                    }
                }
                if (!empty($this->_realtimeIdx['catalog_product_flat']['by_attr'])) {
                    foreach ($this->_realtimeIdx['catalog_product_flat']['by_attr'] as $attrCode => $wData) {
                        if (!empty($wData['C'])) {
                            foreach ($wData['C'] as $wId => $_pIds) {
                                $pIds = array_keys($_pIds);
                                if ($pIds = array_diff($pIds, $exPids)) {
                                    $idxProdFlat->reindexList($pIds);
//                                    foreach ($this->_storesByWebsite[$wId] as $sId => $sData) {
//                                        $idxProdFlat->updateAttribute($attrCode, $sId, $pIds);
//                                    }
                                }
                            }
                        }
                    }
                }
                if (!empty($this->_realtimeIdx['catalog_product_flat']['website']['I'])) {
                    foreach ($this->_realtimeIdx['catalog_product_flat']['website']['I'] as $wId => $_pIds) {
                        $pIds = array_keys($_pIds);
                        if ($pIds = array_diff($pIds, $exPids)) {
                            $idxProdFlat->reindexList($pIds);
//                            foreach ($this->_storesByWebsite[$wId] as $sId => $sData) {
//                                $idxProdFlat->updateProduct($pIds, $sId);
//                            }
                        }
                    }
                }
                if (!empty($this->_realtimeIdx['catalog_product_flat']['website']['D'])) {
                    foreach ($this->_realtimeIdx['catalog_product_flat']['website']['D'] as $wId => $_pIds) {
                        $pIds = array_keys($_pIds);
                        $idxProdFlat->reindexList($pIds);
//                        foreach ($this->_storesByWebsite[$wId] as $sId => $sData) {
//                            $idxProdFlat->removeProduct($pIds, $sId);
//                        }
                    }
                }
            }
        }
    }

    protected function _hasColumnsLike($prefix)
    {
        if (empty($this->_fieldsCodes)) {
            return true;
        }
        foreach ($this->_fieldsCodes as $k => $v) {
            if (strpos($k, $prefix) === 0) {
                return true;
            }
        }
        return false;
    }

    protected $_urlKeys = [];

    protected function _createUrlKey($urlKey, $sku, $entityTable)
    {
        if (empty($urlKey)) {
            return $urlKey;
        }
        $hlp = $this->_rapidFlowHelper;
        $urlKey = $hlp->formatUrlKey($urlKey);
//        if (!$hlp->hasMageFeature('no_url_path')) {
//            return $urlKey;
//        }
        if (!$this->_profile->getData('options/import/increment_url_key')) {
            return $urlKey;
        }

        if (!isset($this->_urlKeys[$entityTable])) {
            $this->_loadUrlKeys($entityTable);
        }
        $pId = isset($this->_skus[$sku]) ? $this->_skus[$sku] : null;

        // if product exists and has url key for ee 1.13 and it is the same as new url_key just return it
        if ($pId && isset($this->_urlKeys[$entityTable]['id_key'][$pId]) && $this->_urlKeys[$entityTable]['id_key'][$pId] === $urlKey) {
            return $this->_urlKeys[$entityTable]['id_key'][$pId];
        }

        $exists = isset($this->_urlKeys[$entityTable]['key_id'][$urlKey]);
        if ($exists) {
            $keyLimit = $this->_profile->getData('options/import/increment_url_key_limit');
            if (!$keyLimit) {
                $keyLimit = 100;
            }
            $idx = 1;
            while ($exists) {
                $tmpKey = $urlKey . '-' . $idx++;
                $exists = isset($this->_urlKeys[$entityTable]['key_id'][$tmpKey]);
                if ($idx === $keyLimit && $exists) {
                    $this->_profile->getLogger()->warning(sprintf('Failed to increment url_key in 100 attempts for SKU: %s',
                        $sku));
                    $tmpKey = null;
                    break;
                }
            }
            if (!empty($tmpKey)) {
                $urlKey = $tmpKey;
            }
        }
        if ($urlKey) {
            $idx = $pId ? $pId : $sku;
            $this->_urlKeys[$entityTable]['id_key'][$idx] = $urlKey;
            $this->_urlKeys[$entityTable]['key_id'][$urlKey] = $idx;
        }
        return $urlKey;
    }

    protected function _loadUrlKeys($entityTable)
    {
        $attrId = $this->_attr('url_key', 'attribute_id');
        $select = $this->_read->select()->from($entityTable, [$this->_entityIdField, 'value'])->where('attribute_id=?', $attrId);
        $rows = $this->_read->fetchAll($select);
        $temp = [];
        foreach ($rows as $r) {
            $temp[$r[$this->_entityIdField]] = $r['value'];
        }
        $temp = array_filter($temp);
        $this->_urlKeys[$entityTable]['id_key'] = array_unique($temp);
        $this->_urlKeys[$entityTable]['key_id'] = array_flip($temp);
    }


    protected function _getPathSuffix($storeId)
    {
        $suffix = $this->_scopeConfig->getValue('catalog/seo/category_url_suffix',
            ScopeInterface::SCOPE_STORE,
            $storeId);

        if ($suffix && strpos($suffix, '.') !== 0) {
            $suffix = '.' . $suffix;

            return $suffix;
        }

        return $suffix;
    }

    protected function _updateCategories($cId)
    {
        if(!isset($this->_categories[$cId])){
            $cat = $this->_write->fetchRow(
                $this->_write->select()->from($this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY))
                    ->where($this->_entityIdField . '=?', $cId),
                null,
                Zend_Db::FETCH_ASSOC
            );

            if($cat){
                $this->_categories[$cId] = $cat;
                $this->_categoriesBySeqId[$cat['entity_id']] = $cat;
                $this->_catRow2Entity[$cId] = $cat['entity_id'];
                $this->_catEntity2Row[$cat['entity_id']] = $cId;
            }
        }
    }
}
