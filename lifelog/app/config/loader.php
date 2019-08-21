<?php

$loader = new \Phalcon\Loader();

$loader->registerNamespaces(
    [
        "lifelog"    => $config->application->modelsDir,
		"security"    => $config->application->modelsDir,
    ]
);
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->pluginsDir,

    ]
)->register();
require_once __DIR__ . "/../../vendor/autoload.php";
