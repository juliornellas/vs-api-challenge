<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
// use PHPUnit\Framework\TestCase;


class FavoriteTest extends TestCase
{
    // use DatabaseMigrations;

    public function test_basic_test(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_guest_can_not_favorite_a_post()
    {
        $post = Post::factory()->create();
        $this->postJson(route('favorites.posts', ['post' => $post]))->assertStatus(401);
    }

    public function test_a_user_can_favorite_a_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.post', ['post' => $post]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'favorite_id' => $post->id,
            'favorite_type', 'App\Models\Post'
        ]);
    }

    public function test_a_user_can_remove_a_post_from_his_favorites()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.post', ['post' => $post]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'favorite_id' => $post->id,
            'favorite_type', 'App\Models\Post'
        ]);

        $this->actingAs($user)
            ->deleteJson(route('favorites.post', ['post' => $post]))
            ->assertNoContent();

        $this->assertDatabaseMissing('favorites', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'favorite_id' => $post->id,
            'favorite_type', 'App\Models\Post'
        ]);
    }

    public function test_a_user_can_favorite_a_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.user', ['user' => $u]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'post_id' => 0,
            'user_id' => $user->id,
            'favorite_id' => $u->id,
            'favorite_type', 'App\Models\User'
        ]);
    }

    public function test_a_user_can_remove_a_user_from_his_favorites()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.user', ['user' => $u]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'post_id' => 0,
            'user_id' => $user->id,
            'favorite_id' => $u->id,
            'favorite_type', 'App\Models\User'
        ]);

        $this->actingAs($user)
            ->deleteJson(route('favorites.user', ['user' => $u]))
            ->assertNoContent();

        $this->assertDatabaseMissing('favorites', [
            'post_id' => 0,
            'user_id' => $user->id,
            'favorite_id' => $u->id,
            'favorite_type', 'App\Models\User'
        ]);
    }

    public function test_a_user_can_not_remove_a_non_favorited_item()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('favorites.destroy', ['post' => $post]))
            ->assertNotFound();
    }
}