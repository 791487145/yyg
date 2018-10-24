<?php

namespace App\Http\Middleware;

use App\Models\StoreUser;
use App\Models\SupplierBase;
use Closure;

class StoreAuthMiddleware
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

        $name = SupplierBase::SESSION_SUPPLIER;

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
