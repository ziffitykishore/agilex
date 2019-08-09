<?php

namespace Unirgy\RapidFlowPro\Model\ResourceModel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Unirgy\RapidFlow\Model\Config as ModelConfig;
use Unirgy\RapidFlow\Model\ResourceModel\Catalog\Fixed;
use Unirgy\RapidFlow\Model\ResourceModel\Catalog\Product\AbstractProduct;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\App\ObjectManager;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;

class ProductExtra
    extends Fixed
{
    protected $_translateModule = 'Unirgy_RapidFlowPro';

    protected $_dataType = 'product_extra';

    protected $_linkTypes = [];

    protected $_linkAttrs = [];

    protected $_linkAttrIds = [];

    protected $_bundleOptions = [];

    protected $_customOptions = [];

    protected $_customOptionSelections = [];

    protected $_imagesBaseDir;

    protected $_urlPaths = [];

    protected $_catEntity2Row = [];
    protected $_catRow2Entity = [];

    protected $_catIds = [];

    protected $_exportRowCallback = [
        'CPI' => '_exportCallbackCPI',
        'CPSAP' => '_exportCallbackLoadAttributeOptions',
        'CPPT' => '_exportCallbackCPPT',
        'CCP' => '_exportCallbackCCP',
    ];

    protected $_upPrependRoot;

    protected $_processImageFiles;

    protected $_imagesTargetDir;

    protected $_categoryUrlRewriteIds = [];

    protected function _construct()
    {
        AbstractProduct::validateLicense('Unirgy_RapidFlowPro');
        parent::_construct();
    }

    public function setProfile($profile)
    {
        parent::setProfile($profile);

        $this->_processImageFiles = $profile->getData('options/' . $profile->getProfileType() . '/image_files');
        $this->_imagesMediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'product';
        $this->_imagesTargetDir = $profile->getImagesBaseDir();
        $this->_remoteImageSubfolderLevel = $profile->getData('options/' . $profile->getProfileType() . '/image_remote_subfolder_level');

        return $this;
    }

    protected function _afterImport($cnt)
    {
        $this->_updateCategoryUrlRewrites();
    }

    protected function _importLink($row, $linkType)
    {
        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $linkTable = $this->_t(self::TABLE_CATALOG_PRODUCT_LINK);
        $linkAttrTable = $this->_t(self::TABLE_CATALOG_PRODUCT_LINK_ATTRIBUTE);

        $linkTypeId = $this->_getLinkTypeId($linkType);

        $linkAttr = $this->_getLinkAttr($linkTypeId);

        $p1 = $this->_getIdBySku($row[1]);
        $p2 = $this->_getSeqIdBySku($row[2]);
        $lt = $this->_getLinkTypeId($linkType);
        $new = [
            'position' => isset($row[3]) ? $row[3] : null,
            'qty' => isset($row[4]) ? $row[4] : null,
        ];

//        $sql = "SELECT * FROM {$linkTable}
//            WHERE product_id={$p1} AND linked_product_id={$p2} AND link_type_id={$lt}";
        $sql = $this->_read->select()->from($linkTable)
            ->where('product_id=?', $p1)
            ->where('linked_product_id=?', $p2)
            ->where('link_type_id=?', $lt);

        $exists = $this->_write->fetchRow($sql);

        if ($exists) {
            $changed = false;
            foreach ($linkAttr as $code => $attr) {
                $paramTable = $linkAttrTable . '_' . $attr['data_type'];
//                $r = $this->_write->fetchRow("SELECT * FROM {$paramTable}
//                    WHERE link_id={$exists['link_id']} AND product_link_attribute_id={$attr['id']}");
                $r = $this->_write->fetchRow($this->_read->select()->from($paramTable)
                                                 ->where('link_id=?', $exists['link_id'])
                                                 ->where('product_link_attribute_id=?', $attr['id']));
                $empty = $new[$code] === '' || $new[$code] === null || $new[$code] === false;
                if (!$r) {
                    if (!$empty) {
                        $this->_write->insert($paramTable, [
                            'product_link_attribute_id' => $attr['id'],
                            'link_id' => $exists['link_id'],
                            'value' => $new[$code],
                        ]);
                        $changed = true;
                    }
                } elseif ($empty) {
                    $this->_write->delete($paramTable, "value_id={$r['value_id']}");
                    $changed = true;
                } elseif ($new[$code] != $r['value']) {
                    $this->_write->update($paramTable, [
                        'value' => $new[$code]
                    ], "value_id={$r['value_id']}");
                    $changed = true;
                }
            }
            if (!$changed) {
                return self::IMPORT_ROW_RESULT_NOCHANGE;
            }
        } else {
            $this->_write->insert($linkTable, [
                'product_id' => $p1,
                'linked_product_id' => $p2,
                'link_type_id' => $lt,
            ]);
            $linkId = $this->_write->lastInsertId($linkTable);
            if ($linkType === 'super') {
                $relTable = $this->_t(self::TABLE_CATALOG_PRODUCT_RELATION);
//                $sqlRel = "SELECT parent_id FROM {$relTable} WHERE parent_id={$p1} AND child_id={$p2}";
                $sqlRel = $this->_read->select()->from($relTable, 'parent_id')
                                                 ->where('parent_id=?', $p1)
                                                 ->where('child_id=?', $p2);
                if (!$this->_write->fetchOne($sqlRel)) {
                    $this->_write->insert($relTable, ['parent_id' => $p1, 'child_id' => $p2]);
                }
            }
            foreach ($new as $code => $value) {
                if (empty($linkAttr[$code]) || $value === '' || $value === null || $value === false) {
                    continue;
                }
                $this->_write->insert($linkAttrTable . '_' . $linkAttr[$code]['data_type'], [
                    'product_link_attribute_id' => $linkAttr[$code]['id'],
                    'link_id' => $linkId,
                    'value' => $value,
                ]);
            }
        }
        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _importRowCPRI($row)
    {
        return $this->_importLink($row, 'relation');
    }

    protected function _importRowCPUI($row)
    {
        return $this->_importLink($row, 'up_sell');
    }

    protected function _importRowCPXI($row)
    {
        return $this->_importLink($row, 'cross_sell');
    }

    protected function _importRowCPGI($row)
    {
        return $this->_importLink($row, 'super');
    }

    protected function _getNextBundleSelectionSequence($dryRun = false)
    {
        $tableName = $this->_t(static::TABLE_CATALOG_PRODUCT_BUNDLE_SELECTION_SEQ);
        return !$dryRun ? $this->_getNextSequence($tableName) : 1;
    }

    protected function _getNextBundleOptionSequence($dryRun = false)
    {
        $tableName = $this->_t(static::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION_SEQ);
        return !$dryRun ? $this->_getNextSequence($tableName) : 1;
    }

    protected function _getBundleOption($pId, $title)
    {

        if (empty($this->_bundleOptions[$pId][$title])) {
//            $row = $this->_write->fetchRow("SELECT
//                                              bo.*,
//                                              bov.value_id,
//                                              bov.title
//                                            FROM {$this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION)} bo
//                                            INNER JOIN {$this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE)} bov ON bov.option_id=bo.option_id
//                                            WHERE bo.parent_id={$pId} AND bov.store_id=0 AND bov.title = ?"
//                , $title);
            $row = $this->_write->fetchRow(
                $this->_read->select()->from(['bo' => $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION)])
                    ->join(['bov' => $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE)], 'bov.option_id=bo.option_id', ['value_id', 'title'])
                    ->where('bo.parent_id=?', $pId)->where('bov.title=? AND bov.store_id=0', $title)
            );

            if (!$row) {
                return false;
            }
            if (sizeof($this->_bundleOptions) > $this->_maxCacheItems[self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION]) {
                reset($this->_bundleOptions);
                unset($this->_bundleOptions[key($this->_bundleOptions)]);
            }
            $this->_bundleOptions[$pId][$title] = $row;
        }
        return $this->_bundleOptions[$pId][$title];
    }

    protected function _updateBundleOption($pId, $title, array $data)
    {
        if (empty($this->_bundleOptions[$pId][$title])) {
            $this->_bundleOptions[$pId][$title] = ['parent_id' => $pId, 'title' => $title];
        }
        $this->_bundleOptions[$pId][$title] = array_merge($this->_bundleOptions[$pId][$title], $data);
        return $this->_bundleOptions[$pId][$title];
    }

    protected function _importRowCCP($row)
    {
        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_CATALOG_CATEGORY_PRODUCT);

        $entityId = $this->_entityIdField;
        $category = $this->_fetchCategoryRow($row[1]);
        if (!isset($category['entity_id']) && isset($category[$entityId])) {
            $category['entity_id'] = $this->catSeqIdByRowId($category[$entityId]);
        }
        //$pId = $this->_getIdBySku($row[2]);
        $pId = $this->_getSeqIdBySku($row[2]);
        $position = !empty($row[3]) ? (int)$row[3] : 0;

        if (!$category || !@$category['entity_id']) {
            throw new LocalizedException(__("Invalid Category '%1'", $row[1]));
        }
        if (!$pId) {
            throw new LocalizedException(__("Invalid SKU '%1'", $row[2]));
        }

//        $exists = $this->_write->fetchRow("SELECT position FROM {$t}
//                WHERE category_id={$category[$this->_entityIdField]} AND product_id={$pId}");
        $exists = $this->_write->fetchRow(
            $this->_write->select()->from($t, 'position')
                ->where('category_id=?', $category['entity_id'])
                ->where('product_id=?', $pId)
        );
//        if ($exists && $exists['position'] != $position || !$exists) {
//            $existsInPosition = $this->_write->fetchRow(
//                $this->_write->select()->from($t)
//                    ->where('category_id=?', $category[$this->_entityIdField])
//                    ->where('position=?', $position)
//            );
//            if ($existsInPosition) {
//                $lastPosition = $this->_write->fetchRow(
//                    $this->_write->select()->from($t)
//                        ->where('category_id=?', $category[$this->_entityIdField])
//                        ->where('position=?', $position)
//                );
//            }
//        }
        if ($exists) {
            if ($exists['position'] != $position) {
                $this->_write->update($t, ['position' => $position],
                                      "category_id={$category['entity_id']} and product_id={$pId}");
                return self::IMPORT_ROW_RESULT_SUCCESS;
            }
        } else {
            $this->_write->insert($t, [
                'category_id' => $category['entity_id'],
                'product_id' => $pId,
                'position' => $position
            ]);
            $this->_categoryUrlRewriteIds[] = $category[$this->_entityIdField];
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPBO($row)
    {

        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $hasBundleSeq = $this->_rapidFlowHelper->hasMageFeature(self::BUNDLE_SEQ);
        $hasBundleParent = $this->_rapidFlowHelper->hasMageFeature(self::BUNDLE_PARENT);

        $boTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION);
        $bovTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE);

        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);
        $pos = isset($row[3]) ? (int)$row[3] : 0;
        $type = isset($row[4]) ? $row[4] : 'select';
        $required = isset($row[5]) ? (int)$row[5] : 0;

        $exists = $this->_getBundleOption($pId, $title);
        if ($exists) {
            if ($exists['position'] == $pos && $exists['type'] == $type && $exists['required'] == $required) {
                return self::IMPORT_ROW_RESULT_NOCHANGE;
            }
            $new = ['position' => $pos, 'type' => $type, 'required' => $required];
            $this->_write->update($boTable, $new, "option_id={$exists['option_id']}");
        } else {
            $new = [
                'parent_id' => $pId,
                'required' => $required,
                'position' => $pos,
                'type' => $type,
            ];
            if ($hasBundleSeq) {
                $optId = $this->_getNextBundleOptionSequence();
                $new['option_id'] =  $optId;
            }
            $this->_write->insert($boTable, $new);
            if (!$hasBundleSeq) {
                $new['option_id'] = $this->_write->lastInsertId($boTable);
            }
            $new['title'] = $title;
            $bovNew = [
                'option_id' => $new['option_id'],
                'store_id' => 0,
                'title' => $title,
            ];
            if ($hasBundleParent) {
                $bovNew['parent_product_id'] = $pId;
            }
            $this->_write->insert($bovTable, $bovNew);
        }
        $this->_updateBundleOption($pId, $title, $new);
        $this->_newRefreshHoRoPids[$pId] = 1;
        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _importRowCPBOL($row)
    {

        if (sizeof($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $hasBundleParent = $this->_rapidFlowHelper->hasMageFeature(self::BUNDLE_PARENT);

        $pId = $this->_getIdBySku($row[1]);
        $optTitle = $this->_convertEncoding($row[2]);
        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $title = $row[4];

        $option = $this->_getBundleOption($pId, $optTitle);
        if (!$option) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        }
        $bovTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE);
//        $exists = $this->_write->fetchRow("SELECT * FROM {$bovTable}
//            WHERE option_id={$option['option_id']} AND store_id={$storeId}");
        $exists = $this->_write->fetchRow($this->_read->select()
                                              ->from($bovTable)
                                              ->where('option_id=?', $option['option_id'])
                                              ->where('store_id=?', $storeId));
        if ($exists) {
            if ($exists['title'] === $title) {
                return self::IMPORT_ROW_RESULT_NOCHANGE;
            }
            $this->_write->update($bovTable, ['title' => $title], "value_id={$exists['value_id']}");
        } else {
            $new = [
                'option_id' => $option['option_id'],
                'store_id' => $storeId,
                'title' => $title,
            ];
            if ($hasBundleParent) {
                $new['parent_product_id'] = $pId;
            }
            $this->_write->insert($bovTable, $new);
        }
        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _importRowCPBOS($row)
    {

        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $hasBundleSeq = $this->_rapidFlowHelper->hasMageFeature(self::BUNDLE_SEQ);

        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);

        $option = $this->_getBundleOption($pId, $title);
        if (!$option) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        }

        $sId = $this->_getSeqIdBySku($row[3]);
        $new = [
            'position' => isset($row[4]) ? (int)$row[4] : 0,
            'is_default' => isset($row[5]) ? (int)$row[5] : 0,
            'selection_price_type' => isset($row[6]) ? (int)$row[6] : 0,
            'selection_price_value' => isset($row[7]) ? (float)$row[7] : 0,
            'selection_qty' => isset($row[8]) ? (float)$row[8] : 0,
            'selection_can_change_qty' => isset($row[9]) ? (int)$row[9] : 0,
        ];

        $bosTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_SELECTION);
//        $exists = $this->_write->fetchRow("select * from {$bosTable}
//            where option_id='{$option['option_id']}' and product_id='{$sId}'");
        $exists = $this->_write->fetchRow($this->_read->select()
                                              ->from($bosTable)
                                              ->where('option_id=?', $option['option_id'])
                                              ->where('product_id=?', $sId));
        if ($exists) {
            if (!$this->_isChangeRequired($exists, $new)) {
                return self::IMPORT_ROW_RESULT_NOCHANGE;
            }
            $this->_write->update($bosTable, $new, "selection_id={$exists['selection_id']}");
        } else {
            if ($hasBundleSeq) {
                $new['selection_id'] = $this->_getNextBundleSelectionSequence();
            }
            $new['option_id'] = $option['option_id'];
            $new['parent_product_id'] = $pId;
            $new['product_id'] = $sId;
            $this->_write->insert($bosTable, $new);
        }
        $relTable = $this->_t(self::TABLE_CATALOG_PRODUCT_RELATION);
