<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Products {

    public function handle(Request $request, Closure $next): Response {

        $user = Auth::guard('sanctum')->user();

        if ( $user->allow_products ) return $next($request);

        return response(['errors' => ['permission' => 'access denied']]);

    }

}