<?php
namespace Unirgy\RapidFlowSales\Setup;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Psr\Log\LoggerInterface;
use Unirgy\RapidFlowSales\Helper\Data as HelperData;
use Unirgy\RapidFlowSales\Model\Misc\Uuid;

/**
 * Created by pp
 *
 * @project magento216
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var HelperData
     */
    protected $helperData;
    /**
     * @var Config
     */
    protected $resourceConfig;

    public function __construct(
        LoggerInterface $logger,
        HelperData $helperData,
        Config $resourceConfig
    )
    {
        $this->logger      = $logger;
        $this->helperData  = $helperData;
        $this->resourceConfig = $resourceConfig;
    }


    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface     $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup,
        ModuleContextInterface $context)
    {
        $this->setupInstall($setup);
    }

    public function setupInstall(ModuleDataSetupInterface $setup)
    {
        $setup->startSetup();

        $this->_configureInstallUuid();

        $this->_updateSalesTablesData($setup->getConnection());

        $setup->endSetup();
    }
    protected function _configureInstallUuid()
    {
        $path       = HelperData::XML_PATH_UUID;
        $configUuid = $this->getInstallationUuid();
        //$configUuid = $config->getNode($path);
        if (!empty($configUuid)) {
            return;
        }

        $installUuid = $this->_generateInstallationUuid();

        $this->resourceConfig->saveConfig($path, $installUuid, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
    }

    protected function _updateSalesTablesData(AdapterInterface $conn)
    {
        // for each supported table, use v5 uuid, namespace is installation uuid, value is table_name . entity_id
        foreach ($this->_getSupportedSalesTables($conn) as $table) {
            $this->_updateSalesTableData($table, $conn);
        }
    }

    protected function _getSupportedSalesTables(AdapterInterface $conn)
    {
        return array_map(function ($t) use ($conn) {
            return $conn->getTableName($t);
        },
            $this->helperData->getSupportedSalesEntities());
    }
    /**
     * Update supported sales tables' data
     *
     * Insert UUID values for all supported sales tables, that
     * do not currently have values for urf_id
     *
     * @param string $table
     * @param        $conn
     * @throws \RuntimeException
     */
    protected function _updateSalesTableData($table, AdapterInterface $conn)
    {
        $tableData        = $conn->describeTable($table);
        $installationUuid = $this->getInstallationUuid();
        $prefix           = $table . '.';
        $primary          = [];
        foreach ($tableData as $name => $column) {
            if ($column['PRIMARY']) {
                $primary[] = $name;
            }
        }

        try {
            $where  = sprintf('%1$s IS NULL OR %1$s=""', HelperData::URF_ID_FIELD);
            $select = $conn->select()->from($table, $primary)->where($where);
            /** @var \Zend_Db_Statement_Interface $rowsToUpdate */
            $rowsToUpdate = $conn->query($select);
            while ($row = $rowsToUpdate->fetch()) {
                $suffix = '';
                $where  = [];
                foreach ($primary as $key) {
                    $suffix  .= sprintf('%s:%d', $key, $row[$key]);
                    $where[] = $conn->quoteInto($key . '=?', $row[$key]);
                }

                $uuid = Uuid::v5($installationUuid, $prefix . $suffix);

                $conn->update($table, ['urf_id' => $uuid], implode(' AND ', $where));
            }
        } catch(\Exception $e) {
            $this->logger->error($e);
            if (strpos($e->getMessage(), 'Unknown column \'urf_id\'') !== false) {
                throw new \RuntimeException('urf_id column is missing');
            }
        }
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getInstallationUuid($path = HelperData::XML_PATH_UUID)
    {
        return $this->helperData->getInstallationUuid($path);
    }

    protected function _generateInstallationUuid()
    {
        return $this->helperData->generateInstallationUuid();
    }
}
