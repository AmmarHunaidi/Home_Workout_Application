<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostLikesController extends Controller
{
    use GeneralTrait;
    public function like($id, $type)
    //$id is post id // $type is like type
    {
        $likeTypes = [1, 2, 3, 4, 5];
        if (in_array($type, $likeTypes)) {
            PostLike::updateOrCreate([
                "user_id" => Auth::id(),
                "post_id" => Post::find($id)->first()->id
            ], [
                "type" => $type
            ]);
            return $this->success('ok', $this->likeNum($id));
        }
        return $this->fail(__('messages.somthing went wrong'));
    }

    public function unlike($id)
    {
        PostLike::where(['post_id' => $id, 'user_id' => Auth::id()])->delete();
        return $this->success('ok', $this->likeNum($id));
    }

    public function likeNum($id)
    {
        $data = [
            "type1" => PostLike::where(['post_id' => $id, 'type' => 1])->count(),
            "type2" => PostLike::where(['post_id' => $id, 'type' => 2])->count(),
            "type3" => PostLike::where(['post_id' => $id, 'type' => 3])->count(),
            "type4" => PostLike::where(['post_id' => $id, 'type' => 4])->count(),
            "type5" => PostLike::where(['post_id' => $id, 'type' => 5])->count(),
        ];
        return $data;
    }

    public function likeList($id)
    {
        $users = [];
        foreach (PostLike::query()->where('post_id', $id)->paginate(30, ['user_id']) as $user_id) {
            $user = User::find($user_id->user_id);
            $url = $user->prof_img_url;
            if (!(Str::substr($url, 0, 4) == 'http')) {
                $url = 'storage/images/users/' . $url;
            }
            $users[] = [
                "id" => $user->id,
                "name" => $user->f_name . ' ' . $user->l_name,
                "img" => $url,
                "type" => PostLike::where(['post_id' => $id, 'user_id' => $user->id])->first()->type
            ];
        }
        $data = [
            "likeNum" => ['total' => Post::where('id', $id)->first()->likes()->count(), 'types' => $this->likeNum($id)],
            "users" => $users,
        ];
        return $this->success('ok', $data);
    }
}
