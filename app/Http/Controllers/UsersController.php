<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
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
        auth()->login($user);

        session()->flash('success', '欢迎，您将在这里凯一段新的旅程！');
        return redirect()->route('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
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
}
