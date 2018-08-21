<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

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

        $auth = $request->header("Authorization");

        if ($auth == "") {
            return response("Not Authorised: Authorization Token not provided.", 400);
        }
        else {

            $auth_vars = explode(" ", $auth);

            if ($auth_vars[0] != 'Bearer') {
                return response("Not Authorised: Authorization header should be of the form 'Bearer token'", 400);
            }
            else {

                $oldId = session()->getId();

                $id = $auth_vars[1];

                // if ($id !== $oldId) {
                    $session = session()->driver();
                    $session->setId($id);
                    $session->start();
                    //}

                $newId = session()->getId();


                $isAuthenticated = Auth::check();

                if ($isAuthenticated) {
                    return response()->json(['x-hasura-role' => 'user', 'x-hasura-user-id' => '1']);
                    // return response("Status : $isAuthenticated, Auth before: $oldId, Auth after: $newId ", 200);
                }
                else {
                    return response("Authentication Failed\n Session before: $oldId\n Session after: $newId \n Token: $id", 400);
                }

                return $next($request);

                // if ($isAuthenticated) {
                //     return response()->json(['x-hasura-role' => 'user', 'x-hasura-user-id' => '1']);
                // }
                // else {
                //     return response("Not Authorised: Authorization response is $isAuthenticated", 400);
                // }

            }

        }
        return $next($request);
    }
}
