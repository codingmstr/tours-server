<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\MailResource;
use App\Models\Mail;
use App\Models\User;
use App\Events\MailBox;

class MailController extends Controller {

    public function index ( Request $req ) {

        $mails = Mail::where('receiver_id', $this->user()->id)
                ->where('sender_id', '!=', $this->user()->id)
                ->where('removed_receiver', false)
                ->orWhere('sender_id', $this->user()->id)
                ->where('removed_sender', false)
                ->orderBy('id', 'desc')
                ->get();
        
        $users = User::where('role', 1)
                ->where('id', '!=', $this->user()->id)
                ->where('active', true)
                ->where('allow_mails', true)
                ->get();

        $mails = MailResource::collection($mails);
        $users = UserResource::collection($users);

        return $this->success(['mails' => $mails, 'users' => $users]);

    }
    public function send ( Request $req ) {

        $user = User::where('role', 1)->where('id', $req->user_id)->where('active', true)->first();
        if ( !$user ) return $this->failed(['user' => 'not exists']);
        if ( !$user->allow_mails ) return $this->failed(['user' => 'access denied']);

        $data = [
            'sender_id' => $this->user()->id,
            'receiver_id' => $user->id,
            'title' => $this->string($req->title),
            'description' => $this->string($req->description),
            'content' => $this->string($req->content),
        ];

        $mail = Mail::create($data);
        $mail = MailResource::make( Mail::find($mail->id) );
        $this->upload_files( $req->allFiles(), 'mail', $mail->id );

        event(new MailBox($user->id, $mail));
        return $this->success(['mail' => $mail]);

    }
    public function active ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {

            $mail = Mail::find($id);
            if ( !$mail ) continue;
            if ( $mail->receiver_id != $this->user()->id ) continue;
            if ( $mail->readen ) continue;
            $mail->readen = true;
            $mail->readen_at = date('Y-m-d H:i:s');
            $mail->save();

        }

        return $this->success();

    }
    public function unactive ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {

            $mail = Mail::find($id);
            if ( !$mail ) continue;
            if ( $mail->receiver_id != $this->user()->id ) continue;
            if ( !$mail->readen ) continue;
            $mail->readen = false;
            $mail->save();

        }

        return $this->success();

    }
    public function archive ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {

            $mail = Mail::find($id);
            if ( !$mail ) continue;
            if ( $mail->sender_id == $this->user()->id ) $mail->archived_sender = !$mail->archived_sender;
            if ( $mail->receiver_id == $this->user()->id ) $mail->archived_receiver = !$mail->archived_receiver;
            $mail->save();

        }

        return $this->success();

    }
    public function star ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {

            $mail = Mail::find($id);
            if ( !$mail ) continue;
            if ( $mail->sender_id == $this->user()->id ) $mail->star_sender = !$mail->star_sender;
            if ( $mail->receiver_id == $this->user()->id ) $mail->star_receiver = !$mail->star_receiver;
            $mail->save();

        }

        return $this->success();

    }
    public function important ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {

            $mail = Mail::find($id);
            if ( !$mail ) continue;
            if ( $mail->sender_id == $this->user()->id ) $mail->important_sender = !$mail->important_sender;
            if ( $mail->receiver_id == $this->user()->id ) $mail->important_receiver = !$mail->important_receiver;
            $mail->save();

        }

        return $this->success();

    }
    public function delete ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {

            $mail = Mail::find($id);
            if ( !$mail ) continue;
            if ( $mail->sender_id == $this->user()->id ) $mail->removed_sender = true;
            if ( $mail->receiver_id == $this->user()->id ) $mail->removed_receiver = true;
            $mail->save();

        }

        return $this->success();

    }

}
