<?php
return array(
    'cdli-twostagesignup-test' => array(
        'database_schema_up'   => 'schema_up.mysql.sql',
        'database_schema_down' => 'schema_down.sql',
        'enable_zend_db_tests'        => true,
        'enable_doctrine_orm_tests'   => true,
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
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => '',
                    'dbname'   => 'travis_test',
                )
            )
        )
    ),
);
