<?php

namespace App\Http\Middleware;

use App\Http\Models\StoreSql;
use Closure;

class AccessStore
{

    //Testing purposes
    protected $permanentTokens = ['5582a62985bb0b056875b0991db9350f'];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        $valid = StoreSql::getInstance()->isTokenValid($token);
        
        if(!in_array($token, $this->permanentTokens) && !$valid) {
            return response(['status' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
