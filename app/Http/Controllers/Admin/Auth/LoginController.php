<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\AdminController;
use App\MasterData;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Model\PushToken;

class LoginController extends AdminController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->redirectTo = url()->previous();
        $this->middleware('guest')->except('logout');
    }


    public function username(){
        return 'username';
    }

    public function showLoginForm()
    {
        $ip_company = MasterData::where('DataValue', 'like', 'CD001')->first();
        $this->data['qrCode'] = true;
        if (!$ip_company) {
            $this->data['qrCode'] = false;
        }
        if ($ip_company->DataDescription != $this->getIp()) {
            $this->data['qrCode'] = false;
        }
        return view('admin.auth.login', $this->data);
    }

    protected function guard($guard = null)
    {
        return Auth::guard($guard);
    }

    public function logout()
    {
       	Session::forget('api-user');
        Session::forget('checkPass');
        Auth::logout();
        return redirect()->route('login')->withCookie(Cookie::forget('showNoti'));
    }

    protected function credentials(Request $request)
    {
        $array = $request->only($this->username(), 'password');
        $array = array_merge($array,['deleted_at' => null, 'active' => 1]);

        if (Auth::attempt($array)) {
            $user = Auth::user();
            $tokenResult = $user->createToken('Personal Access Token');
            Session::put('api-user', $tokenResult->accessToken);

            return $array;
        }
        else
        {
            return $this->sendFailedLoginResponse($request, 0);
        }
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if(preg_match(CHECK_PASSWORD, $request->input('password'))==0){
            Session::put('checkPass',1);
            return redirect()->route('admin.viewLayoutChangePassword');
        }
    }

//    protected function attemptLogin(Request $request)
//    {
//        return $this->guard('admin')->attempt(
//            $this->credentials($request), $request->filled('remember')
//        );
//    }
}
