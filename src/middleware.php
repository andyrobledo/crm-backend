<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->add(function (Request $req, Response $res, callable $next) {

    //Before request
    $res = $next($req, $res);

    //After request
    $res = $res->withHeader('Content-Type', 'application/json; charset=utf-8');
    $res = $res->withHeader('Access-Control-Allow-Origin', '*');
    $res = $res->withHeader("Access-Control-Allow-Headers", "Origin, X-Requested-With, X-Token, G-Recaptcha, Content-Type, Accept");
    $res = $res->withHeader("Access-Control-Allow-Methods", "POST, GET, PUT, DELETE, OPTIONS");

    return $res;
});
