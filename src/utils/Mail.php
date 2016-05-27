<?php
/**
 * Created by PhpStorm.
 * User: gerardo
 * Date: 4/5/2016
 * Time: 10:55 a.m.
 */

namespace SAIT\Utils;

use Mailgun\Mailgun;


class Mail
{


    //TODO: Para enviar correo se ocupo poner un certificado pem. en php ini.....
    static function Send()
    {
        $client = new \Http\Adapter\Guzzle6\Client();
        $mailgun = new Mailgun("key-0a4f6480d98c7f8eac0b8ec76fd45c49", $client);
        $domain = "sandboxdab02e5233424a358e231a12d889bac7.mailgun.org";

        try {
            $mailgun->sendMessage($domain, array(
                'from' => 'claves@sait.mx',
                'to' => 'gerardo@saitenlinea.com',
                'subject' => 'Correo Prueba',
                'text' => 'Hola mundo!'));
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

    }


    function getTemplate($username, $email, $passwd)
    {
        return <<<EOL
Bienvenido $username <br /><br /><br />
Gracias por crear su cuenta en claves.sait.mx <br /><br />
Credenciales : <br /><br />
Usuario : $email <br /><br />
Contrasena : $passwd <br /><br />
</body></html>
EOL;
    }


}