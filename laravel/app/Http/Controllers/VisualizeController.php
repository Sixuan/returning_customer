<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 10/23/16
 * Time: 3:26 PM
 */

namespace App\Http\Controllers;

use App\Http\Models\Gateways\ReturningCustomerDataVisualizeGateway;
use App\Http\Services\Socket\Exceptions\SocketException;

class VisualizeController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function test()
    {
        try{
            $gateway = ReturningCustomerDataVisualizeGateway::getInstance();
            $response = $gateway->test();
            $content = $response->getContent();
            return self::buildResponse($content, self::SUCCESS_CODE);

        }catch (SocketException $e) {
            $content = array(
                'status' => self::SOCKET_BAD_RESPONSE_MESSAGE,
                'message' => $e->getMessage(),
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }

    }
}