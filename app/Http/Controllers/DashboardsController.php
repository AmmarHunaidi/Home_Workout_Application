<?php

namespace App\Http\Controllers;

use App\Models\CV;
use App\Models\Role;
use App\Models\Post;
use App\Models\PostCommentReport;
use App\Models\PostComments;
use App\Models\PostReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendPostAcceptionNotiJob;
use App\Traits\GeneralTrait;
use App\Traits\EmailTrait;

class DashboardsController extends Controller
{
    use GeneralTrait;
    public function index()
    {
        try {
            $UnReviewdPosts = Post::query()->where('is_reviewed', false)->count();
            if ($UnReviewdPosts > 100)
                $UnReviewdPosts = '+100';
            $UnReviewdCVs = CV::query()->where('acception', false)->count();
            if ($UnReviewdCVs > 100)
                $UnReviewdCVs = '+100';
            $ReportedPosts = PostReport::query()->distinct('post_id')->count();
            if ($ReportedPosts > 100)
                $ReportedPosts = '+100';
            $ReportedPostComments = PostCommentReport::query()->distinct('comment_id')->count();
            if ($ReportedPostComments > 100)
                $ReportedPostComments = '+100';

            $data = [
                'posts' => (string)$UnReviewdPosts,
                'Reported_Posts' => (string)$ReportedPosts,
                'Reported_Comments' => (string)$ReportedPostComments,
                'CVs' => (string)$UnReviewdCVs,
            ];

            return $this->success('ok', $data);
        } catch (\Exception $e) {
            // return $this->fail(__('messages.somthing went wrong'), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function CVsDashboard(Request $request)
    {
        try {
            $data = [];
            $CVs = CV::query()->where('acception', false)->paginate(30);
            foreach ($CVs as $cv) {
                $user = $cv->user;
                $url = $user->prof_img_url;
                if (!(Str::substr($url, 0, 4) == 'http')) {
                    $url = 'storage/images/users/' . $url;
                }
                $data[] = [
                    'id' => $cv->id,
                    'user_id' => (string)$cv->user_id,
                    'user_name' => (string)$user->f_name . ' ' . $user->l_name,
                    'user_img' => (string)$url,
                    'country' => (string)$user->country,
                    'asked_role' => (string)Role::find($cv->role_id)->name,
                    'date' => (string)$cv->created_at->format('Y-m-d H:i:s'),
                    'desc' => (string)$cv->description,
                    'cv_path' => (string)'storage/images/users/' . $cv->cv_path,
                ];
            }
            return $this->success('ok', $data);
        } catch (\Exception $e) {
            // return $this->fail(__('messages.somthing went wrong'), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function PostsDashboard(Request $request)
    {
        try {
            $data = [];
            $posts = Post::query()->where('is_reviewed', false)->paginate(10);
            $data = app('App\Http\Controllers\PostsController')->postData($posts);
            return $this->success('ok', $data);
        } catch (\Exception $e) {
            // return $this->fail(__('messages.somthing went wrong'), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function ReportedPosts(Request $request)
    {
        try {
            $data = [];
            $posts = Post::query()
                ->whereHas('reports')
                ->withCount('reports')
                ->orderBy('reports_count', 'desc')
                ->paginate(10);

            foreach ($posts as $post) {
                $data[] = [
                    'reports' => PostReport::query()->where('post_id', $post->id)->count(),
                    'post' => app('App\Http\Controllers\PostsController')->postData([$post])
                ];
            }
            return $this->success('ok', $data);
        } catch (\Exception $e) {
            // return $this->fail(__('messages.somthing went wrong'), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function ReportedComments(Request $request)
    {
        try {
            $data = [];
            $comments = PostComments::query()
                ->whereHas('reports')
                ->withCount('reports')
                ->orderBy('reports_count', 'desc')
                ->paginate(20);
            foreach ($comments as $comment) {
                $user = User::find($comment->user_id);
                $url = $user->prof_img_url;
                if (!(Str::substr($url, 0, 4) == 'http')) {
                    $url = 'storage/images/users/' . $url;
                }
                $data[] = [
                    'reports' => PostCommentReport::query()->where('comment_id', $comment->id)->count(),
                    "user_id" => $user->id,
                    "name" => $user->f_name . ' ' . $user->l_name,
                    "img" => $url,
                    "comment_id" => $comment->id,
                    "comment" => $comment->text,
                    'created_at' => (string)Carbon::parse($comment->created_at)->utcOffset(config('app.timeoffset'))->format('Y/m/d g:i A')
                ];
            }
            return $this->success('ok', $data);
        } catch (\Exception $e) {
            // return $this->fail(__('messages.somthing went wrong'), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function AcceptPost(Request $request, $id) //accept refuse post and delete reported one
    {
        try {
            $acc = $request->header('acc');
            if ($post = Post::where(['id' => $id])->first()) {
                if ($acc == 'true') {
                    if ($post->is_reviewed == false)
                        dispatch(new SendPostAcceptionNotiJob($post->user, (string)Str::substr($post->text, 0, 40), true));
                    $post->is_reviewed = true;
                    $post->is_accepted = true;
                    $post->reports()->delete();
                    $post->save();
                } elseif ($acc == 'false') {
                    if ($post->is_reviewed == false)
                        dispatch(new SendPostAcceptionNotiJob($post->user, Str::substr($post->text, 0, 40), false));
                    $post->is_reviewed = true;
                    $post->is_accepted = false;
                    $post->reports()->delete();
                    $post->save();
                }
                return $this->success();
            }
            return $this->fail(__("messages.Not found"));
        } catch (\Exception $e) {
            // return $this->fail(__('messages.somthing went wrong'), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function AcceptCommentReport(Request $request, $id) //accept refuse Comment report
    {
        try {
            $acc = $request->header('acc');
            if ($comments = PostCommentReport::where(['comment_id' => $id])->first()) {
                if ($acc == 'true') {
                    $comments->comment()->delete();
                } elseif ($acc == 'false') {
                    $comments->delete();
                }
                return $this->success();
            }
            return $this->fail(__("messages.Not found"));
        } catch (\Exception $e) {
            // return $this->fail(__('messages.somthing went wrong'), 500);
            return $this->fail($e->getMessage(), 500);
        }
    }
}
