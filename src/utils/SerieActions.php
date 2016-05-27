<?php
/**
 * Created by PhpStorm.
 * User: gerardo
 * Date: 5/11/2016
 * Time: 12:56 p.m.
 */

namespace SAIT\Utils;


class SerieActions
{


    public static function check($serie)
    {
        $serieData = null;

        try {
            //solo series sin contrato asignado
            $serieData = db_getrow('SELECT id, serie, num_licences, product_version, product_name_abbr, product_type_abbr, is_standalone FROM series WHERE active = 1 AND contract_id IS NULL AND serie = ?', [$serie]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return $serieData;
    }


    public static function addToContract($serie, $idUser, $idContract)
    {
        //Excepcion a retornar
        $e = null;

        //serie sin contrato
        $serieData = static::check($serie);


        try {
            $contractData = db_getrow('SELECT * FROM contracts WHERE id=?', [$idContract]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $e;
        }

        if ($contractData->product_name_abbr != $serieData->product_name_abbr) {
            return new \Exception('Productos No Compatibles');
        }

        switch ($serieData->product_type_abbr) {

            case 'sw':

                try {
                    db_beginTransaction();

                    db_insert('contract_user', array(
                        'user_id' => $idUser,
                        'contract_id' => $idContract,
                        'is_superuser' => 1
                    ));

                    db_execute('UPDATE serie SET contract_id= ? WHERE id=?', [$idContract, $idUser]);

                    //TODO: crear licencias

                    db_commit();
                } catch (\Exception $e) {
                    db_rollBack();
                    error_log($e->getMessage());
                    return $e;
                }

                break;

            case 'upd':

                //TODO: validar que el numero de licencias de la serie de actualizacion sea mayor o igual al numero de licencias del contrato
                try {
                    db_beginTransaction();

                    db_execute('UPDATE serie SET contract_id=? WHERE id=?', [$idContract, $serieData->id]);

                    db_execute('UPDATE contract SET max_version=? WHERE id=?', [$serieData->product_version, $idContract]);

                    db_commit();
                } catch (\Exception $e) {
                    db_rollBack();
                    error_log($e->getMessage());
                    return $e;
                }

                break;

            case 'add':

                //TODO: validar que la version de producto de la serie sea maoyr o igual a la max_version del contratopl
                try {
                    db_beginTransaction();

                    db_execute('UPDATE serie SET contract_id=? WHERE id=?', [$idContract, $serieData->id]);

                    $totalLicencias = $contractData->num_licences + $serieData->num_licences;

                    db_execute('UPDATE contract SET num_licences=? WHERE id=?', [$totalLicencias, $idContract]);

                    db_commit();
                } catch (\Exception $e) {
                    db_rollBack();
                    error_log($e->getMessage());
                    return $e;
                }
                break;
        }//swith

        return $e;
    }


}