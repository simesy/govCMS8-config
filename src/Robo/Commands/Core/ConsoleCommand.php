<?php

namespace govCMS\Config\Robo\Commands\Core;

use govCMS\Config\Robo\CommandBase;
use Robo\Contract\VerbosityThresholdInterface;

/**
 * Class ConsoleCommand.
 *
 * @package govCMS\Config\Robo\Commands\Core
 */
class ConsoleCommand extends CommandBase
{

    /**
     * Installs the govCMS command console.
     *
     * @command install:console
     */
    public function installConsole()
    {

    }

    /**
     * Find the position of a closing bracket for a given stanza in a string.
     *
     * @param $contents
     *   The string containing the brackets.
     * @param int $start_pos
     *   The position of the opening bracket in the string that should be
     *   matched.
     *
     * @return int|null
     */
    protected function getClosingBracketPosition($contents, $start_pos)
    {
        $brackets = ['{'];
        for ($pos = $start_pos; $pos < strlen($contents); $pos++) {
            $char = substr($contents, $pos, 1);
            if ($char == '{') {
                array_push($brackets, $char);
            } elseif ($char == '}') {
                array_pop($brackets);
            }
            if (count($brackets) == 0) {
                return $pos;
            }
        }

        return null;
    }

    /**
     * Creates a ~/.bash_profile on OSX if one does not exist.
     */
    protected function createOsxBashProfile()
    {
        if ($this->getInspector()->isOsx()) {
            $continue = $this->confirm("Would you like to create ~/.bash_profile?");
            if ($continue) {
                $user = posix_getpwuid(posix_getuid());
                $home_dir = $user['dir'];
                $bash_profile = $home_dir.'/.bash_profile';
                if (!file_exists($bash_profile)) {
                    $result = $this->taskFilesystemStack()
                      ->touch($bash_profile)
                      ->run();
                    if (!$result->wasSuccessful()) {
                        throw new \Exception("Could not create $bash_profile.");
                    }

                    return true;
                }
            }
        }

        return false;
    }

}
