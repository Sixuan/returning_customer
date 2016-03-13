<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/12/16
 * Time: 7:16 PM
 */

namespace App\Http\Controllers;

use App\Http\Models\TransactionSql;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
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


    /***
     * @param  string  $id
     * @return Response
     */
    public function get($id)
    {
        try{
            $content = TransactionSql::getInstance()->getTransaction($id);
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
            $tran = TransactionSql::getInstance()->createTransactionFromInputArray($input);
            return self::buildResponse($tran, self::SUCCESS_CODE);

        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

}