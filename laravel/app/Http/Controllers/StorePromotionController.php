<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 10/23/16
 * Time: 2:45 PM
 */

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Http\Models\StorePropertySql;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StorePromotionController extends Controller
{
    /**
     * Store store's promotion by date
     * @param Request $request
     * @param $storeId
     * @return Response
     */
    public function store(Request $request, $storeId) {
        try {
            $input = $request->input();

            if(!isset($input['is_promotion']) || !isset($input['date'])) {
                throw new BadRequestException("Missing input please check https://wikkit-labs.readme.io/v1.0/docs/adminstoresstore_idpromotion");
            }

            $promotion = StorePropertySql::getInstance()->storeOrUpdateStorePromotion($input, $storeId);
            return self::buildResponse((array)$promotion, self::SUCCESS_CODE);

        } catch (\Exception $e) {
            $content = array(
                'status' => self::GENERAL_BAD_RESPONSE_MESSAGE,
                'message' => $e->getMessage(),
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }


    public function get(Request $request, $storeId) {
        try {
            $input = $request->input();

            $promotions = StorePropertySql::getInstance()->getStorePromotion($input, $storeId);
            return self::buildResponse(['promotions' => $promotions], self::SUCCESS_CODE);

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