//        $exists = $this->_write->fetchRow("select * from {$relTable} where parent_id='{$pId}' and child_id='{$sId}'");
        $exists = $this->_write->fetchRow($this->_write->select()
                                              ->from($relTable)
                                              ->where('parent_id=?', $pId)->where('child_id=?', $sId));
        if (!$exists) {
            $this->_write->insert($relTable, ['parent_id' => $pId, 'child_id' => $sId]);
        }
        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _importRowCPBOSL($row)
    {
        if (sizeof($row) < 7) {
            throw new LocalizedException(__('Invalid row format'));
        }
        
        $hasBundleParent = $this->_rapidFlowHelper->hasMageFeature(self::BUNDLE_PARENT);
        
        $wId = !empty($row[6]) ? (int)$this->_getWebsiteId($row[6], true) : 0;

        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);

        $option = $this->_getBundleOption($pId, $title);
        if (!$option) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        }

        $selSkuId = $this->_getSeqIdBySku($row[3]);
        $new = [
            'website_id' => $wId,
            'selection_price_type' => (int)$row[4],
            'selection_price_value' => (float)$row[5],
        ];

        $bosTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_SELECTION);
        $selectionId = $this->_write->fetchOne($this->_read->select()
                                              ->from($bosTable, 'selection_id')
                                              ->where('option_id=?', $option['option_id'])
                                              ->where('product_id=?', $selSkuId));
        if(!$selectionId){
            return self::IMPORT_ROW_RESULT_DEPENDS;
        }

        $bospTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_SELECTION_PRICE);
        $exists = $this->_write->fetchRow($this->_write->select()
            ->from($bospTable)
            ->where('website_id=?', $wId)->where('selection_id=?', $selectionId)
        );
        if ($exists) {
            if (!$this->_isChangeRequired($exists, $new)) {
                return self::IMPORT_ROW_RESULT_NOCHANGE;
            }
            $this->_write->update($bospTable, $new, "selection_id={$exists['selection_id']} AND website_id={$exists['website_id']}");
        } else {
            if ($hasBundleParent) {
                $new['parent_product_id'] = $pId;
            }
            $new['selection_id'] = $selectionId;
            $this->_write->insert($bospTable, $new);
        }
        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _getCustomOption($pId, $title)
    {

        if (empty($this->_customOptions[$pId][$title])) {
//            $sql = "select co.*, cot.option_title_id, cot.title from {$this->_t(self::TABLE_CATALOG_PRODUCT_OPTION)} co
//                inner join {$this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TITLE)} cot on cot.option_id=co.option_id
//                where co.product_id={$pId} and cot.store_id=0 and cot.title=?";
//            $row = $this->_write->fetchRow($sql, $title);
            $sql = $this->_write->select()
                ->from(['co' => $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION)])
                ->join(['cot' => $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TITLE)], 'cot.option_id=co.option_id', ['option_title_id', 'title'])
                ->where('co.product_id=? AND cot.store_id=0', $pId)
                ->where('cot.title=?', $title);

            $row = $this->_write->fetchRow($sql);
            if (!$row) {
                return false;
            }
            if (sizeof($this->_customOptions) > $this->_maxCacheItems['custom_option']) {
                reset($this->_customOptions);
                unset($this->_customOptions[key($this->_customOptions)]);
            }
            $this->_customOptions[$pId][$title] = $row;
        }
        return $this->_customOptions[$pId][$title];
    }

    protected function _updateCustomOption($pId, $title, array $data)
    {
        if (empty($this->_customOptions[$pId][$title])) {
            $this->_customOptions[$pId][$title] = ['product_id' => $pId, 'title' => $title];
        }
        $this->_customOptions[$pId][$title] = array_merge($this->_customOptions[$pId][$title], $data);
        return $this->_customOptions[$pId][$title];
    }

    protected function _getCustomOptionSelection($oId, $title)
    {
        if (empty($this->_customOptionSelections[$oId][$title])) {
            $cosTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_VALUE);
            $costTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_TITLE);
//            $row = $this->_write->fetchRow(
//                "SELECT cos.*, cost.option_type_title_id, cost.title
//FROM {$cosTable} cos
//INNER JOIN {$costTable} cost ON cost.option_type_id=cos.option_type_id
//WHERE cos.option_id={$oId} AND cost.store_id=0 AND cost.title = ?",
//                $title);
            $row = $this->_write->fetchRow(
                $this->_write->select()
                    ->from(['cos' => $cosTable])
                    ->join(['cost' => $costTable], 'cost.option_type_id=cos.option_type_id', ['option_type_title_id', 'title'])
                    ->where('cos.option_id=?', $oId)
                    ->where('cost.store_id=0 AND cost.title=?', $title)
            );
            if (!$row) {
                return false;
            }
            if (sizeof($this->_customOptionSelections) > $this->_maxCacheItems['custom_option_selection']) {
                reset($this->_customOptionSelections);
                unset($this->_customOptionSelections[key($this->_customOptionSelections)]);
            }
            $this->_customOptionSelections[$oId][$title] = $row;
        }
        return $this->_customOptionSelections[$oId][$title];
    }

    protected function _updateCustomOptionSelection($oId, $title, array $data)
    {
        if (empty($this->_customOptionSelections[$oId][$title])) {
            $this->_customOptionSelections[$oId][$title] = ['option_id' => $oId, 'title' => $title];
        }
        $this->_customOptionSelections[$oId][$title] = array_merge($this->_customOptionSelections[$oId][$title], $data);
        return $this->_customOptionSelections[$oId][$title];
    }

    protected function _importRowCPCO($row)
    {
        if (count($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $coTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION);
        $cotTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TITLE);
        $copTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_PRICE);

        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);
        $price = isset($row[8]) && $row[8] !== '' ? (float)$row[8] : null;
        $priceType = isset($row[9]) && $row[9] !== '' ? $row[9] : 'fixed';
        $new = [
            'type' => $row[3],
            'is_require' => isset($row[4]) ? (int)$row[4] : 0,
            'sku' => isset($row[5]) ? $row[5] : '',
            'sort_order' => isset($row[6]) ? (int)$row[6] : 0,
            'max_characters' => isset($row[7]) ? (int)$row[7] : 0,
            'file_extension' => isset($row[10]) ? $row[10] : '',
            'image_size_x' => isset($row[11]) ? (int)$row[11] : 0,
            'image_size_y' => isset($row[12]) ? (int)$row[12] : 0,
        ];

        $changed = false;

        $option = $this->_getCustomOption($pId, $title);
        if ($option) {
            if ($this->_isChangeRequired($option, $new)) {
                $this->_write->update($coTable, $new, "option_id={$option['option_id']}");
                $changed = true;
                $option = $this->_updateCustomOption($pId, $title, $new);
            }
        } else {
            $new['product_id'] = $pId;
            $this->_write->insert($coTable, $new);
            $new['option_id'] = $this->_write->lastInsertId($coTable);
            $new['title'] = $title;
            $option = $this->_updateCustomOption($pId, $title, $new);

            $this->_write->insert($cotTable, [
                'option_id' => $option['option_id'],
                'store_id' => 0,
                'title' => $title,
            ]);
            $changed = true;
        }

//        $exists = $this->_write->fetchRow("select * from {$copTable}
//            where option_id={$option['option_id']} and store_id=0");
        $exists = $this->_write->fetchRow(
            $this->_write->select()->from($copTable)->where('option_id=? AND store_id=0', $option['option_id']));

        if ($exists) {
            if (null === $price) {
                $this->_write->delete($copTable, "option_price_id={$exists['option_price_id']}");
                $changed = true;
            } elseif ($exists['price'] != $price || $exists['price_type'] != $priceType) {
                $this->_write->update($copTable, [
                    'price' => $price,
                    'price_type' => $priceType,
                ], "option_price_id={$exists['option_price_id']}");
                $changed = true;
            }
        } elseif (null !== $price) {
            $this->_write->insert($copTable, [
                'option_id' => $option['option_id'],
                'store_id' => 0,
                'price' => $price,
                'price_type' => $priceType,
            ]);
            $changed = true;
        }
        if ($changed) {
            $this->_newRefreshHoRoPids[$pId] = 1;
        }
        return $changed ? self::IMPORT_ROW_RESULT_SUCCESS : self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPCOL($row)
    {
        if (sizeof($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $pId = $this->_getIdBySku($row[1]);
        $optTitle = $this->_convertEncoding($row[2]);
        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $title = $this->_convertEncoding($row[4]);
        $price = isset($row[5]) && $row[5] !== '' ? (float)$row[5] : null;
        $priceType = isset($row[6]) && $row[6] !== '' ? $row[6] : 'fixed';

        $option = $this->_getCustomOption($pId, $optTitle);
        if (!$option) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        }
        $changed = false;

        $cotTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TITLE);
//        $exists = $this->_write->fetchRow("select * from {$cotTable}
//            where option_id={$option['option_id']} and store_id={$storeId}");
        $exists = $this->_write->fetchRow(
            $this->_write->select()->from($cotTable)
                ->where('option_id=?', $option['option_id'])->where('store_id=?', $storeId)
        );

        if ($exists) {
            if ($exists['title'] !== $title) {
                $this->_write->update($cotTable, [
                    'title' => $title
                ], "option_title_id={$exists['option_title_id']}");
                $changed = true;
            }
        } else {
            $this->_write->insert($cotTable, [
                'option_id' => $option['option_id'],
                'store_id' => $storeId,
                'title' => $title,
            ]);
            $changed = true;
        }

        $copTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_PRICE);
//        $exists = $this->_write->fetchRow("select * from {$copTable}
//            where option_id={$option['option_id']} and store_id={$storeId}");
        $exists = $this->_write->fetchRow($this->_write->select()->from($copTable)
                      ->where('option_id=?', $option['option_id'])->where('store_id=?', $storeId));
        if ($exists) {
            if (null === $price) {
                $this->_write->delete($copTable, "option_price_id={$exists['option_price_id']}");
                $changed = true;
            } elseif ($exists['price'] != $price || $exists['price_type'] != $priceType) {
                $this->_write->update($copTable, [
                    'price' => $price,
                    'price_type' => $priceType,
                ], "option_price_id={$exists['option_price_id']}");
                $changed = true;
            }
        } elseif (null !== $price) {
            $this->_write->insert($copTable, [
                'option_id' => $option['option_id'],
                'store_id' => $storeId,
                'price' => $price,
                'price_type' => $priceType,
            ]);
            $changed = true;
        }

        return $changed ? self::IMPORT_ROW_RESULT_SUCCESS : self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPCOS($row)
    {

        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $cosTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_VALUE);
        $costTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_TITLE);
        $cospTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_PRICE);

        $pId = $this->_getIdBySku($row[1]);
        $optTitle = $this->_convertEncoding($row[2]);
        $selTitle = $this->_convertEncoding($row[3]);
        $new = [
            'sku' => isset($row[4]) ? $row[4] : '',
            'sort_order' => isset($row[5]) ? (int)$row[5] : 0,
        ];
        $price = isset($row[6]) && $row[6] !== '' ? (float)$row[6] : null;
        $priceType = isset($row[7]) && $row[7] !== '' ? $row[7] : 'fixed';

        $option = $this->_getCustomOption($pId, $optTitle);
        if (!$option) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        }

        $changed = false;

        $selection = $this->_getCustomOptionSelection($option['option_id'], $selTitle);
        if ($selection) {
            if ($this->_isChangeRequired($selection, $new)) {
                $this->_write->update($cosTable, $new, "option_type_id={$selection['option_type_id']}");
                $selection = $this->_updateCustomOptionSelection($option['option_id'], $selTitle, $new);
                $changed = true;
            }
        } else {
            $new['option_id'] = $option['option_id'];
            $this->_write->insert($cosTable, $new);
            $new['option_type_id'] = $this->_write->lastInsertId($cosTable);

            $this->_write->insert($costTable, [
                'option_type_id' => $new['option_type_id'],
                'store_id' => 0,
                'title' => $selTitle,
            ]);
            $selection = $this->_updateCustomOptionSelection($option['option_id'], $selTitle, $new);
            $changed = true;
        }

//        $exists = $this->_write->fetchRow("select * from {$cospTable}
//            where option_type_id={$selection['option_type_id']} and store_id=0");
        $exists = $this->_write->fetchRow(
            $this->_write->select()->from($cospTable)
                ->where('option_type_id=? AND store_id=0', $selection['option_type_id'])
        );

        if ($exists) {
            if (null === $price) {
                $this->_write->delete($cospTable, "option_type_price_id={$exists['option_type_price_id']}");
                $changed = true;
            } elseif ($exists['price'] != $price || $exists['price_type'] != $priceType) {
                $this->_write->update($cospTable, [
                    'price' => $price,
                    'price_type' => $priceType,
                ], "option_type_price_id={$exists['option_type_price_id']}");
                $changed = true;
            }
        } elseif (null !== $price) {
            $this->_write->insert($cospTable, [
                'option_type_id' => $selection['option_type_id'],
                'store_id' => 0,
                'price' => $price,
                'price_type' => $priceType,
            ]);
            $changed = true;
        }
        return $changed ? self::IMPORT_ROW_RESULT_SUCCESS : self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPCOSL($row)
    {

        if (sizeof($row) < 6) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $pId = $this->_getIdBySku($row[1]);
        $optTitle = $this->_convertEncoding($row[2]);
        $selTitle = $this->_convertEncoding($row[3]);
        $storeId = $this->_getStoreId($row[4]);
        if ($this->_skipStore($storeId, 5)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $title = $row[5];
        $price = isset($row[6]) && $row[6] !== '' ? (float)$row[6] : null;
        $priceType = isset($row[7]) && $row[7] !== '' ? $row[7] : 'fixed';

        $option = $this->_getCustomOption($pId, $optTitle);
        if (!$option) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        }
        $selection = $this->_getCustomOptionSelection($option['option_id'], $selTitle);
        if (!$selection) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        }

        $changed = false;

        $costTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_TITLE);
//        $exists = $this->_write->fetchRow("select * from {$costTable}
//            where option_type_id={$selection['option_type_id']} and store_id={$storeId}");
        $exists = $this->_write->fetchRow(
            $this->_write->select()->from($costTable)
                ->where('option_type_id=?', $option['option_id'])->where('store_id=?', $storeId)
        );
        if ($exists) {
            if ($exists['title'] != $title) {
                $this->_write->update($costTable, [
                    'title' => $title
                ], "option_type_title_id={$exists['option_type_title_id']}");
                $changed = true;
            }
        } else {
            $this->_write->insert($costTable, [
                'option_type_id' => $selection['option_type_id'],
                'store_id' => $storeId,
                'title' => $title,
            ]);
            $changed = true;
        }

        $cospTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_PRICE);
//        $exists = $this->_write->fetchRow("select * from {$cospTable}
//            where option_type_id={$selection['option_type_id']} and store_id={$storeId}");

        $exists = $this->_write->fetchRow(
            $this->_write->select()->from($cospTable)
                ->where('option_type_id=?', $option['option_id'])->where('store_id=?', $storeId));
        if ($exists) {
            if (null === $price) {
                $this->_write->delete($cospTable, "option_type_price_id={$exists['option_type_price_id']}");
                $changed = true;
            } elseif ($exists['price'] != $price || $exists['price_type'] != $priceType) {
                $this->_write->update($cospTable, [
                    'price' => $price,
                    'price_type' => $priceType,
                ], "option_type_price_id={$exists['option_type_price_id']}");
                $changed = true;
            }
        } elseif (null !== $price) {
            $this->_write->insert($cospTable, [
                'option_type_id' => $selection['option_type_id'],
                'store_id' => $storeId,
                'price' => $price,
                'price_type' => $priceType,
            ]);
            $changed = true;
        }

        return $changed ? self::IMPORT_ROW_RESULT_SUCCESS : self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPSA($row)
    {

        if (sizeof($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $hasSuperAttrRowId = $this->_rapidFlowHelper->hasMageFeature(self::SUPER_ATTR_ROW_ID);
        $rowId = $pId = $this->_getIdBySku($row[1]);
        if (!$hasSuperAttrRowId) {
            $pId = $this->_getSeqIdBySku($row[1]);
        }
        $aId = $this->_getAttributeId($row[2]);
        $pos = $row[3];
        $label = $this->_convertEncoding($row[4]);

        $superAttrTable = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_ATTRIBUTE);
        $superLabelTable = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL);

        $changed = false;

//        $exists = $this->_write->fetchRow("select sa.*, sal.value_id, sal.value label from {$superAttrTable} sa
//            inner join {$superLabelTable} sal on sal.product_super_attribute_id=sa.product_super_attribute_id
//            where sa.product_id={$pId} and sa.attribute_id={$aId} and sal.store_id=0");

        $exists = $this->_write->fetchRow(
            $this->_write->select()
                ->from(['sa' => $superAttrTable])
                ->join(['sal' => $superLabelTable], 'sal.product_super_attribute_id=sa.product_super_attribute_id', ['value_id', 'label' => 'value'])
                ->where('sa.product_id=?', $pId)->where('sa.attribute_id=? AND sal.store_id=0', $aId)
        );

        if ($exists) {
            if ($exists['position'] != $pos) {
                $this->_write->update($superAttrTable, [
                    'position' => $pos,
                ], "product_super_attribute_id={$exists['product_super_attribute_id']}");
                $changed = true;
            }
            if ($exists['label'] != $label) {
                $this->_write->update($superLabelTable, [
                    'value' => $label,
                ], "value_id={$exists['value_id']}");
                $changed = true;
            }
        } else {
            $this->_write->insert($superAttrTable, [
                'product_id' => $pId,
                'attribute_id' => $aId,
                'position' => $pos,
            ]);
            $saId = $this->_write->lastInsertId($superAttrTable);
            $this->_write->insert($superLabelTable, [
                'product_super_attribute_id' => $saId,
                'store_id' => 0,
                'value' => $label,
            ]);
            $changed = true;
        }

        if ($changed) {
            $this->_newRefreshHoRoPids[$rowId] = 1;
        }

        return $changed ? self::IMPORT_ROW_RESULT_SUCCESS : self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPSAL($row)
    {

        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $pId = $this->_getSeqIdBySku($row[1]);
        $aId = $this->_getAttributeId($row[2]);
        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $label = isset($row[4]) && $row[4] !== '' ? $this->_convertEncoding($row[4]) : null;

        $superAttrTable = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_ATTRIBUTE);
        $superLabelTable = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL);

