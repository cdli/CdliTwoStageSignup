<?php

return array(
    'cdli-twostagesignup' => array(
        'test_database_schema_up'   => 'schema_up.sqlite.sql',
        'test_database_schema_down' => 'schema_down.sql',
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
);
