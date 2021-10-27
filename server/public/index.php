<?php

use App\Application\Controllers\Account;
use App\Application\Controllers\Csrf;
use Padcmoi\BundleApiSlim\DotEnv;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Paris');

DotEnv::load();

$app = AppFactory::create();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header('Access-Control-Allow-Headers: Content-Type, Accept, Origin, Authorization, csrf-token');
header("Access-Control-Max-Age: 3600");

// Error handling 404
$app->addRoutingMiddleware();
$show_errors = isset($_ENV['SHOW_ERRORS']) ? $_ENV['SHOW_ERRORS'] == 1 ? true : false : false;
$errorMiddleware = $app->addErrorMiddleware($show_errors, $show_errors, $show_errors);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

// Controllers
$app->get('/csrf/generate', Csrf::class . ':generate');
$app->put('/csrf/renew', Csrf::class . ':renew');

$app->get('/account/captcha', Account::class . ':captcha');
$app->post('/account/login', Account::class . ':login');
$app->get('/account/check', Account::class . ':renewToken');
$app->put('/account/check', Account::class . ':renewToken');

$app->run();