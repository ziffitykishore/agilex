<?php

namespace SomethingDigital\AssetOptimizer\Console;

use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use SomethingDigital\AssetOptimizer\Dev\Deploy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StaticCommand extends Command
{
    protected $configLoader;
    protected $deploy;
    protected $objectManager;
    protected $state;

    public function __construct(
        ConfigLoaderInterface $configLoader,
        Deploy $deploy,
        ObjectManagerInterface $objectManager,
        State $state
    ) {
        parent::__construct(null);

        $this->configLoader = $configLoader;
        $this->deploy = $deploy;
        $this->objectManager = $objectManager;
        $this->state = $state;
    }

    protected function configure()
    {
        $this->setName('sd:dev:static');
        $this->setDescription('Generate static symlinks.');

        $this->addOption('area', null, InputOption::VALUE_OPTIONAL, 'Area to build', 'frontend');
        $this->addOption('locale', null, InputOption::VALUE_OPTIONAL, 'Locales to build', 'en_US');
        $this->addOption('requirejs-only', null, InputOption::VALUE_NONE, 'Generate only requirejs-config.js.');
        $this->addArgument('theme', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Themes to build');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appMode = $this->state->getMode();
        // We can only generate requirejs-config.js in production mode.
        if ($appMode == State::MODE_PRODUCTION && !$input->getOption('requirejs-only')) {
            $output->writeln('<error>Not supported in production mode yet.</error>');
            return 1;
        }

        $area = (string) $input->getOption('area');
        $this->state->setAreaCode($area);
        $this->objectManager->configure($this->configLoader->load($area));

        foreach ($input->getArgument('theme') as $theme) {
            $params = [
                'area' => $area,
                'theme' => $theme,
                'locale' => (string) $input->getOption('locale'),
                'requirejs-only' => $input->getOption('requirejs-only'),
                'https' => true,
            ];
            $this->deploy->generateTheme($params);

            // Now generate the non-HTTPS requirejs-config, for good measure.
            $params['https'] = false;
            $params['requirejs-only'] = true;
            $this->deploy->generateTheme($params);
        }

        return 0;
    }
}
