@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.employee') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			@if(Entrust::can('create_employee'))
			<div class="col-sm-12 collapse" id="box-detail">
				<div class="box-info">
					<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.employee') !!}
					<div class="additional-btn">
						<button class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#box-detail"><i class="fa fa-minus icon"></i> {!! trans('messages.hide') !!}</button>
					</div></h2>

					{!! Form::open(['route' => 'auth.register','role' => 'form', 'class'=>'employee-form','id' => 'employee-form','data-form-table' => 'employee_table']) !!}
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
								    {!! Form::label('first_name',trans('messages.first_name'),['class' => 'control-label'])!!}
									{!! Form::input('text','first_name','',['class'=>'form-control','placeholder'=>trans('messages.first_name')])!!}
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
								    {!! Form::label('last_name',trans('messages.last_name'),['class' => 'control-label'])!!}
									{!! Form::input('text','last_name','',['class'=>'form-control','placeholder'=>trans('messages.last_name')])!!}
								</div>
							</div>
						</div>	
						<div class="form-group">
						    {!! Form::label('username',trans('messages.username'),['class' => 'control-label'])!!}
							{!! Form::input('text','username','',['class'=>'form-control','placeholder'=>trans('messages.username')])!!}
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
								    {!! Form::label('designation_id',trans('messages.designation'),['class' => 'control-label'])!!}
									{!! Form::select('designation_id', [''=>''] + $designations,'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
								</div>	
							</div>
							<div class="col-md-6">
								<div class="form-group">
								    {!! Form::label('role_id',trans('messages.role'),['class' => 'control-label'])!!}
									{!! Form::select('role_id', [''=>''] + $roles,'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
								</div>	
							</div>
						</div>
						<div class="form-group">
							<div class="radio">
								<label>
									{!! Form::checkbox('send_welcome_email', '1', '').' '.trans('messages.send_welcome_email') !!}
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
								    {!! Form::label('date_of_joining',trans('messages.date_of_joining'))!!}
									{!! Form::input('text','date_of_joining','',['class'=>'form-control datepicker','placeholder'=>trans('messages.date_of_joining'),'readonly' => 'true'])!!}
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
								    {!! Form::label('employee_code',trans('messages.employee_code'),['class' => 'control-label'])!!}
									{!! Form::input('text','employee_code','',['class'=>'form-control','placeholder'=>trans('messages.employee_code')])!!}
								</div>
							</div>
						</div>
						<div class="form-group">
						    {!! Form::label('email',trans('messages.email'),['class' => 'control-label'])!!}
							{!! Form::input('text','email','',['class'=>'form-control','placeholder'=>trans('messages.email')])!!}
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
								    {!! Form::label('password',trans('messages.password'),['class' => 'control-label'])!!}
									{!! Form::input('password','password','',['class'=>'form-control','placeholder'=>trans('messages.password')])!!}
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
								    {!! Form::label('password_confirmation',trans('messages.confirm_password'),['class' => 'control-label'])!!}
									{!! Form::input('password','password_confirmation','',['class'=>'form-control','placeholder'=>trans('messages.confirm_password')])!!}
								</div>
							</div>
						</div>
					</div>
					{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
					{!! Form::close() !!}
				</div>
			</div>
			@endif

			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.employee') !!}
						<div class="additional-btn">
							@if(Entrust::can('create_employee'))
							<a href="#" data-toggle="collapse" data-target="#box-detail"><button class="btn btn-sm btn-primary"><i class="fa fa-plus icon"></i> {!! trans('messages.add_new') !!}</button></a>
							@endif
						</div>
					</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="box-info">
					<h2><strong>{!! trans('messages.employee') !!}</strong> {!! trans('messages.statistics') !!} (Designation Wise)</h2>
					<div id="designation_wise_user_graph"></div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box-info">
					<h2><strong>{!! trans('messages.employee') !!}</strong> {!! trans('messages.statistics') !!} (Status Wise)</h2>
					<div id="status_wise_user_graph"></div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box-info">
					<h2><strong>{!! trans('messages.employee') !!}</strong> {!! trans('messages.statistics') !!} (Department Wise)</h2>
					<div id="department_wise_user_graph"></div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box-info">
					<h2><strong>{!! trans('messages.employee') !!}</strong> {!! trans('messages.statistics') !!} (Role Wise)</h2>
					<div id="role_wise_user_graph"></div>
				</div>
			</div>
		</div>

	@stop