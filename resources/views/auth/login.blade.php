@extends('layouts.guest')

    @section('content')
        @if(config('config.enable_job_application_candidates'))
        <a class="btn btn-primary btn-sm pull-right" style='margin:15px;' role="button" href="/apply">{!! trans('messages.apply_for_job') !!}</a>
        @endif
        @if(config('config.enable_registration'))
        <a class="btn btn-primary btn-sm pull-right" style='margin:15px;' role="button" href="/register">{!! trans('messages.register') !!}</a>
        @endif
        <div class="full-content-center animated fadeInDownBig">
            @if(File::exists(config('constants.upload_path.logo').config('config.logo')))
            <a href="/"><img src="/{!! config('constants.upload_path.logo').config('config.logo') !!}" class="" alt="Logo"></a>
            @endif
            <div class="login-wrap">
                <div class="box-info">
                <h2 class="text-center"><strong>{!! trans('messages.user') !!}</strong> {!! trans('messages.login') !!}</h2>

                    <form role="form" action="{!! URL::to('/login') !!}" method="post" class="login-form" id="login-form" data-submit="noAjax">
                        {!! csrf_field() !!}
                        <div class="form-group login-input">
                        <i class="fa fa-user overlay"></i>
                        @if(config('config.login_with') == 'email')
                            <input type="email" name="email" id="email" class="form-control text-input" placeholder="{!! trans('messages.email') !!}">
                        @else
                            <input type="text" name="username" id="username" class="form-control text-input" placeholder="{!! trans('messages.username') !!}">
                        @endif
                        </div>
                        <div class="form-group login-input">
                        <i class="fa fa-key overlay"></i>
                        <input type="password" name="password" id="password" class="form-control text-input" placeholder="{!! trans('messages.password') !!}">
                        </div>
                        <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember" value="1"> {!! trans('messages.remember_me') !!}
                        </label>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-12">
                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-unlock"></i> {!! trans('messages.login') !!}</button>
                            </div>
                        </div>
                        
                    </form>
                    <p class="text-center"><a href="{!! URL::to('/password/email') !!}"><i class="fa fa-lock"></i> {!! trans('messages.forgot_password') !!}?</a></p>

                        @if(!getMode())
                        <div class="row" style="margin-bottom: 15px;">
                            <h4 class="text-center">For Demo Purpose</h4>
                            <div class="col-md-12">
                                <a href="#" data-username="admin" data-email="support@wmlab.in" data-password="123456" class="btn btn-block btn-default login-as">Login as Admin</a>
                            </div>
                            <div class="col-md-12">
                                <a href="#" data-username="john.doe" data-email="john@example.com" data-password="123456" class="btn btn-block btn-default login-as">Login as Manager</a>
                            </div>
                            <div class="col-md-12">
                                <a href="#" data-username="jack.aristal" data-email="jack@example.com" data-password="123456" class="btn btn-block btn-default login-as">Login as Staff</a>
                            </div>
                        </div>
                        @endif
                </div>
            </div>
        </div>
    @stop