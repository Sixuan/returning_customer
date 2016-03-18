<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/13/16
 * Time: 2:28 PM
 */

namespace App\Http\Controllers;

use App\Exceptions\MemberExistingException;
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
     * @param  string  $id
     * @return Response
     */
    public function get($id)
    {
        try{
            $member = PersonSql::getInstance()->getMember($id);
            return self::buildResponse(['member' => $member], self::SUCCESS_CODE);
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
            if(!isset($input['faces_id'])){
                $content['messages'][] = 'faces_id required';
                return self::buildResponse($content, self::BAD_REQUEST);
            }
            $member = PersonSql::getInstance()->createPersonMemberFromInputArray($input);
            $content = [
                'member' => $member
            ];
            return self::buildResponse($content, self::SUCCESS_CODE);

        }catch (MemberExistingException $e){
            $content = array(
                'status' => $e->getStatusCode(),
                'messages' => [$e->getMessage()]
            );
            return self::buildResponse($content, self::BAD_REQUEST);

        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

}