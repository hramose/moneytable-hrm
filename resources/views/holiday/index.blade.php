@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.holiday') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			@if(Entrust::can('create_holiday'))
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{!! trans('messages.add_new') !!}</strong></h2>
					{!! Form::open(['route' => 'holiday.store','role' => 'form', 'class'=>'holiday-form','id' => 'holiday-form','data-form-table' => 'holiday_table']) !!}
						@include('holiday._form')
					{!! Form::close() !!}
				</div>
			</div>
			@endif
			@if(Entrust::can('list_holiday'))
			<div class="col-sm-8">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.holiday') !!}</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
			@endif
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="box-info">
					<div id="holiday-graph"></div>
				</div>
			</div>
		</div>
	@stop