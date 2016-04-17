<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 4/3/16
 * Time: 5:00 PM
 */

namespace App\Http\Controllers;


use App\Exceptions\AuthException;
use App\Exceptions\BadRequestException;
use App\Http\Models\PersonSql;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Validator;

class AccountController extends Controller
{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }


    /**
     * @param Request $request
     * @param $storeId
     * @return Response
     * @throws \App\Exceptions\BadRequestException
     */
    public function create(Request $request, $storeId)
    {

        try{
            $account = PersonSql::getInstance()->createSaleInputArray($storeId, $request->input());

            return self::buildResponse(['sale' => $account], self::SUCCESS_CODE);

        }catch (BadRequestException $e){
            $content = array(
                'status' => $e->getStatusCode(),
                'error' => $e->getMessage()
            );
            return self::buildResponse($content, self::BAD_REQUEST);

        }catch (\Exception $e) {
            $content = array(
                'status' => self::GENERAL_BAD_RESPONSE_MESSAGE,
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }
    }

    public function login(Request $request) {
        try {
            $account = PersonSql::getInstance()->authenticateSale($request->input());
            return self::buildResponse($account, self::SUCCESS_CODE);

        }catch (AuthException $e){
            $content = array(
                'status' => 'bad_auth',
                'message' => $e->getMessage(),
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_AUTH);

        }catch (BadRequestException $e){
            $content = array(
                'status' => $e->getStatusCode(),
                'error' => $e->getMessage()
            );
            return self::buildResponse($content, self::BAD_REQUEST);

        }catch (\Exception $e) {
            $content = array(
                'status' => self::GENERAL_BAD_RESPONSE_MESSAGE,
                'error' => (string)$e
            );
            return self::buildResponse($content, self::BAD_REQUEST);
        }

    }
}