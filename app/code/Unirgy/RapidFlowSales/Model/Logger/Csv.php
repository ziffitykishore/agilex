<?php

/**
 * Class LoggerCsv
 *
 * @method Sales getProfile()
 * @method $this setLevelSuccess(bool $isSet)
 * @method $this setLevelWarning(bool $isSet)
 * @method $this setLevelError(bool $isSet)
 */
namespace Unirgy\RapidFlowSales\Model\Logger;

use Unirgy\RapidFlow\Exception;
use Unirgy\RapidFlow\Model\Logger\Csv as ModelLoggerCsv;
use Unirgy\RapidFlowSales\Model\Io\Csv as IoCsv;
use Unirgy\RapidFlowSales\Model\Profile\Sales;

/**
 * Class Csv
 * @method Sales getProfile()
 * @method $this setLevelSuccess(bool $lvl)
 * @method $this setLevelWarning(bool $lvl)
 * @method $this setLevelError(bool $lvl)
 *
 * @package Unirgy\RapidFlowSales\Model\Logger
 */
class Csv extends ModelLoggerCsv
{
    /**
     * @var string
     */
    protected $_defaultIoModel = IoCsv::class;
    /**
     * @var string
     */
    protected $_entityType;

    /**
     * @return string
     * @throws \Unirgy\RapidFlow\Exception
     */
    public function getEntityType()
    {
        if (empty($this->_entityType)) {
            throw new Exception(__('Logger Entity Type is unknown'));
        }

        return $this->_entityType;
    }

    /**
     * @param string $entityType
     * @return $this
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = $entityType;

        return $this;
    }

    /**
     * @param $mode
     * @return $this
     * @throws \Unirgy\RapidFlow\Exception
     */
    public function start($mode)
    {
        $this->getIo()->setEntityType($this->getEntityType())->open($this->getProfile()->getLogFilename(), $mode);
        $level = $this->getProfile()->getData('options/log/min_level');
        $this->setLevelSuccess($level === 'SUCCESS');
        $this->setLevelWarning($level === 'SUCCESS' || $level === 'WARNING');
        $this->setLevelError($level === 'SUCCESS' || $level === 'WARNING' || $level === 'ERROR');

        return $this;
    }

    /**
     * @param $rowType
     * @param $data
     * @return $this
     * @throws \Unirgy\RapidFlow\Exception
     */
    public function log($rowType, $data)
    {
        parent::log($rowType, $data);
        if ($this->getEntityType()) {
            $this->getProfile()->getLoggerEt($this->getEntityType())->log($rowType, $data);
        }

        return $this;
    }

}
