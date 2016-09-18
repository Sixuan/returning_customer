<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 9/18/16
 * Time: 2:13 PM
 */

namespace App\Http\Models;

use App\Exceptions\NonExistingException;

class TopViewSql extends BaseModelSql
{

    const POSITION_TOP_VIEW = 'ENTRANCE';
    const POSITION_FRONT_VIEW = 'CHECKOUT';


    /**
     * @var TopViewSql
     */
    private static $topViewSqlSingleton;

    /**
     * @return TopViewSql
     */
    public static function getInstance() {
        if(self::$topViewSqlSingleton == null) {
            self::$topViewSqlSingleton = new TopViewSql();
        }
        return self::$topViewSqlSingleton;
    }

    public function getVisitsForStoreOnDate($storeId, \DateTime $dateTime)
    {
        $camera_id = $this->getTopViewCameraIdByStoreId($storeId);
        
        if (empty($camera_id)) {
           throw new NonExistingException("Top view cameras is not setup for this store: ".$storeId); 
        }

        \Log::info("Get visits for camera: ".$camera_id." date: ".$dateTime->format('Y-m-d'));
        $query = "SELECT sum(if(direction='enter', 1, 0)) as in_count, sum(if(direction='exit', 1, 0)) as out_count, CONCAT(CAST(HOUR(timestamp_in) AS CHAR(2)), ':', (CASE WHEN MINUTE(timestamp_in) < 30 THEN '00' ELSE '30' END)) AS hour
        FROM top_view_entries WHERE date(timestamp_in) = '".$dateTime->format('Y-m-d')."' 
        and cameras_id = ".(int)$camera_id." GROUP BY CONCAT(CAST(HOUR(timestamp_in) AS CHAR(2)), ':', (CASE WHEN MINUTE(timestamp_in) < 30 THEN '00' ELSE '30' END))
        ORDER BY hour";

        $visits = \DB::select(\DB::raw($query));
        
        return $visits;
    }
    
    public function getTopViewCameraIdByStoreId($storeId)
    {
        return $this->getConn()->table('cameras')
            ->where('stores_id', '=', $storeId)
            ->where('position', '=', self::POSITION_TOP_VIEW)
            ->value('cameras_id');
    }
}