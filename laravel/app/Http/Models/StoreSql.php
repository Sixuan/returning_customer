<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/8/16
 * Time: 10:18 PM
 */

namespace App\Http\Models;


use App\Exceptions\ApiInputException;
use App\Exceptions\AuthException;

class StoreSql extends BaseModelSql
{

    /**
     * @var StoreSql
     */
    private static $storeSqlSingleton;

    /**
     * @return StoreSql
     */
    public static function getInstance() {
        if(self::$storeSqlSingleton == null) {
            self::$storeSqlSingleton = new StoreSql();
        }
        return self::$storeSqlSingleton;
    }

    public function getFaces($storeId, $offset, $limit, \DateTime $startTime, \DateTime $endTime)
    {
        $faces = $this->getConn()->table('faces as f')
            ->join('cameras as c', 'f.cameras_id', '=', 'c.cameras_id')
            ->join('stores as s', 's.stores_id', '=', 'c.stores_id')
            ->join('images as i', 'i.faces_id', '=', 'f.faces_id')
            ->where('f.timestamp', '>=', $startTime->format('Y-m-d H:i:s'))
            ->where('f.timestamp', '<=', $endTime->format('Y-m-d H:i:s'))
            ->where('s.stores_id', '=', $storeId)
            ->offset($offset)
            ->limit($limit)
            ->get(['f.idFaces', 'i.img_path', 'i.images_id', 'f.timestamp', 's.stores_id', 'f.cameras_id']);
        
        $res = [];
        foreach ($faces as $face) {
            $res[$face->idFaces]['idFaces'] = $face->idFaces;
            $res[$face->idFaces]['cameras_id'] = $face->cameras_id;
            $res[$face->idFaces]['stores_id'] = $face->stores_id;
            $res[$face->idFaces]['timestamp'] = $face->timestamp;
            $res[$face->idFaces]['images'][] = [
                'images_id' => $face->images_id,
                'img_path' => $face->img_path
            ];
        }

        return array_values($res);
    }

    public function getTotalFaces($storeId, \DateTime $startTime, \DateTime $endTime)
    {
        return (int)$this->getConn()->table('faces as f')
            ->join('cameras as c', 'f.cameras_id', '=', 'c.cameras_id')
            ->join('stores as s', 's.stores_id', '=', 'c.stores_id')
            ->where('f.timestamp', '>=', $startTime->format('Y-m-d H:i:s'))
            ->where('f.timestamp', '<=', $endTime->format('Y-m-d H:i:s'))
            ->where('s.stores_id', '=', $storeId)
            ->count();
    }
    
    /**
     * @param $token
     * @return mixed
     * @throws AuthException
     */
    public function retrieveStoreByToken($token) {
        $token = $this->getConn()->table('sales as s')
            ->join('stores as st', 's.stores_id', '=', 'st.stores_id')
            ->where('s.remember_token', '=', $token)
            ->where('s.updated_at', '>', \DB::raw("DATE(NOW() - INTERVAL 2 DAY)"))
            ->pluck('s.stores_id');
        if(isset($token[0])) {
            return $token[0];
        }else{
            throw new AuthException("Token expired or invalid.", "token_invalid_or_expired");
        }
    }

    public function isTokenValid($token, $manager = 'N') {
        $check = $this->getConn()->table('sales')
            ->where('remember_token', '=', $token)
            ->where('updated_at', '>', \DB::raw("DATE(NOW() - INTERVAL 2 DAY)"));
        
        if($manager == 'Y') {
            $check->where('manager', '=', 'Y');
        }
        
        return $check->exists();
    }

    public function updateStore(array $input, $id) {
        $exist = $this->getConn()->table('stores')
            ->where('stores_id', '=', $id);
    }

    public function updateCamera(array $input, $cameraId) {
        if(!isset($input['rtsp_url'])) {
            throw new ApiInputException("rtsp_url is missing from input");
        }

        $this->getConn()->table('cameras')
            ->where('cameras_id', '=', $cameraId)
            ->update([
                'rtsp_url' => $input['rtsp_url']
            ]);

        return (array)$this->getConn()->table('cameras')
            ->where('cameras_id','=', $cameraId)
            ->first();

    }

    public function getAllStores() {

        $infos = $this->getConn()->table('stores as s')
            ->leftJoin('cameras as c', 's.stores_id', '=', 'c.stores_id')
            ->where('s.active', '=', 'Y')
            ->get(['s.stores_id', 's.name', 'c.position', 'c.cameras_id', 'c.rtsp_url']);

        $stores = [];
        foreach($infos as $info) {
            $store = [];
            if(isset($stores[$info->stores_id])) {
                if($info->cameras_id != null) {
                    $stores[$info->stores_id]['cameras'][] = array(
                        'cameras_id' => $info->cameras_id,
                        'rtsp_url' => $info->rtsp_url,
                        'position' => $info->position
                    );
                }

            } else {
                $store['name'] = $info->name;
                $store['stores_id'] = $info->stores_id;
                if($info->cameras_id != null) {
                    $store['cameras'][] = array(
                        'cameras_id' => $info->cameras_id,
                        'rtsp_url' => $info->rtsp_url,
                        'position' => $info->position
                    );
                }

                $stores[$info->stores_id] = $store;
            }
        }

        return array_values($stores);
    }

