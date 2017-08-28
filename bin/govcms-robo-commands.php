<?php

/**
 * @file
 * govCMS Robo commands.
 */

 use Robo\Common\TimeKeeper;
 use Symfony\Component\Console\Input\ArgvInput;
 use Symfony\Component\Console\Output\ConsoleOutput;
 use govCMS\Config\Robo\Config\ConfigDefault;
 use Consolidation\Config\Loader\YamlConfigLoader;
 use govCMS\Config\Robo\Config\ConfigProcessor;
 use govCMS\Config\Robo\Entry;

// Start Timer.
$timer = new TimeKeeper();
$timer->start();

// Initialize input and output.
$input = new ArgvInput($_SERVER['argv']);
$output = new ConsoleOutput();

// Initialize configurations.
$config = new ConfigDefault($root_path);
$loader = new YamlConfigLoader();
$processor = new ConfigProcessor();
$processor->add($config->export());
$processor->extend($loader->load($config->get('govcms.config.root') . '/config/govcms.build.yml'));

$config->import($processor->export());

$app = new Entry($config, $input, $output);
$status_code = $app->run($input, $output);

// Stop timer.
$timer->stop();
$output->writeln("<comment>" . $timer->formatDuration($timer->elapsed()) . "</comment> total time elapsed.");

exit($status_code);
