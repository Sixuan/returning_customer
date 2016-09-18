<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 9/18/16
 * Time: 2:26 PM
 */

namespace App\Http\Controllers;

use App\Http\Models\TopViewSql;
use Illuminate\Http\Request;

class TopViewController extends Controller
{

    public function index(Request $request, $storeId) {
        try{
            $date = new \DateTime($request->get('date', 'now'));
            $visits = TopViewSql::getInstance()->getVisitsForStoreOnDate($storeId, $date);
            $content['date'] = $date->format('Y-m-d');
            $content ['visits'] = $visits;
            return self::buildResponse($content, self::SUCCESS_CODE);
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