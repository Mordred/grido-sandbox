<?php

use \Nette\Application\Routers\Route;

// Load Nette Framework
$useComposer = require __DIR__ . '/../libs/autoload.php';


// Configure application
$configurator = new Nette\Config\Configurator;
$configurator->setTempDirectory(__DIR__ . '/../temp');


// Enable Nette Debugger for error visualisation & logging
//$configurator->setDebugMode();
//$configurator->setProductionMode();
$configurator->enableDebugger(__DIR__ . '/../log');


// Enable RobotLoader - this will load all classes automatically
$loader = $configurator->createRobotLoader();
$loader->addDirectory(__DIR__);

if ($useComposer === FALSE) {
    $loader->addDirectory(__DIR__ . '/../libs');
}

$loader->register();


// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config.neon');

//// Register extensions
Nella\Doctrine\Config\Extension::register($configurator);

$container = $configurator->createContainer();


// Setup router
$uri = $container->parameters['productionMode'] ? 'example/' : '';
$container->router[] = new Route("$uri<filterRenderType>/<presenter>/<action>/<ajax>/", array(
    'filterRenderType' => 'inner',
    'presenter' => 'Dibi',
    'action' => 'default',
    'ajax' => 'on',
));

return $container;
