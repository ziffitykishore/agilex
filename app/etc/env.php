<?php
return [
    'backend' => [
        'frontName' => 'admin_13wx30'
    ],
    'crypt' => [
        'key' => '71725434c31e28d655290bb16aa04747'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'shopcsn',
                'username' => 'shopcsn',
                'password' => 'shopcsn4321',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1'
            ]
        ]
    ],
   'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'cache_types' => [
        'config' => 0,
        'layout' => 0,
        'block_html' => 0,
        'collections' => 0,
        'reflection' => 0,
        'db_ddl' => 0,
        'eav' => 0,
        'customer_notification' => 0,
        'config_integration' => 0,
        'config_integration_api' => 0,
        'full_page' => 0,
        'translate' => 0,
        'config_webservice' => 0
    ],
    'install' => [
        'date' => 'Thu, 12 Jul 2018 12:40:35 +0000'
    ]
];
