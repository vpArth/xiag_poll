<?php

use DI\Container;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Xiag\Poll\Controller\ApiController;
use Xiag\Poll\Controller\UIController;
use Xiag\Poll\Util\RequestInterface;

/** @var Container $IoC */
$IoC = require dirname(__DIR__) . '/app/bootstrap.php';

$router = FastRoute\simpleDispatcher(static function (RouteCollector $rc) {
  $rc->get('/', [UIController::class, 'createPollPage']);
  $rc->get('/{uuid}', [UIController::class, 'votePage']);

  $rc->post('/api/poll', [ApiController::class, 'createPoll']);
  $rc->post('/api/vote', [ApiController::class, 'submitVote']);
  $rc->get('/api/poll/{uuid}/results', [ApiController::class, 'results']);
});

$request = $IoC->get(RequestInterface::class);
$route   = $router->dispatch($request->getMethod(), $request->getPath());

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

    try {
      $IoC->call($controller, $params);
    } catch (Throwable $ex) {
      header('HTTP/1.1 500 Internal Server Error');
      header('Content-Type: application/json');
      echo json_encode(['error' => $ex->getMessage()]);
      throw $ex;
    }
    break;
}
