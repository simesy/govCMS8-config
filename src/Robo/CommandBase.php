<?php

namespace govCMS\Config\Robo;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Robo\Contract\BuilderAwareInterface;
use Robo\Contract\ConfigAwareInterface;
use Robo\Contract\IOAwareInterface;
use Robo\Contract\VerbosityThresholdInterface;
use Robo\LoadAllTasks;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use govCMS\Config\Robo\Common\IO;
use govCMS\Config\Robo\Config\ConfigAwareTrait;

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
    
}
