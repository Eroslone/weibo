<?php
//用户登录控制器
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
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
        if(Auth::attempt($credentials,$request->has('remember'))){
            //登陆成功后的操作
            session()->flash('sucess','登陆成功，欢迎回来');
            return redirect()->intended($fallback);
        }else{
            //登录失败后的操作
            session()->flash('danger','邮箱密码不匹配，登陆失败');
            return redirect()->back()->withInput();
        }
    }
    public function destroy(){
        Auth::logout();
        session()->flash('success','您已成功退出！');
        return redirect('login');
    }
}
