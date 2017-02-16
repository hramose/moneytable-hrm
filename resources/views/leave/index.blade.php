@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.leave') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			@if(Entrust::can('request_leave'))
			<div class="col-sm-12 collapse" id="box-detail">
				<div class="row">
					<div class="col-sm-4">
						<div class="box-info">
							<h2><strong>{{ trans('messages.leave') }}</strong> {{ trans('messages.statistics') }}</h2>
							<div id="leave-statistics">
							</div>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="box-info">
							<h2><strong>{!! trans('messages.request') !!}</strong> {!! trans('messages.leave') !!}
							<div class="additional-btn">
								<button class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#box-detail"><i class="fa fa-minus icon"></i> {!! trans('messages.hide') !!}</button>
							</div></h2>
							{!! Form::open(['route' => 'leave.store','role' => 'form', 'class'=>'leave-form','id' => 'leave-form','data-form-table' => 'leave_table']) !!}
								@include('leave._form')
							{!! Form::close() !!}
						</div>
					</div>
				</div>
			</div>
			@endif

			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.leave').' '.trans('messages.request') !!}
					@if(Entrust::can('request_leave'))
					<div class="additional-btn">
						<a href="/leave-analysis"><button class="btn btn-sm btn-primary"><i class="fa fa-line-chart icon"></i> {!! trans('messages.leave')!!} Analysis</button></a>
						<a href="/leave-statistics"><button class="btn btn-sm btn-primary"><i class="fa fa-bars icon"></i> {!! trans('messages.leave').' '.trans('messages.statistics') !!}</button></a>
						<a href="#" data-toggle="collapse" data-target="#box-detail"><button class="btn btn-sm btn-primary"><i class="fa fa-plus icon"></i> {!! trans('messages.request') !!}</button></a>
					</div>
					@endif
					</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

	@stop