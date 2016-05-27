<?php
/**
 * Created by IntelliJ IDEA.
 * User: xerardoo
 * Date: 3/21/16
 * Time: 2:41 PM
 */

namespace SAIT\Utils;

use Firebase\JWT\JWT;

class AuthJWT
{
    private $secret_token = 'lawaeX4thWHD9Eq8tlmjjwTIuIYKfpGNOa2hBENB01GQCHHHlZUoEIuQCPrDrRk';

    public function Verify($token)
    {
        $decoded = null;

        try {
            JWT::$leeway = 60;
            $decoded = JWT::decode($token, $this->secret_token, array('HS256'));
        } catch (\Exception $e) {
            error_log('Error ' . $e->getMessage());
        }

        return $decoded;
    }

    public function Generate($data)
    {
        $JWT_Token = null;

        $newToken = array(
            "jti" => uniqid(),
            "iss" => "claves.sait.mx",
            "iat" => time(),
            "nbf" => (time() + 10),
            "exp" => (time() + 7200),
            "data" => $data
        );

        try {
            $JWT_Token = JWT::encode($newToken, $this->secret_token);
        } catch (\Exception $e) {
            error_log('error ' . $e);
        }

        return $JWT_Token;
    }

}