//        $exists = $this->_write->fetchRow("select sal.*, sa.product_super_attribute_id from {$superAttrTable} sa
//            left join {$superLabelTable} sal on sal.product_super_attribute_id=sa.product_super_attribute_id
//                and sal.store_id={$storeId}
//            where sa.product_id={$pId} and sa.attribute_id={$aId}");

        $onClause = $this->_write->quoteInto('sal.product_super_attribute_id=sa.product_super_attribute_id AND sal.store_id=?',
                                             $storeId);
        $exists = $this->_write->fetchRow(
            $this->_write->select()
                ->from(['sa' => $superAttrTable], ['super_attribute_id' => 'product_super_attribute_id'])
                ->joinLeft(['sal' => $superLabelTable], $onClause, '*')
                ->where('sa.product_id=?', $pId)->where('sa.attribute_id=?', $aId)->where('sal.store_id=?', $storeId)
        );

        if (!$exists) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        }
        $changed = false;
        if (null !== $exists['value_id']) {
            if (null === $label) {
                $this->_write->delete($superLabelTable, "value_id={$exists['value_id']}");
                $changed = true;
            } elseif ($exists['value'] != $label) {
                $this->_write->update($superLabelTable, [
                    'value' => $label,
                ], "value_id={$exists['value_id']}");
                $changed = true;
            }
        } elseif (null !== $label) {
            $this->_write->insert($superLabelTable, [
                'product_super_attribute_id' => $exists['super_attribute_id'],
                'store_id' => $storeId,
                'value' => $label,
            ]);
            $changed = true;
        }
        return $changed ? self::IMPORT_ROW_RESULT_SUCCESS : self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPSI($row)
    {

        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_LINK);

        if ($row[1]==$row[2]) {
            throw new LocalizedException(__('Cannot link product with itself'));
        }

        $p1 = $this->_getIdBySku($row[1]);
        $p2 = $this->_getSeqIdBySku($row[2]);

