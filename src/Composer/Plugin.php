<?php

namespace govCMS\Config\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface
{

    /**
     * @var
     */
    protected $composer;

    /**
     * @var
     */
    protected $io;

    /**
     * Activate function.
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

}
