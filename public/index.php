<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../src/libs/MSLDB.php';

$dbinfo = json_decode('{"host":"localhost","user":"root","pswd":"","database":"andrescrm"}');
db_setup("mysql:host={$dbinfo->host};dbname={$dbinfo->database}", $dbinfo->user, $dbinfo->pswd);


require __DIR__ . '/../src/app-loader.php';


// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
