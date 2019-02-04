<?php
return array (
  'backend' =>
  array (
    'frontName' => 'admin_dev',
  ),
  'crypt' =>
  array (
    'key' => '4d010540f36ab90ede0d79980bb892d1',
  ),
  'db' =>
  array (
    'table_prefix' => '',
    'connection' =>
    array (
      'default' =>
      array (
        'host' => 'localhost',
        'dbname' => 'mtwoshop_shopcsnm2_uat',
        'username' => 'mtwoshop_shopcsn',
        'password' => 'PurrsAtriaAutosPlume22',
        'model' => 'mysql4',
        'engine' => 'innodb',
        'initStatements' => 'SET NAMES utf8;',
        'active' => '1',
      ),
    ),
  ),
  'resource' =>
  array (
    'default_setup' =>
    array (
      'connection' => 'default',
    ),
  ),
  'x-frame-options' => 'SAMEORIGIN',
  'MAGE_MODE' => 'production',
  'session' =>
  array (
    'save' => 'redis',
    'redis' =>
    array (
      'host' => '/var/run/redis-multi/csntvqa.m2.shopcsntv.com_sessions.sock',
      'port' => '0',
      'password' => '',
      'timeout' => '2.5',
      'persistent_identifier' => '',
      'database' => '0',
      'compression_threshold' => '2048',
      'compression_library' => 'snappy',
      'log_level' => '1',
      'max_concurrency' => '21',
      'break_after_frontend' => '5',
      'break_after_adminhtml' => '30',
      'first_lifetime' => '600',
      'bot_first_lifetime' => '60',
      'bot_lifetime' => '7200',
      'disable_locking' => '0',
      'min_lifetime' => '60',
      'max_lifetime' => '2592000',
    ),
  ),
  'cache_types' =>
  array (
    'config' => 1,
    'layout' => 1,
    'block_html' => 1,
    'collections' => 1,
    'reflection' => 1,
    'db_ddl' => 1,
    'eav' => 1,
    'customer_notification' => 1,
    'config_integration' => 1,
    'config_integration_api' => 1,
    'full_page' => 1,
    'translate' => 1,
    'config_webservice' => 1,
    'compiled_config' => 1,
  ),
  'install' =>
  array (
    'date' => 'Fri, 20 Jul 2018 14:44:44 +0000',
  ),
  'system' =>
  array (
    'default' =>
    array (
      'dev' =>
      array (
        'debug' =>
        array (
          'debug_logging' => '0',
        ),
      ),
    ),
  ),
  'cache' =>
  array (
    'frontend' =>
    array (
      'default' =>
      array (
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' =>
        array (
          'server' => '/var/run/redis-multi/csntvqa.m2.shopcsntv.com_cache.sock',
          'database' => '0',
          'port' => '0',
        ),
      ),
    ),
  ),
);