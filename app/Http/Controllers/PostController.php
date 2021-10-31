<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
        ]);

        $post = Post::create([
            'title' => $request->title,
            "body" => $request->body,
            "image" => $request->image,
        ]);

        if ($post) {
            return response()->json([
                'status' => true,
                "message" => "Post Created !",
                "data" => $post
            ], 201);
        }
        return response()->json([
            'status' => true,
            "message" => "Error !",
            "data" => ""
        ], 400);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
        ]);

        $post = Post::find($id);
        if ($post) {
            $post->title = $request->title;
            $post->body = $request->body;
            $post->image = $request->image;
            $post->save();
            return response()->json([
                'status' => true,
                "message" => "Post Updated !",
                "data" => $post
            ], 200);
        }
        return response()->json([
            'status' => false,
            "message" => "Not Found !",
            "data" => ''
        ], 404);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if ($post) {
            $post->delete();
            return response()->json([
                'status' => true,
                "message" => "Post Deleted !",
            ], 200);
        }
        return response()->json([
            'status' => false,
            "message" => "Not Found !",
            "data" => ''
        ], 404);
    }

    public function all()
    {
        $posts = Post::all();
        return response()->json([
            'status' => true,
            "message" => "Success !",
            "data" => $posts,
        ], 200);
    }

    public function find($id)
    {
        $post = Post::find($id);

        if ($post) {
            return response()->json([
                'status' => true,
                "message" => "Success !",
                "data" => $post
            ], 200);
        }
        return response()->json([
            'status' => false,
            "message" => "Not Found !",
            "data" => ''
        ], 404);
    }
}
