<?php

require 'vendor/autoload.php';

use app\middlewares\CorsMiddleware;
use app\middlewares\JsonBodyParserMiddleware;
use app\routes\Routes;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteContext;

//Access-Control-Allow-Origin header with wildcard.
header('Access-Control-Allow-Origin: *');

$app = AppFactory::create();


$app->add(JsonBodyParserMiddleware::class);

$app->addErrorMiddleware(true, true, true);


session_start();

$app->get('/', function(Request $req, Response $res){
$res->getBody()->write(json_encode('AAA'));

return $res->withHeader('Content-type', 'application/json');
});

$routes = Routes::get();


$routes->ligarRotas($app);

//Enable CORS
$app->add(CorsMiddleware::class);

$app->addRoutingMiddleware();

$app->run();