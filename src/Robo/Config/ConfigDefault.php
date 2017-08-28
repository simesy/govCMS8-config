<?php

namespace govCMS\Config\Robo\Config;

class ConfigDefault extends ConfigBase
{

  public function __construct($repo_root) {
    parent::__construct();
    $this->set('repo.root', $repo_root);
    $this->set('docroot', $repo_root . '/docroot');
    $this->set('govcms.config.root', $this->getGovCMSConfigRoot());
    $this->set('composer.bin', $repo_root . '/vendor/bin');
  }


  /**
   * Gets the govCMS config vendor directory. E.g., /vendor/govcms/govcms8-config.
   *
   * @return string
   *   THe filepath for the govCMS config vendor directory.
   *
   * @throws \Exception
   */
  protected function getGovCMSConfigRoot()
  {
    $possible_paths = [
      dirname(dirname(dirname(dirname(__FILE__)))),
      dirname(dirname(dirname(__FILE__))),
    ];
    foreach ($possible_paths as $possible_path) {
      if (file_exists("$possible_path/template")) {
        return $possible_path;
      }
    }
    throw new Exception('Could not find the govCMS config vendor directory');
  }

}
