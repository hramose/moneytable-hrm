@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.task') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			@if(Entrust::can('create_task'))
			<div class="col-sm-12 collapse" id="box-detail">
				<div class="box-info">
					<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.task') !!}
					<div class="additional-btn">
						<button class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#box-detail"><i class="fa fa-minus icon"></i> {!! trans('messages.hide') !!}</button>
					</div>
					</h2>
					{!! Form::open(['route' => 'task.store','role' => 'form', 'class'=>'task-form','id' => 'task-form','data-form-table' => 'task_table']) !!}
						@include('task._form')
					{!! Form::close() !!}
				</div>
			</div>
			@endif

			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.task') !!}
					<div class="additional-btn">
						<a href="/user-task-rating" class="btn btn-sm btn-primary"><i class="fa fa-bars icon"></i> User Task Rating</a>
						<a href="/user-task" class="btn btn-sm btn-primary"><i class="fa fa-bars icon"></i> User Task</a>
						@if(Entrust::can('create_task'))
						<button class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#box-detail"><i class="fa fa-plus icon"></i> {!! trans('messages.add_new') !!}</button>
						@endif
					</div>
					</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

	@stop