//        $linkId = $this->_write->fetchOne("SELECT link_id FROM {$t} WHERE parent_id='{$p1}' AND product_id='{$p2}'");
        $linkId = $this->_write->fetchOne(
            $this->_write->select()
                ->from($t, ['link_id'])
                ->where('parent_id=?', $p1)->where('product_id=?', $p2)
        );
        if (!$linkId) {
            $this->_write->insert($t, ['parent_id' => $p1, 'product_id' => $p2]);
            $relTable = $this->_t(self::TABLE_CATALOG_PRODUCT_RELATION);
//                if (!$this->_write->fetchOne("SELECT parent_id FROM {$relTable} WHERE parent_id={$p1} AND child_id={$p2}")) {
            $select = $this->_write->select()
                ->from($relTable, ['parent_id'])
                ->where('parent_id=?', $p1)->where('child_id=?', $p2);
            if (!$this->_write->fetchOne($select)
            ) {
                $this->_write->insert($relTable, ['parent_id' => $p1, 'child_id' => $p2]);
            }
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _updateMediaLabel($tGalleryValue, $valueId, $entityIdField, $productId, $label, $position, $disabled, $storeId = 0)
    {
//            $lbl = $this->_write->fetchRow("SELECT * FROM {$tv} WHERE `value_id`={$imgId} AND `{$entityId}` = {$pId} AND store_id=0");

        $select = $this->_write->select()
            ->from($tGalleryValue)
            ->where('value_id=?', $valueId)
            ->where($entityIdField . '=?', $productId)
            ->where('store_id=?', $storeId);

        $lbl = $this->_write->fetchRow($select);
        if (!$lbl) {
            $lbl = [
                'value_id' => $valueId,
                $entityIdField => $productId,
                'store_id' => $storeId,
                'label' => !empty($label) ? $this->_convertEncoding($label) : '',
                'position' => $position,
                'disabled' => $disabled,
            ];
            $this->_write->insert($tGalleryValue, $lbl);
            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ((!empty($label) && ($lbl['label'] !== $label))
            || (!empty($position) && ((int) $lbl['position'] !== (int) $position))
            || ((int)$lbl['disabled'] !== (int) $disabled)
        ) {
            $lbl = [
                'label' => !empty($label) ? $label : '',
                'position' => $position,
                'disabled' => $disabled,
            ];
            $this->_write->update($tGalleryValue, $lbl, "value_id={$valueId} AND `{$entityIdField}` = {$productId} AND store_id={$storeId}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _updateMediaProductRelation($tGalleryValueEntity, $valueId, $entityIdField, $productId)
    {
        $rel = $this->_write->fetchRow($this->_write->select()
                                           ->from($tGalleryValueEntity)
                                           ->where('value_id=?', $valueId)
                                           ->where($entityIdField . '=?', $productId));
        if (!$rel) {
            $this->_write->insert($tGalleryValueEntity, [
                'value_id' => $valueId,
                $entityIdField => $productId
            ]);
        }
    }

    protected function _updateMediaVideo($row, $tGalleryValueVideo, $valueId, $videoUrl, $storeId = 0)
    {
        $select = $this->_write->select()
            ->from($tGalleryValueVideo)
            ->where('value_id=?', $valueId)
            ->where('store_id=?', $storeId);
        $videoData = $this->_write->fetchRow($select);

        $title = (array_key_exists(5, $row) && $row[5]) ? $this->_convertEncoding($row[5]) : null;
        $provider = (array_key_exists(6, $row) && $row[6]) ? $this->_convertEncoding($row[6]) : null;
        $description = (array_key_exists(7, $row) && $row[7]) ? $this->_convertEncoding($row[7]) : null;
        $metaData = (array_key_exists(8, $row) && $row[8]) ? $this->_convertEncoding($row[8]) : null;

        if(!$videoData){
            // if record does not exist, add it
            $videoData = [
                'value_id' => $valueId,
                'store_id' => $storeId,
                'url' => $videoUrl,
                'title' => $title,
                'provider' => $provider,
                'description' => $description,
                'metadata' => $metaData,
            ];

            if ($this->_write->insert($tGalleryValueVideo, $videoData)) {
                return self::IMPORT_ROW_RESULT_SUCCESS;
            } else {
                $this->_profile->addValue('num_warnings');
                $this->_profile->getLogger()->warning('Failed to insert video data');
            }
        } else if($videoData['url'] !== $videoUrl || $videoData['title'] !== $title || $videoData['provider'] !== $provider ||
            $videoData['description'] !== $description || $videoData['metadata'] !== $metaData) {
            // if any imported data differs, just update the record
            $videoData = [
                'url' => $videoUrl,
                'title' => $title,
                'provider' => $provider,
                'description' => $description,
                'metadata' => $metaData,
            ];
            if ($this->_write->update($tGalleryValueVideo, $videoData, "value_id={$valueId} AND store_id={$storeId}")) {
                return self::IMPORT_ROW_RESULT_SUCCESS;
            } else {
                $this->_profile->addValue('num_warnings');
                $this->_profile->getLogger()->warning('Failed to update video data');
            }
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPV($row)
    {
        if (count($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $tGallery = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $tGalleryValue = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);
        $tGalleryValueEntity = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_ENTITY);
        $tGalleryValueVideo = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_VIDEO);

        $pId = $this->_getIdBySku($row[1]);
        $storeId = $this->_getStoreId($row[4], true);

        if ($this->_skipStore($storeId, 4, false)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        $videoUrl = $this->_convertEncoding($row[2]);
        $screenShotFileName = $this->_convertEncoding($row[3]);

        $result = self::IMPORT_ROW_RESULT_NOCHANGE;

        $screenShotFileChanged = false;
        if (!$this->_processImageFiles) {
            if ($this->_validateImageFile($screenShotFileName, $this->_imagesMediaDir)) {
                $screenShotFileChanged = true;
            }
        } elseif ($screenShotFileName !== '') {
            $this->_profile->getLogger()->setColumn(3);
            if ($this->_copyImageFile($this->_imagesTargetDir, $this->_imagesMediaDir, $screenShotFileName, true)) {
                $screenShotFileChanged = true;
                $result = self::IMPORT_ROW_RESULT_SUCCESS;
            }
        }

        $galleryAttrId = $this->_getGalleryAttrId();
        $entityIdField = $this->_entityIdField;

        $sql = $this->_write->select()->from(['mgv' => $tGalleryValue], ['value_id'])
            ->join(['mg' => $tGallery], 'mgv.value_id = mg.value_id', [])
            ->where("mgv.`{$entityIdField}`=?", $pId)
            ->where('mg.`attribute_id`=?', $galleryAttrId)
            ->where('BINARY mg.`value`=?', $screenShotFileName)
            ->where('mg.`media_type`=?', 'external-video');

        $valueId = $this->_write->fetchOne($sql);
        if (!$valueId && $screenShotFileChanged) {
            $img = ['attribute_id' => $galleryAttrId, 'media_type' => 'external-video', 'value' => $screenShotFileName];
            $this->_write->insert($tGallery, $img);
            $valueId = $this->_write->lastInsertId();
            $result = self::IMPORT_ROW_RESULT_SUCCESS;
        }

        if($valueId){
            $result = $this->_updateMediaVideo($row, $tGalleryValueVideo, $valueId, $videoUrl, $storeId);
            if ($result === self::IMPORT_ROW_RESULT_SUCCESS) {
                $position = !empty($row[9]) && $row[9] !== '0' ? (int)$row[9] : 1;
                $disabled = !empty($row[10]) ? (int)$row[10] : 0;
                $result = $this->_updateMediaLabel($tGalleryValue, $valueId, $entityIdField, $pId, '', $position,
                                                   $disabled, $storeId);
            }
            if ($result === self::IMPORT_ROW_RESULT_SUCCESS) {
                $this->_updateMediaProductRelation($tGalleryValueEntity, $valueId, $entityIdField, $pId);
            }
        }
        return $result;
    }

    protected function _importRowCPI($row)
    {
        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $tv = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);
        $tve = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_ENTITY);

        $pId = $this->_getIdBySku($row[1]);
        $imgFilename = $this->_convertEncoding($row[2]);
        $label = !empty($row[3]) ? $this->_convertEncoding($row[3]) : '';
        $position = !empty($row[4]) && $row[4] !== '0' ? (int)$row[4] : 1;
        $disabled = !empty($row[5]) ? (int)$row[5] : 0;
        $mediaType = !empty($row[6]) ? $row[6] : 'image';

        $result = self::IMPORT_ROW_RESULT_NOCHANGE;

        $imgFileChanged = false;
        if (!$this->_processImageFiles) {
            $this->_validateImageFile($imgFilename, $this->_imagesMediaDir);
            $imgFileChanged = true;
        } elseif ($imgFilename !== '') {
            $this->_profile->getLogger()->setColumn(3);
            if ($this->_copyImageFile($this->_imagesTargetDir, $this->_imagesMediaDir, $imgFilename, true)) {
                $imgFileChanged = true;
                $result = self::IMPORT_ROW_RESULT_SUCCESS;
            }
        }
        $galleryAttrId = $this->_getGalleryAttrId();
        $entityId = $this->_entityIdField;
//        $sql = "SELECT mgv.value_id
//FROM {$tv} mgv
//  JOIN {$t} mg ON mgv.value_id = mg.value_id
//WHERE mgv.`{$entityId}` = {$pId} AND mg.attribute_id = {$galleryAttrId} AND  BINARY mg.`value` = {$this->_write->quote($imgFilename)}";
        $sql = $this->_write->select()->from(['mgv'=>$tv], ['value_id'])
            ->join(['mg' => $t], 'mgv.value_id = mg.value_id')
            ->where("mgv.`{$entityId}`=?", $pId)
            ->where('mg.`attribute_id`=?', $galleryAttrId)->where('BINARY mg.`value`=?', $imgFilename);

        $imgId = $this->_write->fetchOne($sql);
        if (!$imgId && $imgFileChanged) {
            $img = ['attribute_id' => $galleryAttrId, 'media_type' => $mediaType, 'value' => $imgFilename];
            $this->_write->insert($t, $img);
            $imgId = $this->_write->lastInsertId();
            $result = self::IMPORT_ROW_RESULT_SUCCESS;
        }

        if ($imgId) {
            $result = $this->_updateMediaLabel($tv, $imgId, $entityId, $pId, $label, $position, $disabled);
            if ($result === self::IMPORT_ROW_RESULT_SUCCESS) {
                $this->_updateMediaProductRelation($tve, $imgId, $entityId, $pId);
            }
        }
        return $result;
    }

    protected function _importRowCPIL($row)
    {
        if (count($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $tv = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);

        $pId = $this->_getIdBySku($row[1]);
        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        $imgFilename = $this->_convertEncoding($row[2]);
        $label = !empty($row[4]) ? $this->_convertEncoding($row[4]) : '';
        $entityId = $this->_entityIdField;

        $sql = $this->_write->select()->from(['mgv' => $tv], ['value_id'])
                ->join(['mg' => $t], 'mgv.value_id = mg.value_id')
                ->where('mgv.' . $entityId . '=?', $pId)
                ->where('BINARY mg.`value`=?', $imgFilename);

        $imgId = $this->_write->fetchOne($sql);
        if (!$imgId) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        }

        $position = !empty($row[5]) && $row[5] !== '0' ? (int)$row[5] : 1;
        $disabled = !empty($row[6]) ? (int)$row[6] : 0;
        return $this->_updateMediaLabel($tv, $imgId, $entityId, $pId, $label, $position, $disabled, $storeId);
    }

    protected function _importRowCPVL($row)
    {
        if (count($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $tv = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);

        $pId = $this->_getIdBySku($row[1]);
        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        $imgFilename = $this->_convertEncoding($row[2]);
        $label = !empty($row[4]) ? $this->_convertEncoding($row[4]) : '';
        $entityId = $this->_entityIdField;

        $sql = $this->_write->select()->from(['mgv' => $tv], ['value_id'])
                ->join(['mg' => $t], 'mgv.value_id = mg.value_id')
                ->where('mgv.' . $entityId . '=?', $pId)
                ->where('BINARY mg.`value`=?', $imgFilename)
                ->where('mg.`media_type`=?', 'external-video');

        $imgId = $this->_write->fetchOne($sql);
        if (!$imgId) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        }

        $position = !empty($row[5]) && $row[5] !== '0' ? (int)$row[5] : 1;
        $disabled = !empty($row[6]) ? (int)$row[6] : 0;
        return $this->_updateMediaLabel($tv, $imgId, $entityId, $pId, $label, $position, $disabled, $storeId);
    }

    protected function _importRowCPPT($row)
    {
        if (count($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_TIER_PRICE);

        $pId = $this->_getIdBySku($row[1]);
        $allGroups = $row[2] === '*' ? 1 : 0;
        $gId = $allGroups ? 0 : (int)$this->_getCustomerGroup($this->_convertEncoding($row[2]), true);
        $qty = (float)$row[3];
        $price = (float)$row[4];
        $wId = !empty($row[5]) ? (int)$this->_getWebsiteId($row[5], true) : 0;
        $percent = !isset($row[6]) || empty($row[6]) ? null : (float)$row[6];
        $hasPercent = $this->_rapidFlowHelper->compareMageVer('2.2.0');
        $entityId = $this->_entityIdField;
//        $exists = $this->_write->fetchRow(
//            "SELECT *
//             FROM {$t} WHERE {$entityId}={$pId} AND customer_group_id={$gId} AND qty={$qty} AND website_id={$wId} AND all_groups={$allGroups}"
//        );
        $exists = $this->_write->fetchRow(
            $this->_write->select()->from($t)
                ->where($entityId . '=?', $pId)
                ->where(' customer_group_id=?', $gId)
                ->where(' qty=?', $qty)
                ->where(' website_id=?', $wId)
                ->where('all_groups=?', $allGroups)
        );
        if (!$exists) {
            $exists = [
                $entityId => $pId,
                'all_groups' => $allGroups,
                'customer_group_id' => $gId,
                'qty' => $qty,
                'value' => $price,
                'website_id' => $wId
            ];
            if ($hasPercent) {
                $exists['percentage_value'] = $percent;
            }
            $this->_write->insert($t, $exists);
            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ($exists['value'] != $price || ($hasPercent && $exists['percentage_value'] != $percent)) {
            $this->_write->update($t, ['value' => $price, 'percentage_value' => $percent], "value_id={$exists['value_id']}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPPG($row)
    {
        $this->_profile->getLogger()->error('Group pricing is not supported in Magento 2');
        return self::IMPORT_ROW_RESULT_NOCHANGE;
        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_GROUP_PRICE);

        $pId = $this->_getIdBySku($row[1]);
        $gId = (int)$this->_getCustomerGroup($this->_convertEncoding($row[2]), true);
        $price = (float)$row[3];
        $wId = !empty($row[4]) ? (int)$this->_getWebsiteId($row[4], true) : 0;

//        $exists = $this->_write->fetchRow(
//            "SELECT *
//FROM {$t} WHERE {$this->_entityIdField}={$pId} AND customer_group_id={$gId} AND website_id = {$wId}"
//        );
        $exists = $this->_write->fetchRow(
            $this->_write->select()->from($t)->where('entity_id=?', $pId)
                ->where(' customer_group_id=?', $gId)->where(' website_id=?', $wId)
        );
        if (!$exists) {
            $exists = [
                $this->_entityIdField => $pId,
                'customer_group_id' => $gId,
                'all_groups' => 0,
                'value' => $price,
                'website_id' => $wId
            ];
            $this->_write->insert($t, $exists);
            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ($exists['value'] != $price) {
            $this->_write->update($t, ['value' => $price], "value_id={$exists['value_id']}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPD($row)
    {
        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_DOWNLOADABLE_LINK);
        $tl = $this->_t(self::TABLE_DOWNLOADABLE_LINK_TITLE);
        $tp = $this->_t(self::TABLE_DOWNLOADABLE_LINK_PRICE);

        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);
        $price = isset($row[3]) ? (float)$row[3] : 0;

        $new = [
            'number_of_downloads' => isset($row[4]) ? (int)$row[4] : 0,
            'is_shareable' => !empty($row[5]) && in_array($row[5], [0, 1, 2]) ? $row[5] : 0,

            'sort_order' => !empty($row[6]) ? $row[6] : 0,
            'link_url' => !empty($row[7]) ? $row[7] : '',
            'link_file' => !empty($row[8]) ? $row[8] : '',
            'link_type' => !empty($row[9]) ? $row[9] : '',
            'sample_url' => !empty($row[10]) ? $row[10] : '',
            'sample_file' => !empty($row[11]) ? $row[11] : '',
            'sample_type' => !empty($row[12]) ? $row[12] : '',
        ];

        $updated = false;
//        $exists = $this->_write->fetchRow(
//            "SELECT t.*, tl.title_id, tl.title, tp.price_id, tp.price
//FROM {$t} t
//LEFT JOIN {$tl} tl ON tl.link_id=t.link_id AND tl.store_id=0
//LEFT JOIN {$tp} tp ON tp.link_id=t.link_id AND tp.website_id=0
//WHERE t.product_id={$pId} AND tl.title={$this->_write->quote($title)}"
//        );
        $exists = $this->_write->fetchRow(
            $this->_read->select()->from(['t' => $t])
                ->joinLeft(['tl' => $tl], 'tl.link_id=t.link_id and tl.store_id=0', ['title_id', 'title'])
                ->joinLeft(['tp' => $tp], 'tp.link_id=t.link_id and tp.website_id=0', ['price_id', 'price'])
                ->where('t.product_id=?', $pId)->where('tl.title=?', $title)
        );
        if (!$exists) {
            $new['product_id'] = $pId;
            $this->_write->insert($t, $new);
            $exists = ['link_id' => $this->_write->lastInsertId()];
            $updated = true;
        } elseif ($this->_isChangeRequired($exists, $new)) {
            $this->_write->update($t, $new, "link_id={$exists['link_id']}");
            $updated = true;
        }

        if (empty($exists['title_id'])) {
            $this->_write->insert($tl, ['link_id' => $exists['link_id'], 'store_id' => 0, 'title' => $title]);
            $updated = true;
        } elseif ($exists['title'] != $title) {
            $this->_write->update($tl, ['title' => $title], "title_id={$exists['title_id']}");
            $updated = true;
        }

        if (empty($exists['price_id'])) {
            $this->_write->insert($tp, ['link_id' => $exists['link_id'], 'website_id' => 0, 'price' => $price]);
            $updated = true;
        } elseif ($exists['price'] != $price) {
            $this->_write->update($tp, ['price' => $price], "price_id={$exists['price_id']}");
            $updated = true;
        }

        return $updated ? self::IMPORT_ROW_RESULT_SUCCESS : self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPDL($row)
    {
        if (sizeof($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_DOWNLOADABLE_LINK);
        $tl = $this->_t(self::TABLE_DOWNLOADABLE_LINK_TITLE);

        $defTitle = $this->_convertEncoding($row[2]);
        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $title = $this->_convertEncoding($row[4]);

//        $exists = $this->_write->fetchRow(
//            "SELECT t.link_id, tl.title_id, tl.title
//FROM {$t} t
//INNER JOIN {$tl} td ON td.link_id=t.link_id AND td.store_id=0 AND td.title={$this->_write->quote($defTitle)}
//LEFT JOIN {$tl} tl ON tl.link_id=t.link_id AND tl.store_id={$storeId}"
//        );
        $exists = $this->_write->fetchRow(
            $this->_read->select()->from(['t' => $t], ['link_id'])
                ->join(['td' => $tl], 'td.link_id=t.link_id and td.store_id=0', null)
                ->joinLeft(['tl' => $tl], 'tl.link_id=t.link_id', ['title_id', 'title'])
                ->where('td.title=?', $title)->where('tl.store_id=?', $storeId)
        );

        if (!$exists) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        } elseif (!$exists['title_id']) {
            $this->_write->insert($tl,
                                  ['link_id' => $exists['link_id'], 'store_id' => $storeId, 'title' => $title]);
            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ($exists['title'] != $title) {
            $this->_write->update($tl, ['title' => $title], "title_id={$exists['title_id']}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPDP($row)
    {
        if (sizeof($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_DOWNLOADABLE_LINK);
        $tl = $this->_t(self::TABLE_DOWNLOADABLE_LINK_TITLE);
        $tp = $this->_t(self::TABLE_DOWNLOADABLE_LINK_PRICE);

        $defTitle = $this->_convertEncoding($row[2]);
        $websiteId = $this->_getWebsiteId($row[3]);
        $price = $row[4];
        if (!$websiteId) {
            $this->_profile->getLogger()->setColumn(4);
            throw new LocalizedException(__('Invalid website'));
        }
//        $exists = $this->_write->fetchRow(
//            "SELECT t.link_id, tl.title_id, tl.title
//FROM {$t} t
//INNER JOIN {$tl} td ON td.link_id=t.link_id AND td.store_id=0 AND td.title={$this->_write->quote($defTitle)}
//LEFT JOIN {$tp} tp ON tp.link_id=t.link_id AND tp.website_id={$websiteId}"
//        );
        $exists = $this->_write->fetchRow(
            $this->_write->select()->from(['t' => $t], ['link_id'])
                ->join(['td' => $tl], 'td.link_id=t.link_id and td.store_id=0', [])
                ->joinLeft(['tp' => $tp], 'tp.link_id=t.link_id', ['title_id', 'title'])
                ->where('td.title=?', $defTitle)
                ->where('tp.website_id=?', $websiteId)
        );

        if (!$exists) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        } elseif (!$exists['price_id']) {
            $this->_write->insert($tp, [
                'link_id' => $exists['link_id'],
                'website_id' => $websiteId,
                'price' => $price
            ]);
            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ($exists['price'] != $price) {
            $this->_write->update($tp, ['price' => $price], "price_id={$exists['price_id']}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPDS($row)
    {
        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE);
        $tl = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE_TITLE);

        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);

        $new = [
            'sort_order' => !empty($row[3]) ? $row[3] : 0,
            'sample_url' => !empty($row[4]) ? $row[4] : '',
            'sample_file' => !empty($row[5]) ? $row[5] : '',
            'sample_type' => !empty($row[6]) ? $row[6] : '',
        ];

        $updated = false;
//        $exists = $this->_write->fetchRow(
//            "SELECT t.*, tl.title_id, tl.title
//FROM {$t} t
//LEFT JOIN {$tl} tl ON tl.sample_id=t.sample_id AND tl.store_id=0
//WHERE t.product_id={$pId} AND tl.title={$this->_write->quote($title)}"
//        );
        $exists = $this->_write->fetchRow($this->_write->select()
                           ->from(['t' => $t])
                           ->join(['tl' => $tl], 'tl.sample_id=t.sample_id and tl.store_id=0', ['title_id', 'title'])
                           ->where('t.product_id=?', $pId)
                           ->where('tl.title=?', $title));
        if (!$exists) {
            $new['product_id'] = $pId;
            $this->_write->insert($t, $new);
            $exists = ['sample_id' => $this->_write->lastInsertId()];
            $updated = true;
        } elseif ($this->_isChangeRequired($exists, $new)) {
            $this->_write->update($t, $new, "sample_id={$exists['sample_id']}");
            $updated = true;
        }

        if (empty($exists['title_id'])) {
            $this->_write->insert($tl, ['sample_id' => $exists['sample_id'], 'store_id' => 0, 'title' => $title]);
            $updated = true;
        } elseif ($exists['title'] != $title) {
            $this->_write->update($tl, ['title' => $title], "title_id={$exists['title_id']}");
            $updated = true;
        }

        return $updated ? self::IMPORT_ROW_RESULT_SUCCESS : self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowCPDSL($row)
    {
        if (sizeof($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE);
        $tl = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE_TITLE);


        $defTitle = $this->_convertEncoding($row[2]);
        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $title = $this->_convertEncoding($row[4]);

//        $exists = $this->_write->fetchRow(
//            "SELECT t.sample_id, tl.title_id, tl.title
//FROM {$t} t
//INNER JOIN {$tl} td ON td.sample_id=t.sample_id AND td.store_id=0 AND td.title={$this->_write->quote($defTitle)}
//LEFT JOIN {$tl} tl ON tl.sample_id=t.sample_id AND tl.store_id={$storeId}"
//        );
        $select = $this->_read->select()
            ->from(['t' => $t], 'sample_id')
            ->join(['td' => $tl], 'td.sample_id=t.sample_id AND td.store_id=0', [])
            ->joinLeft(['tl' => $tl], 'tl.sample_id=t.sample_id', ['title_id', 'title'])
            ->where('td.title=?', $defTitle)
            ->where('tl.store_id=?', $storeId);

        $exists = $this->_write->fetchRow($select);
        if (!$exists) {
            return self::IMPORT_ROW_RESULT_DEPENDS;
        } elseif (!$exists['title_id']) {
            $this->_write->insert($tl, [
                'sample_id' => $exists['sample_id'],
                'store_id' => $storeId,
                'title' => $title
            ]);
            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ($exists['title'] != $title) {
            $this->_write->update($tl, ['title' => $title], "title_id={$exists['title_id']}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _renameRowCP($row)
    {

        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $oldSku = $row[1];
        $newSku = $row[2];
        if ($oldSku === $newSku) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $pId = $this->_getIdBySku($oldSku);
        $this->_write->update($this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY), ['sku' => $newSku],
                              "{$this->_entityIdField}={$pId}");

        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _renameRowCPCO($row)
    {
        return self::IMPORT_ROW_RESULT_ERROR;
    }

    protected function _renameRowCPCOS($row)
    {

        return self::IMPORT_ROW_RESULT_ERROR;
    }

    protected function _renameRowCPSA($row)
    {

        return self::IMPORT_ROW_RESULT_ERROR;
    }

    protected function _renameRowCPD($row)
    {

        return self::IMPORT_ROW_RESULT_ERROR;
    }

    protected function _renameRowCPDS($row)
    {

        return self::IMPORT_ROW_RESULT_ERROR;
    }

    protected function _renameRowCPBO($row)
    {

        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $pId = $this->_getIdBySku($row[1]);
        $oldTitle = $row[2];
        $newTitle = $row[3];
        if ($oldTitle === $newTitle) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $option = $this->_getBundleOption($pId, $oldTitle);
        if (!$option) {
            $this->_profile->getLogger()->setColumn(3);
            throw new LocalizedException('Bundle option not found (' . $row[1] . ':' . $row[2] . ')');
        }
        $this->_write->update($this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE), ['title' => $newTitle],  "value_id={$option['value_id']}");

        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _deleteRowCP($row)
    {
        if (sizeof($row) < 2) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
//        $entityId = $this->_write->fetchOne("SELECT {$this->_entityIdField} FROM {$t} WHERE sku=?", $row[1]);

        $p1 = $this->_getIdBySku($row[1]);
        $p2 = $this->_getSeqIdBySku($row[1]);

        if (!$p1) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $this->_write->delete($t, "{$this->_entityIdField}={$p1}");

        $this->_deleteProductUrlRewrite($p2,0);
        foreach ($this->_storeManager->getStores() as $sId => $store) {
            $this->_deleteProductUrlRewrite($p2,$sId);
        }

        unset($this->_skus[$row[1]]);

        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _deleteProductUrlRewrite($pId,$sId)
    {
        $this->__urlPersist()->deleteByData(
            [
                UrlRewrite::ENTITY_ID => $pId,
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::REDIRECT_TYPE => 0,
                UrlRewrite::STORE_ID => $sId
            ]
        );
    }

    /**
     * @return \Magento\UrlRewrite\Model\UrlPersistInterface
     */
    protected function __urlPersist()
    {
        return $this->_rapidFlowHelper->om()->get('Unirgy\RapidFlow\Model\UrlRewriteDbStorage');
    }

    protected $_categoryUrlSuffix;

    protected $_categoryUrlSuffixLen;

    protected $_categoryUrlPathAttrId;

    protected $_categoryNameAttrId;

    protected $_rootCatPaths = [];

    protected function _fetchCategoryRow($urlPath)
    {
        $t = $this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY);
        if (!$this->_urlPaths) {

            $this->_upPrependRoot = $this->_profile->getData('options/' . $this->_profile->getProfileType() . '/urlpath_prepend_root');

            $storeId = $this->_profile->getStoreId();
            if (null === $this->_categoryUrlSuffix) {
                $this->_categoryUrlSuffix = $this->_scopeConfig->getValue('catalog/seo/category_url_suffix',
                                                                          ScopeInterface::SCOPE_STORE, $storeId);
                $this->_categoryUrlSuffixLen = strlen($this->_categoryUrlSuffix);
                $this->_categoryUrlPathAttrId = $this->_getAttributeId('url_path', 'catalog_category');
                $this->_categoryNameAttrId = $this->_getAttributeId('name', 'catalog_category');
            }

            if ($storeId) {
                $this->_rootCatId = $this->_storeManager->getStore($storeId)->getGroup()->getRootCategoryId();
            } elseif (!$this->_upPrependRoot) {
                //$sql = sprintf('SELECT g.root_category_id FROM `%s` w INNER JOIN `%s` g ON g.group_id=w.default_group_id WHERE w.is_default=1',
                //    $this->_t('core/website'), $this->_t('core/store_group'));
                $sql = $this->_write->select()
                    ->from(['w' => $this->_t(self::TABLE_STORE_WEBSITE)], [])
                    ->join(['g' => $this->_t(self::TABLE_STORE_GROUP)], 'g.group_id=w.default_group_id', 'root_category_id')
                    ->where('w.is_default=1');
                $this->_rootCatId = $this->_read->fetchOne($sql);
            }
            $rootPath = $this->_rootCatId ? '1/' . $this->_rootCatId : '1';

            $entityId = $this->_entityIdField;
            if ($this->_upPrependRoot) {
                $rootCatPathsSel = $this->_read->select()
                    ->from(['g' => $this->_t(self::TABLE_STORE_GROUP)], [])
                    ->join(['e' => $t], "e.{$entityId}=g.root_category_id", ["concat('1/',e.{$entityId})"])
                    ->join(['name' => $t . '_varchar'], "name.{$entityId}=e.{$entityId}
                        and name.attribute_id={$this->_categoryNameAttrId}
                        and name.value<>''
                        and name.value is not null
                        and name.store_id=0",
                           ['value'])
                    ->group("e.{$entityId}");
                if ($storeId) {
                    $rootCatPathsSel->where("e.{$entityId}=?", $this->_rootCatId);
                }
                $this->_rootCatPaths = $this->_read->fetchPairs($rootCatPathsSel);
            }

            $select = $this->_write->select()
                ->from(['e' => $t], [$entityId, 'path', 'entity_id'])
                ->joinLeft(['v' => $t . '_varchar'],
                           "v.{$entityId}=e.{$entityId}
                                    and v.attribute_id={$this->_categoryUrlPathAttrId}
                                    and v.store_id in (0, {$storeId})", ['url_path' => 'value'])
                ->order('v.store_id desc');

            if ($this->_upPrependRoot && !empty($this->_rootCatPaths)) {
                $_rcPaths = [];
                foreach ($this->_rootCatPaths as $_rcPath => $_rcName) {
                    $_rcPaths[] = $this->_read->quoteInto('path=?', $_rcPath);
                    $_rcPaths[] = $this->_read->quoteInto('path like ?', $_rcPath . '/%');
                }
                $select->where(implode(' OR ', $_rcPaths));
            } else {
                $select->where(
                    $this->_read->quoteInto('path=?', $rootPath)
                    . $this->_read->quoteInto(' OR path like ?', $rootPath . '/%')
                );
            }
            $this->_logger->debug((string)$select);
            $rows = $this->_write->fetchAll($select);
            $entities = [];
            $row2Entity = [];
            $entity2Row = [];
            foreach ($rows as $r) {
                $entities[$r[$entityId]] = $r;
                $row2Entity[$r[$entityId]] = $r['entity_id'];
                $entity2Row[$r['entity_id']] = $r[$entityId];
            }
            foreach ($rows as $r) {
                if (!empty($this->_urlPaths[$r['url_path']])) {
                    continue;
                }
                $r['url_path'] = $this->_upPrependRoot($r, $r['url_path']);
                $adjUrlPath = substr($r['url_path'], -$this->_categoryUrlSuffixLen) === $this->_categoryUrlSuffix
                    ? substr($r['url_path'], 0, strlen($r['url_path']) - $this->_categoryUrlSuffixLen)
                    : $r['url_path'] . $this->_categoryUrlSuffix;

                $data = [$entityId => $r[$entityId], 'path' => $r['path']];
                $this->_urlPaths[$r['url_path']] = $data;
                $this->_urlPaths[$adjUrlPath] = $data;
                $this->_catIds[(int)$r[$entityId]] = $data;
            }
            $this->_catEntity2Row = $entity2Row;
            $this->_catRow2Entity = $row2Entity;
        }
        if (is_numeric($urlPath)) {
            if (empty($this->_catIds[(int)$urlPath])) {
                return false;
            }
            return $this->_catIds[(int)$urlPath];
        }
        return !empty($this->_urlPaths[$urlPath]) ? $this->_urlPaths[$urlPath] : false;
    }

    public function catRowIdBySeqId($seqId)
    {
        return @$this->_catEntity2Row[$seqId];
    }
    public function catSeqIdByRowId($rowId)
    {
        return @$this->_catRow2Entity[$rowId];
    }

    protected function _deleteRowCC($row)
    {

        if (sizeof($row) < 2) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $t = $this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY);

        $category = $this->_fetchCategoryRow($row[1]);

        if ($category === false) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        $this->_write->delete($t,
                              "{$this->_entityIdField}={$category[$this->_entityIdField]} OR path LIKE '{$category['path']}/%'");

        unset($this->_urlPaths[$row[1]], $this->_catIds[$category[$this->_entityIdField]]);

        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _deleteRowCCP($row)
    {

        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $t = $this->_t(self::TABLE_CATALOG_CATEGORY_PRODUCT);

        $entityIdField = $this->_entityIdField;
        $cWild = trim($row[1]) === '*';
        $pWild = trim($row[2]) === '*';
        $category = $this->_fetchCategoryRow($row[1]);
        //$pId = !$pWild ? $this->_getIdBySku($row[2]) : null;
        $pId = !$pWild ? $this->_getSeqIdBySku($row[2]) : null;
        if (!$category && !$pId || !$category && $pId && !$cWild || !$pId && $category && !$pWild) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        if (!isset($category['entity_id']) && isset($category[$entityIdField])) {
            $category['entity_id'] = $this->catSeqIdByRowId($category[$entityIdField]);
        }
        $cId = $category['entity_id'];

        $dWhere = '1';
        if (!$cWild) $dWhere .= " AND category_id='{$cId}'";
        if (!$pWild) $dWhere .= " AND product_id='{$pId}'";
        $this->_write->delete($t, $dWhere);
        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _deleteLink($row, $linkType)
    {

        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_LINK);
        $p1 = $this->_getIdBySku($row[1]);
        $p2 = $this->_getSeqIdBySku($row[2]);
        $lt = $this->_getLinkTypeId($linkType);
//        $linkId = $this->_write->fetchOne(
//            "SELECT link_id
//FROM {$t}
//WHERE product_id={$p1} AND linked_product_id={$p2} AND link_type_id = {$lt}"
//        );
        $linkId = $this->_write->fetchOne($this->_write
                                              ->select()
                                              ->from($t, 'link_id')
                                              ->where('product_id=?', $p1)
                                              ->where('linked_product_id=?', $p2)
                                              ->where('link_type_id=?', $lt));

        if (!$linkId) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        if ($linkType === 'super') {
            $this->_write->delete($this->_t(self::TABLE_CATALOG_PRODUCT_RELATION), "(parent_id={$p1} AND child_id={$p2})");
        }
        $this->_write->delete($t, "link_id={$linkId}");
        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _deleteRowCPRI($row)
    {
        return $this->_deleteLink($row, 'relation');
    }

    protected function _deleteRowCPUI($row)
    {
        return $this->_deleteLink($row, 'up_sell');
    }

    protected function _deleteRowCPXI($row)
    {
        return $this->_deleteLink($row, 'cross_sell');
    }

    protected function _deleteRowCPGI($row)
    {
        return $this->_deleteLink($row, 'super');
    }

    protected function _deleteRowCPBO($row)
    {

        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION);
        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);
        $option = $this->_getBundleOption($pId, $title);
        if (!$option) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $this->_write->delete($t, "option_id={$option['option_id']}");
        unset($this->_bundleOptions[$pId][$title]);

        $this->_newRefreshHoRoPids[$pId] = 1;

        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _deleteRowCPBOL($row)
    {

        if (count($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE);
        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);

        $option = $this->_getBundleOption($pId, $title);
        if (!$option) {
            $this->_profile->getLogger()->setColumn(3);
            throw new LocalizedException(__('Invalid option (%1: %2)', $row[1], $row[2]));
        }

        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
//        $valueId = $this->_write->fetchOne("select value_id from {$t}
//            where option_id={$option['option_id']} and store_id={$storeId}");
        $valueId = $this->_write->fetchOne($this->_write->select()->from($t, ['store_id'])
                        ->where('option_id=?', $option['option_id'])->where(' store_id=?', $storeId));
        if (!$valueId) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $this->_write->delete($t, "value_id={$valueId}");
        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _deleteRowCPBOS($row)
    {

        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_SELECTION);
        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);

        $option = $this->_getBundleOption($pId, $title);
        if (!$option) {
            $this->_profile->getLogger()->setColumn(3);
            throw new LocalizedException(__('Invalid option (%1: %2)', $row[1], $row[2]));
        }

        $sId = $this->_getSeqIdBySku($row[3]);
//        $selectionId = $this->_write->fetchOne(
//            "SELECT selection_id
//FROM {$t}
//WHERE option_id='{$option['option_id']}' AND product_id='{$sId}'"
//        );
        $selectionId = $this->_write->fetchOne($this->_write->select()
                                                   ->from($t, ['selection_id'])
                                                   ->where('option_id=?', $option['option_id'])->where(' product_id=?', $sId));
        if (!$selectionId) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $this->_write->delete($t, "selection_id={$selectionId}");
        $this->_write->delete($this->_t(self::TABLE_CATALOG_PRODUCT_RELATION),
                              "parent_id='{$pId}' and child_id='{$sId}'");
        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _deleteRowCPBOSL($row)
    {
        if (count($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $bosTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_SELECTION);
        $bospTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_SELECTION_PRICE);
        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);
        $wId = (int)$this->_getWebsiteId($row[4], true);

        $option = $this->_getBundleOption($pId, $title);
        if (!$option) {
            $this->_profile->getLogger()->setColumn(3);
            throw new LocalizedException(__('Invalid option (%1: %2)', $pId, $title));
        }

        $sId = $this->_getSeqIdBySku($row[3]);
        $selectionId = $this->_write->fetchOne(
            $this->_write->select()
                ->from(['cpbs' => $bosTable], ['selection_id'])
                ->join(['cpbsp' => $bospTable], 'cpbsp.`selection_id`=cpbs.`selection_id`', null)
                ->where('option_id=?', $option['option_id'])
                ->where('product_id=?', $sId));
        if (!$selectionId) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $this->_write->delete($bospTable, "selection_id={$selectionId} AND " . $this->_write->quoteInto('website_id=?', $wId));
        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _deleteRowCPCO($row)
    {

        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION);
        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);
        $option = $this->_getCustomOption($pId, $title);
        if (!$option) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $this->_write->delete($t, "option_id={$option['option_id']}");

        unset($this->_customOptions[$pId][$title]);
        unset($this->_customOptionSelections[$option['option_id']]);

        $this->_newRefreshHoRoPids[$pId] = 1;

        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _deleteRowCPCOL($row)
    {

        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);

        $option = $this->_getCustomOption($pId, $title);
        if (!$option) {
            $this->_profile->getLogger()->setColumn(3);
            throw new LocalizedException(__('Invalid option (%1: %2)', $row[1], $row[2]));
        }

        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $changed = false;

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TITLE);
//        $valueId = $this->_write->fetchOne(
//            "SELECT value_id
//FROM {$t}
//WHERE option_id={$option['option_id']} AND store_id = {$storeId}"
//        );
        $valueId = $this->_write->fetchOne($this->_write->select()->from($t, 'value_id')
                                               ->where('option_id=?', $option['option_id'])->where(' store_id=?', $storeId));
        if ($valueId) {
            $this->_write->delete($t, "option_title_id={$valueId}");
            $changed = true;
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_PRICE);
//        $valueId = $this->_write->fetchOne(
//            "SELECT value_id
//FROM {$t}
//WHERE option_id={$option['option_id']} AND store_id = {$storeId}"
//        );
        $valueId = $this->_write->fetchOne($this->_write->select()->from($t, 'value_id')
                                               ->where('option_id=?', $option['option_id'])->where(' store_id=?', $storeId));
        if ($valueId) {
            $this->_write->delete($t, "option_price_id={$valueId}");
            $changed = true;
        }

        return $changed ? self::IMPORT_ROW_RESULT_SUCCESS : self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPCOS($row)
    {

        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $pId = $this->_getIdBySku($row[1]);
        $optTitle = $this->_convertEncoding($row[2]);
        $selTitle = $this->_convertEncoding($row[3]);

        $option = $this->_getCustomOption($pId, $optTitle);
        if (!$option) {
            $this->_profile->getLogger()->setColumn(3);
            throw new LocalizedException(__('Invalid option (%1: %2)', $row[1], $row[2]));
        }

        $selection = $this->_getCustomOptionSelection($option['option_id'], $selTitle);
        if (!$selection) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_VALUE);
        $this->_write->delete($t, "option_type_id={$selection['option_type_id']}");

        unset($this->_customOptionSelections[$option['option_id']][$selTitle]);

        return self::IMPORT_ROW_RESULT_SUCCESS;
    }

    protected function _deleteRowCPCOSL($row)
    {

        if (sizeof($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $pId = $this->_getIdBySku($row[1]);
        $optTitle = $this->_convertEncoding($row[2]);
        $selTitle = $this->_convertEncoding($row[3]);
        $storeId = $this->_getStoreId($row[4]);
        if ($this->_skipStore($storeId, 5)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        $option = $this->_getCustomOption($pId, $optTitle);
        if (!$option) {
            $this->_profile->getLogger()->setColumn(3);
            throw new LocalizedException(__('Invalid option (%1: %2)', $row[1], $row[2]));
        }

        $selection = $this->_getCustomOptionSelection($option['option_id'], $selTitle);
        if (!$selection) {
            $this->_profile->getLogger()->setColumn(4);
            throw new LocalizedException(__('Invalid selection (%1: %2: %3)', $row[1], $row[2], $row[3]));
        }

        $changed = false;

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_TITLE);
//        $valueId = $this->_write->fetchOne(
//            "SELECT option_type_title_id
//FROM {$t}
//WHERE option_type_id={$selection['option_type_id']} AND store_id = {$storeId}"
//        );
        $valueId = $this->_write->fetchOne($this->_write->select()->from($t, 'option_type_title_id')
                                               ->where('option_type_id=?', $selection['option_type_id'])->where(' store_id=?', $storeId));
        if ($valueId) {
            $this->_write->delete($t, "option_type_title_id={$valueId}");
            $changed = true;
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_PRICE);
//        $valueId = $this->_write->fetchOne(
//            "SELECT option_type_price_id
//FROM {$t}
//WHERE option_type_id={$selection['option_type_id']} AND store_id = {$storeId}"
//        );
        $valueId = $this->_write->fetchOne($this->_write->select()->from($t, 'option_type_price_id')
                                               ->where('option_type_id=?', $selection['option_type_id'])->where(' store_id=?', $storeId));
        if ($valueId) {
            $this->_write->delete($t, "option_type_price_id={$valueId}");
            $changed = true;
        }

        return $changed ? self::IMPORT_ROW_RESULT_SUCCESS : self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPSA($row)
    {

        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $pId = $this->_getIdBySku($row[1]);
        if (!($aWild = trim($row[2]) == '*')) {
            $aId = $this->_getAttributeId($row[2]);
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_ATTRIBUTE);

//        $saId = $this->_write->fetchCol("SELECT product_super_attribute_id FROM {$t}
//            WHERE product_id={$pId} " . (!$aWild ? "AND attribute_id={$aId}" : ''));
        $select = $this->_write->select()->from($t, 'product_super_attribute_id')->where('product_id=?', $pId);
        if (!$aWild && isset($aId)) {
            $select->where('attribute_id=?', $aId);
        }
        $saId = $this->_write->fetchCol($select);
        if (!empty($saId)) {
            $this->_write->delete($t, $this->_write->quoteInto('product_super_attribute_id IN (?)', $saId));
            $this->_newRefreshHoRoPids[$pId] = 1;
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPSAL($row)
    {

        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $pId = $this->_getIdBySku($row[1]);
        $aId = $this->_getAttributeId($row[2]);
        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        $superAttrTable = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_ATTRIBUTE);
        $superLabelTable = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL);

//        $sql = "SELECT sal.value_id
//FROM {$superAttrTable} sa
//JOIN {$superLabelTable} sal ON sal.product_super_attribute_id=sa.product_super_attribute_id
//WHERE sa.product_id={$pId} AND sa.attribute_id={$aId} AND sal.store_id = {$storeId}";
        $sql = $this->_write->select()
            ->from(['sa' => $superAttrTable], null)
            ->join(['sal' => $superLabelTable], 'sal.product_super_attribute_id=sa.product_super_attribute_id', 'value_id')
            ->where('sa.product_id=?', $pId)->where(' sa.attribute_id=?', $aId)->where(' sal.store_id=?', $storeId);

        $valueId = $this->_write->fetchOne($sql);
        if ($valueId) {
            $this->_write->delete($superLabelTable, "value_id={$valueId}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPSI($row)
    {

        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_LINK);

        $p1 = $this->_getIdBySku($row[1]);
        $pWild = $pWild = trim($row[2]) === '*';
        $p2 = !$pWild ? $this->_getSeqIdBySku($row[2]) : null;

//        $linkId = $this->_write->fetchCol("SELECT link_id FROM {$t}
//            WHERE parent_id='{$p1}'" . (!$pWild ? " AND product_id='{$p2}'" : ''));
        $select = $this->_write->select()->from($t, 'link_id')->where('parent_id=?', $p1);
        if (!$pWild) {
            $select->where('product_id=?', $p2);
        }
        $linkId = $this->_write->fetchCol($select);
        if ($linkId) {
            $this->_write->delete($t, $this->_write->quoteInto('link_id IN (?)', $linkId));
            $this->_write->delete($this->_t(self::TABLE_CATALOG_PRODUCT_RELATION),
                                  "parent_id={$p1}" . (!$pWild ? " AND child_id={$p2}" : ''));
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPI($row)
    {

        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $tv = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);

        $pId = $this->_getIdBySku($row[1]);
        $imgFilename = $this->_convertEncoding($row[2]);
        $iWild = trim($row[2]) === '*';

//        $select = "SELECT `mgv`.`value_id`, `mg`.`value`
//                                          FROM {$tv} mgv
//                                          JOIN {$t} mg ON mgv.value_id = mg.value_id
//                                          WHERE mg.attribute_id={$this->_getGalleryAttrId()} AND mgv.{$this->_entityIdField} = {$pId}" .
//            (!$iWild ? " AND mg.value={$this->_write->quote($imgFilename)}" : '');

        $select = $this->_write->select()
            ->from(['mgv'=>$tv], 'value_id')
            ->join(['mg'=>$t], 'mgv.value_id = mg.value_id', 'value')
            ->where('mg.attribute_id=?', $this->_getGalleryAttrId())->where(" mgv.{$this->_entityIdField}=?", $pId);
        if (!$iWild) {
            $select->where('mg.value=?', $imgFilename);
        }
        $imgId = $this->_write->fetchPairs($select);
        if (!empty($imgId)) {
            $this->_write->delete($t, $this->_write->quoteInto('value_id in (?)', array_keys($imgId)));
            $imgToDel = $imgId;
            if (!$this->_deleteOldImageSkipUsageCheck) {
//                $imgNoToDel = $this->_write->fetchCol("SELECT `value` FROM {$t} WHERE `value` IN ({$this->_write->quote($imgToDel)})");
                $imgNoToDel = $this->_write->fetchCol($this->_write->select()->from($t, 'value')->where('value=?', $imgToDel));
                if (!empty($imgNoToDel)) {
                    $imgToDel = array_diff($imgToDel, $imgNoToDel);
                }
            }
            if (!empty($imgToDel)) {
                $directory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $mediaConfig = $this->_rapidFlowHelper->om()->get(Config::class);
                foreach ($imgToDel as $_imgToDel) {
                    $absoluteImgPath = $directory->getAbsolutePath($mediaConfig->getMediaPath($_imgToDel));
                    @unlink($absoluteImgPath);
                }
            }
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPIL($row)
    {

        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $tGallery = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $tGalleryValue = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);

        $pId = $this->_getIdBySku($row[1]);
        $img = $this->_convertEncoding($row[2]);
        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

//        $select = "SELECT t.value_id
//FROM {$tGallery} t
//INNER JOIN {$tGalleryValue} tl ON tl.value_id=t.value_id
//WHERE t.attribute_id={$this->_getGalleryAttrId()} AND t.{$this->_entityIdField}={$pId}
//AND tl.store_id={$storeId} AND t.value={$this->_write->quote($img)}";

        $select = $this->_write->select()->from(['t' => $tGallery], 'value_id')
            ->join(['tl' => $tGalleryValue], 'tl.value_id=t.value_id')
            ->where('t.attribute_id=?', $this->_getGalleryAttrId())
            ->where(" t.{$this->_entityIdField}=?", $pId)
            ->where(' tl.store_id=?', $storeId)->where(' t.value=?', $img);

        $imgId = $this->_write->fetchOne($select);
        if ($imgId) {
            $this->_write->delete($tGalleryValue, "value_id={$imgId} and store_id={$storeId}");
//            $count = $this->_write->fetchOne("SELECT count(`value_id`) FROM {$tGalleryValue} WHERE value_id={$imgId}");
            $count = $this->_write->fetchOne($this->_write->select()->from($tGalleryValue, new \Zend_Db_Expr('count(`value_id`)'))->where('value_id=?', $imgId));
            if ($count == 0) {
                $this->_write->delete($tGallery, "value_id={$imgId}");
            }
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPV($row)
    {
        if (count($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $tGallery = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $tGalleryValue = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);
        $tGalleryVideo = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_VIDEO);

        $storeId = $this->_getStoreId($row[4], true);
        $pId = $this->_getIdBySku($row[1]);
        $videoUrl = $this->_convertEncoding($row[2]);
        $videoShotWild = trim($row[2]) === '*';

        $screenShotFilename = $this->_convertEncoding($row[3]);
        $screenShotWild = trim($row[3]) === '*';

        $select = $this->_write->select()
            ->from(['mgv' => $tGalleryValue], 'value_id')
            ->join(['mg' => $tGallery], 'mgv.value_id = mg.value_id', 'value')
            ->join(['mgi' => $tGalleryVideo], 'mgi.value_id = mg.value_id')
            ->where("mg.media_type='external-video' AND mg.attribute_id=?", $this->_getGalleryAttrId())
            ->where('mgv.store_id=?', $storeId)
            ->where("mgv.{$this->_entityIdField}=?", $pId);

        if (!$screenShotWild) {
            $select->where('mg.value=?', $screenShotFilename);
        }

        if(!$videoShotWild){
            $select->where('mgi.url=?', $videoUrl);
        }

        $valueId = $this->_write->fetchPairs($select);
        if (!empty($valueId)) {
            $this->_write->delete($tGallery, $this->_write->quoteInto('value_id in (?)', array_keys($valueId)));
            $imgToDel = $valueId;
            if (!$this->_deleteOldImageSkipUsageCheck) {
                $imgNoToDel = $this->_write->fetchCol($this->_write->select()
                                                          ->from($tGallery, 'value')
                                                          ->where('value=?', $imgToDel));
                if (!empty($imgNoToDel)) {
                    $imgToDel = array_diff($imgToDel, $imgNoToDel);
                }
            }
            if (!empty($imgToDel)) {
                $directory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $mediaConfig = $this->_rapidFlowHelper->om()->get(Config::class);
                foreach ($imgToDel as $_imgToDel) {
                    $absoluteImgPath = $directory->getAbsolutePath($mediaConfig->getMediaPath($_imgToDel));
                    @unlink($absoluteImgPath);
                }
            }
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPVL($row)
    {
        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $tGallery = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $tGalleryValue = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);
        $tGalleryVideo = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_VIDEO);

        $pId = $this->_getIdBySku($row[1]);
        $videoUrl = $this->_convertEncoding($row[2]);
        $screenShotFilename = $this->_convertEncoding($row[3]);
        $storeId = $this->_getStoreId($row[4]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        $select = $this->_write->select()->from(['t' => $tGallery], 'value_id')
            ->join(['tl' => $tGalleryValue], 'tl.value_id=t.value_id')
            ->join(['mgi' => $tGalleryVideo], 'mgi.value_id=t.value_id')
            ->where('t.attribute_id=?', $this->_getGalleryAttrId())
            ->where("t.{$this->_entityIdField}=?", $pId)
            ->where('tl.store_id=?', $storeId)
            ->where('t.value=?', $screenShotFilename)
            ->where('mgi.url=?', $videoUrl);

        $valueId = $this->_write->fetchOne($select);
        if ($valueId) {
            $this->_write->delete($tGalleryValue, "value_id={$valueId} and store_id={$storeId}");
//            $count = $this->_write->fetchOne("SELECT count(`value_id`) FROM {$tGalleryValue} WHERE value_id={$valueId}");
            $count = $this->_write->fetchOne($this->_write->select()
                                                 ->from($tGalleryValue, new \Zend_Db_Expr('count(`value_id`)'))
                                                 ->where('value_id=?', $valueId));
            if ($count == 0) {
                $this->_write->delete($tGallery, "value_id={$valueId}");
            }
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPPT($row)
    {

        if (count($row) < 3) {
            throw new \InvalidArgumentException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_TIER_PRICE);

        $pId = $this->_getIdBySku($row[1]);
        $allGroups = $row[2] === '*' ? 1 : 0;
        $gId = $allGroups ? 0 : $this->_getCustomerGroup($row[2], true);
        $qty = (float)$row[3];
        $wId = isset($row[4]) ? $this->_getWebsiteId($row[4], true) : 0;

//        $select = "SELECT value_id
//FROM {$t}
//WHERE {$this->_entityIdField}='{$pId}' AND customer_group_id='{$gId}' AND qty='{$qty}' AND website_id='{$wId}' AND all_groups={$allGroups}";
        $select = $this->_write->select()->from($t, 'value_id')
            ->where($this->_entityIdField.'=?', $pId)
            ->where(' customer_group_id=?', $gId)
            ->where(' qty=?', $qty)
            ->where(' website_id=?', $wId)
            ->where(' all_groups=?', $allGroups);

        $priceId = $this->_write->fetchOne($select);
        if ($priceId) {
            $this->_write->delete($t, "value_id='{$priceId}'");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPPG($row)
    {
        $this->_profile->getLogger()->error('Group pricing is not supported in Magento 2');
        return;
        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_GROUP_PRICE);

        $pId = $this->_getIdBySku($row[1]);
        $gId = $this->_getCustomerGroup($row[2], true);

        $wId = isset($row[4]) ? $this->_getWebsiteId($row[4], true) : 0;

//        $select = "SELECT value_id FROM {$t} WHERE {$this->_entityIdField}='{$pId}' AND customer_group_id='{$gId}' AND website_id='{$wId}'";
        $select = $this->_write->select()->from($t, 'value_id')
            ->where($this->_entityIdField . '=?', $pId)->where(' customer_group_id=?', $gId)->where(' website_id=?', $wId);
        $priceId = $this->_write->fetchOne($select);
        if ($priceId) {
            $this->_write->delete($t, "value_id='{$priceId}'");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }
        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPD($row)
    {
        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_DOWNLOADABLE_LINK);
        $tl = $this->_t(self::TABLE_DOWNLOADABLE_LINK_TITLE);

        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);

//        $linkId = $this->_write->fetchOne(
//            "SELECT t.link_id
//FROM {$t} t
//LEFT JOIN {$tl} tl ON tl.link_id=t.link_id AND tl.store_id=0
//WHERE t.product_id={$pId} AND tl.title={$this->_write->quote($title)}"
//        );
        $linkId = $this->_write->fetchOne($this->_read->select()->from(['t' => $t], ['link_id'])
                                              ->joinLeft(['tl' => $tl], 'tl.link_id=t.link_id and tl.store_id=0', [])
                                              ->where('t.product_id=?', $pId)->where('tl.title=?', $title));
        if ($linkId) {
            $this->_write->delete($t, "link_id={$linkId}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPDL($row)
    {
        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_DOWNLOADABLE_LINK);
        $tl = $this->_t(self::TABLE_DOWNLOADABLE_LINK_TITLE);

//        $pId = $this->_getIdBySku($row[1]);
        $defTitle = $this->_convertEncoding($row[2]);
        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

//        $titleId = $this->_write->fetchOne(
//            "SELECT tl.title_id
//FROM {$t} t
//INNER JOIN {$tl} td ON td.link_id=t.link_id AND td.store_id=0 AND td.title={$this->_write->quote($defTitle)}
//INNER JOIN {$tl} tl ON tl.link_id=t.link_id AND tl.store_id={$storeId}"
//        );
        $titleId = $this->_write->fetchOne($this->_read->select()->from(['t' => $t], [])
                                               ->join(['td' => $tl], 'td.link_id=t.link_id and td.store_id=0', [])
                                               ->join(['tl' => $tl], 'tl.link_id=t.link_id', ['title_id'])
                                               ->where('tl.store_id=?', $storeId)->where('td.title=?', $defTitle));
        if ($titleId) {
            $this->_write->delete($t, "title_id={$titleId}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPDP($row)
    {
        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_DOWNLOADABLE_LINK);
        $tl = $this->_t(self::TABLE_DOWNLOADABLE_LINK_TITLE);
        $tp = $this->_t(self::TABLE_DOWNLOADABLE_LINK_PRICE);

//        $pId = $this->_getIdBySku($row[1]);
        $defTitle = $this->_convertEncoding($row[2]);
        $websiteId = $this->_getWebsiteId($row[3]);
        if (!$websiteId) {
            $this->_profile->getLogger()->setColumn(4);
            throw new LocalizedException(__('Invalid website'));
        }

//        $priceId = $this->_write->fetchOne("SELECT tp.price_id
//FROM {$t} t
//INNER JOIN {$tl} td ON td.link_id=t.link_id AND td.store_id=0 AND td.title={$this->_write->quote($defTitle)}
//INNER JOIN {$tp} tp ON tp.link_id=t.link_id AND tp.website_id={$websiteId}");
        $priceId = $this->_write->fetchOne($this->_read->select()->from(['t' => $t], [])
                                               ->join(['td' => $tl], 'td.link_id=t.link_id and tl.store_id=0', [])
                                               ->join(['tp' => $tp], 'tp.link_id=t.link_id', ['price_id'])
                                               ->where('tp.website_id=?', $websiteId)->where('td.title=?', $defTitle));
        if ($priceId) {
            $this->_write->delete($t, "price_id={$priceId}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPDS($row)
    {
        if (sizeof($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE);
        $tl = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE_TITLE);

        $pId = $this->_getIdBySku($row[1]);
        $title = $this->_convertEncoding($row[2]);

//        $sampleId = $this->_write->fetchOne("SELECT t.sample_id
//FROM {$t} t
//LEFT JOIN {$tl} tl ON tl.sample_id=t.sample_id AND tl.store_id=0
//WHERE t.product_id={$pId} AND tl.title={$this->_write->quote($title)}");
        $sampleId = $this->_write->fetchOne($this->_read->select()->from(['t' => $t], ['sample_id'])
                                                ->joinLeft(['tl' => $tl], 'tl.sample_id=t.sample_id AND tl.store_id=0', [])
                                                ->where('t.product_id=?', $pId)->where('tl.title=?', $title));
        if ($sampleId) {
            $this->_write->delete($t, "sample_id={$sampleId}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowCPDSL($row)
    {
        if (sizeof($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $t = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE);
        $tl = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE_TITLE);

//        $pId = $this->_getIdBySku($row[1]);
        $defTitle = $this->_convertEncoding($row[2]);
        $storeId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($storeId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

//        $titleId = $this->_write->fetchOne("SELECT tl.title_id
//FROM {$t} t
//INNER JOIN {$tl} td ON td.sample_id=t.sample_id AND td.store_id=0 AND td.title={$this->_write->quote($defTitle)}
//INNER JOIN {$tl} tl ON tl.sample_id=t.sample_id AND tl.store_id={$storeId}");
        $sql = $this->_write->select()->from(['t' => $t], [])
            ->join(['td' => $tl], 'td.sample_id=t.sample_id AND td.store_id=0')
            ->join(['tl' => $tl], 'tl.sample_id=t.sample_id', 'title_id')
            ->where('td.title=?', $defTitle)
            ->where('tl.store_id=?', $storeId);
        $titleId = $this->_write->fetchOne($sql);
        if ($titleId) {
            $this->_write->delete($t, "title_id={$titleId}");
            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _exportInitCP()
    {

    }

    protected function _exportInitCC()
    {

    }

    protected function _exportInitCCP()
    {

        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $categoryTable = $this->_t(self::TABLE_CATALOG_CATEGORY_ENTITY);
        $categoryProductTable = $this->_t(self::TABLE_CATALOG_CATEGORY_PRODUCT);
        $tWebsite = $this->_t(self::TABLE_STORE_WEBSITE);
        $tStoreGroup = $this->_t(self::TABLE_STORE_GROUP);

        $upAttrId = $this->_getAttributeId('url_path', 'catalog_category');
        $nameAttrId = $this->_getAttributeId('name', 'catalog_category');
        $storeId = $this->_profile->getStoreId();


        $this->_upPrependRoot = $this->_profile->getData('options/' . $this->_profile->getProfileType() . '/urlpath_prepend_root');

        if ($storeId) {
            $this->_rootCatId = $this->_storeManager->getStore($storeId)->getGroup()->getRootCategoryId();
        } elseif (!$this->_upPrependRoot || $storeId == 0) {
//            $this->_rootCatId = $this->_read->fetchOne("SELECT g.root_category_id
//FROM {$tWebsite} w INNER JOIN {$tStoreGroup} g ON g.group_id=w.default_group_id WHERE w.is_default = 1");
            $sql = $this->_read->select()->from(['w' => $tWebsite], [])
                ->join(['g' => $tStoreGroup], 'g.group_id=w.default_group_id', 'root_category_id')
                ->where('w.is_default=1');
            $this->_rootCatId = $this->_read->fetchOne($sql);
        }
        $rootPath = $this->_rootCatId ? '1/' . $this->_rootCatId : '1';

        if ($this->_upPrependRoot) {
            $rootCatPathsSel = $this->_read->select()
                ->from(['w' => $tWebsite], [])
                ->join(['g' => $tStoreGroup], 'g.group_id=w.default_group_id', [])
                ->join(['e' => $categoryTable], "e.{$this->_entityIdField}=g.root_category_id",
                       ["concat('1/',e.{$this->_entityIdField})"])
                ->join(['name' => $categoryTable . '_varchar'],
                       "name.{$this->_entityIdField}=e.{$this->_entityIdField} and name.attribute_id={$nameAttrId} and name.value<>'' and name.value is not null and name.store_id=0",
                       ['value'])
                ->group("e.{$this->_entityIdField}");
            if ($storeId) {
                $rootCatPathsSel->where("e.{$this->_entityIdField}=?", $this->_rootCatId);
            }
            $this->_rootCatPaths = $this->_read->fetchPairs($rootCatPathsSel);
        }

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['cp' => $categoryProductTable], "cp.product_id=main.entity_id", ['position'])
            ->join(['cat' => $categoryTable], "cp.category_id=cat.entity_id", ['path']);


        $this->addUrlPath($categoryTable, $upAttrId, $storeId);

        if ($this->_upPrependRoot && !empty($this->_rootCatPaths)) {
            $_rcPaths = [];
            foreach ($this->_rootCatPaths as $_rcPath => $_rcName) {
                $_rcPaths[] = $this->_read->quoteInto('path=?', $_rcPath);
                $_rcPaths[] = $this->_read->quoteInto('path like ?', $_rcPath . '/%');
            }
            $this->_select->where(implode(' OR ', $_rcPaths));
        } else {
            $this->_select->where(
                $this->_read->quoteInto('path=?', $rootPath)
                . $this->_read->quoteInto(' OR path like ?', $rootPath . '/%')
            );
        }

        $this->_applyProductFilter();

    }

    protected function _exportLinkSelect($linkType)
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $linkTable = $this->_t(self::TABLE_CATALOG_PRODUCT_LINK);
        $linkAttrTable = $this->_t(self::TABLE_CATALOG_PRODUCT_LINK_ATTRIBUTE);

        $linkTypeId = $this->_getLinkTypeId($linkType);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku' => 'sku'])
            ->join(['l' => $linkTable], "l.product_id=main.{$this->_entityIdField}", [])
            ->join(['lp' => $productTable], "lp.entity_id=l.linked_product_id", ['linked_sku' => 'sku'])
            ->where('l.link_type_id=?', $linkTypeId);
        $attrs = $this->_getLinkAttr($linkTypeId);
        foreach ($attrs as $code => $r) {
            $alias = 'a_' . $code;
            $this->_select->joinLeft(
                [$alias => "{$linkAttrTable}_{$r['data_type']}"],
                "{$alias}.link_id=l.link_id and {$alias}.product_link_attribute_id={$r['id']}",
                [$code => 'value']
            );
        }

        $this->_applyProductFilter();
    }

    protected function _exportInitCPRI()
    {
        $this->_exportLinkSelect('relation');
    }

    protected function _exportInitCPUI()
    {
        $this->_exportLinkSelect('up_sell');
    }

    protected function _exportInitCPXI()
    {
        $this->_exportLinkSelect('cross_sell');
    }

    protected function _exportInitCPGI()
    {
        $this->_exportLinkSelect('super');
    }

    protected function _exportInitCPCO()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $optionTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION);
        $optionTitleTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TITLE);
        $optionPriceTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_PRICE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['o' => $optionTable], "o.product_id=main.{$this->_entityIdField}",
                   ['type', 'is_require', 'option_sku' => 'sku', 'max_characters', 'sort_order'])
            ->join(['ot' => $optionTitleTable], 'ot.option_id=o.option_id and ot.store_id=0',
                   ['default_title' => 'title'])
            ->joinLeft(['op' => $optionPriceTable], 'op.option_id=o.option_id and op.store_id=0',
                       ['price', 'price_type']);

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['default_title'];
    }

    protected function _exportInitCPCOL()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $storeTable = $this->_t(self::TABLE_STORE);
        $optionTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION);
        $optionTitleTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TITLE);
        $optionPriceTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_PRICE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['o' => $optionTable], "o.product_id=main.{$this->_entityIdField}", [])
            ->join(['ot' => $optionTitleTable], 'ot.option_id=o.option_id and ot.store_id=0',
                   ['default_title' => 'title'])
            ->join(['otl' => $optionTitleTable], 'otl.option_id=o.option_id and otl.store_id<>0', ['title'])
            ->join(['s' => $storeTable], 's.store_id=otl.store_id', ['store' => 'code'])
            ->joinLeft(['opl' => $optionPriceTable], 'opl.option_id=o.option_id and opl.store_id=otl.store_id',
                       ['price', 'price_type'])
            ->where('otl.option_id is not null or opl.option_id is not null');
        if ($this->_getStoreIds()) {
            $this->_select->where('otl.store_id in (?)', $this->_getStoreIds());
        }

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['default_title', 'title'];
    }

    protected function _exportInitCPCOS()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $optionTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION);
        $optionTitleTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TITLE);
        $optionSelTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_VALUE);
        $optionSelTitleTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_TITLE);
        $optionSelPriceTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_PRICE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['o' => $optionTable], "o.product_id=main.{$this->_entityIdField}", [])
            ->join(['ot' => $optionTitleTable], 'ot.option_id=o.option_id and ot.store_id=0',
                   ['default_title' => 'title'])
            ->join(['os' => $optionSelTable], 'os.option_id=o.option_id',
                   ['selection_sku' => 'sku', 'sort_order'])
            ->join(['ost' => $optionSelTitleTable], 'ost.option_type_id=os.option_type_id and ost.store_id=0',
                   ['selection_default_title' => 'title'])
            ->joinLeft(['osp' => $optionSelPriceTable], 'osp.option_type_id=os.option_type_id and osp.store_id=0',
                       ['price', 'price_type']);

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['default_title', 'selection_default_title'];
    }

    protected function _exportInitCPCOSL()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $optionTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION);
        $storeTable = $this->_t(self::TABLE_STORE);
        $optionTitleTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TITLE);
        $optionSelTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_VALUE);
        $optionSelTitleTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_TITLE);
        $optionSelPriceTable = $this->_t(self::TABLE_CATALOG_PRODUCT_OPTION_TYPE_PRICE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['o' => $optionTable], "o.product_id=main.{$this->_entityIdField}", [])
            ->join(['ot' => $optionTitleTable], 'ot.option_id=o.option_id and ot.store_id=0',
                   ['default_title' => 'title'])
            ->join(['os' => $optionSelTable], 'os.option_id=o.option_id', [])
            ->join(['ost' => $optionSelTitleTable], 'ost.option_type_id=os.option_type_id and ost.store_id=0',
                   ['selection_default_title' => 'title'])
            ->join(['ostl' => $optionSelTitleTable], 'ostl.option_type_id=os.option_type_id and ostl.store_id<>0',
                   ['title'])
            ->join(['s' => $storeTable], 's.store_id=ostl.store_id', ['store' => 'code'])
            ->joinLeft(['ospl' => $optionSelPriceTable],
                       'ospl.option_type_id=os.option_type_id and ospl.store_id=ostl.store_id',
                       ['price', 'price_type']);
        if ($this->_getStoreIds()) {
            $this->_select->where('ostl.store_id in (?)', $this->_getStoreIds());
        }

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['default_title', 'selection_default_title', 'title'];
    }

    protected function _exportInitCPBO()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $bundleOptionTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION);
        $bundleOptionValueTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['bo' => $bundleOptionTable], "bo.parent_id=main.{$this->_entityIdField}",
                   ['required', 'position', 'type'])
            ->join(['bov' => $bundleOptionValueTable], 'bov.option_id=bo.option_id and store_id=0',
                   ['default_title' => 'title']);

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['default_title'];
    }

    protected function _exportInitCPBOL()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $storeTable = $this->_t(self::TABLE_STORE);
        $bundleOptionTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION);
        $bundleOptionValueTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['bo' => $bundleOptionTable], "bo.parent_id=main.{$this->_entityIdField}", [])
            ->join(['bov' => $bundleOptionValueTable], 'bov.option_id=bo.option_id and bov.store_id=0',
                   ['default_title' => 'title'])
            ->join(['bovl' => $bundleOptionValueTable], 'bovl.option_id=bo.option_id and bovl.store_id<>0',
                   ['title' => 'title'])
            ->join(['s' => $storeTable], 's.store_id=bovl.store_id', ['store' => 'code']);
        if ($this->_getStoreIds()) {
            $this->_select->where('bovl.store_id in (?)', $this->_getStoreIds());
        }

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['default_title', 'title'];
    }

    protected function _exportInitCPBOS()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $bundleOptionTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION);
        $bundleOptionValueTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE);
        $bundleSelectionTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_SELECTION);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['bo' => $bundleOptionTable], "bo.parent_id=main.{$this->_entityIdField}", [])
            ->join(['bov' => $bundleOptionValueTable], 'bov.option_id=bo.option_id and bov.store_id=0',
                   ['default_title' => 'title'])
            ->join(['bos' => $bundleSelectionTable], 'bos.option_id=bo.option_id', [
                'position',
                'is_default',
                'selection_price_type',
                'selection_price_value',
                'selection_qty',
                'selection_can_change_qty'
            ])
            ->join(['bp' => $productTable], "bp.entity_id=bos.product_id", ['selection_sku' => 'sku']);

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['default_title'];
    }

    protected function _exportInitCPBOSL()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $bundleOptionTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION);
        $bundleOptionValueTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_OPTION_VALUE);
        $bundleSelectionTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_SELECTION);
        $bundleSelectionPriceTable = $this->_t(self::TABLE_CATALOG_PRODUCT_BUNDLE_SELECTION_PRICE);
        $websiteTable = $this->_t(self::TABLE_STORE_WEBSITE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['bo' => $bundleOptionTable], "bo.parent_id=main.{$this->_entityIdField}", [])
            ->join(['bov' => $bundleOptionValueTable], 'bov.option_id=bo.option_id and bov.store_id=0', ['default_title' => 'title'])
            ->join(['bos' => $bundleSelectionTable], 'bos.option_id=bo.option_id', [])
            ->join(['bosp' => $bundleSelectionPriceTable], 'bos.selection_id=bosp.selection_id', [
                'selection_price_type',
                'selection_price_value',
            ])
            ->join(['ws' => $websiteTable], 'ws.website_id=bosp.website_id', ['website' => 'code'])
            ->join(['bp' => $productTable], "bp.{$this->_entityIdField}=bos.product_id", ['selection_sku' => 'sku']);

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['default_title'];
    }

    protected function _exportInitCPSA()
    {


        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $attrTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $superAttrTable = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_ATTRIBUTE);
        $superLabelTable = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['sa' => $superAttrTable], "sa.product_id=main.{$this->_entityIdField}", ['position'])
            ->join(['a' => $attrTable], 'a.attribute_id=sa.attribute_id', ['attribute_code'])
            ->join(['sl' => $superLabelTable],
                   'sl.product_super_attribute_id=sa.product_super_attribute_id and store_id=0',
                   ['label' => 'value']);

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['label'];
    }

    protected function _exportInitCPSAL()
    {


        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $attrTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $storeTable = $this->_t(self::TABLE_STORE);
        $superAttrTable = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_ATTRIBUTE);
        $superLabelTable = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['sa' => $superAttrTable], "sa.product_id=main.{$this->_entityIdField}", [])
            ->join(['a' => $attrTable], 'a.attribute_id=sa.attribute_id', ['attribute_code'])
            ->join(['sl' => $superLabelTable],
                   'sl.product_super_attribute_id=sa.product_super_attribute_id and sl.store_id<>0',
                   ['label' => 'value'])
            ->join(['s' => $storeTable], 's.store_id=sl.store_id', ['store' => 'code']);
        if ($this->_getStoreIds()) {
            $this->_select->where('sl.store_id in (?)', $this->_getStoreIds());
        }

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['label'];
    }

    protected function _exportCallbackLoadAttributeOptions(&$row)
    {
        $attrCode = $row['attribute_code'];
        $this->_fetchAttributeOptions($attrCode);
        $aId = $this->_getAttributeId($attrCode);
        if (!isset($this->_attrOptionsByValue[$aId][$row['value_index']])) {
            throw new LocalizedException(__('Invalid attribute option value: %1, %2',
                                            [$attrCode, $row['value_index']]));
        }
        $row['value_label'] = $this->_attrOptionsByValue[$aId][$row['value_index']];

        if (!isset($row['website'])) {
            $row['website'] = '';
        }

        return true;
    }

    protected function _exportInitCPSI()
    {

        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $superLinkTable = $this->_t(self::TABLE_CATALOG_PRODUCT_SUPER_LINK);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['sl' => $superLinkTable], "sl.parent_id=main.{$this->_entityIdField}", [])
            ->join(['l' => $productTable], "l.entity_id=sl.product_id", ['linked_sku' => 'sku']);

        $this->_applyProductFilter();
    }

    protected function _exportCallbackCPI($row)
    {
        if ($this->_processImageFiles && !empty($row['image_url'])) {
            $this->_profile->getLogger()->setColumn(3);
            $this->_copyImageFile($this->_imagesMediaDir, $this->_imagesTargetDir, $row['image_url']);
        }
        return true;
    }

    protected function _exportCallbackCPV($row)
    {
        if ($this->_processImageFiles && !empty($row['screenshot_url'])) {
            $this->_profile->getLogger()->setColumn(3);
            $this->_copyImageFile($this->_imagesMediaDir, $this->_imagesTargetDir, $row['screenshot_url']);
        }
        return true;
    }

    protected function _exportInitCPI()
    {

        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $galleryTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $galleryEntityTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_ENTITY);
        $galleryStoreTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);
        $this->_select = $this->_read->select()->distinct()
            ->from(['main' => $productTable], ['sku'])
            ->join(['gl' => $galleryStoreTable], "gl.{$this->_entityIdField}=main.{$this->_entityIdField}",
                   ['label', 'position', 'disabled'])
            ->join(['g' => $galleryTable], 'g.value_id=gl.value_id', ['image_url' => 'value', 'media_type'])
            ->where('gl.store_id=0 or gl.store_id is null');

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['image_url', 'label'];
    }

    protected function _exportInitCPIL()
    {

        $storeTable = $this->_t(self::TABLE_STORE);
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $galleryTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $galleryEntityTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_ENTITY);
        $galleryStoreTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['gve' => $galleryEntityTable], "gve.{$this->_entityIdField}=main.{$this->_entityIdField}")
            ->join(['g' => $galleryTable], 'g.value_id=gve.value_id', ['image_url' => 'value'])
            ->join(['gl' => $galleryStoreTable], 'gl.value_id=g.value_id', ['label', 'position', 'disabled'])
            ->join(['s' => $storeTable], 's.store_id=gl.store_id', ['store' => 'code'])
            ->where('gl.store_id<>0');

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['image_url', 'label'];
    }

    protected function _exportInitCPV()
    {
        $storeTable = $this->_t(self::TABLE_STORE);
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $galleryTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $galleryVideoTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_VIDEO);
        $galleryStoreTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);
        $this->_select = $this->_read->select()->distinct()
            ->from(['main' => $productTable], ['sku'])
            ->join(['gl' => $galleryStoreTable], "gl.{$this->_entityIdField}=main.{$this->_entityIdField}",
                   ['position', 'disabled'])
            ->join(['g' => $galleryTable], 'g.value_id=gl.value_id', ['screenshot_url' => 'value'])
            ->join(['gv' => $galleryVideoTable], 'gv.value_id=gl.value_id',
                   ['url', 'provider', 'title', 'description', 'metadata'])
            ->join(['s' => $storeTable], 's.store_id=gl.store_id', ['store' => 'code'])
            ->where("g.media_type='external-video'");

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['screenshot_url', 'url', 'title', 'provider', 'description', 'metadata'];
    }

    protected function _exportInitCPVL()
    {

        $storeTable = $this->_t(self::TABLE_STORE);
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $galleryTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
        $galleryEntityTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_ENTITY);
        $galleryStoreTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['gve' => $galleryEntityTable], "gve.{$this->_entityIdField}=main.{$this->_entityIdField}")
            ->join(['g' => $galleryTable], 'g.value_id=gve.value_id', ['screenshot_url' => 'value'])
            ->join(['gl' => $galleryStoreTable], 'gl.value_id=g.value_id', ['label', 'position', 'disabled'])
            ->join(['s' => $storeTable], 's.store_id=gl.store_id', ['store' => 'code'])
            ->where("g.media_type='external-video' AND gl.store_id<>0");

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['screenshot_url', 'label'];
    }

    protected function _exportCallbackCCP(&$row)
    {
        $row['url_path'] = $this->_upPrependRoot($row, $row['url_path']);
        return true;
    }

    protected function _exportCallbackCPPT(&$row)
    {
        if ($row['all_groups']) {
            $row['customer_group'] = '*';
        }
        return true;
    }

    protected function _exportInitCPPT()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $priceTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_TIER_PRICE);
        $cGroupTable = $this->_t(self::TABLE_CUSTOMER_GROUP);
        $websiteTable = $this->_t(self::TABLE_STORE_WEBSITE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['tp' => $priceTable], "tp.{$this->_entityIdField}=main.{$this->_entityIdField}",
                   ['qty', 'price' => 'value', 'all_groups'])
            ->join(['cg' => $cGroupTable], 'cg.customer_group_id=tp.customer_group_id',
                   ['customer_group' => 'customer_group_code'])
            ->join(['w' => $websiteTable], 'w.website_id=tp.website_id', ['website' => 'code']);

        if ($this->_rapidFlowHelper->compareMageVer('2.2.0')) {
            $this->_select->columns('percentage_value', 'tp');
        }

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['customer_group'];
    }

    protected function _exportInitCPPG()
    {
        $this->_profile->getLogger()->error('Group pricing is not supported in Magento 2');
        return;
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $priceTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY_GROUP_PRICE);
        if (empty($priceTable)) {
            return;
        }
        $cGroupTable = $this->_t(self::TABLE_CUSTOMER_GROUP);
        $websiteTable = $this->_t(self::TABLE_STORE_WEBSITE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['gp' => $priceTable], "gp.{$this->_entityIdField}=main.{$this->_entityIdField}",
                   ['price' => 'value'])
            ->join(['cg' => $cGroupTable], 'cg.customer_group_id=gp.customer_group_id',
                   ['customer_group' => 'customer_group_code'])
            ->join(['w' => $websiteTable], 'w.website_id=gp.website_id', ['website' => 'code']);

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['customer_group'];
    }

    protected function _exportInitCPD()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $dTable = $this->_t(self::TABLE_DOWNLOADABLE_LINK);
        $dtTable = $this->_t(self::TABLE_DOWNLOADABLE_LINK_TITLE);
        $dpTable = $this->_t(self::TABLE_DOWNLOADABLE_LINK_PRICE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['d' => $dTable], "d.product_id=main.{$this->_entityIdField}", [
                'sort_order',
                'max_downloads' => 'number_of_downloads',
                'is_shareable',
                'link_url',
                'link_file',
                'link_type',
                'sample_url',
                'sample_file',
                'sample_type'
            ])
            ->join(['dt' => $dtTable], 'dt.link_id=d.link_id', ['title'])
            ->join(['dp' => $dpTable], 'dp.link_id=d.link_id', ['price'])
            ->where('dt.store_id=0 and dp.website_id=0');

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['title'];
    }

    protected function _exportInitCPDL()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $dTable = $this->_t(self::TABLE_DOWNLOADABLE_LINK);
        $dtTable = $this->_t(self::TABLE_DOWNLOADABLE_LINK_TITLE);
        $storeTable = $this->_t(self::TABLE_STORE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['d' => $dTable], "d.product_id=main.{$this->_entityIdField}", [])
            ->join(['dt' => $dtTable], 'dt.link_id=d.link_id', ['default_title' => 'title'])
            ->join(['dl' => $dtTable], 'dl.link_id=d.link_id', ['title'])
            ->join(['s' => $storeTable], 's.store_id=dl.store_id', ['store' => 'code'])
            ->where('dt.store_id=0 and dl.store_id<>0');

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['default_title', 'title'];
    }

    protected function _exportInitCPDP()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $dTable = $this->_t(self::TABLE_DOWNLOADABLE_LINK);
        $dtTable = $this->_t(self::TABLE_DOWNLOADABLE_LINK_TITLE);
        $dpTable = $this->_t(self::TABLE_DOWNLOADABLE_LINK_PRICE);
        $websiteTable = $this->_t(self::TABLE_STORE_WEBSITE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['d' => $dTable], "d.product_id=main.{$this->_entityIdField}", [])
            ->join(['dt' => $dtTable], 'dt.link_id=d.link_id', ['default_title' => 'title'])
            ->join(['dp' => $dpTable], 'dp.link_id=d.link_id', ['price'])
            ->join(['w' => $websiteTable], 'w.website_id=dp.website_id', ['website' => 'code'])
            ->where('dt.store_id=0 and dp.website_id<>0');

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['default_title'];
    }

    protected function _exportInitCPDS()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $dTable = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE);
        $dtTable = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE_TITLE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['d' => $dTable], "d.product_id=main.{$this->_entityIdField}",
                   ['sort_order', 'sample_url', 'sample_file', 'sample_type'])
            ->join(['dt' => $dtTable], 'dt.sample_id=d.sample_id', ['title'])
            ->where('dt.store_id=0');

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['title'];
    }

    protected function _exportInitCPDSL()
    {
        $productTable = $this->_t(self::TABLE_CATALOG_PRODUCT_ENTITY);
        $dTable = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE);
        $dtTable = $this->_t(self::TABLE_DOWNLOADABLE_SAMPLE_TITLE);
        $storeTable = $this->_t(self::TABLE_STORE);

        $this->_select = $this->_read->select()
            ->from(['main' => $productTable], ['sku'])
            ->join(['d' => $dTable], "d.product_id=main.{$this->_entityIdField}",
                   ['sort_order', 'sample_url', 'sample_file', 'sample_type'])
            ->join(['dt' => $dtTable], 'dt.sample_id=d.sample_id', ['default_title' => 'title'])
            ->join(['dl' => $dtTable], 'dl.sample_id=d.sample_id', ['title'])
            ->join(['s' => $storeTable], 's.store_id=dl.store_id', ['store' => 'code'])
            ->where('dt.store_id=0 and dl.store_id<>0');

        $this->_applyProductFilter();

        $this->_exportConvertFields = ['default_title', 'title'];
    }

    protected function _getLinkTypeId($linkType)
    {
        if (empty($this->_linkTypes)) {
            $s = $this->_read->select()
                ->from($this->_t(self::TABLE_CATALOG_PRODUCT_LINK_TYPE), ['code', 'link_type_id']);
            $this->_linkTypes = $this->_read->fetchPairs($s);
        }
        if (empty($this->_linkTypes[$linkType])) {
            throw new LocalizedException(__('Invalid product link type (%1)', $linkType));
        }
        return $this->_linkTypes[$linkType];
    }

    protected function _getLinkAttr($linkType, $linkAttr = null, $param = null)
    {
        if (!(null === $linkType)) {
            $linkTypeId = is_numeric($linkType) ? $linkType : $this->_getLinkTypeId($linkType);
        }
        if (empty($this->_linkAttrs)) {
//            $attrs = $this->_read->fetchAll("SELECT * FROM {$this->_t(self::TABLE_CATALOG_PRODUCT_LINK_ATTRIBUTE)}");
            $attrs = $this->_read->fetchAll($this->_write->select()->from($this->_t(self::TABLE_CATALOG_PRODUCT_LINK_ATTRIBUTE)));
            foreach ($attrs as $a) {
                $this->_linkAttrs[$a['link_type_id']][$a['product_link_attribute_code']] = [
                    'id' => $a['product_link_attribute_id'],
                    'code' => $a['product_link_attribute_code'],
                    'data_type' => $a['data_type'],
                ];
                $this->_linkAttrIds[$a['product_link_attribute_id']] = $a['product_link_attribute_code'];
            }
        }
        if (null === $linkType) {
            return $this->_linkAttrs;
        }
        if (empty($this->_linkAttrs[$linkTypeId])) {
            throw new LocalizedException(__('Invalid product link type (%1)', $linkType));
        }
        if (null === $linkAttr) {
            return $this->_linkAttrs[$linkTypeId];
        }
        if (is_numeric($linkAttr) && !empty($this->_linkAttrIds[$linkAttr])) {
            $linkAttr = $this->_linkAttrIds[$linkAttr];
        }
        if (empty($this->_linkAttrs[$linkTypeId][$linkAttr])) {
            throw new LocalizedException(__('Invalid product link attribute (%1, %2)', $linkType, $linkAttr));
        }
        $attr = $this->_linkAttrs[$linkTypeId][$linkAttr];
        return null === $param ? $attr : $attr[$param];
    }

    protected function _upPrependRoot($row, $value)
    {
        if ($this->_upPrependRoot) {
            $_rootCat = explode('/', $row['path'], 3);
            unset($_rootCat[2]);
            $_rootCat = implode('/', $_rootCat);
            if (isset($this->_rootCatPaths[$_rootCat])) {
                if (empty($value)) {
                    $value = $this->_rootCatPaths[$_rootCat];
                } else {
                    $value = $this->_rootCatPaths[$_rootCat] . '/' . $value;
                }
            }
        }
        return $value;
    }

    protected $_catEntities;

    protected function addEe13UrlPath($categoryTable, $upAttrId, $storeId)
    {
        $entityId = $this->_entityIdField;
        $this->_select->joinLeft(
            ['catup' => $categoryTable . '_varchar'],
            "catup.{$entityId}=cat.{$entityId} and catup.attribute_id={$upAttrId} and catup.store_id=0",
            []
        );
        if ($storeId != 0) {
            $this->_select->joinLeft(
                ['catups' => $categoryTable . '_varchar'],
                "catups.{$entityId}=cat.{$entityId} and catups.attribute_id={$upAttrId} and catups.store_id='{$storeId}'",
                []
            );
            $this->_select->columns(['url_path' => 'IFNULL(catups.value, catup.value)']);
        } else {
            $this->_select->columns(['url_path' => 'catup.value']);
        }

        $ukAttrId = $this->_getAttributeId('url_key', 'catalog_category');
        $this->_select->joinLeft(
            ['catupk' => $categoryTable . '_url_key'],
            "catupk.{$entityId}=cat.{$entityId} and catupk.attribute_id={$ukAttrId}
                              and catupk.store_id=0",
            []
        );

        if ($storeId != 0) {
            $this->_select->joinLeft(
                ['catupks' => $categoryTable . '_url_key'],
                "catupks.{$entityId}=cat.{$entityId} and catupks.attribute_id={$ukAttrId}
                                  and catupks.store_id={$storeId}",
                []
            );
            $this->_select->columns(['url_key' => 'IFNULL(catupks.value, catupk.value)']);
        } else {
            $this->_select->columns(['url_key' => 'catupk.value']);
        }

        $select = $this->_write->select()->from(['e' => $categoryTable], [$entityId, 'path'])
            ->join(['up' => $categoryTable . '_url_key'],
                   "up.{$entityId}=e.{$entityId} and up.attribute_id={$ukAttrId} and up.store_id=0",
                   []);
        if ($storeId != 0) {
            $select->joinLeft(['ups' => $categoryTable . '_url_key'],
                              "ups.{$entityId}=e.{$entityId} and ups.attribute_id={$ukAttrId} and ups.store_id={$storeId}",
                              []);

            $select->columns(['url_key' => 'IFNULL(ups.value, up.value)']);
        } else {
            $select->columns(['url_key' => 'up.value']);
        }

        $this->_logger->debug((string)$select);

        $catEntities = $this->_read->fetchAll($select);
        foreach ($catEntities as $entity) {
            $this->_catEntities[$entity[$entityId]] = $entity;
        }

    }

    protected function addUrlPath($categoryTable, $upAttrId, $storeId)
    {
        $this->_select->join(
            ['catup' => $categoryTable . '_varchar'],
            "catup.{$this->_entityIdField}=cat.{$this->_entityIdField} and catup.attribute_id={$upAttrId}
                                and catup.value<>'' and catup.value is not null and catup.store_id=0",
            []
        );
        if ($storeId != 0) {
            $this->_select->joinLeft(
                ['catups' => $categoryTable . '_varchar'],
                "catups.{$this->_entityIdField}=cat.{$this->_entityIdField} and catups.attribute_id={$upAttrId}
                                    and catups.value<>'' and catup.value is not null and catups.store_id='{$storeId}'",
                []
            );
            $this->_select->columns(['url_path' => 'IFNULL(catups.value, catup.value)']);
        } else {
            $this->_select->columns(['url_path' => 'catup.value']);
        }
    }

    protected function _updateCategoryUrlRewrites()
    {
        foreach ($this->_categoryUrlRewriteIds as $cId) {
            $this->_rapidFlowHelper->addCategoryIdForRewriteUpdate($cId, $this->catSeqIdByRowId($cId));
        }
    }
}
