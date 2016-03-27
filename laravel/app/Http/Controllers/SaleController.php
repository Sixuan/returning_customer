<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/13/16
 * Time: 2:44 PM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Models\PersonSql;

class SaleController extends Controller
{


    /**
     * Load store sales
     * @param $storeId
     * @return Response
     */
    public function sales($storeId) {
        try{
            $sales = PersonSql::getInstance()->getStoreSales($storeId);
            return self::buildResponse(['sales' => $sales], self::SUCCESS_CODE);

        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

    /**
     * @param $storeId
     * @param Request $request
     * @return Response
     */
    public function store($storeId, Request $request) {
        /**
         * {
        "name" : "L B",
        "phone" : "1232222222",
        "age" : 20,
        "email" : "123@d.com",
        "address" : "九龙"
        }

         */
        $input = $request->input();
        try{
            $sale = PersonSql::getInstance()->createSaleInputArray($storeId, $input);
            return self::buildResponse(['sale' => $sale], self::SUCCESS_CODE);

        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

    public function destroy($saleId) {
        try{
            PersonSql::getInstance()->deleteSale($saleId);
            return self::buildSuccessResponse();

        }catch (\Exception $e) {
            $content = array(
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
            $sale = PersonSql::getInstance()->updateSaleInfo($input, $id);
            return self::buildResponse(['sale' => $sale], self::SUCCESS_CODE);

        }catch (\Exception $e) {
            $content = array(
                'message' => $e->getMessage(),
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }
}