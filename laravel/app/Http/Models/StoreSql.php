<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/8/16
 * Time: 10:18 PM
 */

namespace App\Http\Models;


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
            $store['store_id'] = $info->stores_id;
            if($info->cameras_id != null) {
                $store['cameras'][] = array(
                    'camera_id' => $info->cameras_id,
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
                      m.name,
                      m.timestamp
                from faces f
                join persons p on (f.persons_id = p.persons_id)
                left join members m on (m.persons_id = p.persons_id)
                left join transactions t on (t.faces_id = f.faces_id)
                where f.faces_id in ("'.implode('","', $faces).'")
                group by p.persons_id'));

        $personsRe = [];

        foreach($persons as $person) {
            $personsRe[] = array(
                'person_id' => $person->persons_id,
                'name' => $person->name,
                'visit_count' => $person->visit_count,
                'total_purchase' => $person->total_amount,
                'time_registered' => $person->timestamp
            );
        }

        return $personsRe;
    }

    //@todo use correct time range when this goes live
    //right now it's entire time range
    public function loadFaces($storeId) {

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
            join transactions t on (f.faces_id = t.faces_id)
            where DATE(f.timestamp) < CURRENT_DATE
                  and s.stores_id = '.$storeId
        ));

        //@todo change to f.timestamp >= DATE_SUB(NOW(),INTERVAL 1 HOUR)
        $faceData = \DB::select(\DB::raw(
            'select
                i.img_path,
                f.faces_id,
                f.cameras_id,
                f.persons_id,
                f.possible_returning_customers
            from faces f
            join cameras c on (f.cameras_id = c.cameras_id )
            join stores s on (c.stores_id = s.stores_id)
            join images i on (i.faces_id = f.faces_id)
            where f.timestamp < DATE_SUB(NOW(),INTERVAL 1 MINUTE)
                and i.is_display = "Y"
                and s.stores_id = '.$storeId
        ));

        $store = array(
            'visit_count' => $storeData[0]->face_count,
            'female' => $storeData[0]->female,
            'male' => $storeData[0]->male,
            'sales_total' => $storeData[0]->total_tran
        );

        return array(
            'store' => $store,
            'faces' => $faceData
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
        $input['stores_id'] = $storeId;
        $this->getConn()->table('cameras')
            ->insert($input);
    }
}