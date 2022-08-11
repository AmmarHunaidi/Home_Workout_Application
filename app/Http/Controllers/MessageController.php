<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Str;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Carbon\Carbon;

class MessageController extends Controller
{
    use GeneralTrait;
    public function index(Request $request)
    {
        try { //user id
            $id = (int)$request->header('id');
            if (is_null($user = User::find($id))) {
                return $this->fail(__('messages.Not found'));
            }
            if ($id == Auth::id())
                return $this->fail(__("messages.You can not make a chat with yourself"));

            $chat = Chat::query()->where(['user_id' => Auth::id(), 'to_user_id' => $id])
                ->orWhere(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->where('to_user_id', Auth::id());
                })
                ->first('id');
            if (!$chat)
                $chat = Chat::create([
                    'user_id' => Auth::id(),
                    'to_user_id' => $id
                ]);
            //
            $url = $user->prof_img_url;
            if (!(Str::substr($url, 0, 4) == 'http')) {
                $url = 'storage/images/users/' . $url;
            }
            $msgs = Message::query()->where('chat_id', $chat->id)
                ->orderByDesc('created_at')
                ->get(['id', 'message', 'user_id', 'created_at']);
            $msgsArr = [];
            foreach ($msgs as $msg) {
                $msgsArr[] = [
                    'id' => $msg->id,
                    'message' => (string)$msg->message,
                    'user_id' => $msg->user_id,
                    'created_at' => (string)Carbon::parse($msg->created_at)->utcOffset(config('app.timeoffset'))->format('Y/m/d g:i A'),
                ];
            }
            $data = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->f_name . ' ' . $user->l_name,
                    'img' => $url,
                    'role_id' => $user->role_id,
                    'role' => Role::find($user->role_id)->name,
                ],
                'msgs' => [
                    $msgsArr
                ]
            ];
            return $this->success('ok', $data);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
            // return $this->fail(__("messages.somthing went wrong"));
        }
    }

    public function store(Request $request)
    {
        try {
            $request->chat_id = (int)$request->chat_id;
            $validator = Validator::make($request->only('chat_id', 'message'), [
                'message' => ['required', 'min:1', 'max:2000', 'string'],
                'chat_id' => ['required', 'exists:chats,id']
            ]);
            if ($validator->fails())
                return $this->fail($validator->errors()->first(), 400);
            //
            if (Chat::where(['id' => $request->chat_id, 'blocked' => true])) {
                return $this->fail(__('messages.You can not send messages to a blocked chat'));
            }
            $msg = Message::create([
                'message' => $request->message,
                'chat_id' => $request->chat_id,
                'user_id' => Auth::id(),
            ]);
            event(new messageEvent($msg->id, Auth::id(), $request->chat_id, $request->messages, $msg->created_at));
            return $this->success();
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
            // return $this->fail(__("messages.somthing went wrong"));
        }
    }

    public function destroy($id)
    {
        try {
            if ($message = Message::where(['id' => $id, 'user_id' => Auth::id()])->first()) {
                $message->message = 'Deleted message';
                $message->save();
                return $this->success();
            }
            return $this->fail(__("messages.Not found"));
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
            // return $this->fail(__("messages.somthing went wrong"));
        }
    }

    public function chatList()
    {
        try {
            $id = Auth::id();
            $chats = Chat::query()->where(function ($query) use ($id) {
                $query->where('user_id', $id)
                    ->orWhere('to_user_id', $id);
            })
                ->paginate(20);
            $data = [];
            foreach ($chats as $chat) {
                if ($chat->user_id == Auth::id()) {
                    $user = User::where('id', $chat->to_user_id)->first();
                } elseif ($chat->to_user_id == Auth::id()) {
                    $user = User::where('id', $chat->user_id)->first();
                }
                if (is_null($chat->messages()->first()))
                    continue;
                //
                $url = $user->prof_img_url;
                if (!(Str::substr($url, 0, 4) == 'http')) {
                    $url = 'storage/images/users/' . $url;
                }
                $data[] = [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->f_name . ' ' . $user->l_name,
                        'img' => $url,
                        'role_id' => $user->role_id,
                        'role' => Role::find($user->role_id)->name,
                    ],
                    'chat' => [
                        'id' => $chat->id,
                        'from' => $chat->user_id,
                        'to' => $chat->to_user_id,
                        'last_msg' => (string)Message::where('chat_id', $chat->id)->get('message')->last()->message,
                        'last_date' => (string)Carbon::parse(Message::where('chat_id', $chat->id)->get('created_at')->last()->created_at)->utcOffset(config('app.timeoffset'))->format('Y/m/d g:i A'),
                    ]
                ];
            }
            return $this->success('ok', $data);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
            // return $this->fail(__("messages.somthing went wrong"));
        }
    }

    public function block(Request $request)
    {
        try {
            if (!in_array($request->user()->role_id, [2, 3])) {
                return $this->fail(__("messages.Access denied"));
            }
            $id = (int)$request->header('id');
            if (is_null(Chat::find($id))) {
                return $this->fail(__('messages.Not found'));
            }
            if (
                $chat = Chat::query()->where(function ($query) use ($id) {
                    $query->where(['id' => $id, 'user_id' => Auth::id()])
                        ->orWhere('to_user_id', Auth::id())
                        ->where('id', $id);
                })
                ->first()
            ) {
                if ($chat->blocked == 0)
                    $chat->blocked = 1;
                elseif ($chat->blocked == 1)
                    $chat->blocked = 0;
                $chat->save();
                return $this->success();
            }
            return $this->fail(__("messages.Not found"));
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
            // return $this->fail(__("messages.somthing went wrong"));
        }
    }
}
