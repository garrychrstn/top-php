<?php

use Slim\Factory\AppFactory;
use App\Controllers\CustomerController;
use App\Controllers\ItemController;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Parse json body middleware
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Customer routes
$app->get('/customers', [CustomerController::class, 'getAll']);
$app->post('/customers', [CustomerController::class, 'create']);
$app->get('/customers/{id}', [CustomerController::class, 'getById']);
$app->put('/customers/{id}', [CustomerController::class, 'update']);
$app->delete('/customers/{id}', [CustomerController::class, 'delete']);

// Item routes
$app->get('/items', [ItemController::class, 'getAll']);
$app->post('/items', [ItemController::class, 'create']);
$app->get('/items/{id}', [ItemController::class, 'getById']);
$app->put('/items/{id}', [ItemController::class, 'update']);
$app->delete('/items/{id}', [ItemController::class, 'delete']);

$app->run();
