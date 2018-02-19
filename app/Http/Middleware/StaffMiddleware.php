<?php

namespace App\Http\Middleware;

use Closure;

class StaffMiddleware
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
        if ($request->user() != null) {
            if ($request->user()->usertype == 'student') {
                return redirect('/studauth');
            }
            if ($request->user()->usertype == 'admin') {
                return redirect('/adminauth');
            }
            if ($request->user()->usertype == 'staff') {
                return $next($request);
            }
        }
        return redirect('/');
    }
}
