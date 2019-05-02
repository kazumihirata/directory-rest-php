<?php
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

require __DIR__ . '/Controllers/EmployeeController.php';

$app = new \Slim\Slim();
$controller = new \Controllers\Employee();

$app->get('/', [$controller, 'getEmployees']);
$app->get('/employees/:id', [$controller, 'getEmployee']);
$app->get('/employees/:id/reports', [$controller, 'getReports']);

$app->run();

