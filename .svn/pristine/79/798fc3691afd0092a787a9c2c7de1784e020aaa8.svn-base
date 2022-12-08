<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class OptimizeController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
    }

    public function index(Request $request){
        Artisan::call('storage:link', []);
        Artisan::call('optimize', []);
        echo "The site is optimized! Please wait for redirect back.";
        echo "
            <script>
                setTimeout(function(){
                    window.history.go(-1);
                }, 3000);
            </script>
        ";
        // return $this->viewAdminLayout('optimize', $this->data);
        // return redirect()->back();
    }
}
