@extends('layouts.guest')
    @section('content')
		<div class="modal fade" id="myModal" role="basic" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				</div>
			</div>
		</div>
        <div class="full-content-center animated bounceIn">
            <h2>{!! trans('messages.account_invalid') !!}</h2>
            <p><a href="/logout">{!! trans('messages.logout') !!}</a></p>
        </div>
    @stop