<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class webhookMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $response = new Response();

        $auth = $request->input('HTTP_AUTHORIZATION', "");

        if ($auth == "") {
            return response()->setStatusCode(400, "Not Authorised: Authorization Token not provided.");
        }
        else {

            $auth_vars = explode(" ", $auth);

            if ($auth_vars[0] != 'Bearer') {
                return response()->setStatusCode(400, "Not Authorised: Authorization header should be of the form 'Bearer token'");
            }
            else {

                $response->headers->set('x-hasura-role: user');
                $response->headers->set('x-hasura-user-id: 1' );

                return $response()->setStatusCode(200, 'Authorized');
            }

        }
        return $next($request);
    }
}
