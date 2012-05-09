<?php

chdir(__DIR__);

$previousDir = '.';
while (!file_exists('config/application.config.php')) {
    $dir = dirname(getcwd());
    if($previousDir === $dir) {
        throw new RuntimeException(
            'Unable to locate "config/application.config.php":'
            . ' is DoctrineORMModule in a subdir of your application skeleton?'
        );
    }
    $previousDir = $dir;
    chdir($dir);
}

if (is_readable(__DIR__ . '/TestConfiguration.php')) {
    require_once __DIR__ . '/TestConfiguration.php';
} else {
    require_once __DIR__ . '/TestConfiguration.php.dist';
}

set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());
require_once (getenv('ZF2_PATH') ?: 'vendor/ZendFramework/library') . '/Zend/Loader/AutoloaderFactory.php';
\Zend\Loader\AutoloaderFactory::factory();

$defaultListeners = new Zend\Module\Listener\DefaultListenerAggregate(
    new Zend\Module\Listener\ListenerOptions(
        array(
            'module_paths' => array(
                realpath(__DIR__ . '/../..'),
                realpath(__DIR__ . '/../../../vendor'),
            ),
        )
    )
);

$moduleManager = new \Zend\Module\Manager(array(
    'ZfcBase',
    'ZfcUser',
    'CdliTwoStageSignup',
));
$moduleManager->events()->attachAggregate($defaultListeners);
$moduleManager->loadModules();

$config = $defaultListeners->getConfigListener()->getMergedConfig()->toArray();

$di = new \Zend\Di\Di();
$di->instanceManager()->addTypePreference('Zend\Di\Locator', $di);

$config = new \Zend\Di\Configuration($config['di']);
$config->configure($di);

\CdliTwoStageSignupTest\Framework\TestCase::setLocator($di);
