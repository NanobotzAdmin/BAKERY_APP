<?php

namespace App\Http\Middleware;

use Closure;

class LogUserCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$role)
    {

        if($role == 'admindashboard'){
            if (session('logged_user_id') === null) {

                return redirect('/admin_login');
            }else {
                return $next($request);
            }

        }else {
            if (session('logged_user_id') === null) {

                return redirect('/admin_login');
            } else {

                return $next($request);


            }
        }

    }
}
