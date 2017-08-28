<?php

namespace govCMS\Config\Robo;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Robo\Common\ConfigAwareTrait;
use Robo\Config\Config;
use Robo\Robo;
use Robo\Runner as RoboRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Consolidation\AnnotatedCommand\CommandFileDiscovery;

/**
 * The govCMS Robo application entry.
 */
class Entry implements ContainerAwareInterface, LoggerAwareInterface
{

    use ConfigAwareTrait;
    use ContainerAwareTrait;
    use LoggerAwareTrait;

    const VERSION = '1.0.0';

    private $runner;

    /**
     * An array of Robo commands available to the application.
     *
     * @var string[]
     */
    private $commands = [];

    /**
     * Entry constructor.
     *
     * @param \Robo\Config\Config $config
     * @param \Symfony\Component\Console\Input\InputInterface|NULL $input
     * @param \Symfony\Component\Console\Output\OutputInterface|NULL $output
     */
    public function __construct(Config $config, InputInterface $input = null, OutputInterface $output = null)
    {
        // Create applicaton.
        $this->setConfig($config);
        $application = new Application('govCMS', static::VERSION);

        // // Create and configure container.
        $container = Robo::createDefaultContainer($input, $output, $application, $config);
        $this->setContainer($container);

        // Using Multiple RoboFiles commands and hooks.
        $this->addBuiltInCommandsAndHooks();

        // Instantiate Robo Runner.
        $this->runner = new RoboRunner();
        $this->runner->setContainer($container);

        $this->setLogger($container->get('logger'));
    }

    /**
     * Run application.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getContainer()->get('application');
        $status_code = $this->runner->run($input, $output, $application, $this->commands);

        return $status_code;
    }

    /**
     * Add the commands and hooks which are shipped with govCMS core.
     */
    private function addBuiltInCommandsAndHooks()
    {
        $commands = $this->getCommands([
          'path' => __DIR__ . '/Commands',
          'namespace' => 'govCMS\Config\Robo\Commands',
        ]);
        $hooks = $this->getHooks([
          'path' => __DIR__ . '/Hooks',
          'namespace' => 'govCMS\Config\Robo\Hooks',
        ]);
        $this->commands = array_merge($commands, $hooks);
    }

    /**
     * Discovers command classes using CommandFileDiscovery.
     *
     * @param string[] $options
     *   Elements as follow
     *    string path      The full path to the directory to search for commands
     *    string namespace The full namespace for the command directory.
     *
     * @return array
     *   An array of Command classes
     */
    private function getCommands(array $options = ['path' => null, 'namespace' => null])
    {
        $discovery = new CommandFileDiscovery();
        $discovery
          ->setSearchPattern('*Command.php')
          ->setSearchLocations([])
          ->addExclude('Internal');

        return $discovery->discover($options['path'], $options['namespace']);
    }

    /**
     * Discovers hooks using CommandFileDiscovery.
     *
     * @param string[] $options
     *   Elements as follow
     *    string path      The full path to the directory to search for commands
     *    string namespace The full namespace for the command directory.
     *
     * @return array
     *   An array of Hook classes
     */
    private function getHooks(array $options = ['path' => null, 'namespace' => null])
    {
        $discovery = new CommandFileDiscovery();
        $discovery->setSearchPattern('*Hook.php')->setSearchLocations([]);
        
        return $discovery->discover($options['path'], $options['namespace']);
    }

}
