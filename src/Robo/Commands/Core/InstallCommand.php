<?php

namespace govCMS\Config\Robo\Commands\Core;

use govCMS\Config\Robo\CommandBase;
use govCMS\Config\Robo\Common\ComposerMunge;
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
        // Update root files.
        $this->updateRootFiles();
        // Re-install composer.json packages.
        $this->reInstallComposerPackages();
        // Display the logo art.
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
        $this->mungeComposerJson();
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
        $source = $this->getConfigValue('govcms.config.root').'/template';
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
     * Munges govCMS's templated composer.json with project's composer.json.
     */
    protected function mungeComposerJson()
    {
        // Merge in the extras configuration. This pulls in
        // wikimedia/composer-merge-plugin and composer/installers settings.
        $this->say("Merging default configuration into composer.json...");
        $project_composer_json = $this->getConfigValue('govcms.repo.root').'/composer.json';
        $template_composer_json = $this->getConfigValue('govcms.config.root').'/template/composer.json';
        $munged_json = ComposerMunge::munge($project_composer_json, $template_composer_json);
        $bytes = file_put_contents($project_composer_json, $munged_json);
        if (!$bytes) {
            throw new \Exception("Could not update $project_composer_json.");
        }
    }

    /**
     * Re-install packages.
     *
     * @throws \Exception
     */
    protected function reInstallComposerPackages()
    {
        $this->say("Installing new Composer dependencies provided by govCMS. This make take a while...");
        $result = $this->taskFilesystemStack()
          ->remove([
            $this->getConfigValue('govcms.repo.root').'/composer.lock',
            $this->getConfigValue('govcms.repo.root').'/vendor',
          ])
          ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
          ->run();
        if (!$result->wasSuccessful()) {
            throw new \Exception("Could not remove Composer files.");
        }
        $result = $this->taskExecStack()
          ->dir($this->getConfigValue('govcms.repo.root'))
          ->exec("composer install --no-interaction --prefer-dist --ansi")
          ->detectInteractive()
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
        $logo_art = $this->getConfigValue('govcms.config.root').'/govCMS8.txt';
        $this->say(file_get_contents($logo_art));
    }

}
