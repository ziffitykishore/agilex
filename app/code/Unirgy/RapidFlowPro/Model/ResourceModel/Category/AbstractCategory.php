<?php

namespace Unirgy\RapidFlowPro\Model\ResourceModel\Category;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Staging\Model\VersionManager;
use Magento\Store\Model\ScopeInterface;
use Unirgy\RapidFlow\Exception;
use Unirgy\RapidFlow\Helper\Data as HelperData;
use Unirgy\RapidFlow\Model\ResourceModel\Catalog\AbstractCatalog;
use Unirgy\RapidFlow\Model\ResourceModel\Catalog\Product\AbstractProduct;

abstract class AbstractCategory
    extends AbstractCatalog
{

    protected $_logger;


    protected $_scopeConfig;


    protected $_translateModule = 'Unirgy_RapidFlowPro';


    protected $_entityType = 'catalog_category';


    protected $_entityTypeId;


    protected $_attributeSetId;


    protected $_pathSuffix;


    protected $_rootCatId;


    protected $_rootCatIds;


    protected $_upPrependRoot;


    protected $_categories = [];


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


    protected $_entities = [];


    protected $_entityIds = [];


    protected $_entityIdsUpdated = [];


    protected $_defaultUsed = [];


    protected $_valid = [];


    protected $_pathLine = [];


    protected $_attrValueIds = [];


    protected $_attrValuesFetched = [];


    protected $_websiteScope = [];


    protected $_websiteScopeProducts = [];


    protected $_websiteScopeAttributes = [];


    protected $_insertEntity = [];


    protected $_updateEntity = [];


    protected $_changeAttr = [];


    protected $_insertAttr = [];


    protected $_updateAttr = []; // type/#=>row


    protected $_deleteAttr = []; // type/vId=>value


    protected $_childrenCount = []; // type/#=>vId


    protected $_changeChildrenCount = [];


    protected $_urlPaths = [];


    protected $_parentPath = [];


    protected $_parentPathExtra = []; //


    protected $_newDataTemplate = []; //


    protected $_newData = [];


    protected $_pathIdx = null;


    protected $_startLine;


    protected $_isLastPage = false;

    protected $_catEntity2Row = [];
    protected $_catRow2Entity = [];


    protected $_fieldAttributes = [
        'product.attribute_set' => 'attribute_set_id',
        'product.type' => 'type_id',
        'product.store' => 'store_id',
        'category.entity_id' => 'entity_id'
    ];


    protected $_rootCatPaths;


    protected $_modelProductImage;

    protected $_urlsToUpdate = [];
    protected $_categoriesBySeqId;

    protected function _importPrepareColumns()
    {
        AbstractProduct::validateLicense('Unirgy_RapidFlowPro');

        $columns = (array) $this->_profile->getColumns();
        $attrs = [];
        $dups = [];
        $alias = [];
        $this->_fields = [];
        $this->_newDataTemplate = [];
        $this->_fieldsCodes = [
            'url_key' => 0,
        ];
        foreach ($columns as $i => &$f) {
            if (!empty($f['alias'])/* && strtolower(trim($f['alias']))!=$f['field']*/) {
                $aliasKey = strtolower(trim($f['alias']));
                if (!isset($alias[$aliasKey])) {
                    $alias[$aliasKey] = $f['field'];
                } elseif (!is_array($alias[$aliasKey]) && $alias[$aliasKey] != $f['field']) {
                    $alias[$aliasKey] = array($alias[$aliasKey], $f['field']);
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
                    $f['default'] = explode(',', $f['default']);
                }
                $this->_newDataTemplate[$f['field']] = $f['default'];
            }
        }
        unset($f);
        if ($dups) {
            throw new LocalizedException(__('Duplicate attributes: %1', implode(', ', $dups)));
        }

        $headers = $this->_profile->ioRead();
        if (!$headers) {
            //no data
            $this->_profile->ioClose();
            return;
        }
        $this->_fieldsIdx = [];
        foreach ($headers as $i => $f) {
            if ($f === '') {
                $this->_fieldsIdx[$i] = false;
                $this->_profile->addValue('num_warnings');
                $this->_profile->getLogger()->setLine(2)->setColumn($i + 1)
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

        if (!isset($this->_fieldsCodes['url_path'])) {
            //no url_path field
            $this->_profile->ioClose();
            throw new LocalizedException(__('Missing url_path column'));
        }
        $this->_pathIdx = $this->_fieldsCodes['url_path'];
    }


    protected function _prepareAttributes(array $columns = [])
    {
        AbstractProduct::validateLicense('Unirgy_RapidFlowPro');

        // reset all attributes
        $this->_attributesById = [];
        $this->_attributesByCode = [];
        $this->_attributesByType = [];
        $storeId = $this->_profile->getStoreId();

        $removeFields = [
            'children',
            'children_count',
            'all_children',
            'path_in_store',
            'path',
            'parent_id',
            'level'
        ];
        if ($this->_profile->getProfileType() === 'import') {
            $removeFields = array_merge($removeFields, ['created_at', 'updated_at']);
        }
        // collect data about all attributes used in profile
        $select = $this->_read->select()->from(['a' => $this->_t(self::TABLE_EAV_ATTRIBUTE)])
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
                if (is_array($f) && !empty($this->_attrDepends[$f['field']])) {
                    foreach ((array)$this->_attrDepends[$f['field']] as $v) {
                        $attrCodes[$v] = $v;
                    }
                }
            }
            $attrCodes[] = 'url_key';
            array_unique($attrCodes);

            $select->where("is_required=1 or default_value<>'' or attribute_code in (?)", $attrCodes);
            if (($catalogAttrTable = $this->_t(self::TABLE_CATALOG_EAV_ATTRIBUTE))) {
                $select->join(array('c' => $catalogAttrTable), 'c.attribute_id=a.attribute_id');
            }
        }
        $rows = $this->_read->fetchAll($select);
        foreach ($rows as $r) {
//            $a = [];
//            if (!empty($r['apply_to'])) {
//                foreach (explode(',', $r['apply_to']) as $t) {
//                    $a[$t] = true;
//                }
//            }
//            $r['apply_to'] = $a;

            if ($r['default_value'] !== '' && !isset($this->_newDataTemplate[$r['attribute_code']])) {
                $this->_newDataTemplate[$r['attribute_code']] = $r['default_value'];
            }

            // if special source_model (options) fetch them from model class
            if (!empty($r['source_model']) && $r['source_model'] !== 'Magento\Eav\Model\Entity\Attribute\Source\Table') {
                $model = $this->_rapidFlowHelper->om()->get($r['source_model']);
                if ($model && is_callable([$model, 'getAllOptions']) && ($options = $model->getAllOptions())) {
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
            // save with different fetch methods
            $this->_attributesById[$r['attribute_id']] = $r;
            $this->_attributesByCode[$r['attribute_code']] =& $this->_attributesById[$r['attribute_id']];
            $aType = $this->getAttrType($r, 'catalog/category');
            $this->_attributesByType[$aType][$r['attribute_id']] =& $this->_attributesById[$r['attribute_id']];
        }
        // retrieve all options for regular eav source
//        $sql = $this->_read->quoteInto(
//            "SELECT o.attribute_id, o.option_id, v.value
//FROM {$this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE)} v
//INNER JOIN {$this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION)} o USING (option_id)
//WHERE v.store_id IN (0, $storeId) AND o.attribute_id IN (?)
//ORDER BY v.store_id DESC",
//            array_keys($this->_attributesById)
//        );
        $sql = $this->_read->select()
            ->from(['v' => $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE)], 'value')
            ->join(['o' => $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION)], 'v.option_id=o.option_id', ['attribute_id', 'option_id'])
            ->where('v.store_id IN (0, ?)', $storeId)->where('o.attribute_id IN (?)', array_keys($this->_attributesById))
            ->order('v.store_id DESC');
//        $this->_profile->getLogger()->notice($sql);
        $rows = $this->_read->fetchAll($sql);
        if ($rows) {
            foreach ($rows as $r) {
                if (empty($this->_attributesById[$r['attribute_id']]['options'][$r['option_id']])) {
                    $this->_attributesById[$r['attribute_id']]['options'][$r['option_id']] = $r['value'];
                    $this->_attributesById[$r['attribute_id']]['options_bytext'][strtolower(trim($r['value']))] = $r['option_id'];
                }
            }
        }
    }


    protected function _prepareSystemAttributes()
    {
        $sysAttr = ['custom_design_apply', 'available_sort_by', 'landing_page', 'custom_design', 'default_sort_by', 'page_layout'];
        foreach ($sysAttr as $k) {
            if (!empty($this->_attributesByCode[$k]['is_required'])) {
                $this->_attributesByCode[$k]['is_required'] = false;
            }
        }
        $this->_attributesByCode['custom_design_apply']['options_bytext']['all'] = 1;

        if (!isset($this->_profile) || $this->_profile->getProfileType() === 'export') {
            $this->_attributesByCode['category.entity_id'] = [
                'frontend_label' => 'Entity ID',
                'frontend_input' => 'text',
                'backend_type' => 'static',
                'force_field' => $this->_entityIdField
            ];
        }
    }


    protected function _importValidateColumns()
    {
        foreach ($this->_fieldsIdx as $i => $f) {
            if ($f === false) {
                continue;
            }
            if (!isset($this->_attributesByCode[$f])) {
                $this->_profile->addValue('num_warnings');
                $this->_profile->getLogger()->setLine(2)->setColumn($i + 1)
                    ->warning(__('Unknown field, the column will be ignored: %1', htmlentities($f)));
            }
        }
    }


    protected function _prepareCategories()
    {
        AbstractProduct::validateLicense('Unirgy_RapidFlowPro');

        $storeId = $this->_profile->getStoreId();
        $suffix = $this->_getPathSuffix($storeId);
        $suffixLen = strlen($suffix);

        $this->_upPrependRoot = $this->_profile->getData('options/import/urlpath_prepend_root');

        $categoryTable = $this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY);

        // load categories for the store specified in profile
        $eav = $this->_eavModelConfig;
        $rootCatId = $this->_getRootCatId();
        $rootPath = $rootCatId ? '1/' . $rootCatId : '1';

        $entityId = $this->_entityIdField;
        if ($this->_upPrependRoot) {
            $nameAttrId = $this->_attr('name', 'attribute_id');
            $rootCatPathsSel = $this->_read->select()
                ->from(['w' => $this->_t(self::TABLE_STORE_WEBSITE)], [])
                ->join(['g' => $this->_t(self::TABLE_STORE_GROUP)], 'g.group_id=w.default_group_id', [])
                ->join(['e' => $categoryTable], "e.entity_id=g.root_category_id", ["concat('1/',e.entity_id)"])
                ->join(['name' => $categoryTable . '_varchar'],
                       "name.{$entityId}=e.{$entityId} and name.attribute_id={$nameAttrId} and name.value<>'' and name.value is not null and name.store_id=0",
                       ['value'])
                ->group("e.entity_id");
            if ($storeId) {
                $rootCatPathsSel->where("e.entity_id=?", $rootCatId);
            }
            $this->_rootCatPaths = $this->_read->fetchPairs($rootCatPathsSel);
        }

        // find all categories for this store
        $_rcPaths = [];
        if ($this->_upPrependRoot && !empty($this->_rootCatPaths)) {
            foreach ($this->_rootCatPaths as $_rcPath => $_rcName) {
                $_rcPaths[] = $this->_read->quoteInto('path=?', $_rcPath);
                $_rcPaths[] = $this->_read->quoteInto('path like ?', $_rcPath . '/%');
            }
        } else {
            $_rcPaths[] = $this->_read->quoteInto('path=?', $rootPath);
            $_rcPaths[] = $this->_read->quoteInto('path like ?', $rootPath . '/%');
        }
//        $sql = "SELECT {$entityId}, path, children_count FROM {$categoryTable}
//                WHERE {$entityId}=1 OR " . implode(' OR ', $_rcPaths);
        $cols = [$entityId, 'path', 'children_count'];
        if ($this->_rapidFlowHelper->hasMageFeature(static::ROW_ID)) {
            $cols[] = 'entity_id';
        }
        $sql = $this->_write->select()->from($categoryTable, $cols)
            ->where($entityId . '=1 OR ' . implode(' OR ', $_rcPaths));

        if ($this->currentVersion && $this->currentVersion->getId()) {
            $sql->setPart('disable_staging_preview', true);
            $sql->where($categoryTable . '.created_in <= ?', $this->currentVersion->getId());
            $sql->where($categoryTable . '.updated_in > ?', $this->currentVersion->getId());
        }
        $rows = $this->_read->fetchAll($sql);

        if ($rows) {
            $this->_categories = [];
            $row2Entity = [];
            $entity2Row = [];
            // create associated array
            foreach ($rows as $r) {
                $this->_categories[$r[$entityId]] = $r;
                $this->_categoriesBySeqId[$r['entity_id']] = $r;
                $row2Entity[$r[$entityId]] = $r['entity_id'];
                $entity2Row[$r['entity_id']] = $r[$entityId];
            }
            $this->_catEntity2Row = $entity2Row;
            $this->_catRow2Entity = $row2Entity;

            // fetch names and url_paths for loaded categories
            // start select
            $rows = $this->getUrlPaths($storeId, $categoryTable);

            foreach ($rows as $r) {
                // load names for specific store OR default
                if (empty($this->_categories[$r[$entityId]][$r['attribute_code']])) {
                    $this->_categories[$r[$entityId]][$r['attribute_code']] = $r['value'];
                    $this->_categoriesBySeqId[$r['entity_id']][$r['attribute_code']] = $r['value'];
                }
                if (empty($this->_categories[$r[$entityId]]['url_key']) && isset($r['url_key'])) {
                    $this->_categories[$r[$entityId]]['url_key'] = $r['url_key'];
                    $this->_categoriesBySeqId[$r['entity_id']]['url_key'] = $r['url_key'];
                }
            }

            // generate breadcrumbs for loaded categories

            foreach ($this->_categories as $id => &$c) {
                $urlPath = !empty($c['url_path']) ? $c['url_path'] : $this->catBuildPath($c);
                $c['url_path'] = $this->_upPrependRoot($c, $urlPath);
                if (empty($c['url_path'])) {
                    $this->_childrenCount[$id] = $c['children_count'];
                    continue;
                }
                $this->_urlPaths[$c['url_path']] = $id;
                $this->_childrenCount[$c['url_path']] = $c['children_count'];
                if ($suffix) {
                    $additionalKey = substr($c['url_path'], -$suffixLen) === $suffix
                        ? substr($c['url_path'], 0, strlen($c['url_path']) - $suffixLen)
                        : $c['url_path'] . $suffix;
                    $this->_urlPaths[$additionalKey] = $id;
                    $this->_childrenCount[$additionalKey] = $c['children_count'];
                }
            }
            unset($c);
        }
    }


    protected function _getPathSuffix($storeId)
    {
        $suffix = $this->_scopeConfig->getValue('catalog/seo/category_url_suffix', ScopeInterface::SCOPE_STORE, $storeId);
        if (strpos($suffix, '.') !== 0) {
            $suffix = '.' . $suffix;
            return $suffix;
        }

        return $suffix;
    }


    protected function getUrlPaths($storeId, $table)
    {
        $rows = [];
        $entityId = $this->_entityIdField;

        foreach (['url_path', 'url_key'] as $field) {
            $select = $this->_read->select();

            $cols = [$entityId];
            if ($field === 'url_path') {
                $cols[] = 'value';
            } else {
                $cols[$field] = 'value';
            }
            $select->from(['v' => "{$table}_varchar"], $cols)
                ->joinLeft(['a' => $this->_t(self::TABLE_EAV_ATTRIBUTE)], 'a.attribute_id=v.attribute_id', 'attribute_code')
                ->where('v.store_id in (?)', [0, $storeId])
                ->where("v.{$entityId} in (?)", array_keys($this->_categories))
                ->where('a.entity_type_id=?', $this->_entityTypeId)
                ->where('a.attribute_code=?', $field)
                ->order('v.store_id asc');

            $entCols = ['path'];
            if ($this->_rapidFlowHelper->hasMageFeature(self::ROW_ID)) {
                $entCols[] = 'entity_id';
            }
            $select->join(['e' => $table], 'v.row_id=e.row_id', $entCols);

            foreach ($this->_read->fetchAll($select) as $r) {
                if (isset($rows[$r[$entityId]])) {
                    $rows[$r[$entityId]] = array_merge($rows[$r[$entityId]], $r);
                } else {
                    $rows[$r[$entityId]] = $r;
                }
                $rows[$r[$entityId]]['attribute_code'] = 'url_path';
                if (!isset($rows[$r[$entityId]]['value'])) {
                    $rows[$r[$entityId]]['value'] = null;
                }
                if ($this->_upPrependRoot && !empty($this->_rootCatPaths)) {
                    foreach ($this->_rootCatPaths as $_rcPath => $_rcName) {
                        if ($r['path'] == $_rcPath) {
                            $rows[$r[$entityId]]['value'] = $_rcName;
                        }
                    }
                }
            }
        }
        return $rows;
    }


    protected function _upPrependRoot($row, $value)
    {
        if ($this->_upPrependRoot) {
            $_rootCat = explode('/', $row['path'], 3);
            $_rootCatCnt = count($_rootCat);
            unset($_rootCat[2]);
            $_rootCat = implode('/', $_rootCat);
            if (isset($this->_rootCatPaths[$_rootCat])) {
                if (empty($value) || $_rootCatCnt<3) {
                    $value = $this->_rootCatPaths[$_rootCat];
                } else {
                    $value = $this->_rootCatPaths[$_rootCat] . '/' . $value;
                }
            }
        }
        return $value;
    }


    protected function _importFetchNewData()
    {
        $defaultSeparator = $this->_profile->getData('options/csv/multivalue_separator');
        if (!$defaultSeparator) {
            $defaultSeparator = ';';
        }

        $this->_newData = [];

        // $i1 should be preserved during the loop
        for ($i1 = 0; $i1 < $this->_pageRowCount; $i1++) {
            $error = false;
            $row = $this->_profile->ioRead();
            if (!$row) {
                // last row
                $this->_isLastPage = true;
                return true;
            }
            $empty = true;
            foreach ($row as $v) {
                if (trim($v) !== '') {
                    $empty = false;
                    break;
                }
            }
            if ($empty) {
                $this->_profile->addValue('rows_empty');
                continue;
            }
            $this->_profile->addValue('rows_processed');
            $this->_profile->getLogger()->setLine($this->_startLine + $i1);
            if (empty($row[$this->_pathIdx]) || !empty($this->_newData[$row[$this->_pathIdx]])) {
                $this->_profile->addValue('rows_errors')->addValue('num_errors');
                $this->_profile->getLogger()->setColumn($this->_pathIdx + 1)
                    ->error(empty($row[$this->_pathIdx]) ? __('Empty URL Path') : __('Duplicate URL Path'));
                continue;
            }
            $urlPath = trim($row[$this->_pathIdx]);
            $this->_pathLine[$urlPath] = $this->_startLine + $i1;
            $this->_newData[$urlPath] = $this->_newDataTemplate;
            $this->_defaultUsed[$urlPath] = $this->_newDataTemplate;

            $error = false;
            foreach ($row as $col => $v) {
                if ($v !== '' && !isset($this->_fieldsIdx[$col])) {
                    $this->_profile->addValue('num_warnings');
                    $this->_profile->getLogger()->setColumn($col + 1)
                        ->warning(__('Column is out of boundaries, ignored'));
                    continue;
                }
                $k = $this->_fieldsIdx[$col];
                if ($k === false || $k === 'const.value') {
                    continue;
                }
                /*
                if ($k=='url_path' && $this->_upPrependRoot) {
                    $_up = explode('/', $v, 2);
                    array_shift($_up);reset($_up);
                    $v = current($_up);
                }
                */
                $input = $this->_attr($k, 'frontend_input');
                $multiselect = $input === 'multiselect';
                $separator = trim(!empty($this->_fields[$k]['separator']) ? $this->_fields[$k]['separator'] : $defaultSeparator);
                try {
                    $v = $this->_convertEncoding($v);
                } catch (Exception $e) {
                    $this->_profile->addValue('num_warnings');
                    $this->_profile->getLogger()->setColumn($col + 1)->warning($e);
                    #error = true;
                }
                if ($v !== '') {
                    // options and multiselect
                    if ($input === 'select') {
                        $v = trim($v);
                    } elseif ($multiselect) {
                        $values = explode($separator, $v);
                        $v = [];
                        foreach ($values as $v1) {
                            $v[] = $v1;
                        }
                    }
                }
                if (!isset($this->_defaultUsed[$urlPath][$k]) || ($v !== '' && $v !== [])) {
                    $this->_newData[$urlPath][$k] = $v;
                    unset($this->_defaultUsed[$urlPath][$k]);
                }
            }
            if ($error) {
                unset($this->_newData[$urlPath]);
            }
        }
        return false;
    }


    protected function _importFetchOldData()
    {
        $attributeFields = array_flip($this->_fieldAttributes);
        $table = $this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY);
        $upAttrId = $this->_attr('url_key', 'attribute_id');
        $entityId = $this->_entityIdField;
        $storeId = $this->_profile->getStoreId();
