<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
        ]);

        $post = Post::create([
            'title' => Crypt::encrypt($request->title),
            "body" => Crypt::encrypt($request->body),
            "image" => $request->image,
        ]);
        $decrypt = array(
            'title'=>Crypt::decrypt($post->title),
            'body'=>Crypt::decrypt($post->body),
            'image'=>$post->image
        );
        if ($post) {
            return response()->json([
                'status' => true,
                "message" => "Post Created !",
                "data" => $decrypt
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
            $post->title = Crypt::encrypt($request->title);
            $post->body =Crypt::encrypt($request->body);
            $post->image = $request->image;
            $post->save();
            $decrypt = array(
                'title'=>Crypt::decrypt($post->title),
                'body'=>Crypt::decrypt($post->body),
                'image'=>$post->image
            );
            return response()->json([
                'status' => true,
                "message" => "Post Updated !",
                "data" => $decrypt
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
        $posts =[];
        foreach (Post::all() as $key) {
            $data = [
                'title'=>Crypt::decrypt($key->title),
                'body'=>Crypt::decrypt($key->body),
                'image'=>$key->image,
            ];
            array_push($posts,$data);
        }
        return response()->json([
            'status' => true,
            "message" => "Success !",
            "data" => $posts,
        ], 200);
    }

    public function find($id)
    {
        $post = Post::find($id);
        $decrypt = array(
            'title'=>Crypt::decrypt($post->title),
            'body'=>Crypt::decrypt($post->body),
            'image'=>$post->image
        );
        if ($post) {
            return response()->json([
                'status' => true,
                "message" => "Success !",
                "data" => $decrypt
            ], 200);
        }
        return response()->json([
            'status' => false,
            "message" => "Not Found !",
            "data" => ''
        ], 404);
    }
}
