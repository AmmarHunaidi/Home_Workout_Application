<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReport;
use App\Models\PostVote;
use App\Models\UserVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Traits\GeneralTrait;
use Exception;
use Throwable;

use function PHPUnit\Framework\throwException;

class PostsController extends Controller
{
    use GeneralTrait;
    public function index()
    {
    }

    public function storeNormal(Request $request)
    {
        try {
            // $request->media = json_decode($request->media);
            $validator = Validator::make($request->only('text', 'media'), [
                'text' => ['string', 'max:10000', 'nullable'],
            ]);
            if ($validator->fails())
                return $this->fail($validator->errors()->first(), 400);
        } catch (\Exception $e) {
            return $this->fail(__("messages.somthing went wrong"), 500);
        }
    }

    public function storepoll(Request $request)
    {
        try {
            $validator = Validator::make($request->only('text', 'type'), [
                'text' => ['string', 'required'],
                'type' => ['required', 'integer', 'between:2,3'],
            ]);
            if ($validator->fails())
                return $this->fail($validator->errors()->first(), 400);

            if ($request->type == 2) {
                return $this->storetype2($request);
            }

            if ($request->type == 3) {
                return $this->storetype3($request);
            }
            return $this->fail(__("messages.somthing went wrong"), 500);
        } catch (\Exception $e) {
            return $this->fail(__("messages.somthing went wrong"), 500);
        }
    }

    // tip post type
    public function storetype2(Request $request)
    {
        try {
            $validator = Validator::make($request->only('text'), [
                'text' => ['max:50'],
            ]);
            if ($validator->fails())
                return $this->fail($validator->errors()->first(), 400);

            $post = Post::create([
                'user_id' => Auth::id(),
                'text' => $request->text
            ]);
            $post->votes()->createMany([
                [
                    'post_id' => $post->id,
                    'vote' => 'Agree'
                ],
                [
                    'post_id' => $post->id,
                    'vote' => 'Disgree'
                ],
            ]);
            return $this->success();
        } catch (\Exception $e) {
            $post->delete();
            return $this->fail(__("messages.somthing went wrong"), 500);
        }
    }

    // public poll post type
    public function storetype3(Request $request)
    {
        try {
            // $request->votes = json_decode($request->votes);
            $validator = Validator::make($request->only('text', 'votes'), [
                'text' => ['max:100'],
                'votes' => ['array', 'required', 'between:2,10'],
                'votes.*' => ['string', 'between:1,36'],
            ]);
            if ($validator->fails())
                return $this->fail($validator->errors()->first(), 400);

            $post = Post::create([
                'user_id' => Auth::id(),
                'text' => $request->text
            ]);
            foreach ($request->votes as $vote) {
                PostVote::create([
                    'post_id' => $post->id,
                    'vote' => $vote
                ]);
            }
            return $this->success();
        } catch (\Exception $e) {
            $post->delete();
            return $this->fail(__("messages.somthing went wrong"), 500);
        }
    }

    public function show($id) //three types
    {
    }

    public function update(Request $request, $id) //three types
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

    public function vote($id, $vote_id)
    {
        try {
            if (!(($post = Post::find($id)) && ($postVote = PostVote::where(['psot_id' => $id, 'id' => $vote_id])))) {
                return $this->fail(__("messages.Not found"), 404);
            }
            if (!is_null($vote = UserVote::where(['user_id' => Auth::id(), 'vote_id' => $vote_id])->first())) {
                $vote->delete();
                return $this->success('ok', $this->getVotes($id));
            }
            $vote = UserVote::updateOrCreate([
                'user_id' => Auth::id(),
            ], [
                'vote_id' => $vote_id,
            ]);
            return $this->success('ok', $this->getVotes($id));
        } catch (\Exception $e) {
            $vote->delete();
            return $this->fail(__('messages.somthing went wrong'), 500);
        }
    }

    public function getVotes($id)
    {
        try {
            $votes = Post::find($id)->votes()->get('id');
            $allVotesCount = 0;
            foreach ($votes as $vote) {
                $allVotesCount += $vote->votes()->count();
            }
            foreach ($votes as $vote) {
                $thisVoteCount = $vote->votes()->count(); //How many users vote for this option
                $data[] = [
                    'vote_id' => $vote->id,
                    'rate' => (string)((int)(100 * ($thisVoteCount / $allVotesCount)))
                ];
            }
            return $data;
            return $data;
        } catch (\Exception $e) {
            return $this->fail(__('messages.somthing went wrong'), 500);
        }
    }

    public function report($id)
    {
        try {
            if ($post = Post::where('id', $id)->first()) {
                if (PostReport::query()->where(['post_id' => $post->id, 'user_id' => Auth::id()])->count() < 2)
                    PostReport::create([
                        'user_id' => Auth::id(),
                        'post_id' => $post->id,
                    ]);
                return $this->success();
            }
            return $this->fail(__("messages.Not found"));
        } catch (\Exception $e) {
            return $this->fail(__('messages.somthing went wrong'), 500);
        }
    }
}
