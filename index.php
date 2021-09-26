<?php

require 'vendor/autoload.php';

use app\routes\Routes;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

session_start();

$app->get('/', function(RequestInterface $req, ResponseInterface $res){
$res->getBody()->write(json_encode('AAA'));

return $res->withHeader('Content-type', 'application/json');
});

$routes = Routes::get();
$routes->ligarRotas($app);

$app->run();