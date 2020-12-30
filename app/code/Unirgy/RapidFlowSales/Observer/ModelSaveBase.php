<?php

/**
 * Created by pp
 *
 * @project pp-dev-2-unirgy-ext
 */
namespace Unirgy\RapidFlowSales\Observer;

use Magento\Framework\Db\Adapter\AdapterInterface;
use Magento\Framework\Model\AbstractModel;
use Unirgy\RapidFlowSales\Helper\Data as HelperData;
use Unirgy\RapidFlowSales\Model\Misc\Uuid;

class ModelSaveBase
{
    /**
     * @var HelperData
     */
    protected $_helperData;
    /**
     * @var array of update conditions
     */
    protected $_cond;

    public function __construct(HelperData $helperData)
    {
        $this->_helperData = $helperData;

    }

    /**
     * @param AbstractModel $object
     * @param bool          $save
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _execute($object, $save = true)
    {
        $resource = $object->getResource();
        // all sales models' resources have this method
        if (!method_exists($resource, 'getMainTable')) {
            return;
        }
        $mainTable = $resource->getMainTable();

        if ($this->helper()->tableShouldBeUpdated($mainTable)) {
            $conn = $resource->getConnection();
            $uuid = $this->updateUrfId($conn, $mainTable, $object);
            if ($save) {
                $this->_save($uuid, $mainTable, $conn);
            } else {
                $object->setData(HelperData::URF_ID_FIELD, $uuid);
            }
        }
    }

    /**
     * @return HelperData
     */
    protected function helper()
    {
        return $this->_helperData;
    }

    /**
     * @param AdapterInterface $conn
     * @param string                      $mainTable
     * @param AbstractModel    $object
     * @return bool|string
     */
    protected function updateUrfId($conn, $mainTable, $object)
    {
        $tableData        = $conn->describeTable($mainTable);
        $installationUuid = $this->helper()->getInstallationUuid();
        $prefix           = $mainTable . '.';
        $suffix           = '';
        $where            = [];
        foreach ($tableData as $name => $column) {
            if ($column['PRIMARY']) {
                $value   = $object->getData($name);
                $suffix  .= sprintf('%s:%d', $name, $value);
                $where[] = $conn->quoteInto($name . '=?', $value);
            }
        }

        $this->_cond = $where;

        return Uuid::v5($installationUuid, $prefix . $suffix);
    }

    /**
     * @param $uuid
     * @param $table
     * @param AdapterInterface $conn
     */
    protected function _save($uuid, $table, $conn)
    {
        $conn->update($table, [HelperData::URF_ID_FIELD => $uuid],
            implode(' AND ', $this->_cond));
    }
}
