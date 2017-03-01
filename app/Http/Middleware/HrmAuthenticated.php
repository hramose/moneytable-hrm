<?php

namespace App\Http\Middleware;

use Closure;

class HrmAuthenticated
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
      if ($this->auth->check()) {
          return property_exists($this, 'redirectTo') ? $this->redirectTo : redirect('/dashboard');
      }
        return $next($request);
    }
}
