<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'United Skills',
            'email' => 'talent@unitedskills.az',
            'password' => \Hash::make('talent')
        ]);


        $posts = Post::factory()
            ->has(Comment::factory()->count(2))
            ->create();

        $posts->each(fn($post) => $post->comments->each(function ($comment) use ($post) {
            Comment::factory()
                ->count(2)
                ->create([
                    'post_id' => $post,
                    'parent_id' => $comment,
                ]);
        }));

        Post::factory()
            ->has(Comment::factory()->count(3))
            ->create();


        Post::factory()
            ->create();
    }
}
