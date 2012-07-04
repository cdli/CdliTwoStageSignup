<?php
return array(
    'cdli-twostagesignup-test' => array(
        'database_schema_up'   => 'schema_up.mysql.sql',
        'database_schema_down' => 'schema_down.sql',
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => function ($sm) {
                return new Zend\Db\Adapter\Adapter(array(
                    'driver'    => 'pdo',
                    'dsn'       => 'mysql:dbname=travis_test;host=localhost',
                    'database'  => 'travis_test',
                    'username'  => 'root',
                    'password'  => '',
                    'hostname'  => 'localhost',
                ));
            },
        ),
    ),
);
