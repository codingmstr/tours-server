<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function(){

    Route::prefix('auth')->group(function(){

        Route::post('login', 'App\Http\Controllers\Admin\AuthController@login');

        Route::middleware(['auth:sanctum', 'admin'])->group(function(){
            Route::post('unlock', 'App\Http\Controllers\Admin\AuthController@unlock');
            Route::post('logout', 'App\Http\Controllers\Admin\AuthController@logout');
        });

    });
    Route::middleware(['auth:sanctum', 'admin'])->group(function(){

        Route::prefix('account')->group(function(){
            Route::post('', 'App\Http\Controllers\Admin\AccountController@index');
            Route::post('save', 'App\Http\Controllers\Admin\AccountController@save');
            Route::post('password', 'App\Http\Controllers\Admin\AccountController@password');
        });
        Route::middleware('mails')->group(function(){

            Route::prefix('mail')->group(function(){
                Route::post('', 'App\Http\Controllers\Admin\MailController@index');
                Route::post('send', 'App\Http\Controllers\Admin\MailController@send');
                Route::post('active', 'App\Http\Controllers\Admin\MailController@active');
                Route::post('unactive', 'App\Http\Controllers\Admin\MailController@unactive');
                Route::post('archive', 'App\Http\Controllers\Admin\MailController@archive');
                Route::post('star', 'App\Http\Controllers\Admin\MailController@star');
                Route::post('important', 'App\Http\Controllers\Admin\MailController@important');
                Route::post('delete', 'App\Http\Controllers\Admin\MailController@delete');
            });

        });
        Route::middleware('messages')->group(function(){

            Route::prefix('chat')->group(function(){

                Route::prefix('friends')->group(function(){

                    Route::post('', 'App\Http\Controllers\Admin\MessageController@relations');

                    Route::prefix('{user}')->group(function(){
                        Route::post('', 'App\Http\Controllers\Admin\MessageController@messages');
                        Route::post('send', 'App\Http\Controllers\Admin\MessageController@send');
                        Route::post('active', 'App\Http\Controllers\Admin\MessageController@active');
                        Route::post('delete', 'App\Http\Controllers\Admin\MessageController@delete');
                        Route::post('archive', 'App\Http\Controllers\Admin\MessageController@archive');
                        Route::post('unarchive', 'App\Http\Controllers\Admin\MessageController@unarchive');
                    });

                });
                Route::prefix('messages')->group(function(){

                    Route::prefix('{message}')->group(function(){
                        Route::post('star', 'App\Http\Controllers\Admin\MessageController@star_message');
                        Route::post('unstar', 'App\Http\Controllers\Admin\MessageController@unstar_message');
                        Route::post('delete', 'App\Http\Controllers\Admin\MessageController@delete_message');
                    });

                });

            });

        });
        Route::middleware('statistics')->group(function(){
            Route::prefix('statistic')->group(function(){
                Route::post('', 'App\Http\Controllers\Admin\StatisticController@index');
            });
        });
        Route::middleware('categories')->group(function(){

            Route::prefix('category')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\CategoryController@index');
                Route::post('store', 'App\Http\Controllers\Admin\CategoryController@store');
                Route::post('delete', 'App\Http\Controllers\Admin\CategoryController@delete_group');

                Route::prefix('{category}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Admin\CategoryController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\CategoryController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\CategoryController@delete');
                });

            });

        });
        Route::middleware('products')->group(function(){

            Route::prefix('product')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\ProductController@index');
                Route::post('default', 'App\Http\Controllers\Admin\ProductController@default');
                Route::post('store', 'App\Http\Controllers\Admin\ProductController@store');
                Route::post('delete', 'App\Http\Controllers\Admin\ProductController@delete_group');

                Route::prefix('{product}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Admin\ProductController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\ProductController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\ProductController@delete');
                });

            });

        });
        Route::middleware('coupons')->group(function(){

            Route::prefix('coupon')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\CouponController@index');
                Route::post('default', 'App\Http\Controllers\Admin\CouponController@default');
                Route::post('store', 'App\Http\Controllers\Admin\CouponController@store');
                Route::post('delete', 'App\Http\Controllers\Admin\CouponController@delete_group');

                Route::prefix('{coupon}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Admin\CouponController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\CouponController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\CouponController@delete');
                });

            });

        });
        Route::middleware('orders')->group(function(){

            Route::prefix('order')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\OrderController@index');
                Route::post('default', 'App\Http\Controllers\Admin\OrderController@default');
                Route::post('store', 'App\Http\Controllers\Admin\OrderController@store');
                Route::post('delete', 'App\Http\Controllers\Admin\OrderController@delete_group');

                Route::prefix('{order}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Admin\OrderController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\OrderController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\OrderController@delete');
                });

            });

        });
        Route::middleware('reviews')->group(function(){

            Route::prefix('review')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\ReviewController@index');
                Route::post('default', 'App\Http\Controllers\Admin\ReviewController@default');
                Route::post('store', 'App\Http\Controllers\Admin\ReviewController@store');
                Route::post('delete', 'App\Http\Controllers\Admin\ReviewController@delete_group');

                Route::prefix('{review}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Admin\ReviewController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\ReviewController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\ReviewController@delete');
                });

            });

        });
        Route::middleware('blogs')->group(function(){

            Route::prefix('blog')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\BlogController@index');
                Route::post('default', 'App\Http\Controllers\Admin\BlogController@default');
                Route::post('store', 'App\Http\Controllers\Admin\BlogController@store');
                Route::post('delete', 'App\Http\Controllers\Admin\BlogController@delete_group');

                Route::prefix('{blog}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Admin\BlogController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\BlogController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\BlogController@delete');
                });

            });

        });
        Route::middleware('comments')->group(function(){

            Route::prefix('comment')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\CommentController@index');
                Route::post('default', 'App\Http\Controllers\Admin\CommentController@default');
                Route::post('store', 'App\Http\Controllers\Admin\CommentController@store');
                Route::post('delete', 'App\Http\Controllers\Admin\CommentController@delete_group');

                Route::prefix('{comment}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Admin\CommentController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\CommentController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\CommentController@delete');
                });

            });
          
        });
        Route::middleware('replies')->group(function(){

            Route::prefix('reply')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\ReplyController@index');
                Route::post('default', 'App\Http\Controllers\Admin\ReplyController@default');
                Route::post('store', 'App\Http\Controllers\Admin\ReplyController@store');
                Route::post('delete', 'App\Http\Controllers\Admin\ReplyController@delete_group');

                Route::prefix('{reply}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Admin\ReplyController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\ReplyController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\ReplyController@delete');
                });

            });
          
        });
        Route::middleware('contacts')->group(function(){

            Route::prefix('contact')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\ContactController@index');
                Route::post('delete', 'App\Http\Controllers\Admin\ContactController@delete_group');

                Route::prefix('{contact}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Admin\ContactController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\ContactController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\ContactController@delete');
                });

            });

        });
        Route::middleware('reports')->group(function(){

            Route::prefix('report')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\ReportController@index');
                Route::post('delete', 'App\Http\Controllers\Admin\ReportController@delete_group');

                Route::prefix('{report}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Admin\ReportController@show');
                    Route::post('delete', 'App\Http\Controllers\Admin\ReportController@delete');
                });

            });

        });
        Route::middleware('clients')->group(function(){

            Route::prefix('client')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\ClientController@index');
                Route::post('store', 'App\Http\Controllers\Admin\ClientController@store');
                Route::post('delete', 'App\Http\Controllers\Admin\ClientController@delete_group');

                Route::prefix('{user}')->group(function(){

                    Route::post('', 'App\Http\Controllers\Admin\ClientController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\ClientController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\ClientController@delete');

                    Route::middleware('clients_wallet')->group(function(){

                        Route::prefix('wallet')->group(function(){
                            Route::post('', 'App\Http\Controllers\Admin\WalletController@index');
                            Route::post('deposit', 'App\Http\Controllers\Admin\WalletController@deposit');
                            Route::post('withdraw', 'App\Http\Controllers\Admin\WalletController@withdraw');
                            Route::post('convert', 'App\Http\Controllers\Admin\WalletController@convert');
                        });

                    });

                });

            });

        });
        Route::middleware('vendors')->group(function(){

            Route::prefix('vendor')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\VendorController@index');
                Route::post('store', 'App\Http\Controllers\Admin\VendorController@store');
                Route::post('delete', 'App\Http\Controllers\Admin\VendorController@delete_group');

                Route::prefix('{user}')->group(function(){

                    Route::post('', 'App\Http\Controllers\Admin\VendorController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\VendorController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\VendorController@delete');

                    Route::middleware('vendors_wallet')->group(function(){

                        Route::prefix('wallet')->group(function(){
                            Route::post('', 'App\Http\Controllers\Admin\WalletController@index');
                            Route::post('deposit', 'App\Http\Controllers\Admin\WalletController@deposit');
                            Route::post('withdraw', 'App\Http\Controllers\Admin\WalletController@withdraw');
                            Route::post('convert', 'App\Http\Controllers\Admin\WalletController@convert');
                        });

                    });

                });

            });

        });
        Route::middleware('supervisor')->group(function(){

            Route::prefix('admin')->group(function(){

                Route::post('', 'App\Http\Controllers\Admin\AdminController@index');
                Route::post('store', 'App\Http\Controllers\Admin\AdminController@store');
                Route::post('delete', 'App\Http\Controllers\Admin\AdminController@delete_group');

                Route::prefix('{user}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Admin\AdminController@show');
                    Route::post('update', 'App\Http\Controllers\Admin\AdminController@update');
                    Route::post('delete', 'App\Http\Controllers\Admin\AdminController@delete');
                });

            });

        });
        Route::middleware('super')->group(function(){

            Route::prefix('setting')->group(function(){
                Route::post('', 'App\Http\Controllers\Admin\SettingController@index');
                Route::post('update', 'App\Http\Controllers\Admin\SettingController@update');
                Route::post('content', 'App\Http\Controllers\Admin\SettingController@content');
                Route::post('option', 'App\Http\Controllers\Admin\SettingController@option');
                Route::post('delete', 'App\Http\Controllers\Admin\SettingController@delete');
            });

        });

    });

});
Route::prefix('client')->group(function(){

    Route::prefix('auth')->group(function(){

        Route::post('register', 'App\Http\Controllers\Client\AuthController@register');
        Route::post('login', 'App\Http\Controllers\Client\AuthController@login');
        Route::post('recovery', 'App\Http\Controllers\Client\AuthController@recovery');
        Route::post('check-token/{token}', 'App\Http\Controllers\Client\AuthController@check');
        Route::post('change-password/{token}', 'App\Http\Controllers\Client\AuthController@change');

        Route::middleware(['auth:sanctum', 'client'])->group(function(){
            Route::post('logout', 'App\Http\Controllers\Client\AuthController@logout');
        });

    });
    Route::middleware(['auth:sanctum', 'client'])->group(function(){
        
        Route::prefix('account')->group(function(){

            Route::post('', 'App\Http\Controllers\Client\AccountController@index');
            Route::post('save', 'App\Http\Controllers\Client\AccountController@save');
            Route::post('password', 'App\Http\Controllers\Client\AccountController@password');

            Route::prefix('history')->group(function(){
                
                Route::post('', 'App\Http\Controllers\Client\HistoryController@index');
                Route::post('delete', 'App\Http\Controllers\Client\HistoryController@delete_group');

                Route::prefix('{order}')->group(function(){
                    Route::post('', 'App\Http\Controllers\Client\HistoryController@show');
                    Route::post('update', 'App\Http\Controllers\Client\HistoryController@update');
                    Route::post('delete', 'App\Http\Controllers\Client\HistoryController@delete');
                    Route::post('review', 'App\Http\Controllers\Client\HistoryController@review');
                });

            });

        });
        Route::middleware('messages')->group(function(){

            Route::prefix('chat')->group(function(){
                Route::post('', 'App\Http\Controllers\Client\ChatController@index');
                Route::post('active', 'App\Http\Controllers\Client\ChatController@active');
                Route::post('send', 'App\Http\Controllers\Client\ChatController@send');
            });
            Route::prefix('message/{user}/{product}')->group(function(){
                Route::post('', 'App\Http\Controllers\Client\MessageController@index');
                Route::post('send', 'App\Http\Controllers\Client\MessageController@send');
                Route::post('active', 'App\Http\Controllers\Client\MessageController@active');
            });

        });
        Route::middleware('orders')->group(function(){

            Route::prefix('order/{product}')->group(function(){
                Route::post('checkout', 'App\Http\Controllers\Client\OrderController@checkout');
            });

        });
        Route::prefix('pay')->group(function(){
            Route::post('verify', 'App\Http\Controllers\Payment\VerifyController@index');
            Route::post('stripe', 'App\Http\Controllers\Payment\StripeController@index');
            Route::post('paytabs', 'App\Http\Controllers\Payment\PaytabsController@index');
            Route::post('paymob', 'App\Http\Controllers\Payment\PaymobController@index');
            Route::post('paypal', 'App\Http\Controllers\Payment\PaypalController@index');
            Route::post('crypto', 'App\Http\Controllers\Payment\CryptoController@index');
            Route::post('kashier', 'App\Http\Controllers\Payment\KashierController@index');
            Route::post('wallet', 'App\Http\Controllers\Payment\KashierController@wallet');
            Route::post('fawry', 'App\Http\Controllers\Payment\KashierController@wallet');
            Route::post('payeer', 'App\Http\Controllers\Payment\PayeerController@index');
            Route::post('perfect', 'App\Http\Controllers\Payment\PerfectController@index');
        });

    });
    Route::prefix('home')->group(function(){
        Route::post('', 'App\Http\Controllers\Client\HomeController@index');
        Route::post('contact', 'App\Http\Controllers\Client\HomeController@contact');
        Route::post('categories', 'App\Http\Controllers\Client\HomeController@categories');
        Route::post('categories/{category}', 'App\Http\Controllers\Client\HomeController@products');
    });
    Route::prefix('search')->group(function(){
        Route::post('', 'App\Http\Controllers\Client\SearchController@index');
        Route::post('form', 'App\Http\Controllers\Client\SearchController@form');
    });
    Route::prefix('vendor/{user}')->group(function(){
        Route::post('', 'App\Http\Controllers\Client\VendorController@index');
        Route::post('products', 'App\Http\Controllers\Client\VendorController@products');
        Route::post('reviews', 'App\Http\Controllers\Client\VendorController@reviews');
    });
    Route::prefix('product/{product}')->group(function(){
        Route::post('', 'App\Http\Controllers\Client\ProductController@index');
        Route::post('reviews', 'App\Http\Controllers\Client\ProductController@reviews');
    });
    Route::prefix('order/{product}')->group(function(){
        Route::post('', 'App\Http\Controllers\Client\OrderController@index');
        Route::post('coupon', 'App\Http\Controllers\Client\OrderController@coupon');
    });
    Route::prefix('webhook')->group(function(){
        Route::post('stripe', 'App\Http\Controllers\Payment\StripeController@callback');
        Route::post('paytabs', 'App\Http\Controllers\Payment\PaytabsController@callback');
        Route::post('paymob', 'App\Http\Controllers\Payment\PaymobController@callback');
        Route::post('paypal', 'App\Http\Controllers\Payment\PaypalController@callback');
        Route::post('crypto', 'App\Http\Controllers\Payment\CryptoController@callback');
        Route::post('kashier', 'App\Http\Controllers\Payment\KashierController@callback');
        Route::post('payeer', 'App\Http\Controllers\Payment\PayeerController@callback');
        Route::post('perfect', 'App\Http\Controllers\Payment\PerfectController@callback');
    });

});
Route::prefix('client/old')->group(function(){

    Route::prefix('auth')->group(function(){

        Route::post('register', 'App\Http\Controllers\Client\Old\AuthController@register');
        Route::post('login', 'App\Http\Controllers\Client\Old\AuthController@login');
        Route::post('recovery', 'App\Http\Controllers\Client\Old\AuthController@recovery');
        Route::post('check-token/{token}', 'App\Http\Controllers\Client\Old\AuthController@check');
        Route::post('change-password/{token}', 'App\Http\Controllers\Client\Old\AuthController@change');

    });
    Route::prefix('home')->group(function(){
        Route::post('', 'App\Http\Controllers\Client\Old\HomeController@index');
        Route::post('wishlist', 'App\Http\Controllers\Client\Old\HomeController@wishlist');
        Route::post('search', 'App\Http\Controllers\Client\Old\HomeController@search');
        Route::post('categories/{category}', 'App\Http\Controllers\Client\Old\HomeController@products');
        Route::post('products/{product}', 'App\Http\Controllers\Client\Old\HomeController@product');
        Route::post('products/{product}/coupon', 'App\Http\Controllers\Client\Old\OrderController@coupon');
        Route::post('products/{product}/checkout', 'App\Http\Controllers\Client\Old\OrderController@checkout');

        Route::middleware(['auth:sanctum', 'client'])->group(function(){
            Route::post('account', 'App\Http\Controllers\Client\Old\HomeController@account');
            Route::post('update-account', 'App\Http\Controllers\Client\Old\HomeController@update_account');
            Route::post('reset-password', 'App\Http\Controllers\Client\Old\HomeController@reset_password');
            Route::post('history', 'App\Http\Controllers\Client\Old\HomeController@history');
            Route::post('update-history', 'App\Http\Controllers\Client\Old\HomeController@update_history');
            Route::post('delete-history', 'App\Http\Controllers\Client\Old\HomeController@delete_history');

            Route::middleware('messages')->group(function(){
                Route::prefix('chat')->group(function(){
                    Route::post('', 'App\Http\Controllers\Client\ChatController@index');
                    Route::post('active', 'App\Http\Controllers\Client\ChatController@active');
                    Route::post('send', 'App\Http\Controllers\Client\ChatController@send');
                });
            });
            Route::prefix('pay')->group(function(){
                Route::post('verify', 'App\Http\Controllers\Payment\VerifyController@index');
                Route::post('stripe', 'App\Http\Controllers\Payment\StripeController@index');
                Route::post('paytabs', 'App\Http\Controllers\Payment\PaytabsController@index');
                Route::post('paymob', 'App\Http\Controllers\Payment\PaymobController@index');
                Route::post('paypal', 'App\Http\Controllers\Payment\PaypalController@index');
                Route::post('crypto', 'App\Http\Controllers\Payment\CryptoController@index');
                Route::post('kashier', 'App\Http\Controllers\Payment\KashierController@index');
                Route::post('wallet', 'App\Http\Controllers\Payment\KashierController@wallet');
                Route::post('fawry', 'App\Http\Controllers\Payment\KashierController@wallet');
                Route::post('payeer', 'App\Http\Controllers\Payment\PayeerController@index');
                Route::post('perfect', 'App\Http\Controllers\Payment\PerfectController@index');
            });
        });
    });
    Route::prefix('webhook')->group(function(){
        Route::post('stripe', 'App\Http\Controllers\Payment\StripeController@callback');
        Route::post('paytabs', 'App\Http\Controllers\Payment\PaytabsController@callback');
        Route::post('paymob', 'App\Http\Controllers\Payment\PaymobController@callback');
        Route::post('paypal', 'App\Http\Controllers\Payment\PaypalController@callback');
        Route::post('crypto', 'App\Http\Controllers\Payment\CryptoController@callback');
        Route::post('kashier', 'App\Http\Controllers\Payment\KashierController@callback');
        Route::post('payeer', 'App\Http\Controllers\Payment\PayeerController@callback');
        Route::post('perfect', 'App\Http\Controllers\Payment\PerfectController@callback');
    });

});