//        $noUrlPath = $this->_rapidFlowHelper->hasMageFeature('no_url_path');

        $suffix = $this->_getPathSuffix($storeId);
        $suffixLen = strlen($suffix);

        $keys = array_keys($this->_newData);
        if ($suffix) {
            foreach (array_keys($this->_newData) as $key) {
                $keys[] = substr($key, -$suffixLen) === $suffix ?
                    substr($key, 0, strlen($key) - $suffixLen) :
                    $key . $suffix;
            }
        }
        $entityIds = [];
        foreach ($keys as $key) {
            if (isset($this->_urlPaths[$key])) {
                $entityIds[] = $this->_urlPaths[$key];
            }
        }

        $select = $this->_write->select()->from(['e' => $table]);

        // retrieve product rows from database using url_paths from file
        $select->joinLeft(['up' => $table . '_varchar'],
                          "up.{$entityId}=e.{$entityId} and up.attribute_id={$upAttrId}",
                          ['value_id', 'url_key' => 'value', 'store_id'])
            //->where('up.value in (?)', $keys);
            ->where("e.{$entityId} in (?)", $entityIds)
            // when fetching categories data in beginning we filter it by store id
            // we need to do the same here
            ->where('up.store_id in (0, ?)', $storeId);
//        }

        $this->_attrJoined = array($upAttrId);
        $categoryRows = $this->_write->fetchAll($select);
