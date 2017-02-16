@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.task') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-6">
				<div class="box-info">
					<h2><strong>{!! trans('messages.filter') !!}</strong></h2>
					{!! Form::open(['route' => 'clock.shift','role' => 'form','class'=>'','id' => 'user_task_rating_form','data-form-table' => 'user_task_rating_table','data-no-form-clear' => 1]) !!}
					<div class="row">
						<div class="col-md-6">
						  	<div class="form-group">
								<label for="to_date">{!! trans('messages.designation') !!}</label>
								{!! Form::select('designation_id', [null => trans('messages.select_one')] + $designations,'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						  	</div>
						</div>
						<div class="col-md-6">
						  	<div class="form-group">
								<label for="to_date">{!! trans('messages.location') !!}</label>
								{!! Form::select('location_id', [null => trans('messages.select_one')] + $locations,'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						  	</div>
						</div>
					</div>
					<div class="form-group">
					<button type="submit" class="btn btn-default btn-success pull-right">{!! trans('messages.get') !!}</button>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.user').' '.trans('messages.rating') !!}
					<div class="additional-btn">
						<a href="/user-task" class="btn btn-sm btn-primary"><i class="fa fa-bars icon"></i> User Task</a>
					</div>
					</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

	@stop