    public function resetStore($storeId)
    {
        \DB::delete("delete faces from faces join cameras on (faces.cameras_id = cameras.cameras_id) 
join stores on (stores.stores_id = cameras.stores_id) where stores.stores_id = ".$storeId);
    }

    /**
     * @param $storeId
     * @return array
     */
    public function getStore($storeId) {

        $infos = $this->getConn()->table('stores as s')
            ->leftJoin('cameras as c', 's.stores_id', '=', 'c.stores_id')
            ->where('s.stores_id', '=', $storeId)
            ->get(['s.stores_id', 's.name', 'c.position', 'c.cameras_id', 'c.rtsp_url']);

        $store = [];

        foreach($infos as $info) {
            $store['name'] = $info->name;
            $store['stores_id'] = $info->stores_id;
            if($info->cameras_id != null) {
                $store['cameras'][] = array(
                    'cameras_id' => $info->cameras_id,
                    'rtsp_url' => $info->rtsp_url,
                    'position' => $info->position
                );
            }
        }

        return $store;
    }

    private function getFacesForStore($storeId) {
        $faces = $this->getConn()->table('faces as f')
            ->join('cameras as c', 'f.cameras_id', '=', 'c.cameras_id')
            ->join('stores as s', 's.stores_id', '=', 'c.stores_id')
            ->where('s.stores_id', '=', $storeId)
            ->pluck('f.faces_id');

        return $faces;
    }

    private function getFacesPastHour($storeId) {
        $faces = $this->getConn()->table('faces as f')
            ->join('cameras as c', 'f.cameras_id', '=', 'c.cameras_id')
            ->join('stores as s', 's.stores_id', '=', 'c.stores_id')
            ->where('s.stores_id', '=', $storeId)
            ->where(\DB::raw('f.timestamp >= DATE_SUB(NOW(),INTERVAL 1 HOUR)'))
            ->get(['f.faces_id']);

        return (array)$faces;
    }

    public function getPersonsForStore($storeId) {
        $faces = $this->getFacesForStore($storeId);
        $persons = \DB::select(\DB::raw('select
                      count(DISTINCT(f.faces_id)) as visit_count,
                      sum(t.amount) as total_amount,
                      p.persons_id,
                      p.name,
                      i.img_path,
                      p.vip,
                      p.timestamp
                from faces f
                join images i on (i.faces_id = f.faces_id)
                join persons p on (f.persons_id = p.persons_id)
                left join transactions t on (t.faces_id = f.faces_id)
                where f.faces_id in ("'.implode('","', $faces).'")
                and p.algo_added = "N"
                group by p.persons_id'));

        $personsRe = [];

        foreach($persons as $person) {
            $personsRe[] = array(
                'persons_id' => $person->persons_id,
                'name' => $person->name,
                'vip' => $person->vip,
                'visit_count' => $person->visit_count,
                'total_purchase' => $person->total_amount,
                'time_registered' => $person->timestamp,
                'img_path' => $person->img_path
            );
        }

        return $personsRe;
    }

    //@todo use correct time range when this goes live
    //right now it's entire time range
    public function loadFaces($storeId, $hour = 1) {

        //@todo change to DATE(f.timestamp) = CURRENT_DATE
        $storeData = \DB::select(\DB::raw(
            'select
                count(1) as face_count,
                SUM(IF(gender = \'F\', 1, 0)) as female,
                SUM(IF(gender = \'M\', 1, 0)) as male,
                SUM(t.amount) as total_tran
            from faces f
            join cameras c on (f.cameras_id = c.cameras_id )
            join stores s on (c.stores_id = s.stores_id)
            left join transactions t on (f.faces_id = t.faces_id)
            where DATE(f.timestamp) = CURRENT_DATE
                  and s.stores_id = '.$storeId
        ));

        //@todo change to f.timestamp >= DATE_SUB(NOW(),INTERVAL 1 HOUR)
        $faceData = \DB::select(\DB::raw(
            'select
                i.img_path,
                f.faces_id,
                f.cameras_id,
                f.persons_id,
                f.timestamp,
                p.name,
                f.possible_returning_customers
            from faces f
            left join persons p on (f.persons_id = p.persons_id)
            join cameras c on (f.cameras_id = c.cameras_id )
            join stores s on (c.stores_id = s.stores_id)
            join images i on (i.faces_id = f.faces_id)
            where f.timestamp >= DATE_SUB(NOW(),INTERVAL '.$hour.' HOUR)
                and i.is_display = "Y"
                and s.stores_id = '.$storeId." group by f.faces_id order by f.timestamp desc"
        ));

        $facesRe = array();
        foreach($faceData as $face) {
            $face->possible_returning_customers = json_decode($face->possible_returning_customers);
            $facesRe[] = $face;
        }

        $store = array(
            'visit_count' => $storeData[0]->face_count,
            'female' => $storeData[0]->female,
            'male' => $storeData[0]->male,
            'sales_total' => $storeData[0]->total_tran
        );

        return array(
            'store' => $store,
            'faces' => $facesRe
        );
    }

    public function createStoreFromInputArray(array $input) {
        $id = $this->getConn()->table('stores')
            ->insertGetId($input);
        $store = $this->getConn()->table('stores')
            ->where(array('stores_id' => $id))
            ->first();

        return (array)$store;
    }

    public function addCameraToStore(array $input, $storeId) {
        $position = $input['position'];
        $exist = $this->getConn()->table('cameras')
            ->where('position', '=', $position)
            ->where('stores_id', '=', $storeId)
            ->exists();

        if($exist) {
            $this->getConn()->table('cameras')
                ->where('position', '=', $position)
                ->where('stores_id', '=', $storeId)
                ->update($input);
        }else{
            $input['stores_id'] = $storeId;
            $this->getConn()->table('cameras')
                ->insert($input);
        }
    }
}