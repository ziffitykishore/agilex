<?php

namespace Unirgy\RapidFlowPro\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Swatches\Model\Swatch;
use Unirgy\RapidFlow\Model\ResourceModel\AbstractResource\Fixed;
use Unirgy\RapidFlow\Model\ResourceModel\Catalog\Product\AbstractProduct;

class Eav
    extends Fixed
{

    protected $_translateModule = 'Unirgy_RapidFlowPro';

    protected $_dataType = 'eav_struct';

    protected $_exportRowCallback = [
        'EA' => '_exportCleanEntityType',
        'EAL' => '_exportCleanEntityType',
        'EAX' => '_exportCleanEntityType',
        'EAS' => '_exportCleanEntityType',
        'EASI' => '_exportCleanEntityType',
        'EAO' => '_exportCleanEntityType',
        'EAOL' => '_exportCleanEntityType',
    ];

    protected $_attributes;

    protected function _construct()
    {
        AbstractProduct::validateLicense('Unirgy_RapidFlowPro');
        parent::_construct();
    }

    protected function _importRowEA($row)
    {

        if (count($row) < 7) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $systemAttributes = $this->_profile->getData('options/import/system_attributes');

        $aTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $etId = $this->_getEntityType(!empty($row[7]) ? $row[7] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            throw new LocalizedException(__('Invalid entity type'));
        }
        $new = [
            'attribute_code' => $row[1],
            'backend_type' => $row[2],
            'frontend_input' => $row[3],
            'frontend_label' => $this->_convertEncoding($row[4]),
            'is_required' => $row[5],
            'is_unique' => $row[6],
        ];

//        $exists = $this->_write->fetchRow(
//            "SELECT attribute_id,backend_type,frontend_input,frontend_label,is_required,is_unique,is_user_defined
//             FROM {$aTable} WHERE entity_type_id={$etId} AND attribute_code=?",
//            $new['attribute_code']
//        );
        $select = $this->_write->select()->from($aTable, [
            'attribute_id',
            'backend_type',
            'frontend_input',
            'frontend_label',
            'is_required',
            'is_unique',
            'is_user_defined'
        ])->where('entity_type_id=?', $etId)->where('attribute_code=?', $new['attribute_code']);

        $exists = $this->_write->fetchRow($select);
        if (!$exists) {
            $new['entity_type_id'] = $etId;
            $new['is_user_defined'] = true;
            $this->_write->insert($aTable, $new);
            $this->_updateNewAttribute($new['attribute_code'], $etId);
            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif (($systemAttributes || $exists['is_user_defined']) && $this->_isChangeRequired($exists, $new)) {
            $this->_write->update($aTable, $new, 'attribute_id=' . $exists['attribute_id']);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowEAL($row)
    {
        if (count($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }
        $alTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_LABEL);

        $etId = $this->_getEntityType(!empty($row[4]) ? $row[4] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(5);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $aId = $this->_getAttributeId($row[1], $etId);
        if (!$aId) {
            $this->_profile->getLogger()->setColumn(2);
            throw new LocalizedException(__('Invalid attribute: %1', $row[1]));
        }
        $sId = $this->_getStoreId($row[2]);
        if ($this->_skipStore($sId, 3)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        $label = $this->_convertEncoding($row[3]);

//        $exists = $this->_write->fetchRow(
//            "SELECT attribute_label_id, value FROM {$alTable} WHERE attribute_id={$aId} AND store_id={$sId}"
//        );
        $exists = $this->_write->fetchRow(
            $this->_write->select()->from($alTable, ['attribute_label_id', 'value'])
                                   ->where('attribute_id=?', $aId)->where('store_id=?', $sId));

        if (!$exists) {
            $this->_write->insert($alTable, ['attribute_id' => $aId, 'store_id' => $sId, 'value' => $label]);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        } else if ($exists['value'] !== $row[3]) {
            $this->_write->update($alTable, ['value' => $label],
                                  'attribute_label_id=' . $exists['attribute_label_id']);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowEAX($row)
    {
        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $systemAttributes = $this->_profile->getData('options/import/system_attributes');

        $aTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $etId = $this->_getEntityType(!empty($row[10]) ? $row[10] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(11);
            throw new LocalizedException(__('Invalid entity type'));
        }

        $new = [
            'attribute_code' => $row[1],
            'attribute_model' => !empty($row[2]) ? $row[2] : null,
            'backend_model' => !empty($row[3]) ? $row[3] : null,
            'backend_table' => !empty($row[4]) ? $row[4] : null,
            'frontend_model' => !empty($row[5]) ? $row[5] : null,
            'frontend_class' => !empty($row[6]) ? $row[6] : null,
            'source_model' => !empty($row[7]) ? $row[7] : null,
            'default_value' => !empty($row[8]) ? $this->_convertEncoding($row[8]) : null,
            'note' => !empty($row[9]) && $row[2] !== '' ? $row[9] : null,
        ];

//        $exists = $this->_write->fetchRow(
//            "SELECT attribute_id,attribute_model,backend_model,backend_table,frontend_model,frontend_class,source_model,default_value,note,is_user_defined
//             FROM {$aTable} WHERE entity_type_id={$etId} AND attribute_code=?",
//            $new['attribute_code']
//        );
        $exists = $this->_write->fetchRow($this->_write->select()->from($aTable, [
            'attribute_id',
            'attribute_model',
            'backend_model',
            'backend_table',
            'frontend_model',
            'frontend_class',
            'source_model',
            'default_value',
            'note',
            'is_user_defined'
        ])->where('entity_type_id=?', $etId)->where(' attribute_code=?', $new['attribute_code']));
        if (!$exists) {
            $this->_profile->getLogger()->setColumn(2);
            throw new LocalizedException(__('Invalid attribute: %1', $row[1]));
        } elseif (($systemAttributes || $exists['is_user_defined']) && $this->_isChangeRequired($exists, $new)) {
            $this->_write->update($aTable, $new, 'attribute_id=' . $exists['attribute_id']);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowEAXP($row)
    {
        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $systemAttributes = $this->_profile->getData('options/import/system_attributes');

        $aTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $caTable = $this->_t(self::TABLE_CATALOG_EAV_ATTRIBUTE);

        $new = array_filter([
            'is_global'                     => isset($row[2]) && $row[2] !== ''? (int) $row[2]: false,
            'is_visible'                    => isset($row[3]) && $row[3] !== ''? (int) $row[3]: false,
            'is_searchable'                 => isset($row[4]) && $row[4] !== ''? (int) $row[4]: false,
            'is_filterable'                 => isset($row[5]) && $row[5] !== ''? (int) $row[5]: false,
            'is_comparable'                 => isset($row[6]) && $row[6] !== ''? (int) $row[6]: false,
            'is_visible_on_front'           => isset($row[7]) && $row[7] !== ''? (int) $row[7]: false,
            'is_html_allowed_on_front'      => isset($row[8]) && $row[8] !== ''? (int) $row[8]: false,
            'is_used_for_price_rules'       => isset($row[9]) && $row[9] !== ''? (int) $row[9]: false,
            'is_filterable_in_search'       => isset($row[10]) && $row[10] !== ''? (int) $row[10]: false,
            'used_in_product_listing'       => isset($row[11]) && $row[11] !== ''? (int) $row[11]: false,
            'used_for_sort_by'              => isset($row[12]) && $row[12] !== ''? (int) $row[12]: false,
            'apply_to'                      => isset($row[13])? $row[13]: false,
            'is_visible_in_advanced_search' => isset($row[14]) && $row[14] !== ''? (int) $row[14]: false,
            'position'                      => isset($row[15]) && $row[15] !== ''? (int) $row[15]: false,
            'frontend_input_renderer'       => isset($row[16])? ($row[16] !== ''? $row[16]: null): false,
            'is_wysiwyg_enabled'            => isset($row[17]) && $row[17] !== ''? (int) $row[17]: false,
            'is_used_for_promo_rules'       => isset($row[18]) && $row[18] !== ''? (int) $row[18]: false,
            'is_required_in_admin_store'    => isset($row[19]) && $row[19] !== ''? (int) $row[19]: false,
            'is_used_in_grid'               => isset($row[20]) && $row[20] !== ''? (int) $row[20]: false,
            'is_visible_in_grid'            => isset($row[21]) && $row[21] !== ''? (int) $row[21]: false,
            'is_filterable_in_grid'         => isset($row[22]) && $row[22] !== ''? (int) $row[22]: false,
            'search_weight'                 => isset($row[23])? (float) $row[23]: false,
            'additional_data'               => isset($row[24])? ($row[24] !== ''? $row[24]: null): false,
        ], function ($v) {
            return $v !== false;
        });
        //foreach ($new as $k => $v) {
        //    if ($v === false) {
        //        unset($new[$k]);
        //    }
        //}
        if (!$new) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }

        try {
            // try to get product attribute
            $aId = $this->_getAttributeId($row[1], 'catalog_product');
        } catch (\Exception $e) {
            // if it fails, try with category attribute
            $aId = $this->_getAttributeId($row[1], 'catalog_category');
        }
        if (!$aId) {
            $this->_profile->getLogger()->setColumn(2);
            throw new LocalizedException(__('Invalid attribute: %1', $row[1]));
        }

//        $exists = $this->_write->fetchRow(
//            "SELECT a.is_user_defined, ca.* FROM {$aTable} a INNER JOIN {$caTable} ca ON ca.attribute_id=a.attribute_id
//                 WHERE a.attribute_id=" . $aId);
        $exists = $this->_write->fetchRow($this->_write->select()
                                              ->from(['a' => $aTable], 'is_user_defined')
                                              ->join(['ca' => $caTable], 'ca.attribute_id=a.attribute_id')
                                              ->where('a.attribute_id=?', $aId));
        if (!$exists) {
            $new['attribute_id'] = $aId;
            $this->_write->insert($caTable, $new);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif (($systemAttributes || $exists['is_user_defined']) && $this->_isChangeRequired($exists, $new)) {
            $this->_write->update($caTable, $new, 'attribute_id=' . $aId);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowEAS($row)
    {
        if (count($row) < 2) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $asTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_SET);

        $etId = $this->_getEntityType(!empty($row[3]) ? $row[3] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(4);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $sortOrder = (int)(isset($row[2]) ? $row[2] : null);

//        $exists = $this->_write->fetchRow(
//            "SELECT * FROM {$asTable} WHERE entity_type_id={$etId} AND attribute_set_name=?",
//            $row[1]
//        );
        $exists = $this->_write->fetchRow($this->_write->select()->from($asTable)
                                              ->where('entity_type_id=?', $etId)
                                              ->where(' attribute_set_name=?', $row[1]));
        if (!$exists) {
            $this->_write->insert($asTable,
                                  [
                                      'entity_type_id' => $etId,
                                      'attribute_set_name' => $row[1],
                                      'sort_order' => $sortOrder
                                  ]);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ($sortOrder != $exists['sort_order']) {
            $this->_write->update($asTable, ['sort_order' => $sortOrder],
                                  'attribute_set_id=' . $exists['attribute_set_id']);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowEAG($row)
    {
        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $asTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_SET);
        $agTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_GROUP);

        $setName = $this->_convertEncoding($row[1]);
        $groupName = $this->_convertEncoding($row[2]);

        $etId = $this->_getEntityType(!empty($row[7]) ? $row[7] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(5);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $sortOrder = (int)(isset($row[3]) ? $row[3] : null);

//        $asId = $this->_write->fetchOne(
//            "SELECT attribute_set_id FROM {$asTable} WHERE entity_type_id={$etId} AND attribute_set_name=?",
//            $setName
//        );
        $asId = $this->_write->fetchOne($this->_write->select()->from($asTable, 'attribute_set_id')
                                            ->where('entity_type_id=?', $etId)
                                            ->where('attribute_set_name=?', $setName));
        if (!$asId) {
            $this->_profile->getLogger()->setColumn(2);
            throw new LocalizedException(__('Invalid attribute set'));
        }

//        $exists = $this->_write->fetchRow(
//            "SELECT * FROM {$agTable} WHERE attribute_set_id={$asId} AND attribute_group_name=?",
//            $groupName
//        );
        $exists = $this->_write->fetchRow($this->_write->select()->from($agTable)
                                              ->where('attribute_set_id=?', $asId)
                                              ->where('attribute_group_name=?', $groupName));
        if (!$exists) {
            $this->_write->insert($agTable,
                                  [
                                      'attribute_set_id' => $asId,
                                      'attribute_group_name' => $groupName,
                                      'sort_order' => $sortOrder,
                                      'default_id' => (int)(isset($row[4]) ? $row[4] : null),
                                      'attribute_group_code' => isset($row[5]) ? $row[5] : null,
                                      'tab_group_code' => isset($row[6]) ? $row[6] : null,
                                  ]);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ($sortOrder != $exists['sort_order']) {
            $this->_write->update($agTable, ['sort_order' => $sortOrder],
                                  'attribute_group_id=' . $exists['attribute_group_id']);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowEASI($row)
    {
        if (count($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $asTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_SET);
        $agTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_GROUP);
        $eaTable = $this->_t(self::TABLE_EAV_ENTITY_ATTRIBUTE);

        $etId = $this->_getEntityType(!empty($row[5]) ? $row[5] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(6);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $setName = $this->_convertEncoding($row[1]);
//        $asId = $this->_write->fetchOne("SELECT attribute_set_id FROM {$asTable} WHERE entity_type_id={$etId} AND attribute_set_name=?",
//            $setName);
        $asId = $this->_write->fetchOne($this->_write->select()->from($asTable, 'attribute_set_id')
                                            ->where('entity_type_id=?', $etId)
                                            ->where('attribute_set_name=?', $setName));
        if (!$asId) {
            $this->_profile->getLogger()->setColumn(2);
            throw new LocalizedException(__('Invalid attribute set'));
        }
        $groupName = $this->_convertEncoding($row[2]);
//        $agId = $this->_write->fetchOne("SELECT attribute_group_id FROM {$agTable} WHERE attribute_set_id={$asId} AND attribute_group_name=?",
//            $groupName);
        $agId = $this->_write->fetchOne($this->_write->select()->from($agTable, 'attribute_group_id')
                                            ->where('attribute_set_id=?', $asId)
                                            ->where('attribute_group_name=?', $groupName));
        if (!$agId) {
            $this->_profile->getLogger()->setColumn(3);
            throw new LocalizedException(__('Invalid attribute group'));
        }
        $aId = $this->_getAttributeId($row[3], $etId);
        if (!$aId) {
            $this->_profile->getLogger()->setColumn(4);
            throw new LocalizedException(__('Invalid attribute: %1', $row[3]));
        }
        if (isset($row[4])) {
            $sortOrder = (int)$row[4];
        } else {
//            $sortOrder = 1 + (int)$this->_write->fetchOne("SELECT max(sort_order) FROM {$eaTable} WHERE attribute_set_id={$asId} AND attribute_group_id={$agId}");
            $maxOrder = $this->_write->fetchOne($this->_write->select()
                                                    ->from($eaTable, new \Zend_Db_Expr('max(sort_order)'))
                                                    ->where('attribute_set_id=?', $asId)
                                                    ->where(' attribute_group_id=?', $agId));
            $sortOrder = 1 + (int)$maxOrder;
        }

//        $exists = $this->_write->fetchRow("SELECT ea.* FROM {$eaTable} ea INNER JOIN {$asTable} `as` ON as.attribute_set_id=ea.attribute_set_id WHERE ea.attribute_set_id={$asId} AND ea.attribute_id={$aId}");
        $exists = $this->_write->fetchRow($this->_write->select()->from(['ea' => $eaTable])
                                              ->join(['as' => $asTable],
                                                     'as.attribute_set_id=ea.attribute_set_id', [])
                                              ->where('ea.attribute_set_id=?', $asId)
                                              ->where(' ea.attribute_id=?', $aId));
        if (!$exists) {
            $this->_write->insert($eaTable,
                                  [
                                      'entity_type_id' => $etId,
                                      'attribute_set_id' => $asId,
                                      'attribute_group_id' => $agId,
                                      'attribute_id' => $aId,
                                      'sort_order' => $sortOrder
                                  ]);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ($exists['attribute_group_id'] != $agId || $exists['sort_order'] != $sortOrder) {
            $this->_write->update($eaTable, ['attribute_group_id' => $agId, 'sort_order' => $sortOrder],
                                  'entity_attribute_id=' . $exists['entity_attribute_id']);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowEAO($row)
    {
        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $oTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $olTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE);

        $etId = $this->_getEntityType(!empty($row[4]) ? $row[4] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(5);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $aId = $this->_getAttributeId($row[1], $etId);
        if (!$aId) {
            $this->_profile->getLogger()->setColumn(2);
            throw new LocalizedException(__('Invalid attribute: %1', $row[1]));
        }
        $title = $this->_convertEncoding(trim($row[2]));
        $sortOrder = (int)(isset($row[3]) ? $row[3] : null);

        // are duplicate option values allowed
        $duplicates = $this->_profile->getData('options/duplicate_option_values');

        $result = self::IMPORT_ROW_RESULT_NOCHANGE;
//        $exists = $this->_write->fetchRow("SELECT o.option_id, ol.value_id, o.sort_order FROM {$oTable} o
//            LEFT JOIN {$olTable} ol ON ol.option_id=o.option_id AND ol.store_id=0 WHERE o.attribute_id={$aId} AND ol.value=?",
//            $title);
        $exists = $this->_write->fetchRow($this->_write->select()->from(['o' => $oTable], ['option_id', 'sort_order'])
                                              ->joinLeft(['ol' => $olTable],
                                                         'ol.option_id=o.option_id and ol.store_id=0', 'value_id')
                                              ->where('o.attribute_id=?', $aId)->where(' ol.value=?', $title)
        );
        if ($duplicates || !$exists) { // if duplicates are allowed, or option value does not exist
            $this->_write->insert($oTable, ['attribute_id' => $aId, 'sort_order' => $sortOrder]);
            $exists['option_id'] = $this->_write->lastInsertId();
            $result = self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ($exists['sort_order'] != $sortOrder) {
            $this->_write->update($oTable, ['sort_order' => $sortOrder], 'option_id=' . $exists['option_id']);
            $result = self::IMPORT_ROW_RESULT_SUCCESS;
        }
        if ($duplicates || empty($exists['value_id'])) { // if duplicates are allowed, or option is just created
            $this->_write->insert($olTable,
                                  ['option_id' => $exists['option_id'], 'store_id' => 0, 'value' => $title]);
            $result = self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return $result;
    }

    protected function _importRowEAOL($row)
    {
        if (count($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $oTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $olTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE);

        $etId = $this->_getEntityType(!empty($row[5]) ? $row[5] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(6);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $aId = $this->_getAttributeId($row[1], $etId);
        if (!$aId) {
            $this->_profile->getLogger()->setColumn(2);
            throw new LocalizedException(__('Invalid attribute: %1', $row[1]));
        }
        $defTitle = $this->_convertEncoding(trim($row[2]));
        $sId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($sId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $title = $this->_convertEncoding(trim($row[4]));
//        $exists = $this->_write->fetchRow("SELECT o.option_id, ol.value_id, ol.value FROM {$oTable} o
//            INNER JOIN {$olTable} od ON od.option_id=o.option_id AND od.store_id=0
//            LEFT JOIN {$olTable} ol ON ol.option_id=o.option_id AND ol.store_id={$sId}
//            WHERE o.attribute_id={$aId} AND od.value=" . $this->_write->quote($defTitle));
        $exists = $this->_write->fetchRow(
            $this->_write->select()->from(['o' => $oTable], 'option_id')
                ->join(['od' => $olTable], 'od.option_id=o.option_id and od.store_id=0', [])
                ->joinLeft(['ol' => $olTable],
                           'ol.option_id=o.option_id AND ' . $this->_write->quoteInto('ol.store_id=?', $sId),
                           ['value_id', 'value'])
                ->where('o.attribute_id=?', $aId)->where(' od.value=?', $defTitle)
        );
        if (!$exists) {
            $this->_profile->getLogger()->setColumn(3);
            throw new LocalizedException(__('Invalid attribute option'));
        } elseif (!$exists['value_id']) {
            $this->_write->insert($olTable,
                                  ['option_id' => $exists['option_id'], 'store_id' => $sId, 'value' => $title]);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ($exists['value_id'] && $exists['value'] !== $title) {
            $this->_write->update($olTable, ['value' => $title],
                                  ['option_id=?' => $exists['option_id'], 'store_id=?' => $sId]);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _importRowEAOS($row)
    {
        if (count($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $etId = $this->_getEntityType(!empty($row[5]) ? $row[5] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(6);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $aId = $this->_getAttributeId($row[1], $etId);
        if (!$aId) {
            $this->_profile->getLogger()->setColumn(2);
            throw new LocalizedException(__('Invalid attribute: %1', $row[1]));
        }
        $swatchName = $this->_convertEncoding(trim($row[2]));
        $optionName = $this->_convertEncoding(trim($row[3]));
        $sortOrder = (int)(isset($row[4]) ? $row[4] : null);

        $attrOptTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $attrOptSwatchTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_SWATCH);
        $attrOptValTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE);

        // are duplicate option values allowed
        $duplicates = $this->_profile->getData('options/duplicate_option_values');

        $result = self::IMPORT_ROW_RESULT_NOCHANGE;

        $exists = $this->_write->fetchRow(
            $this->_write->select()->from(['o' => $attrOptTable], ['option_id', 'sort_order'])
                ->joinLeft(['ol' => $attrOptSwatchTable], 'ol.option_id=o.option_id AND ol.store_id=0', ['swatch_id', 'value'])
                ->joinLeft(['ovl' => $attrOptValTable], 'ovl.option_id=o.option_id AND ovl.store_id=0', [])
                ->where('o.attribute_id=?', $aId)
                ->where(' ol.value=?', $swatchName)
                ->orWhere(' ovl.value=?', $optionName)
        );
        if ($duplicates || !$exists) { // if duplicates are allowed, or option value does not exist
            $this->_write->insert($attrOptTable, ['attribute_id' => $aId, 'sort_order' => $sortOrder]);
            $exists['option_id'] = $this->_write->lastInsertId();
            $this->_write->insert($attrOptValTable,
                  [
                      'option_id' => $exists['option_id'],
                      'store_id' => 0,
                      'value' => $optionName
                  ]);
            $result = self::IMPORT_ROW_RESULT_SUCCESS;
        } else if ($exists && !empty($exists['swatch_id'])) {
            if ($exists['sort_order'] != $sortOrder) {
                $this->_write->update($attrOptTable, ['sort_order' => $sortOrder], 'option_id=' . $exists['option_id']);
                $result = self::IMPORT_ROW_RESULT_SUCCESS;
            }
            if ($exists['value'] != $swatchName) {
                $this->_write->update($attrOptSwatchTable, ['value' => $swatchName], ['swatch_id=?' => $exists['swatch_id']]);
                $result = self::IMPORT_ROW_RESULT_SUCCESS;
            }
        }
        if ($duplicates || empty($exists['swatch_id'])) { // if duplicates are allowed, or option is just created
            $type = (int)(isset($row[5]) ? $row[5] : Swatch::SWATCH_TYPE_EMPTY);
            if (!in_array($type, [
                Swatch::SWATCH_TYPE_TEXTUAL,
                Swatch::SWATCH_TYPE_VISUAL_COLOR,
                Swatch::SWATCH_TYPE_VISUAL_IMAGE,
                Swatch::SWATCH_TYPE_EMPTY,
                ])) { // 'Swatch type: 0 - text, 1 - visual color, 2 - visual image',
                $type = Swatch::SWATCH_TYPE_EMPTY;
            }
            $this->_write->insert($attrOptSwatchTable,
                                  [
                                      'option_id' => $exists['option_id'],
                                      'store_id' => 0,
                                      'type' => $type,
                                      'value' => $swatchName
                                  ]);
            $result = self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return $result;
    }

    protected function _importRowEAOSL($row)
    {
        if (count($row) < 5) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $oTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $olTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_SWATCH);

        $etId = $this->_getEntityType(!empty($row[5]) ? $row[5] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(6);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $aId = $this->_getAttributeId($row[1], $etId);
        if (!$aId) {
            $this->_profile->getLogger()->setColumn(2);
            throw new LocalizedException(__('Invalid attribute %1', $row[1]));
        }
        $defTitle = $this->_convertEncoding(trim($row[2]));
        $sId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($sId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
        $title = $this->_convertEncoding(trim($row[4]));
//        $exists = $this->_write->fetchRow("SELECT o.option_id, ol.swatch_id, ol.value, od.type FROM {$oTable} o
//            INNER JOIN {$olTable} od ON od.option_id=o.option_id AND od.store_id=0
//            LEFT JOIN {$olTable} ol ON ol.option_id=o.option_id AND ol.store_id={$sId}
//            WHERE o.attribute_id={$aId} AND od.value=" . $this->_write->quote($defTitle));

        $exists = $this->_write->fetchRow(
            $this->_write->select()->from(['o' => $oTable], 'option_id')
                ->join(['od' => $olTable], 'od.option_id=o.option_id AND od.store_id=0', 'type')
                ->joinLeft(['ol' => $olTable], 'ol.option_id=o.option_id', ['swatch_id', 'value'])
                ->where('o.attribute_id=?', $aId)->where(' od.value=?', $defTitle)
                ->where(' ol.store_id=?', $sId)
        );

        if (!$exists) {
            $this->_profile->getLogger()->setColumn(3);
            $this->_profile->getLogger()->warning(__('Base swatch value not found'));

            return self::IMPORT_ROW_RESULT_DEPENDS;
        } elseif (!$exists['swatch_id']) {
            $this->_write->insert($olTable,
                                  [
                                      'option_id' => $exists['option_id'],
                                      'store_id' => $sId,
                                      'value' => $title,
                                      'type' => $exists['type']
                                  ]);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        } elseif ($exists['value_id'] && $exists['value'] !== $title) {
            $this->_write->update($olTable, ['value' => $title],
                                  ['option_id=?' => $exists['option_id'], 'store_id=?' => $sId]);

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _renameRowEA($row)
    {
        return self::IMPORT_ROW_RESULT_ERROR;

        #%EA,attribute_code,new_attribute_code,entity_type
        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $etId = $this->_getEntityType(!empty($row[3]) ? $row[3] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(4);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $aId = $this->_getAttributeId($row[1], $etId);
        if (!$aId) {
            $this->_profile->getLogger()->setColumn(2);
            throw new LocalizedException(__('Invalid attribute: %1', $row[1]));
        }
        if ($row[1] != $row[2]) {
            #$this->_
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _renameRowEAS($row)
    {
        return self::IMPORT_ROW_RESULT_ERROR;
    }

    protected function _renameRowEAG($row)
    {
        return self::IMPORT_ROW_RESULT_ERROR;
    }

    protected function _renameRowEAO($row)
    {
        return self::IMPORT_ROW_RESULT_ERROR;
    }

    protected function _deleteRowEA($row)
    {

        if (count($row) < 2) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $aTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);

        $label = $this->_convertEncoding($row[1]);
        $etId = $this->_getEntityType(!empty($row[2]) ? $row[2] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(3);
            throw new LocalizedException(__('Invalid entity type'));
        }
//        $existsId = $this->_write->fetchOne("SELECT attribute_id FROM {$aTable} WHERE entity_type_id={$etId} AND attribute_code=?",
//            $label);
        $existsId = $this->_write->fetchOne($this->_write->select()->from($aTable, 'attribute_id')
                                                ->where('entity_type_id=?', $etId)
                                                ->where(' attribute_code=?', $label));
        if ($existsId) {
            $this->_write->delete($aTable, "attribute_id={$existsId}");

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowEAL($row)
    {
        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $aTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $alTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_LABEL);

        $etId = $this->_getEntityType(!empty($row[3]) ? $row[3] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(4);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $sId = $this->_getStoreId($row[2]);
        if ($this->_skipStore($sId, 3)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
//        $existsId = $this->_write->fetchOne("SELECT al.attribute_label_id FROM {$aTable} a
//            INNER JOIN {$alTable} al ON a.attribute_id=al.attribute_id
//            WHERE a.entity_type_id={$etId} AND al.store_id={$sId} AND a.attribute_code=?", $row[1]);
        $existsId = $this->_write->fetchOne($this->_write->select()->from(['a' => $aTable], [])
                                                ->join(['al' => $alTable], 'a.attribute_id=al.attribute_id',
                                                       'attribute_label_id')
                                                ->where('a.entity_type_id=?', $etId)
                                                ->where(' al.store_id=?', $sId)
                                                ->where(' a.attribute_code=?', $row[1]));
        if ($existsId) {
            $this->_write->delete($alTable, "attribute_label_id={$existsId}");

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowEAS($row)
    {
        if (count($row) < 2) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $asTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_SET);

        $setName = $this->_convertEncoding($row[1]);
        $etId = $this->_getEntityType(!empty($row[2]) ? $row[2] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(3);
            throw new LocalizedException(__('Invalid entity type'));
        }
//        $existsId = $this->_write->fetchOne("SELECT attribute_set_id FROM {$asTable} WHERE entity_type_id={$etId} AND attribute_set_name=?",
//            $setName);
        $existsId = $this->_write->fetchOne($this->_write->select()->from($asTable, 'attribute_set_id')
                                                ->where('entity_type_id=?', $etId)
                                                ->where(' attribute_set_name=?', $setName));
        if ($existsId) {
            $this->_write->delete($asTable, "attribute_set_id={$existsId}");

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowEAG($row)
    {

        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $asTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_SET);
        $agTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_GROUP);

        $setName = $this->_convertEncoding($row[1]);
        $groupName = $this->_convertEncoding($row[2]);

        $etId = $this->_getEntityType(!empty($row[3]) ? $row[3] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(4);
            throw new LocalizedException(__('Invalid entity type'));
        }
//        $existsId = $this->_write->fetchOne("SELECT ag.attribute_group_id FROM {$agTable} ag
//            INNER JOIN {$asTable} `as` ON as.attribute_set_id=ag.attribute_set_id
//            WHERE as.entity_type_id={$etId} AND as.attribute_set_name={$this->_write->quote($setName)} AND ag.attribute_group_name={$this->_write->quote($groupName)}");
        $existsId = $this->_write->fetchOne(
            $this->_write->select()->from(['ag' => $agTable], 'attribute_group_id')
                ->join(['as' => $asTable], 'as.attribute_set_id=ag.attribute_set_id', [])
                ->where('as.entity_type_id=?', $etId)->where(' as.attribute_set_name=?', $setName)
                ->where(' ag.attribute_group_name=?', $groupName)
        );
        if ($existsId) {
            $this->_write->delete($agTable, "attribute_group_id={$existsId}");

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowEASI($row)
    {

        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $asTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_SET);
        $eaTable = $this->_t(self::TABLE_EAV_ENTITY_ATTRIBUTE);
        $aTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);

        $setName = $this->_convertEncoding($row[1]);
        $groupName = $this->_convertEncoding($row[2]);

        $etId = $this->_getEntityType(!empty($row[3]) ? $row[3] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(4);
            throw new LocalizedException(__('Invalid entity type'));
        }
//        $existsId = $this->_write->fetchOne("SELECT ea.entity_attribute_id from {$eaTable} ea
//            INNER JOIN {$asTable} `as` ON as.attribute_set_id=ea.attribute_set_id
//            INNER JOIN {$aTable} a ON a.attribute_id=ea.attribute_id
//            WHERE as.entity_type_id={$etId} AND as.attribute_set_name={$this->_write->quote($setName)} AND a.attribute_code={$this->_write->quote($groupName)}");
        $existsId = $this->_write->fetchOne(
            $this->_write->select()->from(['ea' => $eaTable], 'entity_attribute_id')
                ->join(['as' => $asTable], 'as.attribute_set_id=ea.attribute_set_id', [])
                ->join(['a' => $aTable], 'a.attribute_id=ea.attribute_id', [])
                ->where('as.entity_type_id=?', $etId)->where(' as.attribute_set_name=?', $setName)
                ->where(' a.attribute_code=?', $groupName)
        );
        if ($existsId) {
            $this->_write->delete($eaTable, "entity_attribute_id={$existsId}");

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowEAO($row)
    {

        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $aTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $oTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $olTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE);

        $label = $this->_convertEncoding($row[2]);

        $etId = $this->_getEntityType(!empty($row[3]) ? $row[3] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(4);
            throw new LocalizedException(__('Invalid entity type'));
        }
//        $existsId = $this->_write->fetchOne("SELECT o.option_id FROM {$oTable} o
//            INNER JOIN {$aTable} a ON a.attribute_id=o.attribute_id
//            INNER JOIN {$olTable} ol ON ol.option_id=o.option_id AND ol.store_id=0
//            WHERE a.entity_type_id={$etId} AND a.attribute_code={$this->_write->quote($row[1])} AND ol.value={$this->_write->quote($label)}");

        $existsId = $this->_write->fetchOne(
            $this->_write->select()->from(['o' => $oTable], 'option_id')
                ->join(['a' => $aTable], 'a.attribute_id=o.attribute_id', [])
                ->join(['ol' => $olTable], 'ol.option_id=o.option_id and ol.store_id=0', [])
                ->where('a.entity_type_id=?', $etId)->where(' a.attribute_code=?', $row[1])
                ->where(' ol.value=?', $label)
        );

        if ($existsId) {
            $this->_write->delete($oTable, "option_id={$existsId}");

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowEAOS($row)
    {

        if (count($row) < 3) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $aTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $oTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $olTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_SWATCH);

        $label = $this->_convertEncoding($row[2]);

        $etId = $this->_getEntityType(!empty($row[3]) ? $row[3] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(4);
            throw new LocalizedException(__('Invalid entity type'));
        }

//        $existsId = $this->_write->fetchOne("SELECT o.option_id FROM {$oTable} o
//            INNER JOIN {$aTable} a ON a.attribute_id=o.attribute_id
//            INNER JOIN {$olTable} ol ON ol.option_id=o.option_id AND ol.store_id=0
//            WHERE a.entity_type_id={$etId} AND a.attribute_code={$this->_write->quote($row[1])} AND ol.value={$this->_write->quote($label)}");
        $existsId = $this->_write->fetchOne(
            $this->_write->select()->from(['o' => $oTable], 'option_id')
                ->join(['a' => $aTable], 'a.attribute_id=o.attribute_id', [])
                ->join(['ol' => $olTable], 'ol.option_id=o.option_id')
                ->where('a.entity_type_id=?', $etId)->where(' a.attribute_code=?', $row[1])
                ->where(' ol.value=?', $label)
        );
        if ($existsId) {
            $this->_write->delete($oTable, "option_id={$existsId}");

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowEAOSL($row)
    {

        if (count($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $aTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $oTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $olTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_SWATCH);

        $label = $this->_convertEncoding($row[2]);

        $etId = $this->_getEntityType(!empty($row[4]) ? $row[4] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(5);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $sId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($sId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
//        $existsId = $this->_write->fetchOne("SELECT ol.value_id FROM {$oTable} o
//            INNER JOIN {$aTable} a ON a.attribute_id=o.attribute_id
//            INNER JOIN {$olTable} od ON od.option_id=o.option_id AND od.store_id=0
//            INNER JOIN {$olTable} ol ON ol.option_id=o.option_id AND ol.store_id={$sId}
//            WHERE a.entity_type_id={$etId} AND a.attribute_code={$this->_write->quote($row[1])} AND od.value={$this->_write->quote($label)}");

        $existsId = $this->_write->fetchOne(
            $this->_write->select()->from(['o' => $oTable], [])
                ->join(['a' => $aTable], 'a.attribute_id=o.attribute_id', [])
                ->join(['od' => $olTable], 'od.option_id=o.option_id AND od.store_id=0', [])
                ->join(['ol' => $olTable], 'ol.option_id=o.option_id', 'value_id')
                ->where('a.entity_type_id=?', $etId)->where(' a.attribute_code=?', $row[1])
                ->where(' od.value=?', $label)->where(' ol.store_id=?', $sId)
        );
        if ($existsId) {
            $this->_write->delete($olTable, "value_id={$existsId}");

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _deleteRowEAOL($row)
    {

        if (count($row) < 4) {
            throw new LocalizedException(__('Invalid row format'));
        }

        $aTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $oTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $olTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE);

        $label = $this->_convertEncoding($row[2]);

        $etId = $this->_getEntityType(!empty($row[4]) ? $row[4] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(5);
            throw new LocalizedException(__('Invalid entity type'));
        }
        $sId = $this->_getStoreId($row[3]);
        if ($this->_skipStore($sId, 4)) {
            return self::IMPORT_ROW_RESULT_NOCHANGE;
        }
//        $existsId = $this->_write->fetchOne("SELECT ol.value_id FROM {$oTable} o
//            INNER JOIN {$aTable} a ON a.attribute_id=o.attribute_id
//            INNER JOIN {$olTable} od ON od.option_id=o.option_id AND od.store_id=0
//            INNER JOIN {$olTable} ol ON ol.option_id=o.option_id AND ol.store_id={$sId}
//            WHERE a.entity_type_id={$etId} AND a.attribute_code={$this->_write->quote($row[1])} AND od.value={$this->_write->quote($label)}");

        $existsId = $this->_write->fetchOne(
            $this->_write->select()->from(['o' => $oTable], [])
                ->join(['a' => $aTable], 'a.attribute_id=o.attribute_id', [])
                ->join(['od' => $olTable], 'od.option_id=o.option_id and od.store_id=0', [])
                ->join(['ol' => $olTable], 'ol.option_id=o.option_id', 'value_id')
                ->where('a.entity_type_id=?', $etId)->where(' a.attribute_code=?', $row[1])
                ->where(' od.value=?', $label)->where(' ol.store_id=?', $sId)
        );
        if ($existsId) {
            $this->_write->delete($olTable, "value_id={$existsId}");

            return self::IMPORT_ROW_RESULT_SUCCESS;
        }

        return self::IMPORT_ROW_RESULT_NOCHANGE;
    }

    protected function _exportCleanEntityType(&$row)
    {
        if (!empty($row['entity_type']) && $row['entity_type'] === 'catalog_product') {
            $row['entity_type'] = '';
        }

        return true;
    }

    protected function _exportFilterEntityType($tableAlias = 'main')
    {
        $etIds = $this->_profile->getData('options/entity_types');
        if ($etIds) {
            $this->_select->where($tableAlias . '.entity_type_id in (?)', $etIds);
        }
    }

    protected function _exportInitEA()
    {
        $attrTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $etTable = $this->_t(self::TABLE_EAV_ENTITY_TYPE);

        $this->_select = $this->_read->select()
            ->from(['main' => $attrTable],
                   [
                       'attribute_code',
                       'backend_type',
                       'frontend_label',
                       'frontend_input',
                       'is_required',
                       'is_unique'
                   ])
            ->join(['et' => $etTable], 'et.entity_type_id=main.entity_type_id',
                   ['entity_type' => 'entity_type_code']);

        if (!$this->_profile->getData('options/system_attributes')) {
            $this->_select->where('main.is_user_defined=1');
        }

        $this->_exportFilterEntityType();

        $this->_exportConvertFields = ['frontend_label'];
    }

    protected function _exportInitEAX()
    {
        $attrTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $etTable = $this->_t(self::TABLE_EAV_ENTITY_TYPE);

        $this->_select = $this->_read->select()
            ->from(['main' => $attrTable],
                   [
                       'attribute_code',
                       'attribute_model',
                       'backend_model',
                       'backend_table',
                       'frontend_model',
                       'frontend_class',
                       'source_model',
                       'default_value',
                       'note'
                   ])
            ->join(['et' => $etTable], 'et.entity_type_id=main.entity_type_id',
                   ['entity_type' => 'entity_type_code']);

        if (!$this->_profile->getData('options/system_attributes')) {
            $this->_select->where('main.is_user_defined=1');
        }

        $this->_exportFilterEntityType();

        $this->_exportConvertFields = ['default_value', 'note'];
    }

    protected function _exportInitEAL()
    {
        $attrTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $alTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_LABEL);
        $etTable = $this->_t(self::TABLE_EAV_ENTITY_TYPE);
        $sTable = $this->_t(self::TABLE_STORE);

        $this->_select = $this->_read->select()
            ->from(['main' => $attrTable], ['attribute_code'])
            ->join(['al' => $alTable], 'al.attribute_id=main.attribute_id',
                   ['label' => 'value'])
            ->join(['et' => $etTable], 'et.entity_type_id=main.entity_type_id',
                   ['entity_type' => 'entity_type_code'])
            ->join(['s' => $sTable], 's.store_id=al.store_id', ['store' => 'code'])
            ->where('al.store_id in (?)', $this->_getStoreIds());

        if (!$this->_profile->getData('options/system_attributes')) {
            $this->_select->where('main.is_user_defined=1');
        }

        $this->_exportFilterEntityType();

        $this->_exportConvertFields = ['label'];
    }

    protected function _exportInitEAXP()
    {
        $attrTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);

        $this->_select = $this->_read->select();

        $catAttrTable = $this->_t(self::TABLE_CATALOG_EAV_ATTRIBUTE);
        $this->_select->from(['main' => $attrTable], ['attribute_code'])
            ->join(['ca' => $catAttrTable], 'ca.attribute_id=main.attribute_id', ['*']);
        $etId = $this->_getEntityType('catalog_product', 'entity_type_id');
        $this->_select->where('main.entity_type_id=?', $etId);

        if (!$this->_profile->getData('options/system_attributes')) {
            $this->_select->where('main.is_user_defined=1');
        }
    }

    protected function _exportInitEAS()
    {
        $asTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_SET);
        $etTable = $this->_t(self::TABLE_EAV_ENTITY_TYPE);

        $this->_select = $this->_read->select()
            ->from(['main' => $asTable], ['set_name' => 'attribute_set_name', 'sort_order'])
            ->join(['et' => $etTable], 'et.entity_type_id=main.entity_type_id',
                   ['entity_type' => 'entity_type_code']);

        $this->_exportFilterEntityType();

        $this->_exportConvertFields = ['set_name'];
    }

    protected function _exportInitEAG()
    {
        $agTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_GROUP);
        $asTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_SET);
        $etTable = $this->_t(self::TABLE_EAV_ENTITY_TYPE);

        $this->_select = $this->_read->select()
            ->from(['main' => $agTable],
                   [
                       'group_name' => 'attribute_group_name',
                       'sort_order',
                       'default_id',
                       'attribute_group_code',
                       'tab_group_code'
                   ])
            ->join(['as' => $asTable], 'as.attribute_set_id=main.attribute_set_id',
                   ['set_name' => 'attribute_set_name'])
            ->join(['et' => $etTable], 'et.entity_type_id=as.entity_type_id',
                   ['entity_type' => 'entity_type_code']);

        $this->_exportFilterEntityType('as');

        $this->_exportConvertFields = ['group_name'];
    }

    protected function _exportInitEASI()
    {
        $attrTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $eaTable = $this->_t(self::TABLE_EAV_ENTITY_ATTRIBUTE);
        $asTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_SET);
        $agTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_GROUP);
        $etTable = $this->_t(self::TABLE_EAV_ENTITY_TYPE);

        $this->_select = $this->_read->select()
            ->from(['main' => $eaTable], ['sort_order'])
            ->join(['as' => $asTable], 'as.attribute_set_id=main.attribute_set_id',
                   ['set_name' => 'attribute_set_name'])
            ->join(['ag' => $agTable], 'ag.attribute_group_id=main.attribute_group_id',
                   ['group_name' => 'attribute_group_name'])
            ->join(['a' => $attrTable], 'a.attribute_id=main.attribute_id', ['attribute_code'])
            ->join(['et' => $etTable], 'et.entity_type_id=main.entity_type_id',
                   ['entity_type' => 'entity_type_code'])
            ->order([
                        'as.sort_order',
                        'as.attribute_set_name',
                        'ag.sort_order',
                        'ag.attribute_group_name',
                        'main.sort_order',
                        'a.attribute_code'
                    ]);

        $this->_exportFilterEntityType();

        $this->_exportConvertFields = ['set_name', 'group_name'];
    }

    protected function _exportInitEAO()
    {
        $attrTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $etTable = $this->_t(self::TABLE_EAV_ENTITY_TYPE);
        $oTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $olTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE);

        $this->_select = $this->_read->select()
            ->from(['main' => $oTable], ['sort_order'])
            ->join(['a' => $attrTable], 'a.attribute_id=main.attribute_id', ['attribute_code'])
            ->join(['ol' => $olTable], 'ol.option_id=main.option_id AND ol.store_id=0',
                   ['option_name' => 'value'])
            ->join(['et' => $etTable], 'et.entity_type_id=a.entity_type_id',
                   ['entity_type' => 'entity_type_code'])
            ->where('a.frontend_input in (?)', ['multiselect','select']);

        $this->_exportFilterEntityType('a');

        $this->_exportConvertFields = ['option_name'];
    }

    protected function _exportInitEAOS()
    {
        $attrTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $etTable = $this->_t(self::TABLE_EAV_ENTITY_TYPE);
        $attrOptTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $attrOptSwatchTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_SWATCH);
        $attrOptValTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE);

        $this->_select = $this->_read->select()
            ->from(['main' => $attrOptTable], ['sort_order'])
            ->join(['a' => $attrTable], 'a.attribute_id=main.attribute_id', ['attribute_code'])
            ->join(['ol' => $attrOptSwatchTable], 'ol.option_id=main.option_id AND ol.store_id=0',
                   ['swatch_name' => 'value', 'type'])
            ->join(['ovl' => $attrOptValTable], 'ovl.option_id=main.option_id AND ovl.store_id=0',
                   ['option_name' => 'value'])
            ->join(['et' => $etTable], 'et.entity_type_id=a.entity_type_id',
                   ['entity_type' => 'entity_type_code']);

        $this->_exportFilterEntityType('a');

        $this->_exportConvertFields = ['option_name', 'swatch_name'];
    }

    protected function _exportInitEAOL()
    {
        $attrTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $etTable = $this->_t(self::TABLE_EAV_ENTITY_TYPE);
        $oTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $olTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE);
        $sTable = $this->_t(self::TABLE_STORE);

        $this->_select = $this->_read->select()
            ->from(['main' => $oTable], ['sort_order'])
            ->join(['a' => $attrTable], 'a.attribute_id=main.attribute_id', ['attribute_code'])
            ->join(['od' => $olTable], 'od.option_id=main.option_id AND od.store_id=0',
                   ['option_name' => 'value'])
            ->join(['ol' => $olTable], 'ol.option_id=main.option_id AND ol.store_id<>0',
                   ['option_label' => 'value'])
            ->join(['s' => $sTable], 's.store_id=ol.store_id', ['store' => 'code'])
            ->where('ol.store_id in (?)', $this->_getStoreIds());

        $this->_exportFilterEntityType('a');

        $this->_exportConvertFields = ['option_name', 'option_label'];
    }

    protected function _exportInitEAOSL()
    {
        $attrTable = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $etTable = $this->_t(self::TABLE_EAV_ENTITY_TYPE);
        $optionTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION);
        $oSwatchTable = $this->_t(self::TABLE_EAV_ATTRIBUTE_OPTION_SWATCH);
        $storeTable = $this->_t(self::TABLE_STORE);

        $this->_select = $this->_read->select()
            ->from(['main' => $optionTable], ['sort_order'])
            ->join(['a' => $attrTable], 'a.attribute_id=main.attribute_id', ['attribute_code'])
            ->join(['od' => $oSwatchTable], 'od.option_id=main.option_id AND od.store_id=0',
                   ['default_swatch_name' => 'value'])
            ->join(['ol' => $oSwatchTable], 'ol.option_id=main.option_id AND ol.store_id<>0',
                   ['locale_swatch_name' => 'value'])
            ->join(['s' => $storeTable], 's.store_id=ol.store_id', ['store' => 'code'])
            ->where('ol.store_id in (?) AND ol.`value` IS NOT NULL', $this->_getStoreIds());
        $this->_exportFilterEntityType('a');

        $this->_exportConvertFields = ['option_name', 'option_label'];
    }

    protected function _exportInitMSIS()
    {
        if (!$this->_rapidFlowHelper->compareMageVer('2.3.0')) {
            $this->_initEmptySelect();
            return ;
        }

        $sourceTable = $this->_t(self::TABLE_INVENTORY_SOURCE);

        $this->_select = $this->_read->select()
            ->from(['main' => $sourceTable]);

        $this->_exportConvertFields = ['name','description'];
    }

    protected function _exportInitMSIST()
    {
        if (!$this->_rapidFlowHelper->compareMageVer('2.3.0')) {
            $this->_initEmptySelect();
            return ;
        }

        $stockTable = $this->_t(self::TABLE_INVENTORY_STOCK);

        $this->_select = $this->_read->select()
            ->from(['main' => $stockTable], ['name']);

        $this->_exportConvertFields = ['name'];
    }

    protected function _exportInitMSISL()
    {
        if (!$this->_rapidFlowHelper->compareMageVer('2.3.0')) {
            $this->_initEmptySelect();
            return ;
        }

        $sourceTable = $this->_t(self::TABLE_INVENTORY_SOURCE);
        $stockTable = $this->_t(self::TABLE_INVENTORY_STOCK);
        $linkTable = $this->_t(self::TABLE_INVENTORY_SOURCE_STOCK_LINK);

        $this->_select = $this->_read->select()
            ->from(['main' => $sourceTable], ['source_code'])
            ->join(['l' => $linkTable], 'l.source_code=main.source_code', ['priority'])
            ->join(['s' => $stockTable], 'l.stock_id=s.stock_id', ['source_code'])
        ;

        $this->_exportConvertFields = ['name'];
    }

    protected function _getAttributeId($attrCode, $entityType = 'catalog_product')
    {
        $etId = $this->_getEntityType(!empty($row[4]) ? $row[4] : 'catalog_product', 'entity_type_id');
        if (!$etId) {
            $this->_profile->getLogger()->setColumn(0);
            throw new LocalizedException(__('Invalid entity type'));
        }

        if (empty($this->_attributes[$etId][$attrCode])) {
            $this->_loadRawAttributes($etId);
        }

        return isset($this->_attributes[$etId][$attrCode]) ? $this->_attributes[$etId][$attrCode]['attribute_id'] : null;
    }

    protected function _loadRawAttributes($entityId)
    {
        if (!empty($this->_attributes[$entityId])) {
            return;
        }
        $tEav = $this->_t(self::TABLE_EAV_ATTRIBUTE);
//        $attributes = $this->_write->fetchAll(
//            "SELECT attribute_id, attribute_code, backend_type, frontend_input, frontend_label, is_required, is_unique, is_user_defined
//FROM {$tEav} WHERE entity_type_id={$entityId}"
//        );
        $attributes = $this->_write->fetchAll(
            $this->_write->select()->from($tEav, [
                'attribute_id',
                'attribute_code',
                'backend_type',
                'frontend_input',
                'frontend_label',
                'is_required',
                'is_unique',
                'is_user_defined'
            ])->where('entity_type_id=?', [$entityId])
        );
        foreach ($attributes as $attribute) {
            $this->_attributes[$entityId][$attribute['attribute_code']] = $attribute;
        }
    }

    protected function _updateNewAttribute($attributeCode, $entityId)
    {
        $this->_loadRawAttributes($entityId); // make sure to load entity before adding to it
        $tEav = $this->_t(self::TABLE_EAV_ATTRIBUTE);
        $attributes = $this->_write->fetchAll(
            $this->_write->select()->from($tEav, [
                'attribute_id',
                'attribute_code',
                'backend_type',
                'frontend_input',
                'frontend_label',
                'is_required',
                'is_unique',
                'is_user_defined'
            ])->where('attribute_code=?', [$attributeCode])
        );
        foreach ($attributes as $attribute) {
            $this->_attributes[$entityId][$attribute['attribute_code']] = $attribute;
        }
    }
}