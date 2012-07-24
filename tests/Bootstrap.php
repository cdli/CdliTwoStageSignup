<?php

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use CdliTwoStageSignupTest\Framework\TestCase;
use CdliTwoStageSignupTest\Framework\TestCaseOptions;

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
$serviceManager = new ServiceManager(new ServiceManagerConfig(@$configuration['service_manager'] ?: array()));
$serviceManager->setService('ApplicationConfig', $configuration);
$serviceManager->get('ModuleManager')->loadModules();

TestCase::setServiceLocator($serviceManager);

$config = $serviceManager->get('Configuration');
TestCase::setOptions(new TestCaseOptions($config['cdli-twostagesignup-test']));

