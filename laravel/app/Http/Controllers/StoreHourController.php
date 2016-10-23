<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 9/18/16
 * Time: 4:44 PM
 */

namespace App\Http\Controllers;

use App\Http\Models\StoreSql;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class StoreHourController extends Controller
{

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function store(Request $request, $id)
    {
        $input = $request->all();
        try{
            StoreSql::getInstance()->addHours($id, $input);
            return self::buildSuccessResponse();
        }catch (\Exception $e) {
            $content = array(
                'status' => self::GENERAL_BAD_RESPONSE_MESSAGE,
                'message' => $e->getMessage(),
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

    /**
     * @param $id
     * @return Response
     */
    public function get($id)
    {
        try{
            $hours = StoreSql::getInstance()->getStoreHours($id);
            return self::buildResponse(['hours' => $hours], self::SUCCESS_CODE);
        }catch (\Exception $e) {
            $content = array(
                'status' => self::GENERAL_BAD_RESPONSE_MESSAGE,
                'message' => $e->getMessage(),
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }
}