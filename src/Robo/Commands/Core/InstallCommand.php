<?php

namespace govCMS\Config\Robo\Commands\Core;

use govCMS\Config\Robo\CommandBase;
use Robo\Contract\VerbosityThresholdInterface;

class InstallCommand extends CommandBase
{

    /**
     * (internal) Build a brand new govCMS8 site with all files.
     *
     * Called during `composer create-project govcms/govcms8`.
     *
     * @command internal:create-project
     */
    public function createProject()
    {

        $this->updateRootFiles();

        $this->govCMSBrand();

        $this->yell("Your new govCMS8 project has been created in {$this->getConfigValue('govcms.repo.root')}.");
    }

    /**
     * Updates root project files using templated files.
     *
     * @return \Robo\Result
     */
    protected function updateRootFiles()
    {
        $this->updateVersion();
        $this->rsyncTemplate();
    }

    /**
     * Current project version.
     */
    protected function updateVersion()
    {
        // Needs more work here.
    }

    /**
     * Rsync the template.
     */
    protected function rsyncTemplate()
    {
        $source = $this->getConfigValue('govcms.config.root') . '/template';
        $destination = $this->getConfigValue('govcms.repo.root');
        $exclude_from = $this->getConfigValue('govcms.update.ignore');

        $this->say("Copying files from govCMS's template into your project...");

        $result = $this->taskExecStack()
          ->exec("rsync -a --no-g '$source/' '$destination/' --exclude-from='$exclude_from'")
          ->exec("rsync -a --no-g '$source/' '$destination/' --include-from='$exclude_from' --ignore-existing")
          ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
          ->run();

        if (!$result->wasSuccessful()) {
          throw new \Exception("Could not install Composer requirements.");
        }
    }

    /**
     * Print govCMS brand to command line screen.
     */
    protected function govCMSBrand()
    {
        $logo_art = $this->getConfigValue('govcms.config.root') . '/govCMS8.txt';
        $this->say(file_get_contents($logo_art));
    }

}