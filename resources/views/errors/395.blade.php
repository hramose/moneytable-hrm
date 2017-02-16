@extends('layouts.guest')
    @section('content')
        <div class="full-content-center animated bounceIn">
            @if(File::exists(config('constants.upload_path.logo').config('config.logo')))
            <a href="/"><img src="/{!! config('constants.upload_path.logo').config('config.logo') !!}" class="" alt="Logo"></a>
            @endif
            <h2>This domain is not available.</h2>
        </div>
    @stop