<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function import($start, $limit)
    {
        $url = 'https://jsonplaceholder.typicode.com/users?_start='.urlencode($start).'&_limit='.urlencode($limit);

        $promise = Http::async()
        ->get($url)
            ->then(
                function ($response) {
                    return $response->body();
                })
                ->then(
                    function ($json) {
                        return $json;
                    });

        $data = json_decode($promise->wait());

        foreach ($data as $user) {
            User::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => \Hash::make('abcde12345'),
            ]);
        }

        return response()->noContent();

    }
}