<?php

namespace Creatuity\Nav\Console\Command;

use Creatuity\Nav\Model\Task\TaskInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
    protected $state;
    protected $task;

    public function __construct(
        State $state,
        TaskInterface $task,
        $description,
        $name = null
    ) {
        parent::__construct($name);
        $this->setDescription($description);
        $this->state = $state;
        $this->task = $task;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(Area::AREA_GLOBAL);
        $this->task->execute();
    }
}
