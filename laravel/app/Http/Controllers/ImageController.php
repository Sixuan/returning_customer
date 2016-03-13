<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/12/16
 * Time: 6:28 PM
 */

namespace App\Http\Controllers;

use App\Http\Models\ImageSql;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ImageController extends Controller
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
            $content = ImageSql::getInstance()->getImage($id);
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
            $image = ImageSql::getInstance()->createImageFromInputArray($input);
            return self::buildResponse($image, self::SUCCESS_CODE);

        }catch (\Exception $e) {
            $content = array(
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

}