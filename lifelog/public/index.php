<?php
use Phalcon\Di\FactoryDefault;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {
// set up default framework settings
    $di = new FactoryDefault();

// include required files
    include APP_PATH . '/config/router.php';
    include APP_PATH . '/config/services.php';
    $config = $di->getConfig();
    include APP_PATH . '/config/loader.php';
// Start Application
    $application = new \Phalcon\Mvc\Application($di);
    echo $application->handle()->getContent();

} 
//Catch errors
catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
