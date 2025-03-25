<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => [
                'show', 'create', 'store', 'index', 'confirmEmail'
            ]
        ]);

        $this->middleware('guest', [
            'only' => [
                'create'
            ]
        ]);
    }

    public function index() {
        $users = User::paginate(6);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        $statuses = $user->statuses()->latest()->paginate(10);
        return view('users.show', compact('user', 'statuses'));
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|unique:users|max:50|min:3',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // 自动登录
        //auth()->login($user);

        // 发送激活邮件
        $this->sendEmailConfirmationTo($user);

        session()->flash('success', '验证邮件已经发送到您的注册邮箱上，请注意查收！');
        return redirect('/');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        $updateName = '';
        $validateData = [
            'password' => 'nullable|confirmed|min:6'
        ];
        if ($request->get('name') != $user->name) {
            $updateName = $request->get('name');
            $validateData['name'] = 'required|unique:users|max:50|min:3';
        }

        $this->validate($request, $validateData);

        $data = [];
        if ($updateName) {
            $data['name'] = $updateName;
        }
        if ($request->get('password')) {
            $data['password'] = bcrypt($request->get('password'));
        }
        if ($data) {
            $user->update($data);
        }

        // 提示
        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', compact('user'));
    }

    public function destroy(User $user) {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    public function confirmEmail($token) {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', compact('user'));
    }

    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
