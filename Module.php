<?php

namespace CdliTwoStageSignup;

use Zend\ModuleManager\ModuleManager,
    Zend\EventManager\StaticEventManager,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface;

class Module implements 
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    protected static $options;

    public function init(ModuleManager $moduleManager)
    {
        $moduleManager->events()->attach('loadModules.post', array($this, 'modulesLoaded'));
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfiguration()
    {
        return array(
            'invokables' => array(
                'cdlitwostagesignup_ev_form' => 'CdliTwoStageSignup\Form\EmailVerification',
            ),
            'factories' => array(
                'cdlitwostagesignup_ev_validator' => function($sm) {
                    $obj = new Validator\AssertNoValidationInProgress();
                    $obj->setMapper($sm->get('cdlitwostagesignup_ev_modelmapper'));
                    $obj->setOptions(array('key' => 'email_address'));
                    return $obj;
                },
                'cdlitwostagesignup_ev_modelmapper' => function($sm) {
                    $obj = new Model\EmailVerificationMapper();
                    $obj->setTableGateway($sm->get('cdlitwostagesignup_ev_tablegateway'));
                    return $obj;
                },
                'cdlitwostagesignup_ev_tablegateway' => function($sm) {
                    $obj = new \Zend\Db\TableGateway\TableGateway(
                        'user_signup_email_verification', 
                        $sm->get('zfcuser_zend_db_adapter')
                    );
                    return $obj;
                },
                'cdlitwostagesignup_ev_service' => function($sm) {
                    $obj = new Service\EmailVerification();
                    $obj->setEmailVerificationMapper($sm->get('cdlitwostagesignup_ev_modelmapper'));
                    $obj->setMessageRenderer($sm->get('Zend\View\Renderer\PhpRenderer'));
                    $obj->setMessageTransport($sm->get('Zend\Mail\Transport\Sendmail'));
                    return $obj;
                },
                'cdlitwostagesignup_ev_filter' => function($sm) {
                    return new Form\EmailVerificationFilter(
                        $sm->get('zfcuser_uemail_validator'),
                        $sm->get('cdlitwostagesignup_ev_validator')
                    );
                }
            ),
        );
    }

    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function modulesLoaded($e)
    {
        $config = $e->getConfigListener()->getMergedConfig();
        static::$options = $config['cdli-twostagesignup'];
    }

    /**
     * @TODO: Come up with a better way of handling module settings/options
     */
    public static function getOption($option)
    {
        if (!isset(static::$options[$option])) {
            return null;
        }
        return static::$options[$option];
    }
}
