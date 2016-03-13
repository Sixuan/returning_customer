<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/13/16
 * Time: 2:28 PM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Models\PersonSql;

class MemberController extends Controller
{
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
            $person = PersonSql::getInstance()->createOrUpdateMemberForPerson($input, $id);
            $content = [
                'person' => $person
            ];
            return self::buildResponse($content, self::SUCCESS_CODE);

        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }


    /**
     * @todo not finished
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
     * @todo not finished
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