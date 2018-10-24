<?php

namespace App\Http\Middleware;

use App\Models\StoreUser;
use App\Models\SupplierBase;
use App\Models\TaBase;
use Closure;

class TravelAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $name = TaBase::SESSION_TA;
        $user = \Session::get($name, "");

        if ($request->isMethod('post') && !$user) {
            return response()->json(array('ret'=>'no', 'msg'=>'请登录后操作！'));
        }

        if (!$user) {
            return \Redirect::to("/auth/login");
        }

        return $next($request);
    }
}
