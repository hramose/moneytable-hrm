@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.date_wise').' '.trans('messages.attendance') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-8">
				<div class="box-info">
					<h2><strong>{!! trans('messages.select').' '.trans('messages.date') !!}</strong></h2>
					{!! Form::open(['route' => 'clock.date-wise-attendance','role' => 'form','class'=>'','id' => 'date_wise_attendance','data-form-table' => 'date_wise_attendance_table','data-no-form-clear' => 1]) !!}
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								{!! Form::input('text','from_date',isset($from_date) ? $from_date : date('Y-m-d'),['class'=>'form-control datepicker','placeholder'=>trans('messages.from_date'),'readonly' => 'true'])!!}
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								{!! Form::input('text','to_date',isset($to_date) ? $to_date : date('Y-m-d'),['class'=>'form-control datepicker','placeholder'=>trans('messages.to_date'),'readonly' => 'true'])!!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								{!! Form::select('user_id', [null => trans('messages.select_one')] + $users, isset($user_id) ? $user_id : Auth::user()->id,['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
							</div>
						</div>
						<div class="col-md-4">
					  		{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.get'),['class' => 'btn btn-primary pull-right']) !!}
						</div>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-4 pull-right" id="attendance_summary"></div>
			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.attendance') !!}</strong> @if(isset($user)) {!! trans('messages.of') !!} {!! $user->full_name_with_designation.' '.trans('messages.'.$month).' '.$year !!} @endif</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

		<div class="row" id="date-wise-attendance-graph">
			<div class="col-md-12">
				<div class="box-info">
					<div id="date-wise-attendance-late-graph"></div>
				</div>
				<div class="box-info">
					<div id="date-wise-attendance-early-graph"></div>
				</div>
				<div class="box-info">
					<div id="date-wise-attendance-working-graph"></div>
				</div>
				<div class="box-info">
					<div id="date-wise-attendance-rest-graph"></div>
				</div>
				<div class="box-info">
					<div id="date-wise-attendance-overtime-graph"></div>
				</div>
			</div>
		</div>
	@stop