<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SessionsController extends Controller
{
    public function create()
    {
        return view('sessions.create');
    }
    public function store(Request $request)
    {
        //先判断输入的格式是否正确
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
        //在数据库中进行匹配验证
        if(Auth::attempt($credentials)){
            //登陆成功后的操作
            session()->flash('sucess','登陆成功，欢迎回来');
            return redirect()->route('users.show',[Auth::user()]);
        }else{
            //登录失败后的操作
            session()->flash('danger','邮箱密码不匹配，登陆失败');
            return redirect()->back()->withInput();
        }
        return;
    }
}
