<?php
namespace App\Http\Middleware;
use Closure;
use Auth;

class AccountValid
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
        $profile = Auth::user()->Profile;

        if(!isset($profile) && $profile == '' && $profile == null){
            $profile = new \App\Profile;
            $profile->user()->associate(Auth::user());
            $profile->save();
        }

        if($profile->date_of_leaving != null && $profile->date_of_leaving < date('Y-m-d')){
            $user = Auth::user();
            $user->status = 'in-active';
            $user->save();
            return redirect('/account-invalid');
        }

        return $next($request);
    }
}
