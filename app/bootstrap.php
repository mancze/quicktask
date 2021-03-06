<?php

use Nette\Application\Routers\Route;
use Nette\Config\Configurator;

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

/* Setup to allow ladenka in production mode */
//$configurator->setDebugMode(TRUE);  // debug mode MUST NOT be enabled on production server

$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;
