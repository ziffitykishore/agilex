<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */
namespace RocketWeb\ShoppingFeeds\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScheduleCommand extends Command
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Cron\Schedule
     */
    protected $schedule;

    /**
     * Constructor
     *
     * @param \RocketWeb\ShoppingFeeds\Cron\Schedule $schedule
     */
    public function __construct(
        \RocketWeb\ShoppingFeeds\Cron\Schedule $schedule
    ) {
        $this->schedule = $schedule->setDetached();
        parent::__construct();
    }

    /**
     * Set name and description
     */
    protected function configure()
    {
        $this->setName('rocketshoppingfeed:schedule')
            ->setDescription('Generates queues from schedule');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface|\Symfony\Component\Console\Output\Output $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->schedule->execute();
        if ($output->isVerbose()) {
            if ($this->schedule->getCounter() > 0) {
                $output->writeln(sprintf('Schedule created %s queues.', $this->schedule->getCounter()));
            } else {
                $output->writeln('No schedule processed.');
            }
        }
    }
}
