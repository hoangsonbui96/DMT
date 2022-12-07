<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Session;
use Illuminate\Support\Facades\Auth as Auths;
use DateTime;
use App\Http\Controllers\Admin\AdminController;

class  Authenticate extends Middleware
{
    protected $guards = [];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $timeNow = new DateTime();
        $this->authenticate($request, $guards);
        $path = \Request::segments();
        array_shift($path);
        if(!empty($path)&&(strpos(\Request::getRequestUri(), 'api') === false)){
            $NewSegment = config('settings.company')[0].'/'.$path[0];
        }
        if ((strpos(\Request::getRequestUri(), 'admin/index.php') == false)&&(!in_array($request->segment(1), config('settings.company')))&&isset($NewSegment)){
            return redirect()->to($NewSegment);
        }
        if (Session::get('checkPass') == 1 && url()->current() !== route('admin.viewLayoutChangePassword')&&url()->current() !== route('logout')&&url()->current() !== route('admin.changePassword')) {
            return redirect()->route('admin.viewLayoutChangePassword');
        }

        if(Auths::user()->expirationdate != null){
             if ($timeNow->format('Y-m-d') > Auths::user()->expirationdate && url()->current() !== route('logout')) {
                if(isset($guards[0]) && $guards[0] === "api") {
                    return AdminController::responseApi(405,'Tài kho?n h?t h?n');
                } else {
                    Session::forget('api-user');
                    Session::forget('checkPass');
                    Auths::logout();
                    return redirect()->route('login')->withErrors(['TheMessage'=>'Tài kho?n dã h?t h?n']);
                }
            }
        }
        return $next($request);
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        // die;
        if (strpos(\Request::getRequestUri(), 'api') != false) {
            return abort(403);
        }

        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}

