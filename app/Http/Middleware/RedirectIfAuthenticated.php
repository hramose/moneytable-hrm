<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class RedirectIfAuthenticated
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

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

        if($request->is('login') && strtolower($request->method()) == 'post'){
            if(is_connected()){
                $data = verifyPurchase();
                if($data['status'] == 'error'){
                    $config = config('code');
                    $config['purchase_code'] = null;
                    write2Config($config,'code');
                    return redirect('/verify-purchase')->withErrors($data['message']);
                }
            }
        }

        return $next($request);
    }
}
