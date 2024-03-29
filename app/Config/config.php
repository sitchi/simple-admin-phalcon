<?php

/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */

use Phalcon\Config\Config;

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => '',
        'password' => '',
        'dbname' => '',
        'charset' => 'utf8',
    ],
    'application' => [
        'baseUri' => '/',
        'publicUrl' => 'simple-admin.sitchi.dev',
        'appDir' => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/Controllers/',
        'formsDir' => APP_PATH . '/Forms/',
        'helpersDir' => APP_PATH . '/Helpers/',
        'libraryDir' => APP_PATH . '/Library/',
        'migrationsDir' => APP_PATH . '/Migrations/',
        'modelsDir' => APP_PATH . '/Models/',
        'servicesDir' => APP_PATH . '/Services/',
        'viewsDir' => APP_PATH . '/Views/',
        'cacheDir' => BASE_PATH . '/cache/',
        'cryptSalt' => 'eEAfR|_&G&f,+vU]:jFr!!A&+71w1Ms9~8_4L!<@[N@DyaIP_2My|:+.u>/6m,$D'
    ],
    'mail' => [
        'fromName' => 'Simple Admin',
        'fromEmail' => 'info@sitchi.dev',
        'smtp' => [
            'server' => 'smtp.sitchi.dev',
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
