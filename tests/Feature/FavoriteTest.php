<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_can_not_favorite_a_post()
    {
        $post = Post::factory()->create();
        $this->postJson(route('favorites.posts', ['post' => $post]))
        ->assertStatus(401);
    }

    public function test_a_user_can_favorite_a_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.posts', ['post' => $post]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);
    }

    public function test_a_user_can_remove_a_post_from_his_favorites()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.posts', ['post' => $post]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->postJson(route('favorites.posts', ['post' => $post]))
            ->assertNoContent();

        $this->assertDatabaseMissing('favorites', [
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);
    }

    public function test_a_user_can_favorite_a_user()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.users', ['user' => $user2]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'post_id' => 0,
            'user_id' => $user->id
        ]);
    }

    public function test_a_user_can_remove_a_user_from_his_favorites()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.users', ['user' => $user2]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'post_id' => 0,
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->postJson(route('favorites.users', ['user' => $user2]))
            ->assertNoContent();

        $this->assertDatabaseMissing('favorites', [
            'post_id' => 0,
            'user_id' => $user->id
        ]);
    }

}