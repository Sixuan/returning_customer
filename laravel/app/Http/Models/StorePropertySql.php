<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 10/23/16
 * Time: 1:51 PM
 */

namespace App\Http\Models;

use App\Exceptions\NonExistingException;

class StorePropertySql extends BaseModelSql
{
    /**
     * @var StorePropertySql
     */
    private static $storePropertySqlSingleton;

    /**
     * @return StorePropertySql
     */
    public static function getInstance() {
        if(self::$storePropertySqlSingleton == null) {
            self::$storePropertySqlSingleton = new StorePropertySql();
        }
        return self::$storePropertySqlSingleton;
    }

    /**
     * @param array $input
     * @param $storeId
     * @return mixed|static
     * @throws NonExistingException
     */
    public function storeOrUpdateDailySales(array $input, $storeId)
    {
        $conn = $this->getConn();
        $exist = $conn->table('stores')
            ->where('stores_id', '=', $storeId)
            ->exists();
        
        if(!$exist) {
            throw new NonExistingException('store not existing id: '.$storeId);
        }
        
        $date = $input['date_of_sale'];
        $exist = $conn->table('store_daily_sales')
            ->where('stores_id', '=', $storeId)
            ->where('date_of_sale', '=', $date)
            ->exists();
        
        if($exist) {
            $updateArray = [];
            if (isset($input['num_of_sales'])) {
                $updateArray['num_of_sales'] = $input['num_of_sales'];
            }

            if (isset($input['total_sales_amount'])) {
                $updateArray['total_sales_amount'] = $input['total_sales_amount'];
            }
            
            if($updateArray) {
                $conn->table('store_daily_sales')
                    ->where('stores_id', '=', $storeId)
                    ->where('date_of_sale', '=', $date)
                    ->update($updateArray);
            }
        } else {
            $insertArray = [
                'num_of_sales' => $input['num_of_sales'],
                'date_of_sale' => $input['date_of_sale'],
                'total_sales_amount' => $input['total_sales_amount'],
                'stores_id' => $storeId
            ];
            
            $conn->table('store_daily_sales')->insert($insertArray);
        }

        return $conn->table('store_daily_sales')
            ->where('stores_id', '=', $storeId)
            ->where('date_of_sale', '=', $date)
            ->first();
    }

    /**
     * @param $input
     * @param $storeId
     * @return array|mixed|null|static|static[]
     * @throws NonExistingException
     */
    public function getStoreDailySale($input, $storeId)
    {
        $conn = $this->getConn();
        $exist = $conn->table('stores')
            ->where('stores_id', '=', $storeId)
            ->exists();

        if(!$exist) {
            throw new NonExistingException('store not existing id: '.$storeId);
        }
        
        if(isset($input['date_of_sale'])) {
            return $conn->table('store_daily_sales')
                ->where('stores_id', '=', $storeId)
                ->where('date_of_sale', '=', $input['date_of_sale'])
                ->first();
        }
        
        if(isset($input['date_of_sale_start'])) {
            $build = $conn->table('store_daily_sales')
                ->where('stores_id', '=', $storeId)
                ->where('date_of_sale', '>=', $input['date_of_sale_start']);
            
            if(isset($input['date_of_sale_end'])) {
                $build->where('date_of_sale', '<=', $input['date_of_sale_end']);
            }
            
            return $build->get();
        }
        
        return null;
    }

    /**
     * @param array $input
     * @param $storeId
     * @return mixed|static
     * @throws NonExistingException
     */
    public function storeOrUpdateStorePromotion(array $input, $storeId)
    {
        $conn = $this->getConn();
        $exist = $conn->table('stores')
            ->where('stores_id', '=', $storeId)
            ->exists();

        if(!$exist) {
            throw new NonExistingException('store not existing id: '.$storeId);
        }

        $date = $input['date'];
        $exist = $conn->table('store_promotions')
            ->where('stores_id', '=', $storeId)
            ->where('date', '=', $date)
            ->exists();

        if($exist) {
            $updateArray = [];
            if (isset($input['is_promotion'])) {
                $updateArray['is_promotion'] = $input['is_promotion'];
            }

            if($updateArray) {
                $conn->table('store_promotions')
                    ->where('stores_id', '=', $storeId)
                    ->where('date', '=', $date)
                    ->update($updateArray);
            }
        } else {
            $insertArray = [
                'is_promotion' => $input['is_promotion'],
                'date' => $input['date'],
                'stores_id' => $storeId
            ];

            $conn->table('store_promotions')->insert($insertArray);
        }

        return $conn->table('store_promotions')
            ->where('stores_id', '=', $storeId)
            ->where('date', '=', $date)
            ->first();

    }

    /**
     * @param $input
     * @param $storeId
     * @return array|mixed|null|static|static[]
     * @throws NonExistingException
     */
    public function getStorePromotion($input, $storeId)
    {
        $conn = $this->getConn();
        $exist = $conn->table('stores')
            ->where('stores_id', '=', $storeId)
            ->exists();

        if(!$exist) {
            throw new NonExistingException('store not existing id: '.$storeId);
        }

        if(isset($input['date'])) {
            return $conn->table('store_promotions')
                ->where('stores_id', '=', $storeId)
                ->where('date', '=', $input['date'])
                ->first();
        }

        if(isset($input['date_start'])) {
            $build = $conn->table('store_promotions')
                ->where('stores_id', '=', $storeId)
                ->where('date', '>=', $input['date_start']);

            if(isset($input['date_end'])) {
                $build->where('date', '<=', $input['date_end']);
            }

            return $build->get();
        }

        return null;
    }
}