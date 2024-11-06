<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Reply;

class BlogController extends Controller {

    public function index ( Request $req ) {

        $data = $this->paginate( Blog::query(), $req );
        $items = BlogResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total'=> $data['total']]);

    }
    public function show ( Request $req, Blog $blog ) {

        $item = BlogResource::make( $blog );
        return $this->success(['item' => $item]);

    }
    public function store ( Request $req ) {

        if ( Blog::where('slug', $this->slug($req->title))->exists() ) {

            return $this->failed(['title' => 'exists']);

        }
        $data = [
            'admin_id' => $this->user()->id,
            'vendor_id' => $this->integer($req->vendor_id),
            'title' => $this->string($req->title),
            'slug' => $this->slug($req->title),
            'description' => $this->string($req->description),
            'content' => $this->string($req->content),
            'company' => $this->string($req->company),
            'phone' => $this->string($req->phone),
            'language' => $this->string($req->language),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'street' => $this->string($req->street),
            'location' => $this->string($req->location),
            'notes' => $this->string($req->notes),
            'allow_comments' => $this->bool($req->allow_comments),
            'allow_replies' => $this->bool($req->allow_replies),
            'allow_likes' => $this->bool($req->allow_likes),
            'allow_dislikes' => $this->bool($req->allow_dislikes),
            'allow' => $this->bool($req->allow),
            'active' => $this->bool($req->active),
        ];

        $blog = Blog::create($data);
        $this->upload_files( $req->allFiles(), 'blog', $blog->id );
        $this->report($req, 'blog', $blog->id, 'add', 'admin');
        return $this->success();

    }
    public function update ( Request $req, Blog $blog ) {

        if ( Blog::where('slug', $this->slug($req->title))->where('id', '!=', $blog->id)->exists() ) {

            return $this->failed(['title' => 'exists']);

        }
        $data = [
            'vendor_id' => $this->integer($req->vendor_id),
            'title' => $this->string($req->title),
            'slug' => $this->slug($req->title),
            'description' => $this->string($req->description),
            'content' => $this->string($req->content),
            'company' => $this->string($req->company),
            'phone' => $this->string($req->phone),
            'language' => $this->string($req->language),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'street' => $this->string($req->street),
            'location' => $this->string($req->location),
            'notes' => $this->string($req->notes),
            'allow_comments' => $this->bool($req->allow_comments),
            'allow_replies' => $this->bool($req->allow_replies),
            'allow_likes' => $this->bool($req->allow_likes),
            'allow_dislikes' => $this->bool($req->allow_dislikes),
            'allow' => $this->bool($req->allow),
            'active' => $this->bool($req->active),
        ];
        if ( $blog->vendor_id !== $this->integer($req->vendor_id) ) {

            Comment::withTrashed()->where('vendor_id', $blog->vendor_id)->update(['vendor_id' => $this->integer($req->vendor_id)]);
            Reply::withTrashed()->where('vendor_id', $blog->vendor_id)->update(['vendor_id' => $this->integer($req->vendor_id)]);

        }

        $blog->update($data);
        $this->upload_files( $req->allFiles(), 'blog', $blog->id );
        $this->delete_files( $this->parse($req->deleted_files), 'blog' );
        $this->report($req, 'blog', $blog->id, 'update', 'admin');
        return $this->success();

    }
    public function delete ( Request $req, Blog $blog ) {

        $blog->delete();
        $this->report($req, 'blog', $blog->id, 'delete', 'admin');
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {
            Blog::find($id)?->delete();
            $this->report($req, 'blog', $id, 'delete', 'admin');
        }

        return $this->success();

    }

}
