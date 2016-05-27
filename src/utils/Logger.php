<?php
/**
 * Created by PhpStorm.
 * User: gerardo
 * Date: 4/11/2016
 * Time: 8:55 a.m.
 */

namespace SAIT\Utils;
use Monolog;

class Logger
{
    static function type(){
        $logger = new Monolog\Logger('claves.sait.mx');
        $logger->pushProcessor(new Monolog\Processor\UidProcessor());
        $logger->pushHandler(new Monolog\Handler\StreamHandler(__DIR__ . '/../../logs/app.log', Monolog\Logger::DEBUG));
        return $logger;
    }

}