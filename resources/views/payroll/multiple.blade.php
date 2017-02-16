@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/payroll">{!! trans('messages.payroll') !!}</a></li>
		    <li class="active">{!! trans('messages.generate') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{!! trans('messages.select') !!} </strong> {!! trans('messages.option') !!}</h2>
					{!! Form::open(['route' => 'payroll.create-multiple','role' => 'form', 'class'=>'payroll-form','id' => 'payroll-form']) !!}
					  <div class="form-group">
					    {!! Form::label('from_date',trans('messages.from_date'),[])!!}
						{!! Form::input('text','from_date','',['class'=>'form-control datepicker','placeholder'=>trans('messages.from_date'),'readonly' => 'true'])!!}
					  </div>
					  <div class="form-group">
					    {!! Form::label('to_date',trans('messages.to_date'),[])!!}
						{!! Form::input('text','to_date','',['class'=>'form-control datepicker','placeholder'=>trans('messages.to_date'),'readonly' => 'true'])!!}
					  </div>
					  <div class="checkbox">
						<label>
						  {!! Form::checkbox('send_mail', 1,'') !!} {!! trans('messages.send').' '.trans('messages.mail') !!}
						</label>
					  </div>
					  {!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.generate'),['class' => 'btn btn-primary pull-right','name' => 'submit']) !!}
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	@stop