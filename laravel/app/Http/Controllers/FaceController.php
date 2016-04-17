<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/5/16
 * Time: 8:38 PM
 */

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Http\Models\FaceSql;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FaceController extends Controller
{

    /**
     *
     * @param  Request  $request
     * @param  string  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $personId = $request->input('persons_id');
            FaceSql::getInstance()->updateFaceWithPersonId($id, $personId);
            return self::buildSuccessResponse();
        }catch (BadRequestException $e){
            $content = array(
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);

        }catch (\Exception $e) {
            $content = array(
                'status' => self::GENERAL_BAD_RESPONSE_MESSAGE,
                'message' => $e->getMessage(),
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
            $content = FaceSql::getInstance()->getFace($id);
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

    /**
     * @param Request $request
     * @return Response
     */
    public function store(Request $request) {
        $input = $request->input();
        try{
            $face = FaceSql::getInstance()->createFaceFromInputArray($input);
            return self::buildResponse($face, self::SUCCESS_CODE);

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