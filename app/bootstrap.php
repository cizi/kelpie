<?php

$paths = __DIR__ . DIRECTORY_SEPARATOR . 'path.php';
require_once $paths;

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

/** @var Nette\Application\Application $application */
$application = $container->getByType(Nette\Application\Application::class);
$application->errorPresenter = 'Frontend:Error';

(new Dibi\Bridges\Tracy\Panel)->register(
    $container->getByType(Dibi\Connection::class)
);

return $container;
