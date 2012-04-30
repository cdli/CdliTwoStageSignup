<?php
return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
            	'cdli-twostagesignup-controller' => 'CdliTwoStageSignup\Controller\RegisterController',
            ),

            /**
             * Controller Configuration
             */

            'cdli-twostagesignup-controller' => array(
                'parameters' => array(
                    'emailVerificationForm' => 'CdliTwoStageSignup\Form\EmailVerification',
                ),
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
             * Forms Configuration
             */

            'CdliTwoStageSignup\Form\EmailVerification' => array(
                'parameters' => array(
                    'emailValidator'    => 'zfcuser_uemail_validator',
                    'captcha_element'   => 'zfcuser_captcha_element'
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
                                            'controller' => 'cdli-twostagesignup-controller',
                                            'action'     => 'email-validation',
                                        ),
			                            'child_routes' => array(
            			                    'step1' => array(
                        			            'type' => 'Literal',
                                    			'options' => array(
			                                        'route' => '/step1',
            			                            'defaults' => array(
                        			                    'controller' => 'cdli-twostagesignup-controller',
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
            ),
        ),
    ),
);
