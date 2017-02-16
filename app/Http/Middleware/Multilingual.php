<?php
namespace App\Http\Middleware;
use Closure;
use Auth;
use Entrust;

class Multilingual
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
        if(!config('config.multilingual'))
            return redirect('/dashboard')->withErrors(trans('messages.module_not_available'));

        return $next($request);
    }
}
