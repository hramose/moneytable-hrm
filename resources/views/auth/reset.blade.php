@extends('layouts.guest')

    @section('content')
        <div class="full-content-center animated fadeInDownBig">
            @if(File::exists(config('constants.upload_path.logo').config('config.logo')))
            <a href="/"><img src="/{!! config('constants.upload_path.logo').config('config.logo') !!}" class="" alt="Logo"></a>
            @endif
            <div class="login-wrap">
                <div class="box-info">
                <h2 class="text-center"><strong>{!! trans('messages.reset') !!}</strong> {!! trans('messages.password') !!}</h2>
                    
                    <form role="form" action="{!! URL::to('/password/reset') !!}" method="post" class="reset-password-form" id="reset-password-form" data-submit="noAjax">
                        <input type="hidden" name="token" value="{{ $token }}">
                        {!! csrf_field() !!}
                        <div class="form-group login-input">
                        <i class="fa fa-envelope overlay"></i>
                        <input type="email" name="email" id="email" class="form-control text-input" placeholder="{!! trans('messages.email') !!}">
                        </div>
                        <div class="form-group login-input">
                        <i class="fa fa-key overlay"></i>
                        <input type="password" name="password" id="password" class="form-control text-input" placeholder="{!! trans('messages.new_password') !!}">
                        </div>
                        <div class="form-group login-input">
                        <i class="fa fa-key overlay"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control text-input" placeholder="{!! trans('messages.new_confirm_password') !!}">
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-12">
                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-unlock"></i> {!! trans('messages.reset_password') !!}</button>
                            </div>
                        </div>
                    </form>
                    <p class="text-center"><a href="{!! URL::to('/') !!}"><i class="fa fa-lock"></i> {!! trans('messages.back_to_login') !!}</a></p>
                </div>
            </div>
        </div>
    @stop