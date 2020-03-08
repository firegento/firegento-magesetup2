<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Command;

use FireGento\MageSetup\Model\ConfigFactory;
use FireGento\MageSetup\Model\Setup\SubProcessor\SubProcessorPool;
use FireGento\MageSetup\Service\SetupServiceFactory;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\State as AppState;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SetupRunCommand
 *
 * @package FireGento\MageSetup\Command
 */
class SetupRunCommand extends Command
{
    /**
     * command name
     */
    const COMMAND_NAME = 'magesetup:setup:run';

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ConfigLoader
     */
    private $configLoader;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var SetupServiceFactory
     */
    private $setupService;

    /**
     * @var ConfigFactory
     */
    private $magesetupConfig;

    /**
     * @var SubProcessorPool
     */
    private $subProcessorPool;

    /**
     * SetupRunCommand constructor.
     *
     * @param SetupServiceFactory $setupService
     * @param ConfigFactory $magesetupConfig
     * @param SubProcessorPool $subProcessorPool
     * @param Registry $registry
     * @param AppState $appState
     * @param ConfigLoader $configLoader
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        SetupServiceFactory $setupService,
        ConfigFactory $magesetupConfig,
        SubProcessorPool $subProcessorPool,
        Registry $registry,
        AppState $appState,
        ConfigLoader $configLoader,
        ObjectManagerInterface $objectManager
    ) {
        $this->setupService = $setupService;
        $this->magesetupConfig = $magesetupConfig;
        $this->appState = $appState;
        $this->configLoader = $configLoader;
        $this->objectManager = $objectManager;
        $this->registry = $registry;
        $this->subProcessorPool = $subProcessorPool;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Run MageSetup setup')
            ->setDefinition($this->getInputList());

        parent::configure();
    }

    /**
     * Setup command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int Non zero if invalid type, 0 otherwise
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $area = $this->appState->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->appState->setAreaCode('adminhtml');
            $area = $this->appState->getAreaCode();
        }

        // phpcs:ignore
        $configLoader = $this->objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
        $this->objectManager->configure($configLoader->load($area));
        $this->registry->register('isSecureArea', true);

        try {
            /*
             * Get the country and validate against allowed countries
             */
            $country = strtolower(trim($input->getArgument('country')));
            $config = $this->magesetupConfig->create(['country' => $country]);
            $allowedCountries = $config->getAllowedCountries();
            if (!in_array($country, $allowedCountries)) {
                throw new \InvalidArgumentException(
                    'Country code "' . $country . '" is not allowed. Supported countries are: '
                    . implode(', ', $allowedCountries)
                );
            }

            /*
             * Check if subProcessor codes are defined and if they are valid
             */
            $subProcessorCodes = [];
            $allowedSubProcessorCodes = $this->subProcessorPool->getSubProcessorCodes();
            if ($input->getArgument('processors')) {
                foreach ($input->getArgument('processors') as $processor) {
                    if (!in_array($processor, $allowedSubProcessorCodes)) {
                        throw new \InvalidArgumentException('Processor "' . $processor . '" is not defined.');
                    }

                    $subProcessorCodes[] = $processor;
                }
            }

            /*
             * Start setup
             */
            $output->writeln('<info>Start setup</info>');

            /** @var \FireGento\MageSetup\Service\SetupServiceInterface $service */
            $service = $this->setupService->create(['config' => $config, 'subProcessorCodes' => $subProcessorCodes]);
            $service->setOutput($output);
            $service->execute();

            $output->writeln('<info>Setup finished</info>');
        } catch (\InvalidArgumentException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return 1;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $output->writeln($e->getTraceAsString());

            return 1;
        }

        return 0;
    }

    /**
     * Get list of options and arguments for the command
     *
     * @return array
     */
    public function getInputList()
    {
        return [
            new InputArgument(
                'country',
                InputArgument::REQUIRED,
                'Country'
            ),
            new InputArgument(
                'processors',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'Processors'
            ),
        ];
    }
}
