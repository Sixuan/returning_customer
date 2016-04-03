<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    const SUCCESS_CODE = 200;
    const BAD_REQUEST = 400;
    const BAD_AUTH = 401;
    const GENERAL_BAD_RESPONSE_MESSAGE = 'general_error';
    const BAD_RESQUEST_RESPONSE_MESSAGE = 'bad_request';

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function buildResponse($content, $httpCode) {
        return new Response($content, $httpCode);
    }

    public static function buildSuccessResponse() {
        $content = [
            'code' => self::SUCCESS_CODE,
            'status' => 'request_success',
            'message' => []
        ];
        return new Response($content, self::SUCCESS_CODE);
    }

    public static function buildBadResponse() {
        $content = [
            'code' => self::BAD_REQUEST,
            'status' => 'request_failed',
            'message' => []
        ];
        return new Response($content, self::BAD_REQUEST);

    }
}