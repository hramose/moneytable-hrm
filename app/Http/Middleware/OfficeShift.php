<?php

namespace App\Http\Middleware;

use DB;
use Closure;
use App\Classes\Helper;

class OfficeShift
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
        $count_office_shift = \App\OfficeShift::whereIsDefault(1)->count();

        if(!$count_office_shift)
            return redirect('/dashboard')->withErrors('Please define office shift first.');
        
        return $next($request);
    }
}
