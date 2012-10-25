<?php

return array(
    'cdli-twostagesignup-test' => array(
        'database_schema_up'   => 'schema_up.sqlite.sql',
        'database_schema_down' => 'schema_down.sql',
        'enable_zend_db_tests'        => true,
        'enable_doctrine_orm_tests'   => true,
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => function ($sm) {
                return new Zend\Db\Adapter\Adapter(array(
                    'driver'    => 'Pdo',
                    'dsn'       => 'sqlite::memory',
                ));
            },
        ),
    ),
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'memory'   => true,
                )
            )
        )
    ),
);
