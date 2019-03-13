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
use Symfony\Component\Console\Input\InputArgument;
use RocketWeb\ShoppingFeeds\Cron\Process;
use RocketWeb\ShoppingFeeds\Cron\Schedule;
use RocketWeb\ShoppingFeeds\Model\Generator\QueueFactory;
use RocketWeb\ShoppingFeeds\Cron\ProcessFactory;

class GenerateCommand extends Command
{
    /**
     * Feed_id name option
     */
    const INPUT_KEY_FEED_ID = 'feed_id';

    /**
     * Test sku name option
     */
    const INPUT_KEY_TEST_SKU = 'test_sku';

    /**
     * Delimiter char length
     */
    const DELIMITER_LENGTH = 80;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Logger\Handler\Console
     */
    protected $consoleHandler;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Logger
     */
    protected $logger;

    /**
     * @var ProcessFactory
     */
    protected $processFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * Constructor
     * 
     * @param Process $process
     * @param Schedule $schedule
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \RocketWeb\ShoppingFeeds\Cron\ProcessFactory $processFactory,
        \RocketWeb\ShoppingFeeds\Model\Generator\QueueFactory $queueFactory,
        \RocketWeb\ShoppingFeeds\Model\Logger $logger,
        \RocketWeb\ShoppingFeeds\Model\Logger\Handler\Console $consoleHandler
    ){
        $this->consoleHandler = $consoleHandler;
        $this->logger = $logger;
        $this->processFactory = $processFactory;
        $this->state = $state;
        $this->queueFactory = $queueFactory;

        parent::__construct();
    }

    /**
     * Set name and description
     */
    protected function configure()
    {
        $this->setName('rocketshoppingfeed:generate')
            ->setDescription('Generates all feeds or specific feed/sku if arguments passed')
            ->setDefinition($this->getInputList());
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface|\Symfony\Component\Console\Output\Output $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('frontend');
        $feedId = $input->getArgument(self::INPUT_KEY_FEED_ID);
        $testSku = $input->getArgument(self::INPUT_KEY_TEST_SKU);

        if ($feedId && !is_numeric($feedId)) {
            throw new \InvalidArgumentException(
                '"feed_id" argument has to be numeric. "test_sku" argument requires "feed_id" argument specified'
            );
        }
        if ($output->isVerbose()) {
            $this->consoleHandler->setLevel(\Monolog\Logger::DEBUG);
        }
        if (!$output->isQuiet()) {
            $this->consoleHandler->setOutputInterface($output);
            $this->logger->pushHandler($this->consoleHandler);
        }


        $process = $this->processFactory->create()->setDetached();
        if (!$feedId) {
            // Generate feed by queue.
            $process->execute();
        } else if (!$testSku) {
            // Generate feed by specified FeedID
            $process->setFeedId($feedId);
            $process->execute();
        } else {
            // Generating feed by specified FeedID and TestSKU
            $output->writeln(sprintf('Starting generation for feed #%s with SKU #%s', $feedId, $testSku));

            /** @var \RocketWeb\ShoppingFeeds\Model\Generator\Queue $queue */
            $queue = $this->queueFactory->create();
            $queue->setFeedId($feedId);

            try {
                /** @var \RocketWeb\ShoppingFeeds\Model\Generator $generator */
                $generator = $queue->getGenerator();
                $generator->setTestSku($testSku);
                $generator->run();
                $data = $generator->getTestOutput();
                $this->outputTestProduct($output, $data);
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
            }
        }
        $output->writeln('Done!');
    }

    /**
     * @param $output
     * @param $data
     */
    protected function outputTestProduct(OutputInterface $output, Array $data = [])
    {
        foreach ($data as $row) {
            foreach ($row as $field) {
                if (strlen($field['value']) + strlen($field['label']) > 70) {
                    $output->writeln($field['label']);
                } else {
                    $output->write($field['label'] . ': ');
                }
                $output->writeln($field['value']);
                $output->writeln($this->addFieldDelimiter());
            }
            $output->writeln($this->addRowDelimiter());
        }
        $output->writeln(sprintf('Number of products listed: %s', count($data)));
    }

    /**
     * Get list of options and arguments for the command
     *
     * @return mixed
     */
    public function getInputList()
    {
        return [
            new InputArgument(
                self::INPUT_KEY_FEED_ID,
                InputArgument::OPTIONAL,
                'Specify the feed ID if you need to run only one feed at a time. ' . 
                    'Missing feed_id will process the queue'
            ),
            new InputArgument(
                self::INPUT_KEY_TEST_SKU,
                InputArgument::OPTIONAL,
                'Generate the feed only for a product sku. To be used for tests and debuging. ' . 
                    'Requires feed_id specified'
            )
        ];
    }

    protected function addFieldDelimiter()
    {
        return str_repeat('-', self::DELIMITER_LENGTH);
    }

    protected function addRowDelimiter()
    {
        return str_repeat('-_', round(self::DELIMITER_LENGTH/2, 0));
    }
}
