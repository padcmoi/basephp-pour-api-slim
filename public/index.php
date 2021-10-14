<?php

use App\Application\Controllers\Main;
use Padcmoi\BundleApiSlim\DotEnv;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Paris');

DotEnv::load();

$app = AppFactory::create();

// Error handling 404
$app->addRoutingMiddleware();
$show_errors = isset($_ENV['SHOW_ERRORS']) ? $_ENV['SHOW_ERRORS'] == 1 ? true : false : false;
$errorMiddleware = $app->addErrorMiddleware($show_errors, $show_errors, $show_errors);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

// Controllers
$app->get('/', Main::class . ':index');
$app->get('/phpinfo', Main::class . ':phpinfo');
$app->post('/phpinfo', Main::class . ':phpinfo');
$app->get('/home', Main::class . ':helloWorld');
$app->get('/users', Main::class . ':helloUsers');
$app->get('/foo/{hi}', Main::class . ':fooHi');

$app->run();
