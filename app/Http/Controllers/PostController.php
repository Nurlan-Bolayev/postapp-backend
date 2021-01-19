<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function create(Request $request)
    {
        $attrs = $request->validate([
            'text' => 'required|min:3'
        ]);
        $post = Post::query()->forceCreate(array_merge($attrs, [
            'creator_id' => $request->user()->id,
        ]));

        return $post;
    }

    public function update(Request $request, Post $post)
    {
        if(\Gate::denies('modify-post', $post)){
            return 'You can not update this post';
        }

        $this->authorize('modify-post', $post);
        $attrs = $request->validate([
            'text' => 'required|min:3'
        ]);

        $post->forceFill($attrs)->save();

        return $post;
    }

    public function delete(Post $post)
    {
        if(\Gate::denies('modify-post', $post)){
            return 'You can not delete this post';
        }
        $this->authorize('modify-post', $post);
        $post->delete();
        return 'Deleted';
    }
}
