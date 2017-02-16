@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.shift_detail') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{!! trans('messages.set_date') !!}</strong></h2>
					{!! Form::open(['route' => 'clock.shift','role' => 'form','class'=>'form-inline','id' => 'shift_detail','data-form-table' => 'shift_detail_table','data-no-form-clear' => 1]) !!}
						<div class="form-group">
							<label class="sr-only" for="date">{!! trans('messages.date') !!}</label>
							<input type="text" class="form-control datepicker" id="date" name="date" placeholder="{!! trans('messages.date') !!}" readonly="true" value="{!! $date !!}">
							<button type="submit" class="btn btn-default btn-success">{!! trans('messages.get') !!}</button>
					  	</div>
					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-12">
				<div class="box-info">
					<h2><strong>{!! trans('messages.shift_detail') !!}</strong> {!! trans('messages.for') !!} {!! date('d-M-Y',strtotime($date)) !!}</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

	@stop