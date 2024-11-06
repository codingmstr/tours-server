<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class WalletController extends Controller {

    public function wallet ( $user ) {

        return [
            'pending_balance' => $user->pending_balance,
            'buy_balance' => $user->buy_balance,
            'withdraw_balance' => $user->withdraw_balance,
            'withdraws' => $user->withdraws,
            'deposits' => $user->deposits,
            'balance' => $user->withdraw_balance + $user->pending_balance + $user->buy_balance,
        ];

    }
    public function index ( Request $req, User $user ) {

        return $this->success(['wallet' => self::wallet($user)]);

    }
    public function deposit ( Request $req, User $user ) {

        $amount = $this->float($req->amount);

        if ( $req->type === 'pending' ) $user->pending_balance += $amount;
        else if ( $req->type === 'buy' ) $user->buy_balance += $amount;
        else if ( $req->type === 'withdraw' ) $user->withdraw_balance += $amount;
    
        $user->save();
        $this->report($req, $user->role === 2 ? 'vendor' : 'client', $user->id, 'deposit', 'admin', ['amount' => $amount]);
        return $this->success(['wallet' => self::wallet($user)]);

    }
    public function withdraw ( Request $req, User $user ) {

        $amount = $this->float($req->amount);

        if ( $req->type === 'pending' && $user->pending_balance >= $amount ) $user->pending_balance -= $amount;
        else if ( $req->type === 'buy' && $user->buy_balance >= $amount )  $user->buy_balance -= $amount;
        else if ( $req->type === 'withdraw' && $user->withdraw_balance >= $amount ) $user->withdraw_balance -= $amount;

        $user->save();
        $this->report($req, $user->role === 2 ? 'vendor' : 'client', $user->id, 'withdraw', 'admin', ['amount' => $amount]);
        return $this->success(['wallet' => self::wallet($user)]);

    }
    public function convert ( Request $req, User $user ) {

        $amount = $this->float($req->amount);

        if ( $req->from == 'pending' && $user->pending_balance >= $amount ) $user->pending_balance -= $amount;
        else if ( $req->from == 'buy' && $user->buy_balance >= $amount )  $user->buy_balance -= $amount;
        else if ( $req->from == 'withdraw' && $user->withdraw_balance >= $amount ) $user->withdraw_balance -= $amount;
        else return $this->failed();

        if ( $req->to == 'pending' ) $user->pending_balance += $amount;
        else if ( $req->to == 'buy' ) $user->buy_balance += $amount;
        else if ( $req->to == 'withdraw' ) $user->withdraw_balance += $amount;
    
        $user->save();
        $this->report($req, $user->role === 2 ? 'vendor' : 'client', $user->id, 'convert_balance', 'admin', ['amount' => $amount, 'status' => "from_{$req->from}_to_{$req->to}"]);
        return $this->success(['wallet' => self::wallet($user)]);

    }

}
