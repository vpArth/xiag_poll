<?php

use DI\Container;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Xiag\Poll\Controller\ApiController;
use Xiag\Poll\Controller\UIController;

/** @var Container $IoC */
$IoC = require dirname(__DIR__) . '/app/bootstrap.php';

$router = FastRoute\simpleDispatcher(static function (RouteCollector $rc) {
  $rc->get('/', [UIController::class, 'createPollPage']);
  $rc->get('/{uuid}', [UIController::class, 'votePage']);

  $rc->post('/api/poll', [ApiController::class, 'createPoll']);
  $rc->post('/api/vote', [ApiController::class, 'submitVote']);
  $rc->get('/api/poll/{uuid}/results', [ApiController::class, 'results']);
});

$route = $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
$route_status = array_shift($route);

switch ($route_status) {
  case Dispatcher::NOT_FOUND:
    header('HTTP/1.1 404 Not Found');
    echo '404 Not Found';
    break;
  case Dispatcher::METHOD_NOT_ALLOWED:
    header('HTTP/1.1 405 Method Not Allowed');
    echo '405 Method Not Allowed';
    break;
  case Dispatcher::FOUND:
  default:
    [$controller, $params] = $route;

    $IoC->call($controller, $params);
    break;
}
