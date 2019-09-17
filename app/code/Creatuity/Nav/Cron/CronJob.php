<?php

namespace Creatuity\Nav\Cron;

use Creatuity\Nav\Cron\Config\CronJobConfig;
use Creatuity\Nav\Model\Task\TaskInterface;

class CronJob
{
    const CONFIG_ENABLED = 'enabled';

    protected $globalConfig;
    protected $config;
    protected $task;

    public function __construct(
        CronJobConfig $globalConfig,
        CronJobConfig $config,
        TaskInterface $task
    ) {
        $this->globalConfig = $globalConfig;
        $this->config = $config;
        $this->task = $task;
    }

    public function execute()
    {
        if (!$this->isEnabledGlobal() || !$this->isEnabledLocal()) {
            return;
        }

        $this->task->execute();
    }

    protected function isEnabledGlobal()
    {
        return $this->isEnabled($this->globalConfig);
    }

    protected function isEnabledLocal()
    {
        return $this->isEnabled($this->config);
    }

    protected function isEnabled(CronJobConfig $config)
    {
        return (bool) $config->getConfigValue(self::CONFIG_ENABLED);
    }
}
