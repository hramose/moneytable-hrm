@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.job_search') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-3">
				<div class="box-info">
					<h2><strong>{!! trans('messages.job').' </strong>'. trans('messages.search') !!}</h2>
					{!! Form::open(['route' => 'job-search.index','role' => 'form', 'class'=>'job-search-form','id' => 'job-search-form', 'data-submit' => 'noAjax']) !!}
					  <div class="form-group">
						{!! Form::select('country', [null => trans('messages.country')] + config('indeed.country'),($request->input('country')) ? : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					  </div>
					  <div class="form-group">
						{!! Form::input('text','query',($request->input('query')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.job_keyword')])!!}
					  </div>
					  <div class="form-group">
						{!! Form::input('text','location',($request->input('location')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.location')])!!}
					  </div>
					  <div class="form-group">
						{!! Form::select('job_type', [null => trans('messages.job_type')] + config('indeed.job_type'),($request->input('job_type')) ? : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					  </div>
					  {!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.search'),['class' => 'btn btn-primary pull-right']) !!}
					{!! Form::close() !!}
				</div>
			</div>
			@if($request->input('country'))
			<div class="col-sm-9">
				<div class="box-info">
					<h2><strong>{!! trans('messages.job_search') !!}</strong> {!! trans('messages.search') !!}</h2>
					@if(isset($jobs['totalResults']))
						@include('common.notification',['type' => 'info','message' => $jobs['totalResults'].' '.trans('messages.result_found')])
					@else
						@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
					@endif

					@if(isset($jobs['results']))
					<ul class="media-list search-result">
						@foreach($jobs['results'] as $job)
						  <li class="media">
							<div class="media-body">
							  <h4 class="media-heading"><a href="{!! $job['url'] !!}" target="_blank">{!! $job['jobtitle'] !!}</a> <span class="label label-warning">{!! showDate($job['date']) !!}</span></h4>
							  <a href="{!! $job['url'] !!}" target="_blank">{!! $job['company'] !!} <span class="label label-success">{!! $job['city'].' '.$job['state'].' '.$job['country'] !!}</span></a>
							  <p>{!! $job['snippet'] !!}</p>
							</div>
						  </li>
						@endforeach
					</ul>
					@endif
				</div>
			</div>
			@endif
		</div>
	@stop