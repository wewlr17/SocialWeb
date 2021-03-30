<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\PostLiked;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function like(): JsonResponse
    {



        $post = Post::find(request()->id);

        if ($post->isLikedByLoggedInUser()) {
            $res = Like::where([
                'user_id' => auth()->user()->id,
                'post_id' => request()->id
            ])->delete();

            if ($res) {
                return response()->json([
                    'count' => Post::find(request()->id)->likes->count(),
                    'check' => true
                ]);
            }
        } else {
            // $user_id = Post::find(request()->id)->user_id;

            User::find($post->user_id)->notify(new PostLiked($post));

            
            $like = new Like();
            $like->user_id = auth()->user()->id;
            $like->post_id = request()->id;

            $like->save();

            return response()->json([
                'count' => Post::find(request()->id)->likes->count(),
                'check' => false
            ]);
        }
    }

    public function postShow($id)
    {
        $post = Post::find($id);

        return view('post.show', [
            'post' => $post,
            'hash' => ""
        ]);
    }
}
