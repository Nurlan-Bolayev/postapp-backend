<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_creates_post()
    {
        $user = User::factory()->create();
        $text = 'This is a new post';
        $this
            ->actingAs($user)
            ->postJson('api/posts/create', [
                'text' => $text,
            ])
            ->assertJson([
                'creator_id' => $user->id,
                'text' => $text,
            ]);
        $this
            ->assertDatabaseHas('posts', [
                'creator_id' => $user->id,
                'text' => $text,
            ]);
    }

    public function test_user_can_update_post()
    {
        $text = 'This is another post for testing';
        $user = User::factory()->create();
        $post = Post::factory()->create(['creator_id' => $user->id]);
        $this
            ->actingAs($user)
            ->putJson("api/posts/$post->id", [
                'text' => $text
            ])
            ->assertJson([
                'creator_id' => $user->id,
                'text' => $text,
            ]);
        $this
            ->assertDatabaseHas('posts', [
                'creator_id' => $user->id,
                'text' => $text,
            ]);
    }

    public function test_user_can_delete_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'text' => 'This post is to be deleted',
            'creator_id' => $user->id
        ]);

        $this
            ->actingAs($user)
            ->deleteJson("api/posts/$post->id")
            ->assertSee('Deleted');
        $this
            ->assertDatabaseMissing('posts', [
                'id' => $post->id,
            ]);

    }

    public function test_user_cannot_update_post()
    {
        $user = User::factory()->create();
        $text = 'This is the third post';
        $post = Post::factory()->create();
        $this
            ->actingAs($user)
            ->putJson("api/posts/$post->id", [
                'text' => $text
            ])
            ->assertSee('You can not update this post');
    }

    public function test_user_cannot_delete_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $this
            ->actingAs($user)
            ->deleteJson("api/posts/$post->id")
            ->assertSee('You can not delete this post');
    }
}
