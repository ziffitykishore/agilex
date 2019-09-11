<?php

namespace Creatuity\Nav\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class CronSchedule extends Value
{
    protected $groupName;
    protected $jobName;
    protected $defaultExpression;
    protected $configValueFactory;

    public function __construct(
        $groupName,
        $jobName,
        $defaultExpression,
        ValueFactory $configValueFactory,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->groupName = $groupName;
        $this->jobName = $jobName;
        $this->defaultExpression = $defaultExpression;
        $this->configValueFactory = $configValueFactory;
    }

    public function afterSave()
    {
        $cronExpression = (!empty(trim($this->getValue()))) ? $this->getValue() : $this->defaultExpression;

        try {
            $this->configValueFactory->create()
                ->load($this->getExpressionPath(), 'path')
                ->setValue($cronExpression)
                ->setPath($this->getExpressionPath())
                ->save()
            ;
        } catch (\Exception $e) {
            throw new \Exception(__("Failed to save cron expression '{$cronExpression}'"));
        }

        return parent::afterSave();
    }

    protected function getExpressionPath()
    {
        return "crontab/{$this->groupName}/jobs/{$this->jobName}/schedule/cron_expr";
    }
}
