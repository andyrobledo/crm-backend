<?php
/**
 * Created by PhpStorm.
 * User: andres
 * Date: 5/21/2016
 * Time: 10:11 a.m.
 */

namespace ANDRES\ejemplo\slim;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SAIT\Utils\AuthJWT;
use SAIT\Utils\Logger;
use SAIT\Utils\Mail;
use SAIT\Utils\AuditLog;


class Login
{

    function Login(Request $req, Response $res, array $args)
    {
        $JWT = new AuthJWT();
        $body = json_decode($req->getBody());
        $row = array();

        if (empty($body))
            return $res->withJSON(array('error' => 'Bad Request'), 400);

        if (!isset($body->email) || !isset($body->passwd))
            return $res->withJSON(array('error' => 'Bad Request', 'msg' => 'email o passwd no estan definidos correctamente'), 400);

        $user = array(
            $body->email,
            md5($body->passwd),
        );

        $sql = 'SELECT u.iduser, u.name, u.mail FROM usuarios u' .
            ' WHERE u.mail =? AND u.pswd=? LIMIT 1';

        try {
            $row = db_getrow($sql, $user);
        } catch (\Exception $e) {
            Logger::type()->error(' Error en la consulta de login => ', [$e->getMessage()]);
            return $res->withJSON(array('error' => 'Internal Error', 'msg' => $e->getMessage()), 500);
        }

        if (!$row)//no autorizado
            return $res->withJSON(array('error' => 'Unauthorized', 'msg' => 'Credenciales Incorrectas o Inexistentes'), 401);

        //Autorizado
        $row->token = $JWT->Generate($row);
        //  Mail::Send();

        return $res->withJSON($row);
    }

}