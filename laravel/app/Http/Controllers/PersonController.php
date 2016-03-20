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
     * we do not need to update person, should use update member
     * @param  Request  $request
     * @deprecated
     * @param  string  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->input();
        try{
            $person = PersonSql::getInstance()->createOrUpdatePerson($input, $id);
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

    public function getMulti($ids) {
        try{
            $content = PersonSql::getInstance()->getPersons($ids);
            return self::buildResponse(['persons' => $content], self::SUCCESS_CODE);
        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }

    }

    /**
     * Create person without info
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