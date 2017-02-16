@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.attendance') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{!! trans('messages.set_date') !!}</strong></h2>
					{!! Form::open(['route' => 'clock.attendance','role' => 'form','class'=>'form-inline','id' => 'daily_attendance','data-form-table' => 'daily_attendance_table','data-no-form-clear' => 1]) !!}
						<div class="form-group">
							<label class="sr-only" for="date">{!! trans('messages.date') !!}</label>
							<input type="text" class="form-control datepicker" id="date" name="date" placeholder="{!! trans('messages.date') !!}" readonly="true" value="{!! $date !!}">
							<button type="submit" class="btn btn-default btn-success">{!! trans('messages.get') !!}</button>
					  	</div>

					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-2 pull-right" id="attendance_summary"></div>
			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.attendance') !!}</strong> {!! trans('messages.for') !!} {!! date('d-M-Y',strtotime($date)) !!}</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

		<div class="row" id="daily-attendance-graph">
			<div class="col-md-12">
				<div class="box-info">
					<div id="attendance-late-graph"></div>
				</div>
				<div class="box-info">
					<div id="attendance-early-graph"></div>
				</div>
				<div class="box-info">
					<div id="attendance-working-graph"></div>
				</div>
				<div class="box-info">
					<div id="attendance-rest-graph"></div>
				</div>
				<div class="box-info">
					<div id="attendance-overtime-graph"></div>
				</div>
			</div>
		</div>

	@stop