//        $this->_profile->getLogger()->notice('Category old data SELECT: ' . (string)$select);
//        $this->_profile->getLogger()->notice('Category old data rows: ' . print_r($categoryRows, 1));
        unset($select);
        //$this->_urlPaths = [];
        $this->_entities = [];
        foreach ($categoryRows as $r) {
            $sId = $r['store_id'];
            $r1 = [];
            foreach ($r as $k => $v) {
                if ($k === 'value_id') {
                    continue;
                }
                if (!empty($attributeFields[$k])) {
                    $r1[$attributeFields[$k]] = $v;
                } else {
                    $r1[$k] = $v;
                }
            }
//            $r1['url_path'] = $this->_upPrependRoot($r1, $r1['url_path']);
            $this->_attrValueIds[$r[$entityId]][$sId]['url_key'] = $r['value_id'];
            $this->_entities[$r[$entityId]][$sId] = $r1;
        }
        $this->_entityIds = array_keys($this->_entities);
    }

    protected function _importResetPageData()
    {
        $this->_isLastPage = false;
        $this->_entities = [];
        $this->_newData = [];
        $this->_defaultUsed = [];
        $this->_valid = [];
        $this->_pathLine = [];
        $this->_entityIdsUpdated = [];
        $this->_attrValueIds = [];
        $this->_attrValuesFetched = [];
        $this->_insertEntity = [];
        $this->_updateEntity = [];
        $this->_changeAttr = [];
        $this->_insertAttr = []; // type/#=>row
        $this->_updateAttr = []; // type/vId=>value
        $this->_deleteAttr = []; // type/#=>vId
        $this->_changeChildrenCount = [];
    }

    protected function _importProcessNewData()
    {
        foreach ($this->_newData as $urlPath => &$p) {
            // generate url_key if not provided
            if (empty($this->_urlPaths[$urlPath])) {
                if (!empty($p['url_key'])) {
                    $urlKey = $p['url_key'];
                } else {
                    $urlKey = basename($urlPath, $this->_getPathSuffix($this->_storeId));
                }
                $p['url_key'] = $this->_rapidFlowHelper->formatUrlKey($urlKey);
            }

            // do not overwrite attributes with default values for existing categories
            if (!empty($this->_urlPaths[$urlPath])) {
                foreach ($this->_defaultUsed[$urlPath] as $k => $v) {
                    unset($p[$k]);
                }
            }
        }
        unset($p);
    }


    protected function _importValidateNewData()
    {
        $logger = $this->_profile->getLogger();
        $autoCreateOptions = $this->_profile->getData('options/import/create_options');
        $actions = $this->_profile->getData('options/import/actions');
        $allowSelectIds = $this->_profile->getData('options/import/select_ids');

        // find changed data
        foreach ($this->_newData as $urlPath => $p) {
            $logger->setLine($this->_pathLine[$urlPath]);
            // check if the product is new
            $isNew = empty($this->_urlPaths[$urlPath]);

            if (($isNew && $actions === 'update') || (!$isNew && $actions === 'create')) {
                $this->_profile->addValue('rows_nochange');
                $this->_valid[$urlPath] = false;
                continue;
            }

            // validate required attributes
            $this->_valid[$urlPath] = true;

            // check missing required columns
            foreach ($this->_attributesByCode as $k => $attr) {
                if (isset($p[$k]) || empty($attr['is_required']) || !$isNew) {
                    continue;
                }
                $this->_profile->addValue('num_errors');
                $logger->setColumn(1);
                $logger->error(__("Missing required value for '%1'", $k));
                $this->_valid[$urlPath] = false;
            }

            if ($isNew) {
                if (strpos($urlPath, '/') === false) {
                    $this->_parentPath[$urlPath] = '';
                    $this->_urlPaths[$urlPath] = true;
                } else {
                    $parentPath = preg_replace('#/[^/]+$#', '', $urlPath);
                    $parentPathOrig = $parentPath;
                    if (empty($this->_urlPaths[$parentPath])) {
                        $parentPath = $this->_addPathSuffix($parentPath);
                    }
                    if (empty($this->_urlPaths[$parentPath])) {
                        $this->_profile->addValue('num_errors');
                        $logger->setColumn($this->_pathIdx + 1);
                        $logger->error(__("Invalid parent path '%1'", $parentPathOrig));
                        $this->_valid[$urlPath] = false;
                    } else {
                        $this->_parentPath[$urlPath] = $parentPath;
                        $this->_urlPaths[$urlPath] = true;
                    }
                    while (preg_match('#/[^/]+$#', $parentPath)
                        && ($parentPath = preg_replace('#/[^/]+$#', '', $parentPath))
                    ) {
                        $parentPathOrig = $parentPath;
                        if (empty($this->_urlPaths[$parentPath])) {
                            $parentPath = $this->_addPathSuffix($parentPath);
                        }
                        if (!empty($this->_urlPaths[$parentPath])) {
                            if (empty($this->_parentPathExtra[$urlPath])) {
                                $this->_parentPathExtra[$urlPath] = [];
                            }
                            $this->_parentPathExtra[$urlPath][] = $parentPath;
                        }
                    }
                }
            }

            // walk the attributes
            foreach ($p as $k => $newValue) {
                $attr = $this->_attr($k);
                $logger->setColumn(isset($this->_fieldsCodes[$k]) ? $this->_fieldsCodes[$k] + 1 : -1);

                $empty = is_null($newValue) || $newValue === '' || $newValue === [];
                $required = !empty($attr['is_required']);
                $selectable = !empty($attr['frontend_input']) && ($attr['frontend_input'] === 'select' || $attr['frontend_input'] === 'multiselect');

                if ($empty && $required) {
                    $this->_profile->addValue('num_errors');
                    $logger->error(__("Missing required value for '%1'", $k));
                    $this->_valid[$urlPath] = false;
                    continue;
                }

                if ($selectable && !$empty) {
                    foreach ((array)$newValue as $i => $v) {
                        $vLower = strtolower(trim($v));
                        if (isset($this->_defaultUsed[$urlPath][$k])) {
                            // default used, no mapping required
                        } elseif (isset($attr['options_bytext'][$vLower])) {
                            if (is_array($newValue)) {
                                $this->_newData[$urlPath][$k][$i] = $attr['options_bytext'][$vLower];
                            } else {
                                $this->_newData[$urlPath][$k] = $attr['options_bytext'][$vLower];
                            }
                        } elseif ($allowSelectIds && isset($attr['options'][$v])) {
                            // select id used, no mapping required
                        } else {
                            if ($autoCreateOptions && !empty($attr['attribute_id']) && (empty($attr['source_model'])
                                    || $attr['source_model'] === 'Magento\Eav\Model\Entity\Attribute\Source\Table')) {
                                $this->_importCreateAttributeOption($attr, $v);

                                $this->_profile->addValue('num_warnings');
                                $logger->warning(__("Created a new option '%1' for attribute '%2'", $v, $k));
                            } else {
#var_dump($attr); exit;
                                $this->_profile->addValue('num_errors');
                                $logger->error(__("Invalid option '%1'", $v));
                                $this->_valid[$urlPath] = false;
                            }
                        }
                    } // foreach ((array)$newValue as $v)
                }
            } // foreach ($p as $k=>$newValue)

            if (!$this->_valid[$urlPath]) {
                $this->_profile->addValue('rows_errors');
            }
        } // foreach ($this->_newData as $p)
        unset($p);
    }

    protected function _addPathSuffix($path)
    {
        if (null === $this->_pathSuffix) {
            $this->_pathSuffix = $this->_getPathSuffix($this->_storeId);
        }
        return trim($path, '/') . $this->_pathSuffix;
    }

    protected function _importCreateAttributeOption($attr, $name)
    {
        $aId = $attr['attribute_id'];
        $name = trim($name);
        if (!empty($this->_attributesById[$aId]['options_bytext'][strtolower($name)])) {
            return;
        }
        if (!$this->_profile->getData('options/import/dryrun')) {

            $this->_write->insert($this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION), ['attribute_id' => $aId]);
            $oId = $this->_write->lastInsertId();

            $this->_write->insert($this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE),
                                  ['option_id' => $oId, 'store_id' => 0, 'value' => $name]);
        } else {
            $oId = 0;
            foreach ($this->_attributesByCode[$aId]['options'] as $k => $v) {
                $oId = max($oId, $k);
            }
        }

        $this->_attributesById[$aId]['options'][$oId] = $name;
        $this->_attributesById[$aId]['options_bytext'][strtolower($name)] = $oId;

    }

    protected function _importProcessDataDiff()
    {
        //        $oldValues = [];

        $profile = $this->_profile;
        $forceUrlRewritesRefresh = $profile->getData('options/import/force_urlrewrite_refresh');
        // find changed data
        foreach ($this->_newData as $urlPath => $p) {

            if (!$this->_valid[$urlPath]) {
                continue;
            }

            $this->_profile->getLogger()->setLine($this->_pathLine[$urlPath]);

            // check if the category is new
            $isNew = empty($this->_urlPaths[$urlPath]) || $this->_urlPaths[$urlPath] === true;

            $isUpdated = false;

            // create new category
            if ($isNew) {
                $this->_insertEntity[$urlPath] = [
                    'attribute_set_id' => $this->_attributeSetId,
                    'created_at' => $this->_rapidFlowHelper->now(),
                    'updated_at' => $this->_rapidFlowHelper->now(),
                    'position' => isset($p['position']) ? $p['position'] : 0,
                ];
                if($this->_rapidFlowHelper->hasMageFeature(self::ROW_ID)){
                    $this->_insertEntity[$urlPath]['entity_id'] = $this->_getNextCategorySequence((bool)$this->_profile->getData('options/import/dryrun'));
                    $this->_insertEntity[$urlPath]['created_in'] = 1;
                    $this->_insertEntity[$urlPath]['updated_in'] = \Magento\Staging\Model\VersionManager::MAX_VERSION;
                }
                $cId = null;
                $parentPath = $this->_parentPath[$urlPath];
                $this->_changeChildrenCount[$urlPath] = 0;
                if ($parentPath) {
                    if (!isset($this->_changeChildrenCount[$parentPath])) {
                        $count = !empty($this->_childrenCount[$parentPath]) ? $this->_childrenCount[$parentPath] : 0;
                        $this->_changeChildrenCount[$parentPath] = $count + 1;
                    } else {
                        $this->_changeChildrenCount[$parentPath]++;
                    }
                }
                if (!empty($this->_parentPathExtra[$urlPath])) {
                    foreach ($this->_parentPathExtra[$urlPath] as $parentPath) {
                        if (!isset($this->_changeChildrenCount[$parentPath])) {
                            $count = !empty($this->_childrenCount[$parentPath]) ? $this->_childrenCount[$parentPath] : 0;
                            $this->_changeChildrenCount[$parentPath] = $count + 1;
                        } else {
                            $this->_changeChildrenCount[$parentPath]++;
                        }
                    }
                }
            } else {
                $cId = $this->_urlPaths[$urlPath];
                // $urlPath = $this->_entities[$cId][0]['url_path'];
                // $this->_psrLogLoggerInterface->log("cId: ".$cId, null, 'rf.log', true);
                // $this->_psrLogLoggerInterface->log("urlPath: ".$urlPath, null, 'rf.log', true);
                if (isset($p['position']) && $p['position'] !== '' && $p['position'] != $this->_entities[$cId][0]['position']) {
                    $this->_updateEntity[$cId]['position'] = $p['position'];
                    $isUpdated = true;
                }
            }

            // walk the attributes
            foreach ($p as $k => $newValue) {
                $this->_profile->getLogger()->setColumn(isset($this->_fieldsCodes[$k]) ? $this->_fieldsCodes[$k] + 1 : -1);

                $oldValue = !$cId ? null : (
                    isset($this->_entities[$cId][$this->_storeId][$k]) ? $this->_entities[$cId][$this->_storeId][$k] : (
                        isset($this->_entities[$cId][0][$k]) ? $this->_entities[$cId][0][$k] : null
                    )
                );
                $attr = $this->_attr($k);

                // convert options text to values
                /*
                if (!empty($attr['frontend_input'])) {
                    switch ($attr['frontend_input']) {
                    case 'select':
                        $lower = strtolower($newValue);
                        if ($newValue=='' && !isset($attr['options_bytext'][$lower])) {
                            $newValue = null;
                        } elseif (!is_null($newValue)) {
                            $newValue = $attr['options_bytext'][$lower];
                        }
                        break;

                    case 'multiselect':
                        $v1 = [];
                        foreach ((array)$newValue as $k1=>$v) {
                            $v1[] = $attr['options_bytext'][strtolower($v)];
                        }
                        $newValue = $v1;
                        break;
                    }
                }
                */

                // some validation happens here as well
                $this->_cleanupValues($attr, $oldValue, $newValue);

                if (empty($attr['attribute_id']) || empty($attr['backend_type']) || $attr['backend_type'] == 'static') {
                    continue;
                }
                // existing attribute values
                $isValueChanged = false;
                if (is_array($newValue)) {
                    $isValueChanged = array_diff($newValue, $oldValue) || array_diff($oldValue, $newValue);
                } else {
                    $isValueChanged = $newValue !== $oldValue;
                }
                // add updated attribute values
                $empty = $newValue === '' || null === $newValue || $newValue === [];
                if (($isNew && !$empty) || $isValueChanged) {

#$profile->getLogger()->log('DIFF', $p['url_path'].'/'.$k.': '.print_r($oldValue,1).';'.print_r($newValue,1));
//                    $oldValues[$urlPath][$k] = $oldValue;
                    if ($k === 'url_path' && $this->_upPrependRoot) { // strip prepended root category from path
                        $_up = explode('/', $newValue, 2);
                        array_shift($_up);
                        reset($_up);
                        $newValue = current($_up);
                    }

                    if($k === 'url_key' || $k === 'is_anchor'){ // in these cases, url rewrites need to be updated
                        $this->_urlsToUpdate[$urlPath] = true;
                    }
                    $this->_changeAttr[$urlPath][$k] = $newValue;
                    if (!$isNew) {
                        $this->_profile->getLogger()->setColumn(isset($this->_fieldsCodes[$k]) ? $this->_fieldsCodes[$k] + 1 : -1)
                            ->success(null, null, $newValue, $oldValue);
                    }
                    $isUpdated = true;
                }
            } // foreach ($p as $k=>$newValue)
            if ($forceUrlRewritesRefresh) {
                $this->_urlsToUpdate[$urlPath] = true;
            }

            if ($isUpdated) {
                $this->_profile->addValue('rows_success');
                /*
                $logger->setColumn(0);
                if (!empty($oldValues[$p['sku']])) $logger->success('OLD: '.print_r($oldValues[$p['sku']],1));
                if (!empty($this->_changeStock[$p['sku']])) $logger->success('STOCK: '.print_r($this->_changeStock[$p['sku']],1));
                if (!empty($this->_changeWebsite[$p['sku']])) $logger->success('WEBSITE: '.print_r($this->_changeWebsite[$p['sku']],1));
                if (!empty($this->_changeAttr[$p['sku']])) $logger->success('ATTR: '.print_r($this->_changeAttr[$p['sku']],1));
                */
                if (!$isNew) {
                    $this->_updateEntity[$cId]['updated_at'] = HelperData::now();
                }
            } else {
                $this->_profile->addValue('rows_nochange');
            }
        } // foreach ($this->_newData as $p)
        /*
        echo '<table><tr><td>';
        var_dump($oldValues);
        echo '</td><td>';
        var_dump($this->_changeAttr);
        echo '</td></tr></table>';
        exit;
        */
    }

    protected function _importGenerateAttributeValues()
    {
        $sameAsDefault = $this->_profile->getData('options/import/store_value_same_as_default');

        // load old values for changed website scope attributes for comparison
        if (!empty($this->_websiteScope)) {
            $websiteProductIds = array_keys($this->_websiteScopeProducts);
            $websiteAttrIds = array_keys($this->_websiteScopeAttributes);
            foreach ($this->_websiteStores[$this->_storeId] as $sId) {
                $this->_fetchAttributeValues($sId, false, $websiteProductIds, $websiteAttrIds);
            }
        }

        // generate attribute value actions
        foreach ($this->_changeAttr as $urlPath => $p) {
            //$logger->setLine($this->_pathLine[$urlPath]);
            $cId = $this->_urlPaths[$urlPath];
            foreach ($p as $k => $v) {
                $attr = $this->_attr($k);
                if (!$attr) {
                    continue;
                }
                $aId = $attr['attribute_id'];
                $aType = $this->getAttrType($attr, 'catalog/category');
                // multiselect values
                if (is_array($v)) {
                    $v = join(',', $v);
                }
                // find which actions need to be performed
                $values = [];
                if (!empty($this->_entities[$cId])) {
                    foreach ($this->_entities[$cId] as $sId => $sValues) {
                        if (isset($sValues[$k])) {
                            $values[$sId] = $sValues[$k];
                        }
                    }
                }
                $sActions = [];
                if (null !== $v) { // new value exists
                    // no default value at all, save only default value
                    if (!isset($this->_attrValueIds[$cId][0][$k])) {
                        $sActions = [0 => 'I'];
                    } // updating default and it's set
                    elseif (!$this->_storeId) {
                        $sActions = [0 => 'U'];
                    } // attribute is global
                    elseif ($attr['is_global'] == 1) {
                        $sActions = [0 => 'U'];
                    } // updating not defaults, default value is set, check store values
                    else {
                        $sIds = [$this->_storeId];
                        foreach ($sIds as $sId) {
                            if (isset($this->_attrValueIds[$cId][$sId][$k])) {
                                if (isset($values[0]) && $v == $values[0] && $sameAsDefault === 'default') {
                                    $sActions[$sId] = 'D';
                                } else {
                                    $sActions[$sId] = 'U';
                                }
                            } else {
                                $sActions[$sId] = 'I';
                            }
                        }
                    }
                } else { // new value is empty
                    // default value exists and updating defaults - delete default
                    if (isset($this->_attrValueIds[$cId][0][$k]) && !$this->_storeId) {
                        $sActions = [0 => 'D'];
                    } // attribute is global
                    elseif ($attr['is_global'] == 1) {
                        $sActions = [0 => 'D'];
                    } // updating not defaults, delete store values
                    elseif ($this->_storeId && isset($this->_attrValueIds[$cId][$this->_storeId][$k])) {
                        $sActions[$this->_storeId] = 'D';
                    }
                }

                // generate attribute value inserts/updates/deletes
                foreach ($sActions as $sId => $action) {
                    switch ($action) {
                        case 'I':
                            $a = [
                                'attribute_id' => $aId,
                                'store_id' => $sId,
                                $this->_entityIdField => $cId,
                                'value' => $v,
                            ];
                            if ($cId === true) {
                                $a['url_path'] = $urlPath;
                            }
                            $this->_insertAttr[$aType][] = $a;
                            break;

                        case 'U':
                            $this->_updateAttr[$aType][$this->_attrValueIds[$cId][$sId][$k]] = $v;
                            break;

                        case 'D':
                            $this->_deleteAttr[$aType][] = $this->_attrValueIds[$cId][$sId][$k];
                            break;
                    }
                }
            }
        }
    }


    protected function _fetchAttributeValues($storeId, $defaults = false, $entityIds = null, $limitAttrIds = null)
    {
        if (!empty($this->_attrValuesFetched[$storeId])) {
            return;
        }
        // do not fetch attributes of existing products when only creating new products
        if ($this->_profile->getData('options/import/actions') === 'create') {
            return;
        }
        if (null === $entityIds) {
            $entityIds = $this->_entityIds;
        }
        $table = $this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY);

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
            // retrieve attribute data for this page
            $entityId = $this->_entityIdField;
            $select = $this->_read->select()->from($table . '_' . $type)
                ->where("{$entityId} in (?)", $entityIds)// load values only for products in this page
                ->where('attribute_id in (?)', $attrIds)// load only attributes stored in this table
                ->where('store_id in (0, ?)', $storeId);
            //->order('store_id', 'desc')) // first load store specific records
            //$this->_psrLogLoggerInterface->log($select->assemble(), null, 'rf.log', true);
            $rows = $this->_read->fetchAll($select); // load both default and store specific records
            if (empty($rows)) {
                $this->_logger->debug('rows empty: ' . __LINE__);
                continue;
            }
            // retrieve store specific data AND default data
            foreach ($rows as $r) {
                $attrCode = $this->_attr($r['attribute_id'], 'attribute_code');
                if ($attrCode === 'url_path') {
                    $r['value'] = $this->_upPrependRoot($this->_entities[$r[$entityId]][0], $r['value']);
                }
                if (empty($this->_entities[$r[$entityId]][$r['store_id']][$attrCode])) {
                    // text multiselect values separated by commas
                    if ($this->_attr($r['attribute_id'], 'frontend_input') === 'multiselect') {
                        if ($r['value'] === '' || null === $r['value']) {
                            $r['value'] = [];
                        } else {
                            $r['value'] = explode(',', $r['value']);
                        }
                    }
                    // if value was not set before, just set it as plain value
                    $this->_entities[$r[$entityId]][$r['store_id']][$attrCode] = $r['value'];
                } else {
                    if (!is_array($this->_entities[$r[$entityId]][$r['store_id']][$attrCode])) {
                        if ($r['value'] !== $this->_entities[$r[$entityId]][$r['store_id']][$attrCode]) {
                            // if value was set already, it is a multiselect, convert to array
                            $this->_entities[$r[$entityId]][$r['store_id']][$attrCode] = array(
                                $this->_entities[$r[$entityId]][$r['store_id']][$attrCode],
                                $r['value'],
                            );
                        }
                    } else {
                        if (!in_array($r['value'], $this->_entities[$r[$entityId]][$r['store_id']][$attrCode])) {
                            // multiselect was already initialized, add to array
                            $this->_entities[$r[$entityId]][$r['store_id']][$attrCode][] = $r['value'];
                        }
                    }
                }
                $this->_attrValueIds[$r[$entityId]][$r['store_id']][$attrCode] = $r['value_id'];
            }
        } // foreach ($this->_attributesByType as $type=>$attrs)

        if ($defaults) {
            $this->_attrValuesFetched[0] = true;
        }
        $this->_attrValuesFetched[$storeId] = true;
    }


    protected function _importSaveEntities()
    {
        $suffix = $this->_getPathSuffix($this->_storeId);
        $suffixLen = strlen($suffix);
        $hasRowId = $this->_rapidFlowHelper->hasMageFeature(self::ROW_ID);

        $table = $this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY);
        // create new categories

        foreach ($this->_changeChildrenCount as $urlPath => $count) {
            if (strpos($urlPath, '/') === false) {
                $_incVal = $count;
                if (!empty($this->_childrenCount[$urlPath])) {
                    $_incVal -= $this->_childrenCount[$urlPath];
                } else {
                    $_incVal++;
                }
                if (!empty($this->_rootCatId)) {
                    if (!isset($this->_childrenCount[$this->_rootCatId])) {
                        $this->_childrenCount[$this->_rootCatId] = 0;
                    }
                    $this->_childrenCount[$this->_rootCatId] += $_incVal;
                    $this->_updateEntity[$this->_rootCatId]['children_count'] = $this->_childrenCount[$this->_rootCatId];
                }
                if (!isset($this->_childrenCount[1])) {
                    $this->_childrenCount[1] = 0;
                }
                $this->_childrenCount[1] += $_incVal;
                $this->_updateEntity[1]['children_count'] = $this->_childrenCount[1];
            }
        }

        foreach ($this->_insertEntity as $urlPath => $a) {
            $this->_profile->getLogger()->setLine($this->_pathLine[$urlPath]);
            /** @var false|string $parentPath */
            $parentPath = !empty($this->_parentPath[$urlPath]) ? $this->_parentPath[$urlPath] : false;
            if (!$parentPath) {
                $a['parent_id'] = $this->_rootCatId;
                $a['path'] = '1/' . $this->_rootCatId . '/';
                $a['level'] = 2;
            } elseif ($this->_urlPaths[$parentPath] === true) {
                $this->_profile->getLogger()->setColumn($this->_pathIdx + 1)
                    ->error(__("Parent category wasn't created due to error"));
                $this->_profile->addValue('num_errors')->addValue('rows_errors');
                continue;
            } else {
                $parentPathId = $this->_urlPaths[$parentPath];
                if (isset($this->_categories[$parentPathId])) {
                    $parentPathId = $this->_categories[$parentPathId]['entity_id'];
                }
                $a['parent_id'] = $parentPathId;
                $a['path'] = $this->_categories[$this->_urlPaths[$parentPath]]['path'] . '/';
                $a['level'] = count(explode('/', $a['path'])) - 1;
            }
            if (!empty($this->_changeChildrenCount[$urlPath])) {
                $a['children_count'] = $this->_changeChildrenCount[$urlPath];
                unset($this->_changeChildrenCount[$urlPath]);
            } else {
                $a['children_count'] = 0;
            }
            if($hasRowId){
                $a['entity_id'] = isset($a['entity_id']) ? $a['entity_id'] : $this->_getNextCategorySequence((bool)$this->_profile->getData('options/import/dryrun'));
                $a['created_in'] = 1;
                $a['updated_in'] = VersionManager::MAX_VERSION;
            }
            $this->_write->insert($table, $a);
            $cId = $this->_write->lastInsertId();
            if (!$hasRowId) {
                $a['entity_id'] = $cId;
            }
            $pathId = $hasRowId ? $a['entity_id'] : $cId;

            $this->_updateEntity[$cId] = ['path' => $a['path'] . $pathId];
            $newData = [
                $this->_entityIdField => $cId,
                'path' => $a['path'] . $pathId,
                'url_path' => $urlPath,
            ];
            if ($hasRowId) {
                $newData['entity_id'] = $a['entity_id'];
            }
            $this->_categories[$cId] = $newData;
            $this->_categoriesBySeqId[$a['entity_id']] = $newData;
            $this->_urlPaths[$urlPath] = $cId;

            if ($suffix) {
                $additionalKey = substr($urlPath, -$suffixLen) === $suffix
                    ? substr($urlPath, 0, strlen($urlPath) - $suffixLen)
                    : $urlPath . $suffix;
                $this->_urlPaths[$additionalKey] = $cId;
            }

            $this->_childrenCount[$urlPath] = $a['children_count'];
            if (!empty($additionalKey)) $this->_childrenCount[$additionalKey] = $a['children_count'];

            $this->_entityIdsUpdated[$cId] = 1;
            $this->_profile->getLogger()->setColumn(0)->success(null);
        }

        foreach ($this->_changeChildrenCount as $urlPath => $count) {
            $this->_updateEntity[$this->_urlPaths[$urlPath]]['children_count'] = $count;
            $this->_childrenCount[$urlPath] = $count;
        }

        // update existing entity rows
        foreach ($this->_updateEntity as $cId => $a) {
            $this->_write->update($table, $a, $this->_entityIdField . '=' . $cId);
            $this->_entityIdsUpdated[$cId] = 1;
        }
    }


    protected function _importSaveAttributeValues()
    {
        $table = $this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY);
        foreach ($this->_insertAttr as $type => $attrs) {
            if ($type === 'static') {
                continue;
            }
            $rows = [];
            $entityId = $this->_entityIdField;
            $sqlPrefix = "INSERT INTO `{$table}_{$type}` (`attribute_id`, `store_id`, `{$entityId}`, `value`) values ";

            foreach ($attrs as $a) {
                if (isset($a['url_path'])) {
                    if ($this->_urlPaths[$a['url_path']] === true) {
                        continue;
                    }
                    $a[$entityId] = $this->_urlPaths[$a['url_path']];
                    unset($a['url_path']);
                }
                $sqlValue = "('{$a['attribute_id']}', '{$a['store_id']}', '{$a[$entityId]}', ?)";
                $value = $type === 'varchar' ? substr($a['value'], 0, 255) : $a['value'];
                $sql = $this->_write->quoteInto($sqlValue, $value);
                if ($type === 'text' && strlen((string)$value) > 4000) {
                    try {
                        $this->_write->getConnection()->exec($sqlPrefix . $sql);
                    } catch (Exception $e) {
                        $this->_profile->getLogger()->error(__METHOD__ . ':' . __LINE__ . ':' . $e->getMessage());
                        $this->_logger->error($sqlPrefix . $sql);
                    }
                } else {
                    $rows[] = $sql;
                }
            }
            #$chunks = array_chunk($rows, $this->_insertAttrChunkSize);
            $chunks = array_chunk($rows, 100);
            foreach ($chunks as $chunk) {
                try {
                    $this->_write->getConnection()->exec($sqlPrefix . join(',', $chunk));
                } catch (Exception $e) {
                    $this->_profile->getLogger()->error(__METHOD__ . ':' . __LINE__ . ':' . $e->getMessage());
                    $this->_logger->error($sqlPrefix . join(',', $chunk));
                    $this->_logger->error(print_r($attrs, 1));
                }
            }
        }
        foreach ($this->_updateAttr as $type => $attrs) {
            if ($type === 'static') {
                continue;
            }
            foreach ($attrs as $k => $v) {
                try {
                    $this->_write->update($table . '_' . $type, array(
                        'value' => $type === 'varchar' ? substr($v, 0, 255) : $v,
                    ), 'value_id=' . $k);
                } catch (Exception $e) {
                    $this->_profile->getLogger()->error(__METHOD__ . ':' . __LINE__ . ':' . $e->getMessage());
                }
            }
        }
        foreach ($this->_deleteAttr as $type => $vIds) {
            if ($type === 'static') {
                continue;
            }
            try {
                $this->_write->delete($table . '_' . $type, 'value_id in (' . join(',', $vIds) . ')');
            } catch (Exception $e) {
                $this->_profile->getLogger()->error(__METHOD__ . ':' . __LINE__ . ':' . $e->getMessage());
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
//            $select = "SELECT a.attribute_code, a.attribute_id
//                FROM {$this->_t(self::TABLE_EAV_ENTITY_ATTRIBUTE)} ea
//                INNER JOIN {$this->_t(self::TABLE_EAV_ATTRIBUTE)} a ON a.attribute_id=ea.attribute_id
//                WHERE attribute_set_id={$attrSet}";
            $select = $this->_write->select()->from(['ea' => $this->_t(self::TABLE_EAV_ENTITY_ATTRIBUTE)], [])
                ->join(['a'=> $this->_t(self::TABLE_EAV_ATTRIBUTE)], 'a.attribute_id=ea.attribute_id', ['attribute_code', 'attribute_id'])
                ->where('attribute_set_id=?', $attrSet);
            $this->_attributeSetFields[$attrSet] = $this->_write->fetchPairs($select);
        }
        return $this->_attributeSetFields[$attrSet];
    }

    protected function _cleanupValues($attr, &$oldValue, &$newValue)
    {
        // trying to work around PHP's weakly typed mess...
        if (!empty($attr['frontend_input'])) {
            switch ($attr['frontend_input']) {
                case 'media_image':
                    if (null !== $oldValue) {
                        if ($oldValue === 'no_selection') {
                            $oldValue = '';
                        }
                    }
                    break;

                case 'multiselect':
                    if (null === $oldValue) {
                        $oldValue = [];
                    }
                    if ($newValue === '') {
                        $newValue = [];
                    }
                    break;
            }
        }
        if (!empty($attr['backend_type'])) {
            switch ($attr['backend_type']) {
                case 'int':
                    if (null !== $newValue && !is_array($newValue)) {
                        if ($newValue === '') {
                            $newValue = null;
                        } else {
                            $newValue = $this->_locale->getNumber($newValue);
                            if ($newValue != (int)$newValue) {
                                $this->_profile->addValue('num_errors');
                                $this->_profile->getLogger()->error(__("Invalid int value"));
                            } else {
                                $newValue = (int)$newValue;
                            }
                        }
                    }
                    if (null !== $oldValue && !is_array($oldValue)) {
                        if ($oldValue === '') {
                            $oldValue = null;
                        } else {
                            $oldValue = (int)$oldValue;
                        }
                    }
                    break;

                case 'decimal':
                    if (null !== $newValue) {
                        if ($newValue === '') {
                            $newValue = null;
                        } else {
                            $newValue = $this->_locale->getNumber($newValue);
                            if (!is_numeric($newValue)) {
                                $this->_profile->addValue('num_errors');
                                $this->_profile->getLogger()->error(__('Invalid decimal value'));
                            } else {
                                $newValue *= 1.0;
                            }
                        }
                    }
                    if (null !== $oldValue) {
                        if ($oldValue === '') {
                            $oldValue = null;
                        } else {
                            $oldValue *= 1.0;
                        }
                    }
                    break;

                case 'datetime':
                    if (null !== $newValue) {
                        if ($newValue === '') {
                            $newValue = null;
                        } else {
                            static $_dp;
                            if (null === $_dp) {
                                $_dp = $this->_scopeConfig->getValue('urapidflow/import_options/date_processor');
                                if ($_dp === 'date_parse_from_format' && !version_compare(phpversion(), '5.3.0', '>=')) {
                                    $_dp = 'strtotime';
                                }
                            }
                            static $_attrFormat = [];
                            $_attrCode = $attr['attribute_code'];
                            if (!isset($_attrFormat[$_attrCode])) {
                                if (isset($this->_fields[$_attrCode]['format'])) {
                                    $_attrFormat[$_attrCode] = $this->_fields[$_attrCode]['format'];
                                } else {
                                    $_attrFormat[$_attrCode] = $this->_profile->getDefaultDatetimeFormat();
                                }
                                if ($_dp === 'zend_date') {
                                    $_attrFormat[$_attrCode] = \Zend_Locale_Format::convertPhpToIsoFormat($_attrFormat[$_attrCode]);
                                }
                            }
                            switch ($_dp) {
                                case 'zend_date':
                                    /** @var \Zend_Date $_zendDate */
                                    static $_zendDate;
                                    if (null === $_zendDate) {
                                        $_zendDate = new \Zend_Date($newValue, $_attrFormat[$_attrCode],
                                                                    $this->_profile->getProfileLocale());
                                    } else {
                                        $_zendDate->set($newValue, $_attrFormat[$_attrCode]);
                                    }
                                    $newValue = $_zendDate->toString(DateTime::DATETIME_INTERNAL_FORMAT);
                                    break;
                                case 'date_parse_from_format':
                                    $_phpDatetime = \DateTime::createFromFormat($_attrFormat[$_attrCode], $newValue);
                                    $newValue = $_phpDatetime->format('Y-m-d H:i:s');
                                    break;
                                default:
                                    $newValue = date('Y-m-d H:i:s', strtotime($newValue));
                                    break;
                            }
                            if (!$newValue) {
                                $this->_profile->addValue('num_errors');
                                $this->_profile->getLogger()->error(__('Invalid datetime value'));
                            }
                        }
                    }
                    if (null !== $oldValue) {
                        if ($oldValue === '') {
                            $oldValue = null;
                        }
                    }
                    break;

                case 'varchar':
                case 'text':
                    if ($oldValue === '' && null === $newValue) {
                        $newValue = '';
                    } elseif (null === $oldValue && $newValue === '') {
                        $newValue = null;
                    } elseif (is_numeric($newValue)) {
                        $newValue = (string)$newValue;
                    }
                    break;
            }
        }
    }

    protected function _updateUrlRewrites()
    {
        if(!$this->_urlsToUpdate){
            return;
        }
        foreach ($this->_urlsToUpdate as $url=>$dummy) {
            if ($this->_urlPaths[$url] === true) {
                continue;
            }
            $eId = $this->_urlPaths[$url];
            $this->_rapidFlowHelper->addCategoryIdForRewriteUpdate($eId, $this->catSeqIdByRowId($eId));
        }
        $this->_urlsToUpdate = [];// reset after processing
    }
    public function catRowIdBySeqId($seqId)
    {
        return @$this->_catEntity2Row[$seqId];
    }
    public function catSeqIdByRowId($rowId)
    {
        return @$this->_catRow2Entity[$rowId];
    }
}
