<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report-api
 * @version   1.0.6
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Service;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mirasvit\ReportApi\Api\Config\ColumnInterface;
use Mirasvit\ReportApi\Api\Config\RelationInterface;
use Mirasvit\ReportApi\Api\Config\TableInterface;
use Mirasvit\ReportApi\Api\Config\TypeInterface;
use Mirasvit\ReportApi\Api\RequestInterface;
use Mirasvit\ReportApi\Api\Service\SelectServiceInterface;
use Mirasvit\ReportApi\Config\Entity\Relation;
use Mirasvit\ReportApi\Config\Entity\Table;
use Mirasvit\ReportApi\Config\Schema;
use Mirasvit\ReportApi\Handler\Select;
use Mirasvit\ReportApi\Handler\SelectFactory;

class SelectService implements SelectServiceInterface
{
    const MAX_TABLE_LENGTH = 64;

    private static $tmpTableCounter = 0;
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var TableService
     */
    private $tableService;

    /**
     * @var array
     */
    private static $replicatedTables = [];

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var SelectFactory
     */
    private $selectFactory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    private $cache = [];

    public function __construct(
        TableService $tableService,
        SelectFactory $selectFactory,
        Schema $schema,
        ObjectManagerInterface $objectManager,
        ResourceConnection $resource,
        TimezoneInterface $timezone
    ) {
        $this->tableService = $tableService;
        $this->selectFactory = $selectFactory;
        $this->schema = $schema;
        $this->objectManager = $objectManager;
        $this->resource = $resource;
        $this->timezone = $timezone;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function replicateTable(TableInterface $table, TableInterface $baseTable)
    {
        return true;
        if ($table->getConnectionName() == $baseTable->getConnectionName()) {
            return true;
        }

        $baseConnection = $this->resource->getConnection($baseTable->getConnectionName());

        $tableName = $this->resource->getTableName($table->getName());

        if (!$baseConnection->isTableExists($tableName)
            && !in_array($tableName, self::$replicatedTables)
        ) {
            $tblDescriptor = $this->tableService->create($table);
            $schema = $tblDescriptor->describeTable();

            $temporaryTable = $baseConnection->newTable($tableName);

            $usedColumns = $this->getUsedColumns($tableName, $schema);

            foreach ($schema as $column) {
                $type = $column['DATA_TYPE'];
                if ($column['DATA_TYPE'] == 'int') {
                    $type = 'integer';
                } elseif ($column['DATA_TYPE'] == 'varchar') {
                    $type = 'text';
                } elseif ($column['DATA_TYPE'] == 'tinyint') {
                    $type = 'smallint';
                }

                if (isset($usedColumns[$column['COLUMN_NAME']])) {
                    $temporaryTable->setColumn([
                        'COLUMN_NAME'      => $column['COLUMN_NAME'],
                        'TYPE'             => $type,
                        'LENGTH'           => $column['LENGTH'],
                        'COLUMN_POSITION'  => $column['COLUMN_POSITION'],
                        'PRIMARY'          => $column['PRIMARY'],
                        'PRIMARY_POSITION' => $column['PRIMARY_POSITION'],
                        'NULLABLE'         => $column['PRIMARY'] ? false : $column['NULLABLE'],
                        'COMMENT'          => $column['COLUMN_NAME'],
                    ]);
                }
            }

            try {
                $baseConnection->createTemporaryTable($temporaryTable);

                $offset = 1;
                while (true) {
                    $rows = $tblDescriptor->fetchAll($offset, 1000);

                    foreach ($rows as $idx => $row) {
                        $row = array_intersect_key($row, $usedColumns);
                        $rows[$idx] = $row;
                    }

                    if (count($rows)) {
                        $baseConnection->insertMultiple($tableName, $rows);
                    } else {
                        break;
                    }

                    $offset++;

                    if ($offset > 30) {
                        break;
                    }
                }
            } catch (\Exception $e) {
            }

            self::$replicatedTables[] = $tableName;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function applyTimeZone(AdapterInterface $connection)
    {
        $utc = $connection->fetchOne('SELECT CURRENT_TIMESTAMP');
        $offset = (new \DateTimeZone($this->timezone->getConfigTimezone()))->getOffset(new \DateTime($utc));
        $h = floor($offset / 3600);
        $m = floor(($offset - $h * 3600) / 60);
        $offset = sprintf("%02d:%02d", $h, $m);

        if (substr($offset, 0, 1) != "-") {
            $offset = "+" . $offset;
        }

        $connection->query("SET time_zone = '$offset'");

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function restoreTimeZone(AdapterInterface $connection)
    {
        $connection->query("SET time_zone = '+00:00'");

        return $this;
    }

    /**
     * Get columns used for table replication.
     *
     * @param string $tableName
     *
     * @return array
     */
    private function getUsedColumns($tableName, $newSchema)
    {
        $usedColumns = [
            'item_id'        => 1,
            'order_id'       => 1,
            'product_id'     => 1,
            'parent_item_id' => 1,
        ];

        if (strpos($tableName, TableDescriptorInterface::TMP_TABLE_SUFFIX) === false) {
            $mapTable = $this->schema->getTable($tableName);
            foreach ($mapTable->getColumns() as $column) {
                foreach ($column->getFields() as $field) {
                    $usedColumns[$field->getName()] = 1;
                }
            }
        } else { // temporary tables do not exist yet, so we can only use declared columns
            foreach ($newSchema as $column) {
                $usedColumns[$column['COLUMN_NAME']] = 1;
            }
        }

        return $usedColumns;
    }

    /**
     * @param TableInterface $table
     * @param RelationInterface[] $relations
     * @return bool
     */
    public function isTableHaveDuplicates(TableInterface $table, $relations)
    {
        $result = false;
        foreach ($relations as $idx => $relation) {
            if (!$relation->getOppositeTable($table)) {
                continue;
            }

            $type = $relation->getType($table);
            //            echo $table->getName() . ':' . $relation->getOppositeTable($table)->getName() . ':' . $type . PHP_EOL;
            if ($type == RelationInterface::TYPE_MANY_TO_ONE || $type == RelationInterface::TYPE_MANY_TO_MANY) {
                $result = true;
            } else {
                $rels = $relations;
                unset($rels[$idx]);
                $result = $result || $this->isTableHaveDuplicates($relation->getOppositeTable($table), $rels);
            }
        }

        return $result;
    }

    /**
     * Relation type between two tables
     *
     * @param TableInterface $currentTable
     * @param TableInterface $requiredTable
     * @return string 1 or n
     */
    public function getRelationType(TableInterface $currentTable, TableInterface $requiredTable)
    {
        if ($currentTable === $requiredTable) {
            return RelationInterface::TYPE_ONE;
        }

        $relations = $this->joinWay($currentTable, $requiredTable);

        if (!$relations) {
            throw new \Exception("Table $currentTable not related with $requiredTable");
        }


        $type = RelationInterface::TYPE_ONE;
        $xTable = $currentTable;

        foreach ($relations as $relation) {
            $oppositeTable = $relation->getOppositeTable($xTable);

            if ($relation->getType($xTable)[0] == RelationInterface::TYPE_MANY) {
                $type = RelationInterface::TYPE_MANY;
            }

            $xTable = $oppositeTable;
        }
        return $type;
    }

    /**
     * @param TableInterface $currentTable
     * @param TableInterface $requiredTable
     * @return RelationInterface[]
     */
    public function joinWay(TableInterface $currentTable, TableInterface $requiredTable)
    {
        $key = $currentTable->getName() . $requiredTable->getName();

        if (!isset($this->cache[$key])) {
            $factor = [];

            $ways = $this->joinWays($currentTable, $requiredTable);
            foreach ($ways as $idx => $way) {
                $factor[$idx] = count($way);
            }

            if (count($factor)) {
                $minIdx = array_search(min($factor), $factor);
                $this->cache[$key] = $ways[$minIdx];
            } else {
                $this->cache[$key] = [];
            }
        }

        return $this->cache[$key];
    }

    /**
     * @param TableInterface $currentTable
     * @param TableInterface $requiredTable
     * @param RelationInterface[] $relations
     * @param TableInterface[] $tables
     * @param int $level
     * @return RelationInterface[][]
     */
    private function joinWays(TableInterface $currentTable, TableInterface $requiredTable, $relations = [], $tables = [], $level = 0)
    {
        if ($level > 3) {
            return [];
        }

        $ways = [];

        $tables[] = $currentTable;

        foreach ($this->schema->getRelations() as $relation) {
            if (in_array($relation, $relations)) {
                continue;
            }

            $oppositeTable = $relation->getOppositeTable($currentTable);

            if ($oppositeTable && $oppositeTable->getName() == $requiredTable->getName()) {
                $ways[] = array_merge($relations, [$relation]);
            }
        }

        if (count($ways)) {
            return $ways;
        }

        foreach ($this->schema->getRelations() as $relation) {
            if (in_array($relation, $relations)) {
                continue;
            }

            $oppositeTable = $relation->getOppositeTable($currentTable);
            if ($oppositeTable) {
                if (in_array($oppositeTable, $tables)) {
                    continue;
                }

                if ($result = $this->joinWays(
                    $oppositeTable,
                    $requiredTable,
                    array_merge($relations, [$relation]),
                    array_merge($tables, [$oppositeTable]),
                    $level + 1
                )) {
                    foreach ($result as $way) {
                        $ways[] = $way;
                    }
                }
            }

        }

        return $ways;
    }

    public function createTemporaryTable(ColumnInterface $column, RequestInterface $request, TableInterface $baseTable)
    {
        $ts = microtime(true);
        self::$tmpTableCounter++;

        $select = $this->selectFactory->create();
        $select->setBaseTable($column->getTable())
            ->addFieldToSelect($baseTable->getPkField())
            ->addColumnToSelect($column, $column->getName())
            ->addFieldToGroup($baseTable->getPkField());

        $select->where($baseTable->getPkField()->toDbExpr() . '>0');

        foreach ($request->getFilters() as $filter) {
            $col = $this->schema->getColumn($filter->getColumn());
            $select->addColumnToFilter($col, [
                $filter->getConditionType() => $filter->getValue(),
            ]);
        }


        $tmpTableName = $this->getTmpTableName(
            $baseTable->getName(),
            $column->getTable()->getName(),
            self::$tmpTableCounter
        );
        $tmpTable = $this->resource->getConnection()->newTable($tmpTableName);
        $tmpTable->addColumn(
            $baseTable->getPkField()->getName(),
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'unsigned' => true,
                'identity' => true,
                'primary'  => true,
            ]
        );
        if (in_array($column->getType()->getType(), [
            TypeInterface::TYPE_PERCENT,
            TypeInterface::TYPE_PK,
            TypeInterface::TYPE_FK,
            TypeInterface::TYPE_NUMBER,
            TypeInterface::TYPE_MONEY,
            TypeInterface::TYPE_QTY,
        ])) {
            $tmpTable->addColumn(
                $column->getName(),
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'unsigned' => true]
            );
        } else {
            $tmpTable->addColumn(
                $column->getName(),
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true]
            );
        }

        $this->resource->getConnection()
            ->dropTable($tmpTableName);
        //        echo __LINE__.' '.round($ts - microtime(true), 4) . '*<br>';
        $this->resource->getConnection()
            ->createTemporaryTable($tmpTable);
        //        echo __LINE__.' '.round($ts - microtime(true), 4) . '*<br>';

        $insertQuery = $this->resource->getConnection()
            ->insertFromSelect($select, $tmpTableName);
        //        echo $select.'<br>';
        //        echo __LINE__.' '.round($ts - microtime(true), 4) . '*<br>';

        $this->resource->getConnection()->query($insertQuery);

        /** @var TableInterface $table */
        $table = $this->objectManager->create(Table::class, [
            'name'  => $tmpTableName,
            'label' => $tmpTableName,
        ]);
        $clone = clone $column;
        $table->addColumn($clone);
        $this->schema->addTable($table);

        $relation = $this->objectManager->create(Relation::class, [
            'leftTable'  => $table,
            'leftField'  => $table->getPkField(),
            'rightTable' => $baseTable,
            'rightField' => $baseTable->getPkField(),
            'type'       => '11',
        ]);
        $this->schema->addRelation($relation);
        $table->setIsTmp(true);

        return $table;
    }

    /**
     * @param TableInterface $requiredTable
     * @param TableInterface $currentTable
     * @return Select
     */
    public function createSelect(TableInterface $requiredTable, TableInterface $currentTable)
    {
        $relations = $this->joinWay($currentTable, $requiredTable);

        foreach ($relations as $relation) {
            if ($relation->getOppositeTable($currentTable)) {
                $select = $this->selectFactory->create();
                $select->setBaseTable($relation->getOppositeTable($currentTable));

                $fk = $relation->getOppositeField($currentTable->getPkField());
                $select->addFieldToSelect($fk, 'pk');
                $select->addFieldToGroup($fk);
            }
        }

        if (!isset($select)) {
            throw new LocalizedException(__('Select does not exists for required table %1, current table %2',
                $requiredTable,
                $currentTable
            ));
        }


        return $select;
    }

    /**
     * Get name for temporary table.
     *
     * If $tmpTableName greater than MySQL limit set for tables - use imprint of the name.
     *
     * @param $baseTableName
     * @param $columnTableName
     * @param $tmpTableCounter
     *
     * @return string
     */
    private function getTmpTableName($baseTableName, $columnTableName, $tmpTableCounter)
    {
        $tableName = 'tmp_' . $baseTableName . '__' . $columnTableName . '_' . $tmpTableCounter;
        if (strlen($tableName) > self::MAX_TABLE_LENGTH) {
            $tableName = md5($tableName);
        }

        return $tableName;
    }
}
