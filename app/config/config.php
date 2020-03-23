<?php

/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => '',
        'charset' => 'utf8',
    ],
    'application' => [
        'baseUri' => '/',
        'publicUrl' => 'viphost.ge',
        'appDir' => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'formsDir' => APP_PATH . '/forms/',
        'helpersDir' => APP_PATH . '/helpers/',
        'libraryDir' => APP_PATH . '/library/',
        'migrationsDir' => APP_PATH . '/migrations/',
        'modelsDir' => APP_PATH . '/models/',
        'viewsDir' => APP_PATH . '/views/',
        'cacheDir' => BASE_PATH . '/cache/',
        'baseUri' => '/',
    ],
    'mail' => [
        'fromName' => 'Simple Admin',
        'fromEmail' => 'info@viphost.ge',
        'smtp' => [
            'server' => 'smtp.viphost.ge',
            'port' => 465,
            'security' => 'ssl',
            'username' => '',
            'password' => '',
        ],
    ],
    'logger' => [
        'path' => BASE_PATH . '/logs/',
        'filename' => 'application.log',
        'format' => '%date% [%type%] %message%',
        'date' => 'Y-m-d H:i:s',
    ],
    // Set to false to disable sending emails (for use in test environment)
    'useMail' => false
]);
