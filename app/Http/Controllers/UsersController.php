<?php
//用户注册相关
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        $statuses = $user->statuses()->orderBy('created_at','desc')->paginate(10);
        return view('users.show', compact('user','statuses'));
    }
//对用户提交的数据进行验证
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        //用户注册成功后自动登录
        /*Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);*/
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message-->to($to)->subject($subject);
        });
    }
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
    //用户资料修改页
    public function edit(User $user){
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }
    //用户资料修改逻辑
    public function update(User $user,Request $request){
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user);
    }
    //权限过滤，未登录、已登录只能访问指定路由
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store','index', 'confirmEmail']//这里为啥已登录不能查看用户列表？
        ]);
        $this->middleware('guest', [
            'only' => ['create']
        ]);
        // 限流 一个小时内只能提交 10 次请求；
        $this->middleware('throttle:10,60', [
            'only' => ['store']
        ]);
    }
    public function index(){
        $users = User::paginate(6);
        return view('users.index',compact('users'));
    }
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
}