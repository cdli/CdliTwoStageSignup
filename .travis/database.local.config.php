<?php

return array(
    'cdli-twostagesignup' => array(
        'test_database_schema_up'   => 'schema_up.mysql.sql',
        'test_database_schema_down' => 'schema_down.sql',
    ),
    'di' => array(
        'instance' => array(
            'Zend\Db\Adapter\Adapter' => array(
                'parameters' => array(
                    'driver' => array(
                        'driver' => 'Pdo',
                        'dsn'            => "mysql:dbname=travis_test;host=localhost",
                        'username'       => 'root',
                        'password'       => '',
                        'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''),
                    )
                ),
            ),
        ),
    ),
);
