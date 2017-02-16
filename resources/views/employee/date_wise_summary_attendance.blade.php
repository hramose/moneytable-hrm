@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.date_wise').' '.trans('messages.summary').' '.trans('messages.attendance') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info">
					<h2><strong>{!! trans('messages.select').' '.trans('messages.date') !!}</strong></h2>
					{!! Form::open(['route' => 'clock.date-wise-summary-attendance','role' => 'form','class'=>'form-inline','id' => 'date_wise_summary_attendance','data-form-table' => 'date_wise_summary_attendance_table','data-no-form-clear' => 1]) !!}
						<div class="form-group">
							{!! Form::input('text','from_date',isset($from_date) ? $from_date : date('Y-m-d'),['class'=>'form-control datepicker','placeholder'=>trans('messages.from_date'),'readonly' => 'true'])!!}
						</div>
						<div class="form-group">
							{!! Form::input('text','to_date',isset($to_date) ? $to_date : date('Y-m-d'),['class'=>'form-control datepicker','placeholder'=>trans('messages.to_date'),'readonly' => 'true'])!!}
						</div>
					  {!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.get'),['class' => 'btn btn-primary']) !!}
					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.attendance') !!}</strong> </h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>


		<div class="row" id="date-wise-summary-attendance-graph">
			<div class="col-md-12">
				<div class="box-info">
					<div id="date-wise-summary-attendance-late-graph"></div>
				</div>
				<div class="box-info">
					<div id="date-wise-summary-attendance-early-graph"></div>
				</div>
				<div class="box-info">
					<div id="date-wise-summary-attendance-working-graph"></div>
				</div>
				<div class="box-info">
					<div id="date-wise-summary-attendance-rest-graph"></div>
				</div>
				<div class="box-info">
					<div id="date-wise-summary-attendance-overtime-graph"></div>
				</div>
			</div>
		</div>
	@stop