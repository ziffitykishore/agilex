<?php
namespace SomethingDigital\URapidFlowAutoExecute\Console\Command;

use Magento\Framework\App\State as AppState;
use Magento\Framework\App\Area;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class URapidFlowCommand extends Command
{
    const INPUT_KEY_KEEP_RUNNING = 'keep-running';

    protected $appState;
    protected $objectmanager;

    public function __construct(
        AppState $appState,
        ObjectManagerInterface $objectmanager
    ) {
        $this->appState = $appState;
        $this->objectmanager = $objectmanager;
        parent::__construct();
    }
    protected function configure()
    {
        $this->setName('sd:urapidflow:run')
            ->setDescription('Run URapidFlow Command')
            ->setDefinition(
                [
                    new InputArgument(
                        'profile',
                        InputArgument::REQUIRED,
                        'Profile id to run'
                    ),
                    new InputOption(
                        self::INPUT_KEY_KEEP_RUNNING,
                        null,
                        InputOption::VALUE_NONE,
                        'Keep profile running'
                    ),
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
        }
        $profile = $input->getArgument('profile');
        $keepRunning = $input->getOption(self::INPUT_KEY_KEEP_RUNNING);

        if ($keepRunning) {
            $stopIfRunning = false;
        } else {
            $stopIfRunning = true;
        }

        // Can't pass \Unirgy\RapidFlow\Helper\Data to constructor because need set area code before
        $helper = $this->objectmanager->get('\Unirgy\RapidFlow\Helper\Data');
        $helper->run($profile, $stopIfRunning);
    }
}
