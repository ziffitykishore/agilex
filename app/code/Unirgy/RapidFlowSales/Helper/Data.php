<?php

namespace Unirgy\RapidFlowSales\Helper;

use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Simplexml\Element;
use Unirgy\RapidFlow\Model\Config;
use Unirgy\RapidFlow\Model\Profile;
use Unirgy\RapidFlowSales\Helper\Data as HelperData;
use Unirgy\RapidFlowSales\Model\Misc\Uuid;

class Data extends AbstractHelper
{

    const XML_PATH_UUID = 'urfsales/installation/uuid';
    const URF_ID_FIELD  = 'urf_id';
    const SALES         = 'sales';
    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var Config
     */
    protected $config;
    protected $supportedSalesEntities = [];

    protected $_resource;
    protected $_rowTypes = [];
    protected $_installUUID;
    /**
     * @var ResourceConfig
     */
    protected $resourceConfig;

    public function __construct(Context $context,
        ResourceConnection $resource,
        ResourceConfig $resourceConfig,
        Config $config)
    {
        $this->resource       = $resource;
        $this->config         = $config;
        $this->resourceConfig = $resourceConfig;

        parent::__construct($context);
    }

    public function generateInstallationUuid()
    {
        $serverMacs = $this->serverMACs();
        // use secure url as identifier for this installation
        $serverId = $this->scopeConfig->getValue('base/secure/url', 'default');
        //if able to obtain mac addresses, use one of them instead
        if (!empty($serverMacs)) {
            $serverId = $serverMacs[0];
        }

        return Uuid::v5(Uuid::NS_URL, $serverId);
    }

    protected function serverMACs()
    {
        $_macRegExp = '/([0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2})/i';
        $macs       = [];
        $output     = [];
        if (!function_exists('exec')) {
            $this->_logger->debug('exec() seems to be disabled, cannot check mac address');

            return $macs;
        }
        if (strpos(strtolower(PHP_OS), 'win') === 0) {
            exec('ipconfig /all | find "Physical Address"', $output);
        } else {
            exec('/sbin/ifconfig -a | grep -E "HWaddr|ether"', $output);
        }
        foreach ($output as $line) {
            if (preg_match($_macRegExp, $line, $m)) {
                $macs[] = strtoupper($m[1]);
            }
        }

        return $macs;
    }

    public function strBoolIs($flag, $value = true)
    {
        if ($value === true) {
            $flag = strtolower((string) $flag);
            if (!empty($flag) && 'false' !== $flag && '0' !== $flag && 'off' !== $flag) {
                return true;
            }

            return false;
        }

        return !empty($flag) && (0 === strcasecmp($value, (string) $flag));
    }

    public function getFrontendLabel($entity, $field = null)
    {
        if (null !== $field) {
            $lblKey = $entity . '.' . $field;
            if ($lblKey === ($label = __($lblKey))) {
                $label = __($field);
            }
        } else {
            $label = __($entity);
        }

        return $label;
    }

    /**
     * @param Profile $profile
     * @param         $value
     * @return array|string
     * @throws \Exception
     */
    public function convertEncoding($profile, $value)
    {
        $encFrom        = $profile->getData('options/encoding/from');
        $encTo          = $profile->getData('options/encoding/to');
        $encIllegalChar = $profile->getData('options/encoding/illegal_char');
        if ($value && $encFrom && $encTo && $encFrom !== $encTo) {
            if (is_array($value)) {
                foreach ($value as $i => $v) {
                    $value[$i] = $this->convertEncoding($profile, $v);
                }
            } else {
                $encodingTo = $encTo . ($encIllegalChar? '//' . $encIllegalChar: '');
                try {
                    $value1 = iconv($encFrom, $encodingTo, $value);
                } catch(\Exception $e) {
                    if (strpos($e->getMessage(), 'Detected an illegal character in input string') !== false) {
                        $profile->addValue(Profile::NUM_WARNINGS);
                        $profile->getLogger()->warning(__('Illegal character in string: %1', $value));
                        $value1 = $value;
                    } else {
                        throw $e;
                    }
                }
                $value = $value1;
            }
        }

        return $value;
    }

