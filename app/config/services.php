<?php
declare(strict_types=1);

use Phalcon\Escaper;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Stream as SessionAdapter;
use Phalcon\Session\Manager as SessionManager;
use Phalcon\Url as UrlResolver;
use Phalcon\Crypt;
use PSA\Auth\Auth;
use PSA\Acl\Acl;
use PSA\Mail\Mail;
use PSA\Avatar\Gravatar;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    $config = include APP_PATH . "/config/config.php";
    if (is_readable(APP_PATH . "/config/config.dev.php")) {
        $override = include APP_PATH . "/config/config.dev.php";
        $config->merge($override);
    }
    return $config;
});

/**
 * Dispatcher use a default namespace
 */
$di->set('dispatcher', function () {
    //Create/Get an EventManager
    $eventsManager = new \Phalcon\Events\Manager();
    $eventsManager->attach("dispatch", function ($event, $dispatcher, $exception) {
        //controller or action doesn't exist
        $object = $event->getData();
        if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case \Phalcon\Dispatcher\Exception::EXCEPTION_HANDLER_NOT_FOUND:
                case \Phalcon\Dispatcher\Exception::EXCEPTION_ACTION_NOT_FOUND:
                case \Phalcon\Dispatcher\Exception::EXCEPTION_CYCLIC_ROUTING:
                    $dispatcher->forward([
                        'controller' => 'error',
                        'action' => 'show404'
                    ]);
                    return false;
                default :
                    $dispatcher->forward([
                        'controller' => 'error',
                        'action' => 'show500'
                    ]);
            }
        }
    });

    $dispatcher = new Dispatcher();
    //Set default namespace to backend module
    $dispatcher->setDefaultNamespace('PSA\Controllers');
    //Bind the EventsManager to the dispatcher
    //  $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'path' => $config->application->cacheDir . 'volt/',
                'separator' => '_'
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    return new $class($params);
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    $config = $this->getConfig();
    return new MetaDataAdapter([
        'metaDataDir' => $config->application->cacheDir . 'metaData/'
    ]);
});

/**
 * Register the flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    $escaper = new Escaper();
    $flash = new Flash($escaper);
    $flash->setImplicitFlush(false);
    $flash->setCssClasses([
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);

    return $flash;
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flashSession', function () {
    $escaper = new Escaper();
    $flashSession = new FlashSession($escaper);
    $flashSession->setCssClasses([
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning',
    ]);
    return $flashSession;
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionManager();
    $files = new SessionAdapter([
        'savePath' => sys_get_temp_dir(),
    ]);
    $session->setAdapter($files);
    $session->start();

    return $session;
});

/**
 * Crypt service
 */
$di->set('crypt', function () {
    $config = $this->getConfig();
    $crypt = new Crypt();
    $crypt->setKey($config->application->cryptSalt);
    return $crypt;
});

/**
 * Custom authentication component
 */
$di->set('auth', function () {
    return new Auth();
});

/**
 * Setup the private resources, if any, for performance optimization of the ACL.
 */
$di->setShared('AclResources', function () {
    $pr = [];
    if (is_readable(APP_PATH . '/config/privateResources.php')) {
        $pr = include APP_PATH . '/config/privateResources.php';
    }
    return $pr;
});

/**
 * Access Control List
 * Reads privateResource as an array from the config object.
 */
$di->set('acl', function () {
    $acl = new Acl();
    $pr = $this->getShared('AclResources')->privateResources->toArray();
    $acl->addPrivateResources($pr);
    return $acl;
});

/**
 * Mail service
 */
$di->set('mail', function () {
    return new Mail();
});

/**
 * gravatar
 */
$di->setShared('gravatar', function () {
    // Get Gravatar instance
    $gravatar = new Gravatar([]);

    // Setting default image, maximum size and maximum allowed Gravatar rating
    $gravatar->enableSecureURL();
    $gravatar->setDefaultImage('retro')
        ->setSize(220)
        ->setRating(Gravatar::RATING_PG);

    return $gravatar;
});

/**
 * Logger service
 */
$di->set('logger', function ($filename = null, $format = null) {
    $loggerConfigs = $this->getShared('config')->get('logger');

    $format = $format ?: $loggerConfigs->format;
    $filename = trim($loggerConfigs->get('filename'), '\\/');
    $path = rtrim($loggerConfigs->get('path'), '\\/') . DIRECTORY_SEPARATOR;

    $formatter = new \Phalcon\Logger\Formatter\Line($format, $loggerConfigs->date);
    $adapter = new \Phalcon\Logger\Adapter\Stream($path . $filename);
    $adapter->setFormatter($formatter);

    $logger = new \Phalcon\Logger(
        'messages',
        [
            'main' => $adapter,
        ]
    );

    return $logger;
});
