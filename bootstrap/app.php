<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['api', 'auth:sanctum']],
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'client' => \App\Http\Middleware\Client::class,
            'vendor' => \App\Http\Middleware\Vendor::class,
            'admin' => \App\Http\Middleware\Admin::class,
            'supervisor' => \App\Http\Middleware\Supervisor::class,
            'super' => \App\Http\Middleware\Super::class,
            'clients' => \App\Http\Middleware\Clients::class,
            'vendors' => \App\Http\Middleware\Vendors::class,
            'clients_wallet' => \App\Http\Middleware\ClientsWallet::class,
            'vendors_wallet' => \App\Http\Middleware\VendorsWallet::class,
            'categories' => \App\Http\Middleware\Categories::class,
            'products' => \App\Http\Middleware\Products::class,
            'coupons' => \App\Http\Middleware\Coupons::class,
            'orders' => \App\Http\Middleware\Orders::class,
            'reviews' => \App\Http\Middleware\Reviews::class,
            'blogs' => \App\Http\Middleware\Blogs::class,
            'comments' => \App\Http\Middleware\Comments::class,
            'replies' => \App\Http\Middleware\Replies::class,
            'reports' => \App\Http\Middleware\Reports::class,
            'contacts' => \App\Http\Middleware\Contacts::class,
            'statistics' => \App\Http\Middleware\Statistics::class,
            'messages' => \App\Http\Middleware\Messages::class,
            'mails' => \App\Http\Middleware\Mails::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
