<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Block;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    use GeneralTrait;
    public function search(Request $request)
    {
        try {
            $text = $request->text;
            $filter = $request->filter;
            if ($filter == 'users') {
            }
        } catch (\Exception $e) {
            // return $this->fail(__('messages.somthing went wrong'), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function searchSug(Request $request)
    {
        // try {
        $text = $request->text;
        $filter = $request->filter;
        if ($filter == 'users') {
            return $this->success('ok', $this->searchUsersSug($text));
        }
        if ($filter == 'posts') {
            return $this->success('ok', $this->searchPostsSug($request, $text));
        }
        // } catch (\Exception $e) {
        //     // return $this->fail(__('messages.somthing went wrong'), 500);
        //     return $this->fail($e->getMessage(), 500);
        // }
    }

    public function searchUsers($id)
    {
    }

    public function searchUsersSug($text)
    {
        $sugs = User::query()
            ->where('f_name', 'like', '%' . strtolower($text) . '%')
            ->orWhere('l_name', 'like', '%' . strtolower($text) . '%')
            ->orWhere('bio', 'like', '%' . strtolower($text) . '%')
            ->whereNotIn('id', Block::where('blocked', Auth::id())->get('user_id'))
            ->whereNotIn('id', User::query()->whereNotNull('deleted_at')->get('id'))
            ->withCount('followers')
            ->orderBy('followers_count', 'desc')
            ->limit(5)
            ->get();
        $data = [];
        foreach ($sugs as $sug) {
            $data[] = [
                'sug' => $sug->f_name . ' ' . $sug->l_name
            ];
        }
        return $data;
    }

    public function searchPosts($id)
    {
        //
    }

    public function searchPostsSug(Request $request, $text)
    {
        if ($request->user()->role_id != 1)
            $sugs = Post::query()
                ->where('text', 'like', '%' . strtolower($text) . '%')
                ->where('is_accepted', true)
                ->orWhereIn('user_id', User::where('f_name', 'like', '%' . strtolower($text) . '%')->get('id'))
                ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
                ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
                ->withCount('Likes')
                ->orderBy('Likes_count', 'desc')
                ->limit(5)
                ->get();
        elseif ($request->user()->role_id == 1)
            $sugs = Post::query()
                ->where(['text' => 'like', '%' . strtolower($text) . '%', 'is_accepted' => true])
                ->orWhereIn('user_id', User::where('f_name', 'like', '%' . strtolower($text) . '%')->get('id'))
                ->whereNot('type', 2)
                ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
                ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
                ->withCount('Likes')
                ->orderBy('Likes_count', 'desc')
                ->limit(5)
                ->get();
        $data = [];
        foreach ($sugs as $sug) {
            $data[] = [
                'sug' => Str::substr($sug->text, 1, 30) . ' ...'
            ];
        }
        return $data;
    }
}
