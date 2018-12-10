<?php

namespace Ziffity\AccountConfirmation\Model\Config\Backend\AccountConfirmation;

class Cron extends \Magento\Framework\App\Config\Value {

    /**
     * Cron string path
     */
    const CRON_STRING_PATH = 'crontab/default/jobs/ziffity_accountconfirmation_reminder/schedule/cron_expr';
    /**
     * Cron model path
     */
    const CRON_MODEL_PATH = 'crontab/default/jobs/ziffity_accountconfirmation_reminder/run/model';
    const CRON_STRING_EXP = 'ziff/ziffity_accountconfirmation/scheduler_cron_expr';
    protected $_configValueFactory;

    /**
     * @var string
     */
    protected $_runModelPath = '';

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config, 
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, 
        \Magento\Framework\App\Config\ValueFactory $configValueFactory, 
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, 
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        $runModelPath = '', 
        array $data = []
    ) {
        $this->_runModelPath = $runModelPath;
        $this->_configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function afterSave() {
        if ($this->isValueChanged()) {
            $this->cacheTypeList->invalidate(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
        }
        $path = $this->getData(self::CRON_STRING_EXP);
        $value = $this->getValue();
        try {
            $this->_configValueFactory->create()->load(
                    self::CRON_STRING_PATH, 'path'
            )->setValue(
                    $value
            )->setPath(
                    self::CRON_STRING_PATH
            )->save();
            $this->_configValueFactory->create()->load(
                    self::CRON_MODEL_PATH, 'path'
            )->setValue(
                    $this->_runModelPath
            )->setPath(
                    self::CRON_MODEL_PATH
            )->save();
        } catch (\Exception $e) {
            throw new \Exception(__('We can\'t save the cron expression.'));
        }
        return parent::afterSave();
    }

}
