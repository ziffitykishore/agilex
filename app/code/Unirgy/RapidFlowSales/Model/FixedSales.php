<?php

/**
 * Created by pp
 *
 * @project pp-dev-2-unirgy-ext
 */

namespace Unirgy\RapidFlowSales\Model;

use Magento\Customer\Model\CustomerFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteFactory;
use Magento\Framework\Locale\FormatInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Unirgy\RapidFlow\Helper\Data as HelperData;
use Unirgy\RapidFlow\Model\Config as ModelConfig;
use Unirgy\RapidFlow\Model\Io\CsvFactory;
use Unirgy\RapidFlow\Model\Profile;
use Unirgy\RapidFlowSales\Helper\Data as RapidFlowSalesHelperData;
use Unirgy\RapidFlowSales\Model\Fixed\AbstractSales;
use Unirgy\RapidFlowSales\Model\Misc\Uuid;
use Unirgy\RapidFlowSales\Model\Profile\Sales;
use Unirgy\RapidFlowSales\Helper\ProtectedCode\Context;
use Magento\Framework\App\ObjectManager;
use Zend\Log\Logger;
use Magento\Framework\DB\Adapter\AdapterInterface;

class FixedSales extends AbstractSales
{
    protected function _exportInitGM()
    {
        $code = 'GM';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSBA()
    {
        $code = 'SBA';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSCM()
    {
        $code = 'SCM';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSCMC()
    {
        $code = 'SCMC';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSCMG()
    {
        $code = 'SCMG';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSCMI()
    {
        $code = 'SCMI';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSI()
    {
        $code = 'SI';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSIC()
    {
        $code = 'SIC';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSIG()
    {
        $code = 'SIG';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSII()
    {
        $code = 'SII';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSO()
    {
        $code = 'SO';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSOA()
    {
        $code = 'SOA';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSOG()
    {
        $code = 'SOG';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSOI()
    {
        $code = 'SOI';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSOP()
    {
        $code = 'SOP';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSOSH()
    {
        $code = 'SOSH';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSQ()
    {
        $code = 'SQ';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSQA()
    {
        $code = 'SQA';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSQAI()
    {
        $code = 'SQAI';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSQI()
    {
        $code = 'SQI';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSQIO()
    {
        $code = 'SQIO';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSQP()
    {
        $code = 'SQP';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSQSR()
    {
        $code = 'SQSR';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSSHIP()
    {
        $code = 'SSHIP';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSSHIPC()
    {
        $code = 'SSHIPC';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSSHIPG()
    {
        $code = 'SSHIPG';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSSHIPI()
    {
        $code = 'SSHIPI';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSSHIPT()
    {
        $code = 'SSHIPT';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSOS()
    {
        $code = 'SOS';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSOSL()
    {
        $code = 'SOSL';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSOSS()
    {
        $code = 'SOSS';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSOT()
    {
        $code = 'SOT';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSOTI()
    {
        $code = 'SOTI';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSPT()
    {
        $code = 'SPT';
        $this->_exportInitSalesType($code);
    }

    protected function _exportInitSRP()
    {
        $code = 'SRP';
        $this->_exportInitSalesType($code);
    }

    // start import rows
    protected function _importRowGM($row)
    {
        $rowType = 'GM';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSBA($row)
    {
        $rowType = 'SBA';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSCM($row)
    {
        $rowType = 'SCM';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSCMC($row)
    {
        $rowType = 'SCMC';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSCMG($row)
    {
        $rowType = 'SCMG';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSCMI($row)
    {
        $rowType = 'SCMI';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSI($row)
    {
        $rowType = 'SI';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSIC($row)
    {
        $rowType = 'SIC';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSIG($row)
    {
        $rowType = 'SIG';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSII($row)
    {
        $rowType = 'SII';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSO($row)
    {
        $rowType = 'SO';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSOA($row)
    {
        $rowType = 'SOA';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSOG($row)
    {
        $rowType = 'SOG';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSOI($row)
    {
        $rowType = 'SOI';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSOP($row)
    {
        $rowType = 'SOP';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSOSH($row)
    {
        $rowType = 'SOSH';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSQ($row)
    {
        $rowType = 'SQ';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSQA($row)
    {
        $rowType = 'SQA';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSQAI($row)
    {
        $rowType = 'SQAI';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSQI($row)
    {
        $rowType = 'SQI';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSQIO($row)
    {
        $rowType = 'SQIO';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSQP($row)
    {
        $rowType = 'SQP';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSQSR($row)
    {
        $rowType = 'SQSR';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSSHIP($row)
    {
        $rowType = 'SSHIP';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSSHIPC($row)
    {
        $rowType = 'SSHIPC';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSSHIPG($row)
    {
        $rowType = 'SSHIPG';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSSHIPI($row)
    {
        $rowType = 'SSHIPI';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSSHIPT($row)
    {
        $rowType = 'SSHIPT';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSOT($row)
    {
        $rowType = 'SOT';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSOTI($row)
    {
        $rowType = 'SOTI';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSPT($row)
    {
        $rowType = 'SPT';

        return $this->_importSalesRow($row, $rowType);
    }

    protected function _importRowSRP($row)
    {
        $rowType = 'SRP';

        return $this->_importSalesRow($row, $rowType);
    }

    /**
     * @return bool
     * @throws \Exception
     * @override
     */
    protected function _importFetchNewData()
    {
        $profile = $this->_profile;

        for ($i1 = 0; $i1 < $this->_pageRowCount; $i1++) {
            $row = $profile->ioRead();
            if (!$row) {
                // last row
                $this->_isLastPage = true;

                return true;
            }
            if ($row[0] === '' || empty($row[0])) {
                $profile->addValue(Profile::ROWS_PROCESSED)
                        ->addValue(Profile::ROWS_EMPTY);
                continue;
            }

            $method    = false;
            $rowType   = trim($row[0]);
            $rowAction = $rowType[0];
            switch ($rowAction) {
                case '#': // row type structure, first entry of type ##
                    if (strpos($rowType, '##') === 0) {
                        $this->_setStructureFor(substr($rowType, 2), $row);
                    }
                    break;

                case '-': // delete
                    $rowType = substr($rowType, 1);
                    $method  = '_deleteRow' . $rowType;
                    break;

                case '%': // rename
                    $rowType = substr($rowType, 1);
                    $method  = '_renameRow' . $rowType;
                    break;

                case '+': // add/update
                    $rowType = substr($rowType, 1);
                    $method  = '_importRow' . $rowType;
                    break;

                default: // add/update
                    $rowAction = '+';
                    $method    = '_importRow' . $rowType;
            }
            if ($method === false) {
                $profile->addValue(Profile::ROWS_EMPTY);
                continue;
            }
            if (!is_callable([$this, $method])) {
                $profile->addValue(Profile::ROWS_PROCESSED);
                throw new LocalizedException(__('Invalid row type: %1', $rowType));
            }
            if ($this->_rowTypes && !in_array($rowType, $this->_rowTypes, true)) {
                $profile->addValue(Profile::ROWS_PROCESSED)
                        ->addValue(Profile::ROWS_NOCHANGE);
                continue;
            }

            $lineNum                        = $this->_startLine + $i1;
            $this->_newRows[$lineNum]       = $row;
            $this->_newRowTypes[$lineNum]   = $rowType;
            $this->_newRowActions[$lineNum] = $rowAction;
            $this->_newRowMethods[$lineNum] = $method;
        }

        return false;
    }

    protected function _prepareRowTypeData()
    {
        $rowTypes = $this->helper()->getRowTypes();

        $this->_rowTypeFields = [];
        foreach ($rowTypes as $rowType => $config) {
            $this->_rowTypeFields[$rowType]['columns'] = $this->getRowTypeColumns($rowType);
            $this->_rowTypeFields[$rowType]['config']  = $config;
        }
    }

    protected function _importFetchNewDataIds()
    {
        $fieldValues     = [];
        $importNamespace = $this->_profile->getData(Sales::OPTIONS_IMPORT_URF_ID_PREFIX);
        foreach ($this->_newRows as $lineNum => $row) {
            // loop the new rows
            $cmd     = $row[0][0];
            $rowType = $cmd === '+' || $cmd === '-' || $cmd === '%'? substr($row[0], 1): $row[0];
            if (empty($this->_salesImportColumns[$rowType]) || empty($this->_rowTypeFields[$rowType]['columns'])) {
                continue;
            }

            $config    = $this->_rowTypeFields[$rowType]['config'];
            $mainTable = $config['table'];
            $key       = $config['key'];

            $collect = [
                [
                    'col'   => isset($config['urfid_column']) ? $config['urfid_column'] : self::URF_ID,
                    'table' => $mainTable,
                    'id'    => $config['key']
                ]
            ];
            if (!empty($config['mapped'])) {
                // if there are mapped columns, need to fetch their corresponding value, if exists
                foreach ($config['mapped'] as $map) {
                    $collect[] = [
                        'field' => $map['to'],
                        'table' => $map['table'],
                        'col'   => $map['value'],
                        'id'    => $map['dest']
                    ];
                }
            }
            $struct = $this->_salesImportColumns[$rowType];
            $keyCol = isset($struct[$key])? $struct[$key]: false;
            foreach ($collect as $fieldConf) {
                $fieldName = isset($fieldConf['field'])? $fieldConf['field']: $fieldConf['col'];
                if (empty($struct[$fieldName])) {

                    continue;
                }
                $col = $struct[$fieldName];
                if (!empty($row[$col])) {
                    $fieldValues[$fieldConf['table']][$fieldConf['col']][$fieldConf['id']][] = $row[$col];
                } else if ($fieldName === self::URF_ID && $keyCol !== false) {
                    if (empty($row[$keyCol])) {
                        $this->_profile->getLogger()
                                       ->setLine($lineNum)
                                       ->error('Missing both urf_id and entity id values');
                    }

                    $urfId = Uuid::v5($importNamespace, $rowType . $row[$keyCol]);
                    $fieldValues[$fieldConf['table']][$fieldConf['col']][$fieldConf['id']][] = $urfId;
                }
            }
        }

        foreach ($fieldValues as $table => $fields) {
            //            $rows = SELECT dest FROM table where condField IN (?)
            $cols  = [];
            $where = [];
            foreach ($fields as $condField => $condValues) {
                $cols[] = $condField;
                foreach ($condValues as $sel => $value) {
                    $where[] = $this->_read->quoteInto("{$condField} IN (?)", array_unique($value));
                    $cols[]  = $sel;
                }
                $select = $this->_read->select()->from($table, array_unique($cols));
                foreach ($where as $cond) {
                    $select->orWhere($cond);
                }
                $rows = $this->_read->fetchAll($select);
                $this->_logger->debug($table . ': ' . (string) $select);
                foreach ($rows as $row) {
                    $this->_entities[$table][$condField][$row[$condField]] = $row[$sel];
                }
            }
        }
    }

    protected function _importProcessNewData()
    {
        parent::_importProcessNewData();
        $this->_importFetchNewDataIds();
    }

    protected function getRowTypeColumns($rowType)
    {
        if (!isset($this->columns[$rowType])) {
            try {
                $columns = $this->_getRowTypeColumns($rowType);

                $this->columns[$rowType] = array_flip($columns);
            } catch(\InvalidArgumentException $e) {
                $this->_logger->debug($e->getMessage());

                return [];
            }
        }

        return $this->columns[$rowType];
    }
    protected $_profile;
    public function getProfile()
    {
        return $this->_profile;
    }
    public function setProfile($profile)
    {
        $this->_profile = $profile;
        $profileType = $profile->getProfileType();

        $this->_encodingFrom = $profile->getData('options/encoding/from');
        $this->_encodingTo = $profile->getData('options/encoding/to');
        $this->_encodingIllegalChar = $profile->getData('options/encoding/illegal_char');
        $this->_downloadRemoteImages = $profile->getData('options/' . $profileType . '/image_files_remote');
        $this->_downloadRemoteImagesBatch = $profile->getData('options/' . $profileType . '/image_files_remote_batch');
        $this->_missingImageAction = (string)$profile->getData('options/' . $profileType . '/image_missing_file');
        $this->_existingImageAction = (string)$profile->getData('options/' . $profileType . '/image_existing_file');
        $this->_remoteImageSubfolderLevel = $profile->getData('options/' . $profileType . '/image_remote_subfolder_level');
        $this->_imagesMediaDir = $this->_filesystem->getDirectoryWrite('media')->getAbsolutePath() . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'product';
        $this->_deleteOldImage = $profile->getData('options/' . $profileType . '/image_delete_old');
        $this->_deleteOldImageSkipUsageCheck = $profile->getData('options/' . $profileType . '/image_delete_skip_usage_check');

        return $this;
    }
    protected function getRowTypes()
    {
        $profile  = $this->_profile;
        $rowTypes = $profile->getData('options/row_types');
        if (!$rowTypes) {
            $rowTypes = array_keys($this->rfConfig
                ->getRowTypes($profile->getDataType()));
        }

        return $rowTypes;
    }
    /**
     * @var RapidFlowSalesHelperData
     */
    protected $helperData;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var ModelConfig
     */
    protected $rfConfig;
    /**
     * @var CsvFactory
     */
    protected $ioCsvFactory;
    protected function _construct()
    {
        parent::_construct();

        /** @var Context $context */
        $context               = ObjectManager::getInstance()->get(Context::class);
        $this->helperData      = $context->helperData;
        $this->rfHelper        = $context->rfHelper;
        $this->customerFactory = $context->customerFactory;
        $this->ioCsvFactory    = $context->ioCsvFactory;
        $this->rfConfig        = $context->rfConfig;
    }

    protected function _getRowTypeColumns($rowType)
    {
        /** @var AdapterInterface $conn */
        $conn      = $this->_read;
        $config    = $this->helper()->getConfigForSalesEntity($rowType);
        $mainTable = $config['table'];

        $excluded = isset($config['excluded'])? $config['excluded']: [];
        $mapped   = isset($config['mapped'])? $config['mapped']: [];

        $columns = [];

        foreach ($this->getTableColumns($mainTable, $conn) as $column) {
            if (in_array($column, $excluded, true)) {
                continue;
            }

            if (array_key_exists($column, $mapped)) {
                $column = $mapped[$column]['to'];
            }

            $columns[] = $column;
        }

        $exportOnly = $this->getProfile()->getColumnsForRowType($rowType);
        if ($exportOnly) {
            $columns = $exportOnly;
        }

        return $columns;
    }
    public function getConfigForSalesEntity($type)
    {
        return $this->helper()->getConfigForSalesEntity($type);
    }
    protected function helper()
    {
        return $this->helperData;
    }
    protected $_tableDesc;
    protected function getTableColumns($table, $conn)
    {
        if (!isset($this->_tableDesc[$table])) {
            $this->_tableDesc[$table] = $conn->describeTable($table);
        }

        return array_keys($this->_tableDesc[$table]);
    }
}
