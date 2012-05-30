<?php

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfiguration;
use CdliTwoStageSignupTest\Framework\TestCase;

chdir(__DIR__);

$previousDir = '.';
while (!file_exists('config/application.config.php')) {
    $dir = dirname(getcwd());
    if($previousDir === $dir) {
        throw new RuntimeException(
            'Unable to locate "config/application.config.php":'
            . ' is CdliTwoStageSignup in a subdir of your application skeleton?'
        );
    }
    $previousDir = $dir;
    chdir($dir);
}

if (!include('vendor/autoload.php')) {
    throw new RuntimeException(
        'vendor/autoload.php could not be found. '
        . 'Did you run php composer.phar install in your application skeleton?'
    );
}

// Get application stack configuration
$configuration = include 'config/application.config.php';
$configuration['module_listener_options']['config_glob_paths'][] = __DIR__ . '/config/{,*.}{global,local}.php';

// Setup service manager
$serviceManager = new ServiceManager(new ServiceManagerConfiguration($configuration['service_manager']));
$serviceManager->setService('ApplicationConfiguration', $configuration);
$config = $serviceManager->get('Configuration');

TestCase::setServiceLocator($serviceManager);
