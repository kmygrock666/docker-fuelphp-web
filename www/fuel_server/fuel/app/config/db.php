<?php
/**
 * Use this file to override global defaults.
 *
 * See the individual environment DB configs for specific config information.
 */

return array(
    'active' => 'production',
    'development' => array(
        'type'           => 'mysql',
        'connection'     => array(
            'hostname'       => 'localhost',
            'port'           => '3306',
            'database'       => 'games',
            'username'       => 'root',
            'password'       => 'root',
            'persistent'     => false,
            'compress'       => false,
        ),
        'identifier'     => '`',
        'table_prefix'   => '',
        'charset'        => 'utf8',
        'enable_cache'   => true,
        'profiling'      => false,
        'readonly'       => false,
    ),
    
    // PDO 驅动程序配置
    'production' => array(
        'type'           => 'pdo',
        'connection'     => array(
            'dsn'            => 'mysql:host=mysql;dbname=games',
            'username'       => 'game',
            'password'       => 'game',
            'persistent'     => false,
            'compress'       => false,
        ),
        'identifier'     => '`',
        'table_prefix'   => '',
        'charset'        => 'utf8',
        'enable_cache'   => true,
        'profiling'      => false,
        // 'readonly'       => array('slave1', 'slave2', 'slave3'),
    ),

    'redis' => array(
        'default' => array(
            'hostname' => 'redis',
            'port' => '6379',
            'timeout' => '5000',
            'database' => '0',
        ),
    ),
    
    'slave1' => array(
        // 第一生產唯讀 slave db 的配置
    ),
    
    'slave2' => array(
        // 第二生產唯讀 slave db 的配置
    ),
    
    'slave3' => array(
        // 第三生產唯讀 slave db 的配置
    ),
);
