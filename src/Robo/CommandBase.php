<?php

namespace govCMS\Config\Robo;

use govCMS\Config\Robo\Common\ArrayManipulator;
use govCMS\Config\Robo\Common\IO;
use govCMS\Config\Robo\Config\ConfigAwareTrait;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Robo\Contract\BuilderAwareInterface;
use Robo\Contract\ConfigAwareInterface;
use Robo\Contract\IOAwareInterface;
use Robo\LoadAllTasks;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Command base class.
 */
class CommandBase implements ConfigAwareInterface, LoggerAwareInterface, BuilderAwareInterface, IOAwareInterface, ContainerAwareInterface
{

    use ConfigAwareTrait;
    use LoggerAwareTrait;
    use IO;
    use ContainerAwareTrait;
    use LoadAllTasks;

    /**
     * @var int
     */
    protected $invokeDepth = 0;

    /**
     * Invokes an array of Symfony commands.
     *
     * @param array $commands
     *   An array of Symfony commands to invoke. E.g., 'tests:behat'.
     */
    protected function invokeCommands(array $commands)
    {
        foreach ($commands as $key => $value) {
            if (is_numeric($key)) {
                $command = $value;
                $args = [];
            } else {
                $command = $key;
                $args = $value;
            }
            $this->invokeCommand($command, $args);
        }
    }

    /**
     * Invokes a single Symfony command.
     *
     * @param string $command_name
     *   The name of the command. E.g., 'tests:behat'.
     * @param array $args
     *   An array of arguments to pass to the command.
     */
    protected function invokeCommand($command_name, array $args = [])
    {
        $this->invokeDepth++;
        if (!$this->isCommandDisabled($command_name)) {
            $application = $this->getContainer()->get('application');
            $command = $application->find($command_name);
            $input = new ArrayInput($args);
            $prefix = str_repeat(">", $this->invokeDepth);
            $this->output->writeln("<comment>$prefix $command_name</comment>");
            $exit_code = $application->runCommand($command, $input,
              $this->output());
            $this->invokeDepth--;
            // The application will catch any exceptions thrown in the executed
            // command. We must check the exit code and throw our own exception. This
            // obviates the need to check the exit code of every invoked command.
            if ($exit_code) {
                throw new \Exception("Command `$command_name {$input->__toString()}` exited with code $exit_code.");
            }
        }
    }

    /**
     * Gets an array of commands that have been configured to be disabled.
     *
     * @return array
     *   A flat array of disabled commands.
     */
    protected function getDisabledCommands()
    {
        $disabled_commands_config = $this->getConfigValue('disable-targets');
        if ($disabled_commands_config) {
            $disabled_commands = ArrayManipulator::flattenMultidimensionalArray($disabled_commands_config, ':');

            return $disabled_commands;
        }

        return [];
    }

    /**
     * Determines if a command has been disabled via disable-targets.
     *
     * @param string $command
     *   The command name.
     *
     * @return bool
     *   TRUE if the command is disabled.
     */
    protected function isCommandDisabled($command)
    {
        $disabled_commands = $this->getDisabledCommands();
        if (is_array($disabled_commands) && array_key_exists($command,
            $disabled_commands) && $disabled_commands[$command]) {
            $this->logger->warning("The $command command is disabled.");

            return true;
        }

        return false;
    }
    
}
