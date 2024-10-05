<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Clients {

    public function handle(Request $request, Closure $next): Response {

        $user = Auth::guard('sanctum')->user();

        if ( $user->allow_clients ) return $next($request);

        return response(['errors' => ['permission' => 'access denied']]);

    }

}
