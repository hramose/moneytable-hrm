@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/employee">{!! trans('messages.employee') !!}</a></li>
		    <li class="active">{!! trans('messages.edit') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info">
					<h2><strong>{!! trans('messages.edit') !!} </strong> {!! trans('messages.employee') !!}</h2>
					
					{!! Form::model($employee,['method' => 'PATCH','route' => ['employee.update',$employee->id] ,'class' => 'employee-form ']) !!}
						  <div class="form-group">
						    {!! Form::label('first_name',trans('messages.first_name'),['class' => 'control-label'])!!}
							{!! Form::input('text','first_name',isset($employee->first_name) ? $employee->first_name : '',['class'=>'form-control','placeholder'=>trans('messages.first_name')])!!}
						  </div>
						  <div class="form-group">
						    {!! Form::label('last_name',trans('messages.last_name'),['class' => 'control-label'])!!}
							{!! Form::input('text','last_name',isset($employee->last_name) ? $employee->last_name : '',['class'=>'form-control','placeholder'=>trans('messages.last_name')])!!}
						  </div>	
						  <div class="form-group">
						    {!! Form::label('designation_id',trans('messages.designation'),['class' => 'control-label'])!!}
							{!! Form::select('designation_id', [null=>trans('messages.select_one')] + $designations,isset($employee->designation_id) ? $employee->designation_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.designation')])!!}
						  </div>
						  @if(Entrust::can('manage_all_employee'))
						  <div class="form-group">
						    {!! Form::label('role_id',trans('messages.role'),['class' => 'control-label'])!!}
							{!! Form::select('role_id', [''=>''] + $roles, isset($role_id) ? $role_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.role')])!!}
						  </div>
						  @endif
						  <div class="form-group">
						    {!! Form::label('email',trans('messages.email'),['class' => 'control-label'])!!}
							{!! Form::input('text','email',isset($employee->email) ? $employee->email : '',['class'=>'form-control','placeholder'=>trans('messages.email')])!!}
						  </div>
			  				{{ App\Classes\Helper::getCustomFields('employee-form',$custom_field_values) }}
						  <div class="pull-right">
						  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.update'),['class' => 'btn btn-primary']) !!}
						  </div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>

	@stop