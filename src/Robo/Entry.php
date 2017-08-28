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
    public function __construct(Config $config, InputInterface $input = NULL, OutputInterface $output = NULL)
    {
        // Create applicaton.
        $this->setConfig($config);
        $application = new Application('govCMS', static::VERSION);

        // // Create and configure container.
        $container = Robo::createDefaultContainer($input, $output, $application,$config);
        $this->setContainer($container);

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
    public function run(InputInterface $input, OutputInterface $output) {
      $application = $this->getContainer()->get('application');
      $status_code = $this->runner->run($input, $output, $application, $this->commands);
      return $status_code;
    }

}
