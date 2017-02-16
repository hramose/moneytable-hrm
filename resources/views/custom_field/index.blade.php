@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.custom_field') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-8">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.custom_field') !!}
					</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
			<div class="col-sm-4">
				<div class="box-info">
				<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.custom_field') !!}
					</h2>
					{!! Form::open(['route' => 'custom-field.store','role' => 'form', 'class'=>'custom-field-form','id' => 'custom-field-form','data-form-table' => 'custom_field_table']) !!}
					
					  <div class="form-group">
					    {!! Form::label('form',trans('messages.form'),[])!!}
						{!! Form::select('form', [
							''=>''] + config('custom-field'),'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					  </div>
					  <div class="form-group">
					    {!! Form::label('type',trans('messages.type'),[])!!}
						{!! Form::select('type', [
							''=>'',
							'text' => 'Text Box',
							'number' => 'Number',
							'email' => 'Email',
							'url' => 'URL',
							'date' => 'Date',
							'select' => 'Select Box',
							'radio' => 'Radio Button',
							'checkbox' => 'Check Box',
							'textarea' => 'Textarea'
							],'',['id' => 'type', 'class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					  </div>
					  <div class="showhide-textarea">
						<div class="form-group">
						    {!! Form::label('options',trans('messages.option'),[]) !!}
							{!! Form::input('text','options','',['class'=>'form-control select2dynamictag','placeholder'=>trans('messages.option')]) !!}
						</div>
					  </div>
					  <div class="form-group">
					    {!! Form::label('title',trans('messages.title'),[])!!}
						{!! Form::input('text','title','',['class'=>'form-control','placeholder'=>trans('messages.title')])!!}
					  </div>
					  <div class="form-group">
					   <div class="checkbox">
							<label>
							  <input type="checkbox" name="is_required" value="1"> {!! trans('messages.required') !!}
							</label>
						</div>
					  </div>
					  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
	
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	@stop