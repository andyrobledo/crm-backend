<?php
/**
 * Created by IntelliJ IDEA.
 * User: xerardoo
 * Date: 3/23/16
 * Time: 12:26 PM
 */

namespace SAIT\Utils;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


abstract class BaseController
{

    public function get(Request $req, Response $res, array $args)
    {
    }

    public function add(Request $req, Response $res, array $args)
    {
    }

    public function dlt(Request $req, Response $res, array $args)
    {
    }

    public function upd(Request $req, Response $res, array $args)
    {
    }

    public function lst(Request $req, Response $res, array $args)
    {
    }


}