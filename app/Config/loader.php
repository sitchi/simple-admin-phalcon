<?php

use Phalcon\Autoload\Loader;

$loader = new Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->setNamespaces([
    'PSA\Controllers' => $config->application->controllersDir,
    'PSA\Forms' => $config->application->formsDir,
    'PSA\Helpers' => $config->application->helpersDir,
    'PSA\Models' => $config->application->modelsDir,
    'PSA\Services' => $config->application->servicesDir,
    'PSA' => $config->application->libraryDir
]);

$loader->register();

// Use composer autoloader to load vendor classes
require_once BASE_PATH . '/vendor/autoload.php';
