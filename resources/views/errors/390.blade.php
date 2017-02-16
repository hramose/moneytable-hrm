@extends('layouts.guest')
    @section('content')
		<div class="modal fade" id="myModal" role="basic" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				</div>
			</div>
		</div>
        <div class="full-content-center animated bounceIn">
            @if(File::exists(config('constants.upload_path.logo').config('config.logo')))
            <a href="/"><img src="/{!! config('constants.upload_path.logo').config('config.logo') !!}" class="" alt="Logo"></a>
            @endif
            <h1>{!! trans('messages.account_expired') !!}</h1>
            <h2>{!! $exception->getMessage() !!}</h2>
        </div>
    @stop