<?php

namespace Earthlite\ProductAlert\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Magento\ProductAlert\Model\Observer;

class StockAlert extends Command
{
    /**
     * @var Observer
     */
    protected $productAlert;

    /**
     * @param Observer $productAlert
     */
    public function __construct(
        Observer $productAlert
    ) {
        $this->productAlert = $productAlert;
        parent::__construct();
    }

    /**
     * Configure command name for stock alert emails
     */
    protected function configure()
    {
        $this->setName('product:stock:alert')
            ->setDescription('Triggers product stock alert emails');
        parent::configure();
    }

    /**
     * Execute the stock alert emails.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->productAlert->process();
        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
