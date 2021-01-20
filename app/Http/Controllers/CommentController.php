<?php

namespace App\Http\Controllers;


use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function create(Request $request, Post $post)
    {
        $attrs = $request->validate([
            'text' => 'required|string',
        ], [
            'text.required' => 'The text field is required',
        ]);

        $body = [
            'owner_id' => $request->user()->id,
            'post_id' => $post->id,
        ];

        return Comment::query()->forceCreate(array_merge($body, $attrs));
    }

    public function update(Request $request, Comment $comment)
    {
        if (\Gate::denies('modify-comment', $comment)) {
            return response()->json([
                'message' => 'You are not allowed to update this comment!',
            ], 401);
        }

        $attrs = $request->validate([
            'text' => 'required',
        ],
            [
                'text.required' => 'The text field is required',
            ]);

        $comment->forceFill($attrs)->save();
        return $comment;
    }

    public function delete(Comment $comment)
    {
        if (\Gate::denies('modify-comment', $comment)) {
            return response()->json([
                'message' => 'You are not allowed to delete this comment!',
            ], 401);
        }

        $comment->delete();
        return 'Comment deleted';
    }

    public function replyTo(Request $request, Comment $comment)
    {
        $attrs = $request->validate([
            'text' => 'required|string',
        ]);

        $body = [
            'owner_id' => \request()->user()->id,
            'post_id' => $comment->post_id,
            'parent_id' => $comment->id,
        ];

        return Comment::query()->forceCreate(array_merge($body, $attrs));
    }

    public function editReply(Request $request, Comment $comment)
    {
        if (\Gate::denies('modify-comment', $comment)) {
            return response()->json([
                'message' => 'You are not allowed to edit this reply.',
            ], 401);
        }
        $attrs = $request->validate([
            'text' => 'required|string',
        ]);

        $body = [
            'owner_id' => \request()->user()->id,
            'post_id' => $comment->post_id,
            'parent_id' => $comment->parent_id,
        ];

        $comment->forceFill(array_merge($body, $attrs))->save();
        return $comment;
    }

    public function deleteReply(Comment $comment)
    {
        if (\Gate::denies('modify-comment', $comment)) {
            return response()->json([
                'message' => 'You are not allowed to delete this reply.',
            ], 401);
        }
        $comment->delete();
        return "Reply deleted";
    }
}
