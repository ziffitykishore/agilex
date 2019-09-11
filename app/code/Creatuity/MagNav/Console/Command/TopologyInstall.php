<?php

namespace Creatuity\MagNav\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TopologyInstall extends Command {

    const NAME_ARGUMENT = 'name';

    const ALLOW_ANONYMOUS = 'allow-anonymous';

    const ANONYMOUS_NAME = 'Anonymous';

    /***
     * @var \Magento\Amqp\Model\TopologyFactory
     */
    protected $topologyFactory;

    public function __construct(
        \Magento\Amqp\Model\TopologyFactory $topologyFactory
    ){
        parent::__construct();
        $this->topologyFactory = $topologyFactory;
    }

    protected function configure(){
        $this->setName('queue:topology:install');
        $this->setDescription('Installs message queue topology');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $topology = $this->topologyFactory->create();
        $topology->install();
        $output->writeln('<info>Message queue topology installed</info>');
    }
}