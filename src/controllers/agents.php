<?php
/**
 * Created by PhpStorm.
 * User: andres
 * Date: 5/16/2016
 * Time: 11:30 a.m.
 */

namespace ANDRES\ejemplo\slim;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SAIT\Utils\BaseController;

class agents extends BaseController
{
    public function lst(Request $req, Response $res, array $args)
    {
        $queryParams = $req->getQueryParams();
        $offset = isset($queryParams['offset']) ? $queryParams['offset'] : null;
        $limit = isset($queryParams['limit']) ? $queryParams['limit'] : null;
        $search = isset($queryParams['search']) ? $queryParams['search'] : null;


        $sql = 'SELECT * FROM usuarios ';
        $countSql = 'SELECT COUNT(*) AS count FROM usuarios ';

        $params = array(
        );

        if (!empty($search)) {
            $sql .= ' WHERE name LIKE ? OR mail LIKE ?';
            $countSql .= ' WHERE name LIKE ? OR mail LIKE ?';
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
        $iduser= $args['iduser'];

        try {
            $row = db_getrow('SELECT * FROM usuarios WHERE iduser = ?', [$iduser]);
        } catch (\Exception $e) {
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }

        if (!$row) {
            return $res->withJSON(array('error' => 'Not Found', 'msg' => 'No se encontro usuario.'), 404);
        }

        return $res->withJSON($row, 200);
    }

    public function add(Request $req, Response $res, array $args)
    {
        $body = json_decode($req->getBody());

        try
        {
            db_insert('usuarios', array(
                'name' => $body->name,
                'mail' => $body->mail,
                'pswd' => $body->pswd

            ));

            $idagent = db_lastInsertId();
        }
        catch(\Exception $e){
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }
        return $res->withJSON(array('data' => $idagent), 200);
    }

    public function dlt(Request $req, Response $res, array $args)
    {
        $idagent = $args['iduser'];

        try{
            db_beginTransaction();
            db_execute("DELETE from usuarios where iduser=?", [$idagent]);
            db_commit();
        }
        catch(\Exception $e){
            db_rollBack();
            return $res->withJSON(array('error'=> $e->getMessage()), 500);
        }

        return $res->withJSON(array('data' => $idagent), 200);


    }

    public function upd(Request $req, Response $res, array $args)
    {
        $idagent = $args['iduser'];
        $body = json_decode($req->getBody());

        $data = array(
            $body->name,
            $body->mail,
            $body->pswd,
            $idagent
        );

        try {
            db_beginTransaction();

            db_execute("UPDATE usuarios SET name=?, mail=?, pswd=? WHERE iduser = ?", $data);

            db_commit();
        } catch (\Exception $e) {
            db_rollBack();
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }

        return $res->withJSON(array('data' => $idagent), 200);
    }


}