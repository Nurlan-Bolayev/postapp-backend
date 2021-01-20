<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_a_user_comments_successfully()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['creator_id' => $user->id]);
        $text = 'I haven\'t read about it';
        $this
            ->actingAs($user)
            ->postJson("api/posts/{$post->id}/comments/create", [
                'text' => $text,
                'owner_id' => $user->id,
                'post_id' => $post->id,
            ])
            ->assertCreated()
            ->assertJson([
                'text' => $text,
                'owner_id' => $user->id,
                'post_id' => $post->id,
            ]);
        $this
            ->assertDatabaseHas('comments', [
                'text' => $text,
                'owner_id' => $user->id,
            ]);
    }

    public function test_a_user_comment_fails()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['creator_id' => $user->id]);
        $text = '';
        $this
            ->actingAs($user)
            ->postJson("api/posts/{$post->id}/comments/create", [
                'text' => $text,
            ])
            ->assertJson([
                'errors' => [
                    'text' => ['The text field is required']
                ]
            ]);
    }


//    public function test_a_user_comments_fails()
//    {
//        $user = User::factory()->create();
//        $post = Post::factory()->create(['creator_id' => $user->id]);
//        $text = 'This is my first experience';
//        $this
//            ->postJson("api/posts/{$post->id}/comments/create", [
//                'text' => $text,
//            ])
//            ->assertJson([
//               'message' => 'You are unauthorized.'
//            ]);
//    }


    public function test_user_reply_to_a_comment()
    {
        $user = User::factory()->create([
            'name' => 'Nurlan',
        ]);
        $anotherUser = User::factory()->create([
            'name' => 'Nurik',
        ]);// The user that replies to the post

        $post = Post::factory()->create(['creator_id' => $user->id]);

        $replyMessage = 'I like it so much!';

        $comment = Comment::factory()->create([
            'owner_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $body = [
            'text' => $replyMessage,
            'post_id' => $post->id,
            'owner_id' => $anotherUser->id,
            'parent_id' => $comment->id,
        ];

        $this
            ->actingAs($anotherUser)
            ->postJson("api/comments/{$comment->id}/reply", $body)
            ->assertCreated()
            ->assertJson([
                'owner_id' => $anotherUser->id,
                'text' => $replyMessage
            ]);
        $this
            ->assertDatabaseHas('comments', [
                'owner_id' => $anotherUser->id,
                'text' => $replyMessage
            ]);

    }

    public function test_user_can_edit_reply()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $comment = Comment::factory()->create(['post_id' => $post->id]);
        $reply = Comment::factory()->create(['owner_id' => $user->id, 'post_id' => $post->id, 'parent_id' => $comment->id]);
        $replyMessage = 'It is good.';
        $body = [
            'post_id' => $post->id,
            'parent_id' => $comment->id,
            'owner_id' => $user->id,
        ];
        $this
            ->actingAs($user)
            ->putJson("api/comments/$reply->id/reply", [
                'text' => $replyMessage,
            ])
            ->assertJson(array_merge($body, [
                'text' => $replyMessage,
            ]));
        $this
            ->assertDatabaseHas('comments', array_merge($body, [
                'text' => $replyMessage,
            ]));
    }

    public function test_user_deletes_a_reply()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => null]);
        $reply = Comment::factory()->create(['owner_id' => $user->id, 'post_id' => $post->id, 'parent_id' => $comment->id]);

        $this
            ->actingAs($user)
            ->deleteJson("api/comments/$reply->id/reply")
            ->assertSee("Reply deleted");
    }

    public function test_user_cannot_edit_a_reply()
    {
        $this->markTestSkipped();

        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);
        $reply = Comment::factory()->create(['owner_id' => $user->id, 'post_id' => $post->id, 'parent_id' => $comment->id]);
        $replyMessage = 'I do not know.';
        $this
            ->putJson("api/comments/$reply->id/reply", [
                'text' => $replyMessage,
            ])
            ->assertStatus(401)
            ->assertJson([
                'message' => 'You are not allowed to edit this reply.',
            ]);
        $this
            ->assertDatabaseMissing('comments', [
               'text' => $replyMessage,
            ]);
    }

    public function test_user_cannot_delete_a_reply()
    {
        $this->markTestSkipped();

        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);
        $reply = Comment::factory()->create(['owner_id' => $user->id, 'post_id' => $post->id, 'parent_id' => $comment->id]);
        $this
            ->deleteJson("api/comments/$reply->id/reply")
            ->assertStatus(401)
            ->assertJson([
                'message' => 'You are not allowed to delete this reply.',
            ]);
        $this
            ->assertDatabaseHas('comments', [
               'text' => $reply->text,
            ]);
    }

    public function test_a_user_can_update_a_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'creator_id' => $user->id,
        ]);
        $comment = Comment::factory()->create([
            'owner_id' => $user->id,
            'post_id' => $post->id,
        ]);
        $commentText = 'Hey, do you also like this? Wow!';
        $body = [
            'owner_id' => $user->id,
            'post_id' => $post->id,
        ];
        $this
            ->actingAs($user)
            ->putJson("api/posts/comments/$comment->id",
                array_merge($body, ['text' => $commentText])
            )
            ->assertJson(
                array_merge($body, ['text' => $commentText])
            );
        $this
            ->assertDatabaseHas('comments', [
               'text'=> $commentText,
            ]);
    }

    public function test_a_user_can_delete_a_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'owner_id' => $user->id,
            'post_id' => $post->id,
        ]);
        $this
            ->actingAs($user)
            ->deleteJson("api/posts/comments/$comment->id")
            ->assertSee('Comment deleted');
        $this
            ->assertDeleted($comment);
    }

    public function test_a_user_cannot_update_a_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'creator_id' => $user->id,
        ]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
        ]);

        $this
            ->actingAs($user)
            ->putJson("api/posts/comments/$comment->id")
            ->assertJson([
                'message' => 'You are not allowed to update this comment!',
            ]);
        $this
            ->assertDatabaseHas('comments', [
                'id' => $comment->id,
                'text' => $comment->text,
            ]);
    }

    public function test_a_user_cannot_delete_a_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'creator_id' => $user->id,
        ]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
        ]);

        $this
            ->actingAs($user)
            ->deleteJson("api/posts/comments/$comment->id")
            ->assertStatus(401)
            ->assertJson([
                'message' => 'You are not allowed to delete this comment!',
            ]);
        $this
            ->assertDatabaseHas('comments', [
                'id' => $comment->id,
            ]);
    }
}
