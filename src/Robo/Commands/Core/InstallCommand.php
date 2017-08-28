<?php

namespace govCMS\Config\Robo\Commands\Core;

use govCMS\Config\Robo\CommandBase;

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
        $this->govCMSBrand();

        $this->yell("Your new govCMS8 project has been created in {$this->getConfigValue('govcms.repo.root')}.");
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
