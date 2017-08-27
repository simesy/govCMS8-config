<?php

namespace govCMS\Config\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Util\Filesystem;

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

    /**
     * Creates or updates composer include files.
     *
     * @param \Composer\Script\Event $event
     */
    public function scaffoldComposerIncludes(Event $event)
    {
        $files = [
            'composer.config.json',
        ];
    }

    /**
     * Get the path to the 'vendor' directory.
     *
     * @return string
     *   The file path of the 'vendor' directory.
     */
    public function getVendorPath()
    {
        $config = $this->composer->getConfig();
        $filesystem = new Filesystem();
        $filesystem->ensureDirectoryExists($config->get('vendor-dir'));
        $vendorPath = $filesystem->normalizePath(realpath($config->get('vendor-dir')));

        return $vendorPath;
    }

    /**
     * Returns the repo root's path, assumed to be one dir above vendor dir.
     *
     * @return string
     *   The file path of the repository root.
     */
    public function getRepoRoot()
    {
        return dirname($this->getVendorPath());
    }

}
