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

        $user = Auth::user();
        if($profile->date_of_leaving != null && $profile->date_of_leaving < date('Y-m-d')){
            $user->status = 'in-active';
            $user->save();
        }

        if($user->status == 'in-active')
            $response = ['message' => 'Your account is inactive, You cannot login.','type' => 'error'];
        elseif($user->status == 'pending_activation')
            $response = ['message' => 'Your email is not verified, Please check your email & click on the activation link.','type' => 'error'];
        elseif($user->status == 'pending_approval')
            $response = ['message' => 'Your account is not approved, Please contact system administrator.','type' => 'error'];
        elseif($user->status == 'pending_approval')
            $response = ['message' => 'Your account is not approved, Please contact system administrator.','type' => 'error'];
        elseif($user->status == 'banned')
            $response = ['message' => 'Your account is banned, Please contact system administrator.','type' => 'error'];
        else
            $response = ['type' => 'success'];

        if($response['type'] == 'error'){
            Auth::logout();
            return redirect('/login')->withErrors($response['message']);
        }

        return $next($request);
    }
}
