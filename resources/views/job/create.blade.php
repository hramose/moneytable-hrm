@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/job">{!! trans('messages.job') !!}</a></li>
		    <li class="active">{!! trans('messages.add_new') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-8">
				<div class="box-info">
					<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.job') !!}
						<div class="additional-btn">
							@if(Entrust::can('manage_job'))
								<a href="/job"><button class="btn btn-sm btn-primary"><i class="fa fa-bars icon"></i> {!! trans('messages.list_all') !!}</button></a>
							@endif
						</div>
					</h2>
					
					{!! Form::open(['route' => 'job.store','role' => 'form', 'class'=>'job-form']) !!}
						@include('job._form')
					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-4">
				<div class="the-notes info"><h4>{!! trans('messages.help') !!}</h4></div>
			</div>
		</div>

	@stop