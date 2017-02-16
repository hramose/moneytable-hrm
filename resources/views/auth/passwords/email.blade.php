@extends('layouts.guest')

    @section('content')
        <div class="full-content-center animated fadeInDownBig">
            @if(File::exists(config('constants.upload_path.logo').config('config.logo')))
            <a href="/"><img src="/{!! config('constants.upload_path.logo').config('config.logo') !!}" class="" alt="Logo"></a>
            @endif
            <div class="login-wrap">
                <div class="box-info">
                <h2 class="text-center"><strong>{!! trans('messages.forgot') !!}</strong> {!! trans('messages.password') !!}</h2>
                    
                    <form role="form" action="{!! URL::to('/password/email') !!}" method="post" class="forgot-form" id="forgot-form" data-submit="noAjax">
                        {!! csrf_field() !!}
                        <div class="form-group login-input">
                        <i class="fa fa-envelope overlay"></i>
                        <input type="email" name="email" id="email" class="form-control text-input" placeholder="{!! trans('messages.email') !!}">
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