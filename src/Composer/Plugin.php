<?php

namespace govCMS\Config\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvents;
use Composer\Installer\PackageEvent;
use Composer\Script\ScriptEvents;
use Composer\Script\Event;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\Util\ProcessExecutor;
use Composer\Util\Filesystem;

class Plugin implements PluginInterface, EventSubscriberInterface
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
     * @var
     */
    protected $eventDispatcher;

    /**
     * @var ProcessExecutor
     */
    protected $executor;

    /**
     * @var string
     */
    protected $govCMSConfigPackageName = 'govcms/govcms8-config';

    /**
     * @var PackageInterface
     */
    protected $govCMSConfigPackage;

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
        $this->eventDispatcher = $composer->getEventDispatcher();
        // Set timeout 3000.
        ProcessExecutor::setTimeout(3000);
        $this->executor = new ProcessExecutor($this->io);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => "onPostPackageEvent",
            PackageEvents::POST_PACKAGE_UPDATE => "onPostPackageEvent",
            ScriptEvents::POST_UPDATE_CMD => 'onPostCmdEvent',
        ];
    }

    /**
     * Execute update after update command has been executed, if applicable.
     *
     * @param PackageEvent $event
     */
    public function onPostPackageEvent(PackageEvent $event)
    {
        $package = $this->getGovCMSPackage($event->getOperation());
        if ($package) {
            $this->govCMSConfigPackage = $package;
        }
    }

    /**
     * Post update after command has been executed, if applicable.
     *
     * @param Event $event
     */
    public function onPostCmdEvent(Event $event)
    {
        // Only install the template files if govCMS package was installed.
        if (isset($this->govCMSConfigPackage)) {
            $version = $this->govCMSConfigPackage->getVersion();
            $this->executeUpdate($version);
        }
    }

    /**
     * @param $operation
     *
     * @return null
     */
    protected function getGovCMSPackage($operation)
    {
        if ($operation instanceof InstallOperation) {
            $package = $operation->getPackage();
        } elseif ($operation instanceof UpdateOperation) {
            $package = $operation->getTargetPackage();
        }
        if (isset($package) && $package instanceof PackageInterface && $package->getName() == $this->govCMSConfigPackageName) {
            return $package;
        }
        return null;
    }
    /**
     * @param $version
     */
    protected function executeUpdate($version)
    {
        $this->io->write('<comment>Skipping update of templated files</comment>');
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
