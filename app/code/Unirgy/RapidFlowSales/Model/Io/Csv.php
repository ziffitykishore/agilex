<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_RapidFlow
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */


namespace Unirgy\RapidFlowSales\Model\Io;

use Unirgy\Rapidflow\Exception;
use Unirgy\RapidFlow\Model\Io\Csv as IoCsv;

/**
 * Class \Unirgy\RapidFlowSales\Model\Io\Csv
 */
class Csv extends IoCsv
{
    /**
     * @var string
     */
    protected $_entityType;

    /**
     * @param $filename
     * @return string
     * @throws \Unirgy\RapidFlow\Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getFilepath($filename)
    {
        $dir        = $this->dir();
        $fName      = ltrim($filename, '/');
        $fNameParts = explode('.', $fName);
        if (count($fNameParts) > 1) {
            $fNameSuffix = array_pop($fNameParts);
            $fName       = implode('.', $fNameParts) . '_' . $this->getEntityType() . '.' . $fNameSuffix;
        } else {
            $fName = $fName . '_' . $this->getEntityType();
        }

        return rtrim($dir, '/') . '/' . $fName;
    }

    /**
     * @return string
     * @throws \Unirgy\RapidFlow\Exception
     */
    public function getEntityType()
    {
        if (empty($this->_entityType)) {
            throw new Exception(__('IO Entity Type is unknown'));
        }

        return $this->_entityType;
    }

    /**
     * @param $entityType
     * @return $this
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = $entityType;

        return $this;
    }
}
