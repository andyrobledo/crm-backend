<?php
/**
 * Created by PhpStorm.
 * User: gerardo
 * Date: 4/9/2016
 * Time: 1:09 p.m.
 */

namespace SAIT\Utils;


class AuditLog
{
    static function Set($description, $idContract, $idUser)
    {

        try {
            db_beginTransaction();

            db_insert('audit', array(
                'description' => $description,
                'contract_id' => $idContract,
                'user_id' => $idUser
            ));

            db_commit();

        } catch (\Exception $e) {
            error_log('Errror : '.$e->getMessage());
        }

    }


}