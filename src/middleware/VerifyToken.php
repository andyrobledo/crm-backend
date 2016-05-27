<?php
/**
 * Created by IntelliJ IDEA.
 * User: xerardoo
 * Date: 3/22/16
 * Time: 8:50 AM
 */

namespace SAIT\Middleware;

use Monolog\Handler\Curl\Util;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SAIT\Utils\AuthJWT;

class VerifyToken
{
    private $TokenHeader = 'X-Token';

    function __invoke(Request $req, Response $res, callable $next)
    {
        $AUTH = new AuthJWT();

        if ($req->hasHeader($this->TokenHeader)) {

            $headerValues = $req->getHeader($this->TokenHeader);
            //verify if token is valid
            $tokenDecoded = $AUTH->Verify($headerValues[0]);

            if (!isset($tokenDecoded)) {
                //exit request
                return $res->withJSON(array('error' => 'Unauthorized', 'msg' => 'Token Invalido'), 401);
            }

            //pass data to controller
            $req = $req->withAttribute('session', $tokenDecoded->data);
        } else {

            return $res->withJSON(array('error' => 'Bad Request', 'msg' => 'Cabecera X-Token desconocida'), 400);
        }

        $res = $next($req, $res);
        //Here we can modify the request after controller exec

        return $res; // continue
    }
}
