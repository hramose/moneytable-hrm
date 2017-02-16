@extends('layouts.guest')

    @section('content')
        @if(config('config.enable_job_application_candidates'))
        <a class="btn btn-primary btn-sm pull-right" style='margin:15px;' role="button" href="/apply">{!! trans('messages.apply_for_job') !!}</a>
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
                        <input type="text" name="username" id="username" class="form-control text-input" placeholder="{!! trans('messages.username') !!}">
                        </div>
                        <div class="form-group login-input">
                        <i class="fa fa-key overlay"></i>
                        <input type="password" name="password" id="password" class="form-control text-input" placeholder="{!! trans('messages.password') !!}">
                        </div>
                        <div class="checkbox">
                        <label>
                            <input type="checkbox"> {!! trans('messages.remember_me') !!}
                        </label>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-12">
                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-unlock"></i> {!! trans('messages.login') !!}</button>
                            </div>
                        </div>

                        @if(!getMode())
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Role</th>
                                        <th>Username</th>
                                        <th>Password</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <tr>
                                            <td>Admin</td>
                                            <td>john</td>
                                            <td>123456</td>
                                        </tr>
                                        <tr>
                                            <td>Manager</td>
                                            <td>sean</td>
                                            <td>123456</td>
                                        </tr>
                                        <tr>
                                            <td>User</td>
                                            <td>marry</td>
                                            <td>123456</td>
                                        </tr>
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </form>
                    <p class="text-center"><a href="{!! URL::to('/password/email') !!}"><i class="fa fa-lock"></i> {!! trans('messages.forgot_password') !!}?</a></p>
                </div>
            </div>
        </div>
    @stop