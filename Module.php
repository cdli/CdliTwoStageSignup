<?php

namespace CdliTwoStageSignup;

use Zend\ModuleManager\ModuleManager,
    Zend\EventManager\StaticEventManager,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface,
    ZfcUser\Validator\NoRecordExists as ZfcUserUniqueEmailValidator;

class Module implements 
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{

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

    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfiguration()
    {
        return array(
            'invokables' => array(
                'cdlitwostagesignup_ev_form' => 'CdliTwoStageSignup\Form\EmailVerification',
            ),
            'factories' => array(
                'cdlitwostagesignup_module_options' => function($sm) {
                    $config = $sm->get('Configuration');
                    return new Options\ModuleOptions($config['cdli-twostagesignup']);
                },
                'cdlitwostagesignup_ev_validator' => function($sm) {
                    $obj = new Validator\AssertNoValidationInProgress();
                    $obj->setMapper($sm->get('cdlitwostagesignup_ev_modelmapper'));
                    $obj->setOptions(array('key' => 'email_address'));
                    return $obj;
                },
                'cdlitwostagesignup_ev_modelmapper' => function($sm) {
                    $obj = new Mapper\EmailVerification();
                    $obj->setDbAdapter($sm->get('zfcuser_zend_db_adapter'));
                    $obj->setEntityPrototype(new Entity\EmailVerification());
                    $obj->setHydrator(new  Mapper\EmailVerificationHydrator());
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
                    $obj->setEmailMessageOptions($sm->get('cdlitwostagesignup_module_options'));
                    return $obj;
                },
                'cdlitwostagesignup_ev_filter' => function($sm) {
                    return new Form\EmailVerificationFilter(
                        new ZfcUserUniqueEmailValidator(array(
                            'mapper' => $sm->get('zfcuser_user_mapper'),
                            'key'    => 'email'                            
                        )),
                        $sm->get('cdlitwostagesignup_ev_validator')
                    );
                },
                'Zend\Mail\Transport\Sendmail' => function($sm) {
                    return new \Zend\Mail\Transport\Sendmail();
                }
            ),
        );
    }

}
