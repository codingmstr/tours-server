<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\BlogResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\UserResource;
use App\Models\Comment;
use App\Models\Blog;
use App\Models\Reply;
use App\Models\User;

class CommentController extends Controller {

    public function systems () {

        $blogs = Blog::where('active', true)->where('allow_comments', true)->get();
        $blogs = BlogResource::collection( $blogs );

        $clients = User::where('role', '3')->where('active', true)->where('allow_comments', true)->get();
        $clients = UserResource::collection( $clients );

        return ['blogs' => $blogs, 'clients' => $clients];

    }
    public function default ( Request $req ) {
        
        return $this->success(self::systems());

    }
    public function index ( Request $req ) {

        $data = $this->paginate( Comment::query(), $req );
        $items = CommentResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total'=> $data['total']]);

    }
    public function show ( Request $req, Comment $comment ) {

        $item = CommentResource::make( $comment );
        return $this->success(['item' => $item] + self::systems());

    }
    public function store ( Request $req ) {

        $blog = Blog::where('id', $req->blog_id)->where('allow_comments', true)->where('active', true)->first();
        if ( !$blog ) return $this->failed(['blog' => 'not exists']);

        $data = [
            'admin_id' => $this->user()->id,
            'blog_id' => $blog->id,
            'vendor_id' => $blog->vendor_id,
            'client_id' => $this->integer($req->client_id),
            'content' => $this->string($req->content),
            'allow_replies' => $this->bool($req->allow_replies),
            'allow' => $this->bool($req->allow),
            'active' => $this->bool($req->active),
        ];

        $comment = Comment::create($data);
        $this->report($req, 'comment', $comment->id, 'add', 'admin');
        return $this->success();

    }
    public function update ( Request $req, Comment $comment ) {

        $data = [
            'content' => $this->string($req->content),
            'allow_replies' => $this->bool($req->allow_replies),
            'allow' => $this->bool($req->allow),
            'active' => $this->bool($req->active),
        ];

        $comment->update($data);
        $this->report($req, 'comment', $comment->id, 'update', 'admin');
        return $this->success();

    }
    public function delete ( Request $req, Comment $comment ) {

        $comment->delete();
        $this->report($req, 'comment', $comment->id, 'delete', 'admin');
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {
            Comment::find($id)?->delete();
            $this->report($req, 'comment', $id, 'delete', 'admin');
        }
    
        return $this->success();

    }

}
