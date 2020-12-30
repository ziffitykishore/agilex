<?php
namespace Unirgy\RapidFlowSales\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Psr\Log\LoggerInterface;
use Unirgy\RapidFlowSales\Helper\Data as HelperData;

/**
 * Created by pp
 *
 * @project magento216
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var LoggerInterface
     */
    protected $_logLoggerInterface;

    /**
     * @var HelperData
     */
    protected $_helperData;
    /**
     * @var AdapterInterface
     */
    protected $connection;
    /**
     * @var SchemaSetupInterface
     */
    protected $setup;

    public function __construct(
        LoggerInterface $logLoggerInterface,
        HelperData $helperData
    )
    {
        $this->_logLoggerInterface = $logLoggerInterface;
        $this->_helperData         = $helperData;
    }

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->connection = $installer->getConnection();
        $this->setup      = $setup;
        try {
            $this->_updateSalesTables($this->_getSupportedSalesTables());
        } catch(\RuntimeException $rte) {
            $this->_logLoggerInterface->debug($rte->getMessage());
        }

        $installer->endSetup();
    }

    protected function _updateSalesTables(array $tables)
    {
        $self = $this;
        array_walk($tables,
            function ($table) use ($self) {
                // add urf_id column if missing
                if (!$self->getConnection()
                          ->addColumn($table, HelperData::URF_ID_FIELD, ['TYPE'=>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'LENGTH'=>36,'nullable'=>true,'COMMENT'=>'urf_id'])
                ) {
                    throw new \RuntimeException(sprintf('Problem adding "urf_id" to table: %s', $table));
                }

                // add unique index
                $self->getConnection()->addIndex($table,
                    $self->getIdxName($table, [HelperData::URF_ID_FIELD]),
                    [HelperData::URF_ID_FIELD],
                    AdapterInterface::INDEX_TYPE_UNIQUE);
            });
    }

    protected function _getSupportedSalesTables()
    {
        $self = $this;

        return array_map(function ($t) use ($self) {
            return $self->getConnection()->getTableName($t);
        },
            $this->_helperData->getSupportedSalesEntities());
    }

    protected function getConnection()
    {
        return $this->connection;
    }

    protected function getIdxName($table, $fields)
    {
        return $this->setup->getIdxName($table, $fields);
    }

    public function revertInstall()
    {
        $success = true;
        try {
            $this->_revertSalesTables($this->_getSupportedSalesTables());
        } catch(\RuntimeException $rte) {
            $success = false;
            $this->_logLoggerInterface->debug($rte->getMessage());
        }

        return $success;
    }
    protected function _revertSalesTables($tables)
    {
        $self = $this;
        array_walk($tables, function ($table) use($self) {
            if (!$self->getConnection()->dropColumn($table, \Unirgy\RapidFlowSales\Helper\Data::URF_ID_FIELD)) {
                throw new \RuntimeException(sprintf('Problem dropping "urf_id" from table: %s', $table));
            }
        });
    }
}
