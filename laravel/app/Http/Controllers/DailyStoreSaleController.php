<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 10/23/16
 * Time: 1:44 PM
 */

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Http\Models\StorePropertySql;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DailyStoreSaleController extends Controller
{

    /**
     * Store store's daily sale into store_daily_sales
     * @param Request $request
     * @param $storeId
     * @return Response
     */
    public function store(Request $request, $storeId) {
        try {
            $input = $request->input();

            if(!isset($input['date_of_sale']) || !isset($input['total_sales_amount']) || !isset($input['num_of_sales'])) {
                throw new BadRequestException("Missing input please check https://wikkit-labs.readme.io/v1.0/docs/adminstoresstore_iddaily_sales");
            }

            $sales = StorePropertySql::getInstance()->storeOrUpdateDailySales($input, $storeId);
            return self::buildResponse((array)$sales, self::SUCCESS_CODE);

        } catch (\Exception $e) {
            $content = array(
                'status' => self::GENERAL_BAD_RESPONSE_MESSAGE,
                'message' => $e->getMessage(),
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
    public function get(Request $request, $storeId) {
        try {
            $input = $request->input();
            $sales = StorePropertySql::getInstance()->getStoreDailySale($input, $storeId);
            return self::buildResponse(['sales' => $sales], self::SUCCESS_CODE);

        } catch (\Exception $e) {
            $content = array(
                'status' => self::GENERAL_BAD_RESPONSE_MESSAGE,
                'message' => $e->getMessage(),
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
    public function update(Request $request, $storeId)
    {
        try {
            $input = $request->input();
            $sales = StorePropertySql::getInstance()->storeOrUpdateDailySales($input, $storeId);
            return self::buildResponse((array)$sales, self::SUCCESS_CODE);

        } catch (\Exception $e) {
            $content = array(
                'status' => self::GENERAL_BAD_RESPONSE_MESSAGE,
                'message' => $e->getMessage(),
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
    public function destroy(Request $request, $storeId)
    {
        try {
            $input = $request->input();
            StorePropertySql::getInstance()->deleteDailySales($input, $storeId);
            return self::buildSuccessResponse();

        } catch (\Exception $e) {
            $content = array(
                'status' => self::GENERAL_BAD_RESPONSE_MESSAGE,
                'message' => $e->getMessage(),
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }
}