<?php
namespace App\Http\Middleware;
use Closure;
use Auth;
use Entrust;

class ConfigAccessible
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
        if(!defaultRole() && !Entrust::can('manage_configuration'))
            return redirect('/');

        return $next($request);
    }
}
