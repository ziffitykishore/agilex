<?php

namespace Creatuity\Nav\Cron\Config;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Creatuity\Nav\Model\Config\Backend\CronSchedule;

class CronScheduleUpdater
{
    const CONFIG_FIELD_CRON_SCHEDULE = 'cron_schedule';

    protected $cronExpression;
    protected $cronJobConfig;
    protected $cronSchedule;
    protected $configWriter;

    public function __construct(
        $cronExpression,
        CronJobConfig $cronJobConfig,
        CronSchedule $cronSchedule,
        WriterInterface $configWriter
    ) {
        $this->cronExpression = $cronExpression;
        $this->cronJobConfig = $cronJobConfig;
        $this->cronSchedule = $cronSchedule;
        $this->configWriter = $configWriter;
    }

    public function update()
    {
        $configPath = $this->cronJobConfig->getConfigPath(self::CONFIG_FIELD_CRON_SCHEDULE);

        $this->configWriter->save($configPath, $this->cronExpression);

        $this->cronSchedule
            ->setPath($configPath)
            ->setValue($this->cronExpression)
            ->afterSave()
        ;
    }
}
