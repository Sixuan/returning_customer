<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/8/16
 * Time: 10:31 PM
 */

namespace App\Http\Controllers;

use App\Http\Models\PersonSql;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class PersonController extends Controller
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


    /**
     *
     * @param  string  $id
     * @return Response
     */
    public function get($id)
    {
        try{
            $content = PersonSql::getInstance()->getPerson($id);
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
            $person = PersonSql::getInstance()->createPersonFromInputArray($input);
            return self::buildResponse($person, self::SUCCESS_CODE);

        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }


}