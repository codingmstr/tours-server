<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\Review;
use App\Models\User;

class ContactController extends Controller {

    public function index ( Request $req ) {

        $data = $this->paginate( Contact::query(), $req );
        $items = ContactResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total'=> $data['total']]);

    }
    public function show ( Request $req, Contact $contact ) {

        $item = ContactResource::make( $contact );
        return $this->success(['item' => $item]);

    }
    public function update ( Request $req, Contact $contact ) {

        $data = [
            'active' => $this->bool($req->active),
        ];

        $contact->update($data);
        $this->report($req, 'contact', $contact->id, 'update', 'admin');
        return $this->success();

    }
    public function delete ( Request $req, Contact $contact ) {

        $contact->delete();
        $this->report($req, 'contact', $contact->id, 'delete', 'admin');
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {
            Contact::find($id)?->delete();
            $this->report($req, 'contact', $id, 'delete', 'admin');
        }

        return $this->success();

    }

}
