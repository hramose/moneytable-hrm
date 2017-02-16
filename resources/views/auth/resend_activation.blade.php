@extends('layouts.guest')

    @section('content')
        <a class="btn btn-primary btn-sm pull-right" style='margin:15px;' role="button" href="/login">{!! trans('messages.login') !!}</a>
        <div class="full-content-center animated fadeInDownBig">
            @if(File::exists(config('constants.upload_path.logo').config('config.logo')))
            <a href="/"><img src="/{!! config('constants.upload_path.logo').config('config.logo') !!}" class="" alt="Logo"></a>
            @endif
            <div class="login-wrap">
                <div class="box-info">
                <h2 class="text-center"><strong>{!! trans('messages.resend') !!}</strong> {!! trans('messages.activation') !!}</h2>
                    
                        <form role="form" action="{!! URL::to('/resend-activation') !!}" method="post" class="resend-activation-form" id="resend-activation-form">
                        {!! csrf_field() !!}
                        <div class="form-group">
	                        <input type="email" name="email" id="email" class="form-control text-input" placeholder="{!! trans('messages.email') !!}">
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-send"></i> {!! trans('messages.resend').' '.trans('messages.activation') !!}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @stop