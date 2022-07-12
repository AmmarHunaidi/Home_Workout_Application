<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Traits\GeneralTrait;

class PostsController extends Controller
{
    use GeneralTrait;
    public function index()
    {
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function update(Request $request, Post $post)
    {
    }

    public function destroy($id)
    {
        try {
            if ($post = Post::find($id)) {
                if (Post::where(['id' => $id, 'user_id' => Auth::id()])->first()) {
                    $post->delete();
                    Storage::deleteDirectory('public/images/users/' . Auth::id() . '/posts/' . $id);
                    return $this->success();
                }
                return $this->fail(__("messages.Access denied"), 401);
            }
            return $this->fail(__("messages.Not found"), 404);
        } catch (\Exception $e) {
            return $this->fail(__('messages.somthing went wrong'), 500);
        }
    }
}
