<?php
return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
            	'cdli-twostagesignup' => 'CdliTwoStageSignup\Controller\RegisterController',
            ),

            /**
             * View Configuration
             */

            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths'  => array(
                        'cdli-twostagesignup' => __DIR__ . '/../view',
                    ),
                ),
            ),


            /**
             * Routes
             */

            'Zend\Mvc\Router\RouteStack' => array(
                'parameters' => array(
                    'routes' => array(
                        'zfcuser' => array(
                            'child_routes' => array(
                                'register' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                        'route' => '/register',
                                        'defaults' => array(
                                            'controller' => 'cdli-twostagesignup',
                                            'action'     => 'email-validation',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
