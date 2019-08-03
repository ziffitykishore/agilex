<?php

namespace Wyomind\MassStockUpdate\Model\ResourceModel\Type;

/**
 * Class AbstractResource
 * @package Wyomind\MassStockUpdate\Model\ResourceModel\Type
 */
abstract class AbstractResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     *
     */
    const ENABLE = ["true", "yes", "in stock", "enable", "enabled"];
    /**
     *
     */
    const DISABLE = ["false", "no", "out of stock", "disable", "disabled"];

    /**
     * @var string
     */
    public $decimal = "Float Number or Integer Number";
    /**
     * @var string
     */
    public $datetime = "Date + Time GMT (yyyy-mm-dd hh:mm:ss)";
    /**
     * @var string
     */
    public $smallint = "Boolean value";
    /**
     * @var string
     */
    public $int = "Integer number";
    /**
     * @var string
     */
    public $static = "Static";
    /**
     * @var string
     */
    public $text = "Text";
    /**
     * @var string
     */
    public $varchar = "Small text (255 characters maximum)";
    /**
     * @var string
     */
    public $uniqueIdentifier = "Unique Identifier";

    /**
     *
     */
    const QUERY_INDEXER_INCREMENT = 1000;

    /**
     * @var
     */
    public $table;
    /**
     * @var array
     */
    public $queries = array();
    /**
     * @var int
     */
    public $queryIndexer = 0;
    /**
     * @var
     */
    public $entity_type_id;
    /**
     * @var null|\Wyomind\MassStockUpdate\Helper\Data
     */
    protected $_helperData = null;
    /**
     * @var null|\Wyomind\Core\Helper\Data
     */
    protected $_coreHelper = null;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory|null
     */
    protected $_entityAttributeCollection = null;

    /**
     * AbstractResource constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Wyomind\Core\Helper\Data $coreHelper
     * @param \Wyomind\MassStockUpdate\Helper\Data $helperData
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $entityAttributeCollection
     * @param null $connectionName
     */
    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context,
                                \Wyomind\Core\Helper\Data $coreHelper, \Wyomind\MassStockUpdate\Helper\Data $helperData,
                                \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $entityAttributeCollection,
                                $connectionName = null)
    {

        $this->_helperData = $helperData;
        $this->_coreHelper = $coreHelper;
        $this->_entityAttributeCollection = $entityAttributeCollection;

        parent::__construct($context, $connectionName);
        $read = $this->getConnection();
        $tableEet = $this->getTable('eav_entity_type');
        $select = $read->select()->from($tableEet)->where('entity_type_code=\'catalog_product\'');
        $data = $read->fetchAll($select);
        $this->entity_type_id = $data[0]['entity_type_id'];
    }

    /**
     * construct
     */
    public function _construct()
    {
        $this->queries[$this->queryIndexer] = array();
    }

    /**
     * Reset method
     */
    public function reset()
    {

    }

    /**
     * Increment the indexer
     */
    public function incrementQueryIndexer()
    {
        if (!isset($this->queries[$this->queryIndexer])) {
            $this->queries[$this->queryIndexer] = array();
        }
        if (count($this->queries[$this->queryIndexer]) >= self::QUERY_INDEXER_INCREMENT) {

            $this->queryIndexer++;
            $this->queries[$this->queryIndexer] = array();
        }
    }

    /**
     * Return the indexes to refresh
     * @param array $mapping
     * @return array
     */
    public function getIndexes($mapping = [])
    {
        return [];
    }

    /**
     * Collect required data before to process
     * @param \Wyomind\MassSockUpdate\Model\ResourceModel\Profile $profile
     * @param array $columns
     */
    public function beforeCollect($profile, $columns)
    {
        self::incrementQueryIndexer();
    }

    /**
     * Collect data for each product to udpate/import
     * @param int $productId
     * @param string $value
     * @param array $strategy
     * @param \Wyomind\MassSockUpdate\Model\ResourceModel\Profile $profile
     */
    public function collect($productId, $value, $strategy, $profile)
    {
        //self::incrementQueryIndexer();
    }

    /**
     * Update the queries at the and of the process
     * @param int $productId
     * @param \Wyomind\MassSockUpdate\Model\ResourceModel\Profile $profile
     * @return $this->queries
     */
    public function updateQueries($productId, $profile)
    {
        self::incrementQueryIndexer();
    }

    /**
     * Join all queries in $this->queries
     * @param int $productId
     * @param \Wyomind\MassSockUpdate\Model\ResourceModel\Profile $profile
     */
    public function prepareQueries($productId, $profile)
    {

        self::incrementQueryIndexer();
    }

    /**
     * Create the queries on several lines
     * @return string
     */
    public function afterCollect()
    {
        self::incrementQueryIndexer();
    }

    /**
     * Action to perform when at the end of the process
     * @param \Wyomind\MassSockUpdate\Model\ResourceModel\Profile $profile
     */
    public function afterProcess($profile)
    {

    }

    /**
     * Check if the module has fields to add
     * @return boolean
     */
    public function hasFields()
    {
        return $this->getFields();
    }

    /**
     * List all fields to add
     * @param object $fieldset
     * @param object $form
     * @param object $class
     * @return boolean
     */
    public function getFields($fieldset = null, $form = null, $class = null)
    {

        return false;
    }

    /**
     * List all module to add
     * @param \Wyomind\MassSockUpdate\Model\ResourceModel\Profile $profile
     * @return array
     */
    public function addModuleIf($profile)
    {
        return [];
    }

    /**
     * List of new mapping attributes
     * @return array
     */
    public function getDropdown()
    {
        return [];
    }

    /**
     * Transform enable/disable values to 0/1
     * @param string $value
     * @return string
     */
    public function getValue($value)
    {
        if (in_array(strtolower($value), self::ENABLE)) {
            return "1";
        } else if (in_array(strtolower($value), self::DISABLE)) {
            return "0";
        }
        return (string)$value;
    }

    /**
     * Get all attributes
     * @param array $fields
     * @param array $conditions
     * @param bool $and
     * @return array
     */
    public function getAttributesList($fields = array("backend_type", "frontend_input", "attribute_code", "attribute_code", "backend_model"),
                                      $conditions = array(
                                          array("nin" => array("static")),
                                          array("nin" => array("media_image", "gallery")),
                                          array("nin" => array("image_label", "thumbnail_label", "small_image_label")),
                                          array("nin" => array("tax_class_id", "visibility", "status", "url_key", "special_to_date", "special_from_date")),
                                          array(array("nlike" => array("%price%")), array("null" => true))
                                      ), $and = true)
    {

        /*  Liste des  attributs disponible dans la bdd */

        $attributesList = $this->_entityAttributeCollection->create()
            ->setEntityTypeFilter($this->entity_type_id);
        if ($and) {
            foreach ($fields as $i => $field) {
                $attributesList->addFieldToFilter($field, $conditions[$i]);
            }
        } else {
            $attributesList->addFieldToFilter($fields, $conditions);
        }


        $data = $attributesList->addSetInfo()
            ->getData();

        usort($data, [$this, 'attributesSort']);


        return $data;
    }

    /** Sort attribut list
     * @param $a
     * @param $b
     * @return int
     */
    public function attributesSort(
        $a, $b
    )
    {
        return ($a['frontend_label'] < $b['frontend_label']) ? -1 : 1;
    }

    /** insert ignore ... on duplicate key update ... query
     * @param $table
     * @param $data
     * @param bool $ignoreStatement
     * @return string
     */
    public function createInsertOnDuplicateUpdate($table, $data, $ignoreStatement = false)
    {
        $fields = array();
        $values = array();
        $update = array();
        $ignore = "";
        if ($ignoreStatement) {
            $ignore = "IGNORE";
        }
        foreach ($data as $field => $value) {

            $val = $this->getValue((string)$value);
            $fields[] = "`" . $field . "`";
            $values[] = $val;
            $update[] = $field . "=" . $val . "";
        }
        return "INSERT " . $ignore . " INTO  `" . $table . "` (" . implode(",", $fields) . ") "
            . " VALUES (" . implode(",", $values) . ") ON DUPLICATE KEY UPDATE " . implode(",", $update) . ";";
    }


    /** Delete query
     * @param $table
     * @param $data
     * @return string
     */
    public function _delete($table, $data)
    {

        $delete = array();
        foreach ($data as $field => $value) {

            $val = $this->getValue((string)$value);

            $delete[] = $field . "=" . $val . "";
        }
        return "DELETE FROM `" . $table . "`WHERE " . implode(" AND ", $delete) . ";";
    }

}