    /**
     * @param string $code
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getTableForSalesEntityByCode($code)
    {
        $supportedEntities = $this->getSupportedSalesEntities();
        if (!array_key_exists($code, $supportedEntities)) {
            throw new \InvalidArgumentException(__('Unknown sales code: %1', $code));
        }

        return $this->_getMainResource()->getTableName($supportedEntities[$code]);
    }

    public function getSupportedSalesEntities()
    {
        if (count($this->supportedSalesEntities) === 0) {
            foreach ($this->getRowTypes() as $type => $item) {
                $this->supportedSalesEntities[$type] = $item['table'];
            }
        }

        return $this->supportedSalesEntities;
    }

    /**
     * @return ResourceConnection
     */
    protected function _getMainResource()
    {
        return $this->resource;
    }

    /**
     * @return array
     */
    public function getRowTypes()
    {
        if (empty($this->_rowTypes)) {
            $rawTypes = $this->_loadRowTypes();
            foreach ($rawTypes as $type => $definition) {
                $this->_rowTypes[$type] = [];
                /** @var Element $child */
                foreach ($definition->children() as $child) {
                    $name = $child->getName();
                    if ($name === 'map') {
                        /** @var Element $mapped */
                        foreach ($child->children() as $mapped) {
                            $src                                    = $mapped->getName();
                            $this->_rowTypes[$type]['mapped'][$src] = [];
                            foreach ($mapped->attributes() as $attribute => $val) {
                                $this->_rowTypes[$type]['mapped'][$src][$attribute] = (string) $val;
                            }
                        }
                    } else if ($name === 'exclude') {
                        /** @var Element $excluded */
                        foreach ($child->children() as $excluded) {
                            $this->_rowTypes[$type]['excluded'][] = $excluded->getName();
                        }
                    } else if ($name === 'depends') {
                        /** @var Element $depends */
                        foreach ($child->children() as $depends) {
                            $this->_rowTypes[$type]['depends'][] = $depends->getName();
                        }
                    } else if ($name === 'serializable') {
                        /** @var Element $serializable */
                        foreach ($child->children() as $serializable) {
                            $this->_rowTypes[$type]['serializable'][] = $serializable->getName();
                        }
                    } else {
                        $this->_rowTypes[$type][$name] = (string) $child;
                    }
                }
            }
        }

        return $this->_rowTypes;
    }

    /**
     * @return Element[]
     */
    protected function _loadRowTypes()
    {
        return $this->config->getRowTypes(static::SALES);
    }

    /**
     * @param $code
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getConfigForSalesEntity($code)
    {
        $this->isSalesTypeValid($code);
        $rowTypes = $this->getRowTypes();

        return $rowTypes[$code];
    }

    /**
     * @param $code
     * @throws \InvalidArgumentException
     */
    public function isSalesTypeValid($code)
    {
        if (!array_key_exists($code, $this->getRowTypes())) {
            throw new \InvalidArgumentException(__('Unknown sales code: %1', $code));
        }
    }

    public function getSalesRowTypes()
    {
        $rawTypes = $this->getRowTypes();

        $rowTypes = [];
        foreach ($rawTypes as $abrv => $type) {
            $label           = $type['title'];
            $rowTypes[$abrv] = $abrv . ': ' . __($label);
        }

        return $rowTypes;
    }

    public function getDateRowTypes()
    {
        $rawTypes = array_filter($this->getRowTypes(),
            function ($row) {
                return !empty($row['created_at']);
            });

        $rowTypes = [];
        foreach ($rawTypes as $abrv => $type) {
            $label           = $type['title'];
            $rowTypes[$abrv] = $abrv . ': ' . __($label);
        }

        return $rowTypes;
    }

    /**
     * @param $mainTable
     * @return bool
     */
    public function tableShouldBeUpdated($mainTable)
    {
        return in_array($mainTable, $this->getSupportedSalesEntities(), true);
    }

    public function getInstallationUuid($path = HelperData::XML_PATH_UUID)
    {
        if (null === $this->_installUUID) {
            $this->_installUUID = (string) $this->scopeConfig->getValue($path, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        }

        return $this->_installUUID;
    }

    public function updateMissingUrfId()
    {
        $setup = new \Unirgy\RapidFlowSales\Setup\InstallData(
            $this->_logger,
            $this,
            $this->resourceConfig
        );

        $setup->setupInstall($this->_setupObject());
    }

    protected function _setupObject()
    {
        return ObjectManager::getInstance()->get('\Magento\Framework\Setup\ModuleDataSetupInterface');
    }
}
