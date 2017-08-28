<?php

namespace govCMS\Config\Robo\Config;

use govCMS\Config\Robo\Common\ArrayManipulator;
use Consolidation\Config\Loader\ConfigProcessor as RoboConfigProcessor;

class ConfigProcessor extends RoboConfigProcessor
{

    /**
     * Expand dot notated keys.
     *
     * @param array $config
     *   The configuration to be processed.
     *
     * @return array
     *   The processed configuration
     */
    protected function preprocess(array $config) {
      $config = ArrayManipulator::expandFromDotNotatedKeys(ArrayManipulator::flattenToDotNotatedKeys($config));
      return $config;
    }

}
