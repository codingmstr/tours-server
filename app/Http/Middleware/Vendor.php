<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Vendor {

    public function handle(Request $request, Closure $next): Response {

        $user = Auth::guard('sanctum')->user();

        if ( $user->role == 2 && $user->allow_login && $user->active ) return $next($request);

        return response(['errors' => ['permission' => 'access denied']]);

    }

}
