<?php
/**
 * Created by PhpStorm.
 * User: andres
 * Date: 5/16/2016
 * Time: 10:26 a.m.
 */

namespace ANDRES\ejemplo\slim;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SAIT\Utils\BaseController;

class users extends BaseController
{

    public function lst(Request $req, Response $res, array $args)
    {

        $queryParams = $req->getQueryParams();
        $offset = isset($queryParams['offset']) ? $queryParams['offset'] : null;
        $limit = isset($queryParams['limit']) ? $queryParams['limit'] : null;
        $search = isset($queryParams['search']) ? $queryParams['search'] : null;

        $sql = 'SELECT * FROM contactos';
        $countSql = 'SELECT COUNT(*) AS count FROM contactos ';

        $params = array(
        );

        if (!empty($search)) {
            $sql .= ' WHERE nombre LIKE ? OR empresa LIKE ? OR email LIKE ?';
            $countSql .= ' WHERE nombre LIKE ? OR empresa LIKE ? OR email LIKE ?';
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

    public function add(Request $req, Response $res, array $args)
    {

        $body = json_decode($req->getBody());

        try
        {
            db_insert('contactos', array(
                'nombre' => $body->nombre,
                'empresa' => $body->empresa,
                'email' => $body->email,
                'telefono' => $body->telefono

            ));

            $iduser = db_lastInsertId();
        }
        catch(\Exception $e){
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }
        return $res->withJSON(array('data' => $iduser), 200);

    }

    public function get(Request $req, Response $res, array $args)
    {

        $idcont = $args['idcont'];

        $params = array(
            $idcont
        );

        try {
            $row = db_getrow('SELECT * FROM contactos WHERE idcont = ?', $params);
        } catch (\Exception $e) {
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }

        if (!$row) {
            return $res->withJSON(array('error' => 'Not Found', 'msg' => 'No se encontro usuario.'), 404);
        }


        return $res->withJSON($row, 200);
    }

    public function upd(Request $req, Response $res, array $args)
    {
       $idcont = $args['idcont'];
        $body = json_decode($req->getBody());

        $data = array(
            $body->nombre,
            $body->empresa,
            $body->email,
            $body->telefono,
            $idcont
        );

        try {
            db_beginTransaction();

            db_execute("UPDATE contactos SET nombre=?, empresa=?, email=?, telefono=? WHERE idcont = ?", $data);

            db_commit();
        } catch (\Exception $e) {
            db_rollBack();
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }

         return $res->withJSON(array('data' => $idcont), 200);
    }

    public function dlt(Request $req, Response $res, array $args)
    {
        $idcont = $args['idcont'];

        try{
            db_beginTransaction();
            db_execute("DELETE from contactos where idcont=?", [$idcont]);
            db_commit();
        }
        catch(\Exception $e){
            db_rollBack();
            return $res->withJSON(array('error'=> $e->getMessage()), 500);
        }

        return $res->withJSON(array('data' => $idcont), 200);
    }

    public function obtener(Request $req, Response $res, array $args)
    {
        $idcont = $args['idcont'];

        $params = array(
            $idcont
        );

        $sql = 'SELECT *  FROM contactos INNER JOIN ordenes ON ordenes.idcont = contactos.idcont WHERE contactos.idcont = ?';
        $countSql = 'SELECT COUNT(*) AS count FROM ordenes WHERE idcont = ? ';

        try {
            $rows = db_getall($sql, $params);
            $result = db_getrow($countSql, $params);
            $total = $result->count;
        } catch (\Exception $e) {
            return $res->withJSON(array('error' => $e->getMessage()), 500);
        }

        return $res->withJSON(array('rows' => $rows, 'total' => $total), 200);
    }


}