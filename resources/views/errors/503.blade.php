@extends('layouts.guest')
    @section('content')
        
        <div class="full-content-center animated bounceIn">
            @if(File::exists(config('constants.upload_path.logo').config('config.logo')))
            <a href="/"><img src="/{!! config('constants.upload_path.logo').config('config.logo') !!}" class="" alt="Logo"></a>
            @endif
            <h1>{!! trans('messages.under_maintenance') !!}</h1>
            <h2>{!! $exception->getMessage() !!}</h2>
        </div>
    @stop