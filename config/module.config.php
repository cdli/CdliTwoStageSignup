<?php
return array(
    'cdli-twostagesignup' => array(
        'email_from_address' => '',
        'verification_email_subject_line' => 'Email Address Verification',
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'cdlitwostagesignup' => __DIR__ . '/../view',
        ),
        'helper_map' => array() 
    ),

    'controller' => array(
        'classes' => array(
            'cdlitwostagesignup_ev_controller' => 'CdliTwoStageSignup\Controller\RegisterController',
        ),
        'map' => array(),
    ),

    'router' => array(
        'routes' => array(
            'zfcuser' => array(
                'child_routes' => array(
                    'register' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/register',
                            'defaults' => array(
                                'controller' => 'cdlitwostagesignup_ev_controller',
                                'action'     => 'email-verification',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'step1' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/step1',
                                    'defaults' => array(
                                        'controller' => 'cdlitwostagesignup_ev_controller',
                                        'action'     => 'email-verification',
                                    ),
                                ),
                             ),
                            'step2' => array(
                                'type' => 'Regex',
                                'options' => array(
                                    'regex' => '/step2/token/(?<token>[A-F0-9]+)',
                                    'defaults' => array(
                                        'controller' => 'cdlitwostagesignup_ev_controller',
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
);
