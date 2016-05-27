<?php
/**
 * Created by PhpStorm.
 * User: andres
 * Date: 5/16/2016
 * Time: 9:57 a.m.
 */

namespace ANDRES\ejemplo\slim;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SAIT\Utils\BaseController;

class orders extends BaseController
{

    public function lst(Request $req, Response $res, array $args)
    {
        $queryParams = $req->getQueryParams();
        $offset = isset($queryParams['offset']) ? $queryParams['offset'] : null;
        $limit = isset($queryParams['limit']) ? $queryParams['limit'] : null;
        $search = isset($queryParams['search']) ? $queryParams['search'] : null;


        $sql = 'select * from contact_orders';
        $countSql = 'select count(*) as count from contact_orders';

        $params = array(

        );

        if(!empty($search)){
            $sql .= ' where obs like ? or mensaje like ? or Nombre like ? or email like ? or empresa like ?';
            $countSql .= ' where obs like ? or mensaje like ? or Nombre like ? or email like ? or empresa like ?';
            array_push($params, "%$search%");
            array_push($params, "%$search%");
            array_push($params, "%$search%");
            array_push($params, "%$search%");
            array_push($params, "%$search%");
        }

        $limit ? $sql .= ' LIMIT ' . $limit : $sql .= ' LIMIT 100';
        $offset ? $sql .= ' OFFSET ' . $offset : $sql .= ' OFFSET 0';

        try {
            $rows = db_getall($sql, $params);
            $result = db_getrow($countSql, $params);
            $total = $result->count;
        } catch (\Exception $e) {
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }

        return $res->withJSON(array('rows' => $rows, 'total' => $total), 200);
    }


    public function get(Request $req, Response $res, array $args)
    {
        $idord = $args['idord'];

        try {
            $row = db_getrow('SELECT * FROM contact_orders WHERE idord = ?', [$idord]);
        } catch (\Exception $e) {
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }

        if (!$row) {
            return $res->withJSON(array('error' => 'Not Found', 'msg' => 'No se encontro orden.'), 404);
        }


        return $res->withJSON($row, 200);
    }


    public function add(Request $req, Response $res, array $args)
    {

        $body = json_decode($req->getBody());

        try
        {
            db_insert('ordenes', array(
                'idcont' => $body->idcont,
                'obs' => $body->obs,
                'duracion' => $body->duracion

            ));

            $idord = db_lastInsertId();
        }
        catch(\Exception $e){
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }
        return $res->withJSON(array('data' => $idord), 200);

    }

    public function dlt(Request $req, Response $res, array $args)
    {
        $idord = $args['idord'];

        try{
            db_beginTransaction();
            db_execute("DELETE from ordenes where idord=?", [$idord]);
            db_commit();
        }
        catch(\Exception $e){
            db_rollBack();
            return $res->withJSON(array('error'=> $e->getMessage()), 500);
        }

        return $res->withJSON(array('data' => $idord), 200);
    }

    public function upd(Request $req, Response $res, array $args)
    {
        $idord = $args['idord'];
        $body = json_decode($req->getBody());

        $data = array(
            $body->obs,
            $idord
        );

        try {
            db_beginTransaction();

            db_execute("UPDATE ordenes SET obs=? WHERE idord = ?", $data);

            db_commit();
        } catch (\Exception $e) {
            db_rollBack();
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }

        return $res->withJSON(array('data' => $idord), 200);
    }







}