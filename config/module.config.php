<?php
return array(
    'cdli-twostagesignup' => array(
        'email_from_address' => '',
        'verification_email_subject_line' => 'Email Address Verification',
    ),
    'di' => array(
        'instance' => array(
            'alias' => array(
            	'cdli-twostagesignup-controller' => 'CdliTwoStageSignup\Controller\RegisterController',
                'cdli-twostagesignup_evr_tg' => 'Zend\Db\TableGateway\TableGateway',
                'cdli-twostagesignup_evr_validator' => 'CdliTwoStageSignup\Validator\AssertNoValidationInProgress',
                'cdli-twostagesignup_email_view' => 'Zend\View\Renderer\PhpRenderer',
            ),

            /**
             * Controller Configuration
             */

            'cdli-twostagesignup-controller' => array(
                'parameters' => array(
                    'emailVerificationForm' => 'CdliTwoStageSignup\Form\EmailVerification',
                    'emailVerificationService' => 'CdliTwoStageSignup\Service\EmailVerification',
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
            'cdli-twostagesignup_email_view' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\Resolver\AggregateResolver',
                ),
			),

            /**
             * Forms Configuration
             */

            'CdliTwoStageSignup\Form\EmailVerification' => array(
                'parameters' => array(
                    'emailValidator'         => 'zfcuser_uemail_validator',
                    'recordExistsValidator'  => 'cdli-twostagesignup_evr_validator',
                    'captcha_element'        => 'zfcuser_captcha_element'
                ),
            ),
            'cdli-twostagesignup_evr_validator' => array(
                'parameters' => array(
                    'mapper'  => 'CdliTwoStageSignup\Model\EmailVerificationMapper',
                    'options' => array(
                        'key' => 'email_address',
                    ),
                ),
            ),


            /**
             * Service / Mapper / DB
             */
            'CdliTwoStageSignup\Model\EmailVerificationMapper' => array(
                'parameters' => array(
                    'tableGateway'  => 'cdli-twostagesignup_evr_tg',
                ),
            ),
            'cdli-twostagesignup_evr_tg' => array(
                'parameters' => array(
                    'tableName' => 'user_signup_email_verification',
                    'adapter'   => 'zfcuser_zend_db_adapter',
                ),
            ),
            'CdliTwoStageSignup\Service\EmailVerification' => array(
                'parameters' => array(
                    'evrMapper' => 'CdliTwoStageSignup\Model\EmailVerificationMapper',
                    'emailRenderer' => 'cdli-twostagesignup_email_view',
                    'emailTransport' => 'Zend\Mail\Transport\Sendmail',
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
                                    ),
                                    'may_terminate' => true,
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
           			                    'step2' => array(
                       			            'type' => 'Regex',
                                   			'options' => array(
		                                        'regex' => '/step2/token/(?<token>[A-F0-9]+)',
           			                            'defaults' => array(
                       			                    'controller' => 'cdli-twostagesignup-controller',
                                   			        'action'     => 'check-token',
		                                        ),
                                                'spec' => '/step2/token/%token%',
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
