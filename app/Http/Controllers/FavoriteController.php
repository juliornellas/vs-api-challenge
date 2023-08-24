<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\CreateFavoriteRequest;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Response;

/**
 * @group Favorites
 *
 * API endpoints for managing favorites
 */
class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites;
        return FavoriteResource::collection($favorites);
    }

    public function storeOrDestroyFavoritePost(Request $request, Post $post)
    {
        $favorite = Favorite::where([
            ['user_id', $request->user()->id],
            ['favorite_id', $post->id],
            ['favorite_type', 'App\Models\Post']
            ])->first();

        //Like or Dislike POST
        if($favorite){
            $favorite->delete();
            return response()->noContent();
        }else{
            $post->favorites()->create(['post_id' => $post->id, 'user_id' => $request->user()->id]);
            return response()->noContent(Response::HTTP_CREATED);
        }
    }

    public function storeOrDestroyFavoriteUser(Request $request, User $user)
    {
        $favorite = Favorite::where([
            ['user_id', $request->user()->id],
            ['favorite_id', $user->id],
            ['favorite_type', 'App\Models\User']
            ])->first();

        //Follow or Unfollow User
        if($favorite){
            $favorite->delete();
            return response()->noContent();
        }else{
            $user->favorites()->create(['post_id' => 0, 'user_id' => $request->user()->id]);
            return response()->noContent(Response::HTTP_CREATED);
        }
    }

}