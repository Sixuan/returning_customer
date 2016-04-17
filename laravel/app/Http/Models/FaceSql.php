<?php

/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/8/16
 * Time: 10:12 PM
 */
namespace App\Http\Models;

use App\Exceptions\BadRequestException;
use App\Exceptions\NonExistingException;

class FaceSql extends BaseModelSql
{
    /**
     * @var FaceSql
     */
    private static $faceSqlSingleton;

    /**
     * @return FaceSql
     */
    public static function getInstance() {
        if(self::$faceSqlSingleton == null) {
            self::$faceSqlSingleton = new FaceSql();
        }
        return self::$faceSqlSingleton;
    }

    public function updateFaceWithPersonId($faceId, $personId) {

        if(!empty($personId)){
            $exist = $this->getConn()->table('persons')
                ->where('persons_id', '=', $personId)
                ->exists();

            if(!$exist) {
                throw new BadRequestException("Invalid person id provided.", 'invalid_person_id', []);
            }

            $updateArray['confident_rate'] = 1;
        }

        //if $personId is empty, it's trying to wipe out the person id for this face, however it will
        //leave the confident rate to be there, so it could differentiate the face who had never be identified(null confident rate)
        //before vs it had been identified with wrong person id(empty person id with some confident rate).

        $updateArray['persons_id'] = $personId;

        $this->getConn()->table('faces')
            ->where('faces_id', '=', $faceId)
            ->update($updateArray);
    }

    public function getFace($faceId) {
        $face = (array)$this->getConn()->table('faces as f')
            ->leftJoin('persons as p', 'p.persons_id', '=', 'f.persons_id')
            ->where('f.faces_id', '=', $faceId)
            ->first([
                'p.age as person_age',
                'p.gender as person_gender',
                'f.persons_id',
                'f.confident_rate',
                'f.age',
                'f.gender',
                'f.cameras_id',
                'f.faces_id',
                'f.possible_returning_customers'
            ]);
        if(empty($face)) {
            throw new NonExistingException("Can not find face.", 'face_not_found');
        }
        $json = json_decode($face['possible_returning_customers']);
        if(is_object($json)) {
            $face['possible_returning_customers'] = $json->possible_returning_customers;
        }
        return $face;

    }

    public function createFaceFromInputArray(array $input) {

        $id = $this->getConn()->table('faces')
            ->insertGetId($input);

        $face = $this->getConn()->table('faces')
            ->where(array('idFaces' => $id))
            ->first();

        return (array)$face;
    }

}