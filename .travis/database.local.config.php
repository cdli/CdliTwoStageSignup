<?php

return array(
    'di' => array(
        'instance' => array(
            'Zend\Db\Adapter\Adapter' => array(
                'parameters' => array(
                    'driver' => array(
                        'driver' => 'Pdo',
                        'dsn' => 'sqlite::memory:',
                    )
                ),
            ),
        ),
    ),
);
