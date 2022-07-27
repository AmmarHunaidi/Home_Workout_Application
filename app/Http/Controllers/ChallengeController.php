<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Challenge;
use App\Models\ChallengeExcercise;
use App\Models\ChallengeReport;
use App\Models\ChallengeReview;
use App\Models\ChallengeSub;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Traits\GeneralTrait;
use phpDocumentor\Reflection\Types\Boolean;

class ChallengeController extends Controller
{
    use GeneralTrait;
    public function exList()
    {
        $data = [];
        $exs = ChallengeExcercise::all(['id', 'name', 'desc', 'img_path', 'ca']);
        foreach ($exs as $ex) {
            $data[] = [
                'id' => $ex->id,
                'name' => $ex->name,
                'ca' => $ex->ca,
                'img' => 'public/images/ChallengesEx/' . $ex->img_path
            ];
        }
        return $this->success('ok', $data);
    }

    public function index()
    {
        try {
            $blocks = Block::where('blocked', Auth::id())->get('user_id'); //users who blocked me
            $coaches_ids = Follow::query()
                ->where('follower_id', Auth::id())
                ->whereNotIn('following', $blocks)
                ->whereNotIn('following', User::query()->whereNotNull('deleted_at')->get('id'))
                ->get('following'); //users I following without who blocked me
            $chs = Challenge::query()
                ->where('end_time', '>', Carbon::now())
                ->whereIn('user_id', $coaches_ids)
                ->orWhere('user_id', Auth::id())
                ->orderByDesc('created_at')
                ->paginate(15);
            if ($chs->count() == 0) {
                $chs = Challenge::query()
                    ->where('end_time', '>', Carbon::now())
                    ->whereNotIn('user_id', $blocks)
                    ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
                    ->orderByDesc('created_at')
                    ->paginate(15);
            }
            return $this->success('ok', $this->chData($chs));
        } catch (\Exception $e) {
            // return $this->fail(__("messages.somthing went wrong"), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->count = (int)$request->count;
            $validator = Validator::make($request->only('name', 'desc', 'time', 'img', 'count', 'ex_id', 'end_time'), [
                'name' => ['string', 'min:2', 'max:100', 'required'],
                'desc' => ['string', 'min:2', 'max:1000', 'nullable'],
                'time' => ['boolean', 'required'],
                'img' => ['image', 'mimes:jpg,png,jpeg,gif,svg,bmp', 'max:4096', 'nullable'],
                'count' => ['integer', 'min:2', 'max:90000', 'required'],
                'ex_id' => ['required', 'exists:challenges_exercises,id'],
                'end_time' => ['required', 'string'],
            ]);
            if ($validator->fails())
                return $this->fail($validator->errors()->first(), 400);
            if (Carbon::parse($request->end_time)->lte(Carbon::now()->addDay())) {
                return $this->fail(__('messages.End time should be greater than ') . Carbon::now()->addDay()->format('Y-m-d'));
            }
            $ch = Challenge::create([
                'user_id' => Auth::id(),
                'ex_id' => $request->ex_id,
                'name' => $request->name,
                'desc' => $request->desc,
                'is_time' => $request->time,
                'end_time' => Carbon::parse($request->end_time),
            ]);
            if ($request->hasFile('img')) {
                $destination_path = 'public/images/users/';
                $image = $request->file('img');
                $randomString = Str::random(30);
                $image_name = Auth::id() . '/Challenges/' . $ch->id . '/' . $randomString . $image->getClientOriginalName();
                $path = $image->storeAs($destination_path, $image_name);
                $ch->img_path = $image_name;
                $ch->save();
            }
            return $this->success(__("messages.Challeng created"));
        } catch (\Exception $e) {
            // return $this->fail(__("messages.somthing went wrong"), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function show(Request $request)
    {
        //
    }

    public function showMy(Request $request)
    {
        //
    }

    public function showMySubs(Request $request)
    {
        //
    }

    public function update(Request $request, Challenge $challenge)
    {
        //
    }

    public function chData($chs)
    {
        $data = [];
        foreach ($chs as $ch) {
            $user = $ch->user()->first(['id', 'f_name', 'l_name', 'role_id', 'prof_img_url']);
            $url = $user->prof_img_url;
            if (!(Str::substr($url, 0, 4) == 'http')) {
                $url = 'storage/images/users/' . $url;
            }
            $is_sub = false;
            if (ChallengeSub::where(['ch_id' => $ch->id, 'user_id' => Auth::id()])->first())
                $is_sub = true;
            $desc = $ch->desc;
            if (Str::length($desc) > 50)
                $desc = Str::substr($desc, 0, 50)  . ' ...';
            $is_active = true;
            if (Carbon::parse($ch->end_time)->lte(Carbon::now()))
                $is_active = false;
            $my_count = 0;
            if ($meSub = ChallengeSub::where(['ch_id' => $ch->id, 'user_id' => Auth::id()])->first())
                $my_count = $meSub->count;
            $data[] = [
                'id' => $ch->id,
                'name' => (string) $ch->name,
                'desc' => (string) $desc,
                'img' => (string) 'storage/images/users/' . $ch->img_path,
                'end_time' => (string)Carbon::parse($ch->end_time)->utcOffset(config('app.timeoffset'))->format('Y/m/d g:i A'),
                'total_count' => (string)$ch->total_count,
                'my_count' => (string)$my_count,
                'is_time' => (bool)$ch->is_time,
                'created_at' => (string)Carbon::parse($ch->created_at)->utcOffset(config('app.timeoffset'))->format('Y/m/d g:i A'),
                'sub_count' => (string)$this->subCount($ch->id),
                'rate' => (string)$this->reviewCount($ch),
                'is_sub' => $is_sub,
                'is_active' => $is_active,
                'user_id' => $user->id,
                'user_img' => (string)$url,
                'user_name' => (string)$user->f_name . ' ' . $user->l_name,
                'role_id' => $user->role_id,
            ];
        }
        return $data;
    }

    public function destroy($id)
    {
        try {
            if ($ch = Challenge::find($id)) {
                if (Challenge::where(['id' => $id, 'user_id' => Auth::id()])->first()) {
                    $ch->delete();
                    Storage::deleteDirectory('public/images/users/' . Auth::id() . '/Challenges/' . $ch->id);
                    return $this->success(__('messages.deleted'));
                }
                return $this->fail(__("messages.Access denied"), 401);
            }
            return $this->fail(__("messages.Not found"), 404);
        } catch (\Exception $e) {
            // return $this->fail(__("messages.somthing went wrong"), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function report($id)
    {
        try {
            if ($ch = Challenge::find($id)) {
                if ($ch->user()->first()->deleted_at != Null)
                    return $this->fail(__("messages.Not found"));
                if (
                    ChallengeReport::query()->where(['ch_id' => $id, 'user_id' => Auth::id()])->count() < 2
                    && Carbon::parse($ch->end_time)->gt(Carbon::now())
                )
                    ChallengeReport::create([
                        'ch_id' => $id,
                        'user_id' => Auth::id()
                    ]);
                return $this->success();
            }
            return $this->fail(__("messages.Not found"), 404);
        } catch (\Exception $e) {
            // return $this->fail(__("messages.somthing went wrong"), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function review($id, $num, Request $request)
    {
        try {
            if ($ch = Challenge::find($id)) {
                if ($ch->user()->first()->deleted_at != Null)
                    return $this->fail(__("messages.Not found"));
                $stars = [1, 2, 3, 4, 5];
                if ($request->header('delete') == 'true' && Carbon::parse($ch->end_time)->gt(Carbon::now())) {
                    ChallengeReview::where(["user_id" => Auth::id(), "ch_id" => $id])->delete();
                    return $this->success('ok', [$this->reviewCount($ch)]);
                }
                if (in_array($num, $stars) && Carbon::parse($ch->end_time)->gt(Carbon::now())) {
                    ChallengeReview::updateOrCreate([
                        "user_id" => Auth::id(),
                        "ch_id" => $id
                    ], [
                        "stars" => $num
                    ]);
                    return $this->success('ok', [$this->reviewCount($ch)]);
                }
                return $this->fail(__('messages.somthing went wrong'));
            }
            return $this->fail(__("messages.Not found"), 404);
        } catch (\Exception $e) {
            // return $this->fail(__("messages.somthing went wrong"), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function sub($id)
    {
        try {
            $ch = Challenge::find($id);
            if ($ch && Carbon::parse($ch->end_time)->gt(Carbon::now())) {
                if ($ch->user()->first()->deleted_at != Null)
                    return $this->fail(__("messages.Not found"));
                if ($sub = ChallengeSub::where(['ch_id' => $id, 'user_id' => Auth::id()])->first()) {
                    $sub->delete();
                    return $this->success(__("messages.Unsubscribed"), ['subs' => $this->subCount($id)]);
                }
                ChallengeSub::create([
                    'ch_id' => $id,
                    'user_id' => Auth::id()
                ]);
                return $this->success(__("messages.Subscribed"), ['subs' => $this->subCount($id)]);
            }
            return $this->fail(__("messages.Not found"), 404);
        } catch (\Exception $e) {
            // return $this->fail(__("messages.somthing went wrong"), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function done($id, Request $request)
    {
        try {
            $request->count = (int)$request->count;
            $validator = Validator::make($request->only('count'), [
                'count' => ['integer', 'min:2', 'max:90000', 'required'],
            ]);
            if ($validator->fails())
                return $this->fail($validator->errors()->first(), 400);
            $ch = Challenge::find($id);
            if ($ch && Carbon::parse($ch->end_time)->gt(Carbon::now())) {
                if ($ch->user()->first()->deleted_at != Null)
                    return $this->fail(__("messages.Not found"));
                if ($sub = ChallengeSub::where(['ch_id' => $id, 'user_id' => Auth::id()])->first()) {
                    if ($request->count >= $sub->count) {
                        $sub->count = $request->count;
                        $sub->save();
                    }
                    return $this->success('ok', [(string)$sub->count]);
                }
            }
            return $this->fail(__("messages.Not found"), 404);
        } catch (\Exception $e) {
            // return $this->fail(__("messages.somthing went wrong"), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function subCount($id)
    {
        return ChallengeSub::query()->where('ch_id', $id)->count();
    }
    public function reviewCount($ch)
    {
        $total = $ch->reviews()->count(); //مقسوم عليه
        if ($total == 0)
            return '0';
        $revs = $ch->reviews()->get(['stars']); //التقيمات
        $totalCount = 0; //مقسوم
        foreach ($revs as $rev) {
            $totalCount += $rev->stars;
        }
        $count = $totalCount / $total;
        return (string)round($count, 1);
    }
}
