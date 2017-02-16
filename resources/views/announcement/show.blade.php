@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/announcement">{!! trans('messages.announcement') !!}</a></li>
		    <li class="active">{!! $announcement->title !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-md-8">
				<div class="box-info">
					<h2>{!! $announcement->title !!}</h2>
					{!! $announcement->description !!}
					<span class="timeinfo"><i class="fa fa-clock-o"></i> {!! showDateTime($announcement->created_at) !!}</span>
					<span class="pull-right">
					{!! $announcement->User->full_name_with_designation !!}
					</span>
				</div>
			</div>
			<div class="col-md-4">
				<div class="box-info">
					<h2>{!! trans('messages.other').' '.trans('messages.announcement') !!}</h2>
					@if(count($announcements))
					<ul>
						@foreach($announcements as $announcement)
							<li>{!! $announcement->title !!}
								<span class="timeinfo"><i class="fa fa-clock-o"></i> {!! showDateTime($announcement->created_at) !!}</span>
							</li>
						@endforeach
					</ul>
					@else
						@include('common.notification',['message' => trans('messages.no_data_found'),'type' => 'danger'])
					@endif
				</div>
			</div>
		</div>
	@stop