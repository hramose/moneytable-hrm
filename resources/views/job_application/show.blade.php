@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/job-application">{!! trans('messages.job_application') !!}</a></li>
		    <li class="active">{{$job_application->name.' ('.$job_application->Job->full_job_title.')'}}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{!! trans('messages.job_application') !!}</strong> {!! trans('messages.detail') !!}</h2>
					<ul class="list-group">
					  <li class="list-group-item">
						<span class="pull-right">{!! $job_application->Job->full_job_title !!}</span>
						{!! trans('messages.title') !!}
					  </li>
					  <li class="list-group-item">
						<span class="pull-right">{!! $job_application->name !!}</span>
						{!! trans('messages.candidate_name') !!}
					  </li>
					  <li class="list-group-item">
						<span class="pull-right">{!! $job_application->email !!}</span>
						{!! trans('messages.email') !!}
					  </li>
					  <li class="list-group-item">
						<span class="pull-right">{!! $job_application->contact_number !!}</span>
						{!! trans('messages.contact_number') !!}
					  </li>
					  <li class="list-group-item">
						<span class="pull-right">{!! showDate($job_application->date_of_application) !!}</span>
						{!! trans('messages.date_of_application') !!}
					  </li>
					  @if($job_application->status != 'applied')
					  <li class="list-group-item">
						<span class="pull-right">{!! trans('messages.'.$job_application->status) !!}</span>
						{!! trans('messages.status') !!}
					  </li>
					  <li class="list-group-item">
						{!! $job_application->remarks !!}
					  </li>
					  @endif
					</ul>
				</div>
			</div>
			<div class="col-md-8">
				<div class="box-info">
				<h2><strong>{!!trans('messages.update').' </strong>'.trans('messages.status') !!}</h2>
					{!! Form::model($job_application,['method' => 'PATCH','route' => ['job-application.update-status',$job_application] ,'class' => 'job-application-status-form','id' => 'job-application-status-form','data-submit' => 'noAjax']) !!}
					  <div class="form-group">
					    {!! Form::label('status',trans('messages.status'),[])!!}
						{!! Form::select('status', [''=>''] + $job_application_status,isset($job_application->status) ? $job_application->status : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					  </div>
					  <div class="form-group">
					    {!! Form::label('remarks',trans('messages.remarks'),[])!!}
					    {!! Form::textarea('remarks',isset($job_application) ? $job_application->remarks : '',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.remarks')])!!}
					    <span class="countdown"></span>
					  </div>
			  		  {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	@stop