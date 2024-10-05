<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ReplyResource;
use App\Http\Resources\BlogResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\UserResource;
use App\Models\Reply;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Review;
use App\Models\User;

class ReplyController extends Controller {

    public function systems () {

        $blogs = Blog::where('active', true)->where('allow_comments', true)->get();
        $blogs = BlogResource::collection( $blogs );

        $comments = Comment::where('active', true)->where('allow_replies', true)->get();
        $comments = CommentResource::collection( $comments );

        $clients = User::where('role', '3')->where('active', true)->where('allow_replies', true)->get();
        $clients = UserResource::collection( $clients );

        return ['blogs' => $blogs, 'comments' => $comments, 'clients' => $clients];

    }
    public function default ( Request $req ) {
        
        return $this->success(self::systems());

    }
    public function index ( Request $req ) {

        $data = $this->paginate( Reply::query(), $req );
        $items = ReplyResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total'=> $data['total']]);

    }
    public function show ( Request $req, Reply $reply ) {

        $item = ReplyResource::make( $reply );
        return $this->success(['item' => $item] + self::systems());

    }
    public function store ( Request $req ) {

        $comment = Comment::where('id', $req->comment_id)->where('allow_replies', true)->where('active', true)->first();
        if ( !$comment ) return $this->failed(['comment' => 'not exists']);

        $data = [
            'admin_id' => $this->user()->id,
            'vendor_id' => $this->integer($req->vendor_id),
            'client_id' => $this->integer($req->client_id),
            'comment_id' => $this->integer($req->comment_id),
            'blog_id' => Comment::find($req->comment_id)->blog->id,
            'content' => $this->string($req->content),
            'allow' => $this->bool($req->allow),
            'active' => $this->bool($req->active),
        ];

        $reply = Reply::create($data);
        $this->report($req, 'reply', $reply->id, 'add', 'admin');
        return $this->success();

    }
    public function update ( Request $req, Reply $reply ) {

        $data = [
            'content' => $this->string($req->content),
            'allow' => $this->bool($req->allow),
            'active' => $this->bool($req->active),
        ];

        $reply->update($data);
        $this->report($req, 'reply', $reply->id, 'update', 'admin');
        return $this->success();

    }
    public function delete ( Request $req, Reply $reply ) {

        $reply->delete();
        $this->report($req, 'reply', $reply->id, 'delete', 'admin');
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {
            Reply::find($id)?->delete();
            $this->report($req, 'reply', $id, 'delete', 'admin');
        }

        return $this->success();

    }

}
