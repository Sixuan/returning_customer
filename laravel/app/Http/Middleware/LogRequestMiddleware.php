<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 5/2/16
 * Time: 11:14 PM
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogRequestMiddleware implements TerminableInterface
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response)
    {
        Log::info('app.requests', ['request' => $request->all(), 'response' => $response]);
    }
}