<?php

/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 4/17/16
 * Time: 3:09 PM
 */
namespace App\Http\Middleware\FaceRecognition;

use App\Http\Models\StoreSql;
use Closure;

class FaceApiClientAccess
{
    //Testing purposes
    protected $permanentClient = ['5582a62985bb0b056875b0991db9350f'];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        $token = $request->header('Authorization');
//        $valid = StoreSql::getInstance()->isTokenValid($token);

        return $next($request);

        if(!in_array($token, $this->permanentTokens) && !$valid) {
            return response(['status' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }

}