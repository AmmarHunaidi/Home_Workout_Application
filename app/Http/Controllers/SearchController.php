<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Block;
use App\Models\Challenge;
use App\Models\Diet;
use App\Models\DietMeal;
use App\Models\DietReview;
use App\Models\FavoriteDiet;
use App\Models\FavoriteWorkout;
use App\Models\Post;
use App\Models\Role;
use App\Models\Workout;
use App\Models\WorkoutCategorie;
use App\Models\WorkoutReview;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Traits\GeneralTrait;
use App\Traits\CaloriesTrait;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    use GeneralTrait, CaloriesTrait;
    public function search(Request $request)
    {
        try {
            $text = $request->text;
            $filter = $request->filter;
            if (!is_null($request->text)) {
                if ($filter == 'Users') {
                    return $this->success('ok', $this->searchUsers($text));
                }
                if ($filter == 'Posts') {
                    return $this->success('ok', $this->searchPosts($text, $request));
                }
                if ($filter == 'Challenges') {
                    return $this->success('ok', $this->searchCh($text));
                }
                if ($filter == 'Diets') {
                    return $this->success('ok', $this->searchDiets($text));
                }
                if ($filter == 'Workouts') {
                    return $this->success('ok', $this->searchWo($text));
                }
                return $this->fail(__("messages.somthing went wrong"));
            }
            return $this->success();
        } catch (\Exception $e) {
            // return $this->fail(__('messages.somthing went wrong'), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function searchSug(Request $request)
    {
        try {
            $text = $request->text;
            $filter = $request->filter;
            if (!is_null($request->text)) {
                if ($filter == 'Users') {
                    return $this->success('ok', $this->searchUsersSug($text));
                }
                if ($filter == 'Posts') {
                    return $this->success('ok', $this->searchPostsSug($request, $text));
                }
                if ($filter == 'Challenges') {
                    return $this->success('ok', $this->searchChSug($text));
                }
                if ($filter == 'Diets') {
                    return $this->success('ok', $this->searchDietsSug($text));
                }
                if ($filter == 'Workouts') {
                    return $this->success('ok', $this->searchWoSug($text));
                }
                return $this->fail(__("messages.somthing went wrong"));
            }
            return $this->success();
        } catch (\Exception $e) {
            // return $this->fail(__('messages.somthing went wrong'), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    //users
    public function searchUsers($text)
    {
        $users = User::query()->where(function ($query) use ($text) {
            $query->where('f_name', 'like', '%' . strtolower($text) . '%')
                ->orWhere('l_name', 'like', '%' . strtolower($text) . '%')
                ->orWhereRaw("concat(f_name, ' ', l_name) like '%$text%' ");
        })
            ->whereKeyNot(Auth::id())
            ->whereNotIn('id', Block::where('blocked', Auth::id())->get('user_id'))
            ->whereNotIn('id', User::query()->whereNotNull('deleted_at')->get('id'))
            ->whereNotIn('role_id', [1, 4])
            ->withCount('followers')
            ->orderBy('followers_count', 'desc')
            ->paginate(15, ['id', 'f_name', 'l_name', 'role_id', 'prof_img_url', 'country', 'birth_date']);
        //
        $data = [];
        foreach ($users as $user) {
            $url = $user->prof_img_url;
            if (!(Str::substr($url, 0, 4) == 'http')) {
                $url = 'storage/images/users/' . $url;
            }
            $role = '(💪)';
            if ($user->role_id == 3) {
                $role = '(☘️)';
            } elseif ($user->role_id == 5)
                $role = '(👑)';
            $data[] = [
                'id' => $user->id,
                'fname' => $user->f_name,
                'lname' => $user->l_name,
                'role_id' => $user->role_id,
                'role' => $role,
                // 'country' => $user->country,
                // 'age' => (string)Carbon::parse($user->birth_date)->age,
                'img' => $url
            ];
        }
        return $data;
    }

    public function searchUsersSug($text)
    {
        // return Auth::user();
        $sugs = User::query()->where(function ($query) use ($text) {
            $query->where('f_name', 'like', '%' . strtolower($text) . '%')
                ->orWhere('l_name', 'like', '%' . strtolower($text) . '%')
                ->orWhereRaw("concat(f_name, ' ', l_name) like '%$text%' ");
        })
            ->whereKeyNot(Auth::id())
            ->whereNotIn('id', Block::where('blocked', Auth::id())->get('user_id'))
            ->whereNotIn('id', User::query()->whereNotNull('deleted_at')->get('id'))
            ->whereNotIn('role_id', [1, 4])
            // ->orWhere('bio', 'like', '%' . strtolower($text) . '%')
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


    //posts
    public function searchPosts($text, $request)
    {
        if ($request->user()->role_id != 1)
            $posts = Post::query()
                ->where('text', 'like', '%' . strtolower($text) . '%')
                ->where('is_accepted', true)
                ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
                ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
                ->withCount('Likes')
                ->orderBy('Likes_count', 'desc')
                ->paginate(2, ['id', 'user_id', 'text', 'type', 'created_at']);
        elseif ($request->user()->role_id == 1)
            $posts = Post::query()
                ->where('text', 'like', '%' . strtolower($text) . '%')
                ->where('is_accepted', true)
                ->whereNot('type', 2)
                ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
                ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
                ->withCount('Likes')
                ->orderBy('Likes_count', 'desc')
                ->paginate(2, ['id', 'user_id', 'text', 'type', 'created_at']);
        return app('App\Http\Controllers\PostsController')->postData($posts);
    }

    public function searchPostsSug(Request $request, $text)
    {
        if ($request->user()->role_id != 1)
            $sugs = Post::query()
                ->where('text', 'like', '%' . strtolower($text) . '%')
                ->where('is_accepted', true)
                ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
                ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
                ->withCount('Likes')
                ->orderBy('Likes_count', 'desc')
                ->limit(5)
                ->get();
        elseif ($request->user()->role_id == 1)
            $sugs = Post::query()
                ->where('text', 'like', '%' . strtolower($text) . '%')
                ->where('is_accepted', true)
                ->whereNot('type', 2)
                ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
                ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
                ->withCount('Likes')
                ->orderBy('Likes_count', 'desc')
                ->limit(5)
                ->get();
        $data = [];
        foreach ($sugs as $sug) {
            $sug = Str::substr($sug->text, 0, 30);
            if (Str::length($sug) > 30)
                $sug = $sug . ' ...';
            $data[] = [
                'sug' => $sug
            ];
        }
        return $data;
    }


    //chs
    public function searchChSug($text)
    {
        $sugs = Challenge::query()
            ->where('name', 'like', '%' . strtolower($text) . '%')
            ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
            ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
            ->withCount('reviews')
            ->orderBy('reviews_count', 'desc')
            ->limit(5)
            ->get('name');
        $data = [];
        foreach ($sugs as $sug) {
            $sug = Str::substr($sug->name, 0, 30);
            if (Str::length($sug) > 30)
                $sug = $sug . ' ...';
            $data[] = [
                'sug' => $sug
            ];
        }
        return $data;
    }

    public function searchCh($text)
    {
        $chs = Challenge::query()
            ->where('name', 'like', '%' . strtolower($text) . '%')
            ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
            ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
            ->withCount('reviews')
            ->orderBy('reviews_count', 'desc')
            ->paginate(15);

        return app('App\Http\Controllers\ChallengeController')->chData($chs, request());
    }

    //Diets
    public function searchDietsSug($text)
    {
        $sugs = Diet::query()
            ->where('name', 'like', '%' . strtolower($text) . '%')
            ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
            ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
            // ->withCount('reviews')
            // ->orderBy('reviews_count', 'desc')
            ->limit(5)
            ->get('name');
        $data = [];
        foreach ($sugs as $sug) {
            $sug = Str::substr($sug->name, 0, 30);
            if (Str::length($sug) > 30)
                $sug = $sug . ' ...';
            $data[] = [
                'sug' => $sug
            ];
        }
        return $data;
    }

    public function searchDiets($text)
    {
        $diets = Diet::query()->where('name', 'like', '%' . strtolower($text) . '%')
            ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
            ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
            ->withCount('reviews')
            ->orderBy('reviews_count', 'desc')
            ->paginate(15);
        $data = [];
        foreach ($diets as $diet) {
            $save = false;
            if (FavoriteDiet::where(['user_id' => Auth::id(), 'diet_id' => $diet->id])->exists())
                $save = true;
            DietReview::where(['diet_id' => $diet->id, 'user_id' => Auth::id()])->exists() == true ? $rev = true : $rev = false;
            $user = User::where('id', $diet->created_by)->first(['id', 'f_name', 'l_name', 'prof_img_url']);
            $url = $user->prof_img_url;
            if (!(Str::substr($url, 0, 4) == 'http')) {
                $url = 'storage/images/users/' . $url;
            }
            $created_by = [
                'id' => $user->id,
                'f_name' => $user->f_name,
                'l_name' => $user->l_name,
                'prof_img_url' => $url
            ];
            $data[] = [
                'id' => $diet->id,
                'name' => $diet->name,
                'created_by' => $created_by,
                'created_at' => (string)Carbon::parse($diet->created_at)->utcOffset(config('app.timeoffset'))->format('Y/m/d g:i A'),
                'meal_count' => DietMeal::where('diet_id', $diet->id)->count(),
                'saved' => $save,
                'is_reviewed' => $rev
            ];
        }
        return $data;
    }

    //Workouts
    public function searchWoSug($text)
    {
        $sugs = Workout::query()
            ->where('name', 'like', '%' . strtolower($text) . '%')
            ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
            ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
            ->orderByDesc('review_count')
            ->limit(5)
            ->get('name');
        $data = [];
        foreach ($sugs as $sug) {
            $sug = Str::substr($sug->name, 0, 30);
            if (Str::length($sug) > 30)
                $sug = $sug . ' ...';
            $data[] = [
                'sug' => $sug
            ];
        }
        return $data;
    }

    public function searchWo($text)
    {
        $wos = Workout::query()
            ->where('name', 'like', '%' . strtolower($text) . '%')
            ->whereNotIn('user_id', Block::where('blocked', Auth::id())->get('user_id'))
            ->whereNotIn('user_id', User::query()->whereNotNull('deleted_at')->get('id'))
            ->orderByDesc('review_count')
            ->paginate(15);
        $data = [];
        foreach ($wos as $wo) {
            $save = false;
            if (FavoriteWorkout::where(['user_id' => Auth::id(), 'workout_id' => $wo->id])->exists()) $save = true;
            WorkoutReview::where(['user_id' => Auth::id(), 'workout_id' => $wo->id])->exists() == true ? $review = true : $review = false;
            $user = User::where('id', $wo->user_id)->first(['id', 'f_name', 'l_name', 'prof_img_url']);
            $url = $user->prof_img_url;
            if (!(Str::substr($url, 0, 4) == 'http')) {
                $url = 'storage/images/users/' . $url;
            }
            $data[] = [
                'id' => $wo->id,
                'name' => (string)$wo->name,
                'predicted_burnt_calories' => $wo->predicted_burnt_calories,
                'length' => $wo->length,
                'excersise_count' => $wo->excersise_count,
                'equipment' => $wo->equipment,
                'difficulty' => $wo->difficulty,
                'user' => [
                    'id' => $user->id,
                    'f_name' => $user->f_name,
                    'l_name' => $user->f_name,
                    'prof_img_url' => $url,
                ],
                'workout_image_url' => 'storage/images/workout/' . $wo->workout_image_url,
                'description' => $wo->description,
                'categorie_name' => [
                    'name' => WorkoutCategorie::where('id', $wo->categorie_id)->first('name')->name
                ],
                'review_count' => $wo->review_count,
                'created_at' => (string)Carbon::parse($wo->created_at)->utcOffset(config('app.timeoffset'))->format('Y/m/d g:i A'),
                'saved' => $save,
                'is_reviewed' => $review
            ];
        }
        return $data;
    }
}
