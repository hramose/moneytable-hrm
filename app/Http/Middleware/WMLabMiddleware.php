<?php
namespace App\Http\Middleware;
use Closure;
use File;
use Auth;
use Session;
use App;
use DB;
use App\Classes\Helper;

class WMLabMiddleware
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
        defaultDB();

        if(!checkDBConnection() && !$request->is('install') && !$request->is('update'))
            return redirect('/install');

        foreach(config('constants.path') as $key => $path)
            if (!File::exists(base_path().$path) && $key != 'verifier')
                abort(399,$path.' '.trans('messages.file_not_found.'));

        foreach(config('constants.system_default') as $key => $value)
            config(['config.'.$key => config('constants.system_default.'.$key)]);

        if(checkDBConnection()){
            $config_vars = \App\Config::all();
            Helper::setConfig($config_vars);
        }

        if(Auth::check()) {

            $default_role = \App\Role::whereIsHidden(1)->first();
            define("DEFAULT_ROLE",($default_role) ? $default_role->name : '');

            $permissions = DB::table('permissions')->pluck('category','name');
            if(count(config('permission')) > count($permissions)){
                $new_permission = array_diff_key(config('permission'), $permissions);
                $permission_insert = array();
                foreach($new_permission as $key => $value)
                    $permission_insert[] = array('category' => $value,'name' => $key);
                DB::table('permissions')->insert($permission_insert);
            }

            $menus = \App\Menu::pluck('name')->all();
            $config_menus = config('menu');
            $menu_items = array_diff($config_menus, $menus);
            foreach($menu_items as $menu_item){
                $db_menu = \App\Menu::firstOrNew(['name' => $menu_item]);
                if(!isset($db_menu->id))
                	$db_menu->visible = 1;
                $db_menu->name = $menu_item;
                $db_menu->save();
            }
            $menus = \App\Menu::all();

            $permissions = DB::table('permissions')->pluck('id');
            $permission_role = DB::table('permission_role')->where('role_id','=',$default_role->id)->pluck('permission_id');
            $permission_role_array = array_diff($permissions,$permission_role);
            $permission_role_insert = array();
            foreach($permission_role_array as $value)
                $permission_role_insert[] = array('permission_id' => $value,'role_id' => $default_role->id);
            DB::table('permission_role')->insert($permission_role_insert);

            $setup_info = \App\Setup::pluck('module')->all();
            $setup = [];
            foreach(config('setup') as $key => $value)
                $setup[] = $key;

            $setup_array = array_diff($setup, $setup_info);
            $setup_insert = array();
            foreach($setup_array as $value)
                $setup_insert[] = array('module' => $value,'completed' => ($value == 'installation') ? 1 : 0);
            if(count($setup_insert))
                DB::table('setup')->insert($setup_insert);

            if(\App\Ip::count() && !Helper::validateIp())
                abort(398,trans('messages.ip_not_allowed'));

            $setup_info = Helper::setupInfo();
            DB::enableQueryLog(); 
        }

        config(['app.debug' => (config('config.error_display')) ? true : false]);

        $token = csrf_token();
        $custom_field_values = array();
        $page_title = config('config.application_name');
        $table_info = array('name' => '','title' => '');
        $direction = (config('config.direction')) ? : 'ltr';
        $default_timezone = config('config.timezone_id') ? config('timezone.'.config('config.timezone_id')) : 'Asia/Kolkata';
        date_default_timezone_set($default_timezone);
        $currency_decimal_value = currenyDecimalValue();

        $default_language = (Session::has('lang')) ? Session::get('lang') : ((config('config.default_language')) ? : 'en' );
        session(['lang' => $default_language]);
        $datatable_language = (config('lang.'.$default_language.'.datatable')) ? : 'English';
        $calendar_language = (config('lang.'.$default_language.'.calendar')) ? : 'en';
        $assets = array();
        $menu = array();

        $available_date = array();
        view()->share(compact('token','custom_field_values','page_title','default_timezone','currency_decimal_value','default_language','datatable_language','calendar_language','assets','direction','menu','table_info','setup_info','available_date','menus'));

        App::setLocale($default_language);

        if (Auth::check()){
            $header_inbox = \App\Message::whereToUserId(Auth::user()->id)->whereIsRead(0)->get();
            
            $child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
            $child_users = \App\User::whereIn('designation_id',$child_designations)->pluck('id')->all();
            $header_leave = \App\Leave::whereIn('user_id',$child_users)->whereStatus('pending')->get();
            view()->share(compact('header_inbox','header_leave'));
        }

        $response = $next($request);
        return $response;
    }
}
