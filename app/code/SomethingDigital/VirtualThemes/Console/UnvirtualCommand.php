<?php

namespace SomethingDigital\VirtualThemes\Console;

use SomethingDigital\VirtualThemes\Model\Processor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UnvirtualCommand extends Command
{
    private $processor;

    public function __construct(Processor $processor)
    {
        parent::__construct(null);
        $this->processor = $processor;
    }

    protected function configure()
    {
        $this->setName('sd:dev:unvirtual');
        $this->setDescription('Set themes to physical.');

        $this->addOption('area', null, InputOption::VALUE_OPTIONAL, 'Area to build', 'frontend');
        $this->addArgument('theme', InputArgument::IS_ARRAY, 'Themes to change');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $area = $input->getOption('area');
        $themes = (array) $input->getArgument('theme');
        if (count($themes) == 0) {
            $this->processor->makeAllPhysical($area);
        }
        foreach ($themes as $theme) {
            $this->processor->makePhysical($area, $theme);
        }

        return 0;
    }
}
