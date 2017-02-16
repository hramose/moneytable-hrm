@extends('layouts.guest')

    @section('content')
        <a class="btn btn-primary btn-sm pull-right" style='margin:15px;' role="button" href="/login">{!! trans('messages.login') !!}</a>
        @if(config('config.enable_registration'))
        <a class="btn btn-primary btn-sm pull-right" style='margin:15px;' role="button" href="/resend-activation">{!! trans('messages.resend').' '.trans('messages.activation') !!}</a>
        @endif
        <div class="full-content-center animated fadeInDownBig">
            @if(File::exists(config('constants.upload_path.logo').config('config.logo')))
            <a href="/"><img src="/{!! config('constants.upload_path.logo').config('config.logo') !!}" class="" alt="Logo"></a>
            @endif
            <div class="login-wrap">
                <div class="box-info">
                <h2 class="text-center"><strong>{!! trans('messages.user') !!}</strong> {!! trans('messages.registration') !!}</h2>
                    
                    <form role="form" action="{!! URL::to('/register') !!}" method="post" class="registration-form" id="registration-form">
                        {!! csrf_field() !!}
                        <div class="row">
                        	<div class="col-md-6">
		                        <div class="form-group">
			                        <input type="text" name="first_name" id="first_name" class="form-control text-input" placeholder="{!! trans('messages.first').' '.trans('messages.name') !!}">
		                        </div>
                        	</div>
                        	<div class="col-md-6">
		                        <div class="form-group">
			                        <input type="text" name="last_name" id="last_name" class="form-control text-input" placeholder="{!! trans('messages.last').' '.trans('messages.name') !!}">
		                        </div>
                        	</div>
                        </div>
                        <div class="form-group">
	                        <input type="email" name="email" id="email" class="form-control text-input" placeholder="{!! trans('messages.email') !!}">
                        </div>
                        <div class="form-group">
	                        <input type="text" name="username" id="username" class="form-control text-input" placeholder="{!! trans('messages.username') !!}">
                        </div>
                        <div class="form-group">
	                        <input type="password" name="password" id="password" class="form-control text-input" placeholder="{!! trans('messages.password') !!}">
                        </div>
                        <div class="form-group">
	                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control text-input" placeholder="{!! trans('messages.confirm_password') !!}">
                        </div>
                        <div class="form-group">
	                        {!! Form::select('location_id', [null => (trans('messages.select').' '.trans('messages.location'))] + $locations,'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-sign-in"></i> {!! trans('messages.register') !!}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @stop