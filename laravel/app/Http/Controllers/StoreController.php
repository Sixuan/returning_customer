<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/8/16
 * Time: 10:29 PM
 */

namespace App\Http\Controllers;

use App\Http\Models\StoreSql;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class StoreController extends Controller
{
    /**
     *
     * @param  Request  $request
     * @param  string  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        echo "update".$id;
        var_dump($request->input());
    }

    public function login(Request $request) {

        $content = array(
            'store' => array(
                'store_id' => 2
            ),
            'sales' => array(
                'sales_id' => 2,
                'name' => 'Jim'
            )
        );

        return self::buildResponse($content, self::SUCCESS_CODE);
        //@todo
        try{
            $store = StoreSql::getInstance()->loginAndGetStore($input);
            return self::buildResponse($store, self::SUCCESS_CODE);

        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }

    }

    public function persons($id) {
        try{
            $persons = StoreSql::getInstance()->getPersonsForStore($id);
            $content ['persons'] = $persons;
            return self::buildResponse($content, self::SUCCESS_CODE);
        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

    public function sales($id) {
        $sales[] = [
            'sales_id' => 1,
            'name' => 'Sixuan Liu',
            'login_username' => 'sliu'
        ];

        $sales[] = [
            'sales_id' => 2,
            'name' => 'Heo Neu',
            'login_username' => 'hneu'
        ];
        $content = [
            'sales' => $sales
        ];
        return self::buildResponse($content, self::SUCCESS_CODE);
    }

    public function load($id) {
        try{
            $content = StoreSql::getInstance()->loadFaces($id);
            return self::buildResponse($content, self::SUCCESS_CODE);
        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }


    /**
     * Get store info
     *
     * @param  string  $id
     * @return Response
     */
    public function get($id)
    {
        try{
            $content = StoreSql::getInstance()->getStore($id);
            return self::buildResponse($content, self::SUCCESS_CODE);
        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function store(Request $request) {
        $input = $request->input();
        try{
            $store = StoreSql::getInstance()->createStoreFromInputArray($input);
            return self::buildResponse($store, self::SUCCESS_CODE);

        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param $storeId
     * @return Response
     */
    public function addCamera(Request $request, $storeId)
    {
        $input = $request->input();
        StoreSql::getInstance()->addCameraToStore($input, $storeId);

        return self::buildSuccessResponse();

    }

}