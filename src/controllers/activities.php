<?php
/**
 * Created by PhpStorm.
 * User: andres
 * Date: 5/17/2016
 * Time: 10:00 a.m.
 */

namespace ANDRES\ejemplo\slim;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SAIT\Utils\BaseController;

class activities extends BaseController
{

    public function lst(Request $req, Response $res, array $args)
    {
        $queryParams = $req->getQueryParams();
        $offset = isset($queryParams['offset']) ? $queryParams['offset'] : null;
        $limit = isset($queryParams['limit']) ? $queryParams['limit'] : null;
        $search = isset($queryParams['search']) ? $queryParams['search'] : null;


        $sql = 'SELECT * FROM actividades ';

        $limit ? $sql .= ' LIMIT ' . $limit : $sql .= ' LIMIT 100';
        $offset ? $sql .= ' OFFSET ' . $offset : $sql .= ' OFFSET 0';

        try {
            $rows = db_getall($sql);
            $total = count($rows);//TODO: por SQL
        } catch (\Exception $e) {
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }

        return $res->withJSON(array('rows' => $rows, 'total' => $total), 200);
    }


}