@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/job-application">{!! trans('messages.job_application') !!}</a></li>
		    <li class="active">{!! trans('messages.add_new') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-8">
				<div class="box-info">
					<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.job_application') !!}
					<div class="additional-btn">
						<a href="/job-application"><button class="btn btn-sm btn-primary"><i class="fa fa-bars icon"></i> {!! trans('messages.list_all') !!}</button></a>
					</div>
					</h2>
					
					{!! Form::open(['files' => true, 'route' => 'job-application.store','role' => 'form', 'class'=>'job-application-form']) !!}
						@include('job_application._form')
					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-4">
				<div class="the-notes info"><h4>{!! trans('messages.help') !!}</h4></div>
			</div>
		</div>

	@stop