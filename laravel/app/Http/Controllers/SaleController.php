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
     * Create sales account
     * @param Request $request
     * @return Response
     */
    public function store(Request $request) {
        $input = $request->input();
        try{
            $person = PersonSql::getInstance()->createSaleInputArray($input);
            return self::buildResponse($person, self::SUCCESS_CODE);

        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }
}