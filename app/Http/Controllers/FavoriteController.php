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
        $users = Favorite::where('favorite_type', 'App\Models\User')->where('user_id', $request->user()->id)->get();
        $posts = Favorite::where('favorite_type', 'App\Models\Post')->where('user_id', $request->user()->id)->get();

        for ($i=0; $i < count($posts); $i++) {
            $post = Post::findOrFail($posts[$i]->favorite_id);
            $owner = User::findOrFail($post->user_id);
            $p[$i] = [
                "id" => $post->id,
                "title" => $post->title,
                "body" => $post->body,
            ];
            $p[$i]['user'] = [
                "id" => $owner->id,
                "name" => $owner->name,
            ];
        }

        for ($i=0; $i < count($users); $i++) {
            $user = User::findOrFail($users[$i]->favorite_id);
            $u[$i] = [
                "id" => $user->id,
                "name" => $user->name,
            ];
        }

        return new FavoriteResource(['posts' => $p, 'users' => $u]);
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
        //Not able to favorite himself
        if($request->user()->id === $user->id){
            return response()->noContent();
        }

        $favorite = Favorite::where([
            ['user_id', $request->user()->id],
            ['favorite_id', $user->id],
            ['favorite_type', 'App\Models\User']
            ])->first();

        //Follow or Unfollow User and check owner
        if($favorite && $favorite->user_id === $request->user()->id){
            $favorite->delete();
            return response()->noContent();
        }else{
            $user->favorites()->create(['post_id' => 0, 'user_id' => $request->user()->id]);
            return response()->noContent(Response::HTTP_CREATED);
        }
    }

}