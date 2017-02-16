@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/leave">{!! trans('messages.leave') !!}</a></li>
		    <li class="active">{!! trans('messages.request') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-8">
				<div class="box-info">
					<h2><strong>{!! trans('messages.request') !!}</strong> {!! trans('messages.leave') !!}
						<div class="additional-btn">
							<a href="/leave"><button class="btn btn-sm btn-primary"><i class="fa fa-bars icon"></i> {!! trans('messages.list_all') !!}</button></a>
						</div>
					</h2>
					
					{!! Form::open(['route' => 'leave.store','role' => 'form', 'class'=>'leave-form']) !!}
						@include('leave._form')
					{!! Form::close() !!}
				</div>
			</div>
		</div>

	@stop