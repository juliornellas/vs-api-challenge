<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\DestroyPostRequest;
use App\Jobs\SendEmailJob;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;


/**
 * @group Posts
 *
 * API endpoints for managing posts
 */
class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->orderByDesc('created_at')->get();
        return PostResource::collection($posts);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $formFields = $request->all();
        $formFields['user_id'] = $user->id;

        if($request->hasFile('image')){
            $formFields['image'] = $request->file('image')->store('images','public');
        }

        $post = Post::create($formFields);

        $followers = Favorite::where('favorite_type', 'App\Models\User')->where('favorite_id', $post->user_id)->get();

        foreach ($followers as $follow) {
            $user = User::find($follow->user_id);

            $data = [
                'title' => $post->title,
                'body' => $post->body,
                'name' => $user->name,
                'email' => $user->email,
                // 'email' => 'juliornellas@gmail.com',
            ];

            SendEmailJob::dispatch($data);
        }

        return new PostResource($post);
    }

    public function show(Post $post)
    {
        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
        ]);

        return new PostResource($post);
    }

    public function destroy(DestroyPostRequest $request, Post $post)
    {
        $post->delete();

        return response()->noContent();
    }
}