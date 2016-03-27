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

    public function updateRtsp(Request $request, $id){
        $input = $request->input();
        try{
            $camera = StoreSql::getInstance()->updateCamera($input, $id);
            $content ['camera'] = $camera;
            return self::buildResponse($content, self::SUCCESS_CODE);
        }catch (\Exception $e) {
            $content = array(
                'message' => $e->getMessage(),
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }
    /**
     *
     * @param  Request  $request
     * @param  string  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->input();
        try{
            $store = StoreSql::getInstance()->updateStore($input, $id);
            $content ['store'] = $store;
            return self::buildResponse($content, self::SUCCESS_CODE);
        }catch (\Exception $e) {
            $content = array(
                'message' => $e->getMessage(),
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

    public function login(Request $request) {

        $content = array(
            'store' => array(
                'stores_id' => 2
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

    /**
     * Get stores person
     * @param $id
     * @return Response
     */
    public function persons($id) {
        try{
            $persons = StoreSql::getInstance()->getPersonsForStore($id);
            $content ['persons'] = $persons;
            return self::buildResponse($content, self::SUCCESS_CODE);
        }catch (\Exception $e) {
            $content = array(
                'message' => $e->getMessage(),
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

    public function visitsAndTrans($id) {
        $content = [
            'count' => [
                'female' => 100,
                'male' => 20
            ],
            'gender' => [
                '10' => 0,
                '20' => 50,
                '30' => 30,
                '40' => 25,
                '50' => 21,
                '60' => 10,
                '60 +' => 5
            ],
            'visit' => [
                'year_breakdown' => [
                    'Jan' => 10,
                    'Feb' => 20,
                    'Mar' => 30,
                    'Apr' => 20,
                    'May' => 36,
                    'Jun' => 22,
                    'Jul' => 19,
                    'Aug' => 22,
                    'Sep' => 30,
                    'Oct' => 36,
                    'Nov' => 26,
                    'Dec' => 20
                ],
                'quarter_breakdown' => [
                    70, 80, 90, 100
                ],
                'week_breakdown' => [
                    'Mon' => 5,
                    'Tue' => 8,
                    'Wed' => 12,
                    'Tur' => 16,
                    'Fri' => 8,
                    'Sat' => 9,
                    'Sun' => 14
                ],
                'day_breakdown' => [
                    0,0,0,0,0,0,0,0,4,5,6,4,5,6,4,5,6,5,4,6,7,4,0,0
                ]
            ],
            'trans' => [
                'year_breakdown' => [
                    'Jan' => 1000,
                    'Feb' => 2000,
                    'Mar' => 3000,
                    'Apr' => 2000,
                    'May' => 3600,
                    'Jun' => 2200,
                    'Jul' => 1900,
                    'Aug' => 2200,
                    'Sep' => 3000,
                    'Oct' => 3600,
                    'Nov' => 2600,
                    'Dec' => 2000
                ],
                'quarter_breakdown' => [
                    7000, 8000, 9000, 10000
                ],
                'week_breakdown' => [
                    'Mon' => 5,
                    'Tue' => 8,
                    'Wed' => 12,
                    'Tur' => 16,
                    'Fri' => 8,
                    'Sat' => 9,
                    'Sun' => 14
                ],
                'day_breakdown' => [
                    0,0,0,0,0,0,0,0,400,500,600,400,500,600,400,500,600,500,400,600,700,400,0,0
                ]
            ]


        ];

        return self::buildResponse($content, self::SUCCESS_CODE);

    }

    /**
     * Load faces for store
     * @param $id
     * @return Response
     */
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