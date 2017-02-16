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
					<h2><strong>{!! trans('messages.filter') !!}</strong></h2>
					{!! Form::open(['route' => 'clock.shift','role' => 'form','class'=>'','id' => 'shift_detail','data-form-table' => 'shift_detail_table','data-no-form-clear' => 1]) !!}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="sr-only" for="from_date">{!! trans('messages.from_date') !!}</label>
								<input type="text" class="form-control datepicker" id="from_date" name="from_date" placeholder="{!! trans('messages.from_date') !!}" readonly="true" value="{!! $from_date !!}">
						  	</div>
						</div>
						<div class="col-md-6">
						  	<div class="form-group">
								<label class="sr-only" for="to_date">{!! trans('messages.to_date') !!}</label>
								<input type="text" class="form-control datepicker" id="to_date" name="to_date" placeholder="{!! trans('messages.to_date') !!}" readonly="true" value="{!! $to_date !!}">
						  	</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
						  	<div class="form-group">
								<label class="sr-only" for="to_date">{!! trans('messages.employee') !!}</label>
								{!! Form::select('user_id', [null => trans('messages.select_one')] + $users,'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						  	</div>
						</div>
						<div class="col-md-6">
						  	<div class="form-group">
								<label class="sr-only" for="to_date">{!! trans('messages.employee') !!}</label>
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
			<div class="col-sm-8">

				<div class="collapse" id="box-detail">
					<div class="box-info">
						<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.shift') !!}
						<div class="additional-btn">
							<button class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#box-detail"><i class="fa fa-minus icon"></i> {!! trans('messages.hide') !!}</button>
						</div></h2>
						{!! Form::model($user,['method' => 'POST','route' => ['user-shift.store',$user->id] ,'class' => 'user-shift-form','id' => 'user-shift-form','data-table-alter' => 'user-shift-table']) !!}
							@include('employee._user_shift_form')
						{!! Form::close() !!}
					</div>
				</div>

				<div class="box-info full">
					<h2><strong>{{$user->full_name_with_designation.' '.trans('messages.list_all').' '.trans('messages.shift') }}</strong>
						<div class="additional-btn">
							<a href="#" data-toggle="collapse" data-target="#box-detail"><button class="btn btn-sm btn-primary"><i class="fa fa-plus icon"></i> {!! trans('messages.add_new') !!}</button></a>
						</div>
					</h2>
					<div class="notice-widget" >
						<div class="table-responsive">
							<table data-sortable class="table table-hover table-striped table-bordered table-ajax-load"  id="user-shift-table" data-source="/user-shift/lists" data-extra="&employee_id={{$user->id}}">
								<thead>
									<tr>
										<th>{!! trans('messages.date') !!}</th>
										<th>{!! trans('messages.shift') !!}</th>
										<th data-sortable="false" >{!! trans('messages.option') !!}</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.shift_detail') !!}</strong> </h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

	@stop