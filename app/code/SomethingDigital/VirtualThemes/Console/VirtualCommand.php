<?php

namespace SomethingDigital\VirtualThemes\Console;

use SomethingDigital\VirtualThemes\Exception\NotFoundException;
use SomethingDigital\VirtualThemes\Model\Processor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VirtualCommand extends Command
{
    private $processor;

    public function __construct(Processor $processor)
    {
        parent::__construct(null);
        $this->processor = $processor;
    }

    protected function configure()
    {
        $this->setName('sd:dev:virtual');
        $this->setDescription('Set themes to virtual.');

        $this->addOption('area', null, InputOption::VALUE_OPTIONAL, 'Area to build', 'frontend');
        $this->addArgument('theme', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Themes to change');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $area = $input->getOption('area');
        foreach ($input->getArgument('theme') as $theme) {
            $this->processor->makeVirtual($area, $theme);
        }

        return 0;
    }
}
