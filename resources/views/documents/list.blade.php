@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.document') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info">
					<h2><strong>Filter</strong></h2>
					{!! Form::open(['route' => 'document.lists','role' => 'form', 'class'=>'document-filter-form']) !!}
						<div class="col-md-4">
							<div class="form-group">
							    {!! Form::label('user_id',trans('messages.employee'),['class' => 'control-label'])!!}
							    {!! Form::select('user_id', [null=>trans('messages.select_one')] + $user_lists
							    	, ($filter_data['user_id']) ? : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
							</div>
						</div>
						<div class="col-md-4">
						  <div class="form-group">
							{!! Form::label('expired',trans('messages.expired'),[])!!}
							  <div class="checkbox">
								<label>
								  <input type="radio" name="expired" value="all" checked> {!! trans('messages.all') !!}
								</label>
								<label>
								  <input type="radio" name="expired" value="expired" {!! ($filter_data['expired'] == 'expired') ? 'checked' : '' !!}> {!! trans('messages.expired') !!}
								</label>
								<label>
								  <input type="radio" name="expired" value="valid" {!! ($filter_data['expired'] == 'valid') ? 'checked' : '' !!}> {!! trans('messages.valid') !!}
								</label>
							  </div>
						  </div>
						</div>
						<div class="col-md-4">
						  <div class="form-group">
							{!! Form::label('status',trans('messages.status'),[])!!}
							  <div class="checkbox">
								<label>
								  <input type="radio" name="status" value="all" checked> {!! trans('messages.all') !!}
								</label>
								<label>
								  <input type="radio" name="status" value="active" {!! ($filter_data['status'] == 'active') ? 'checked' : '' !!}> {!! trans('messages.active') !!}
								</label>
								<label>
								  <input type="radio" name="status" value="inactive" {!! ($filter_data['status'] == 'inactive') ? 'checked' : '' !!}> {!! trans('messages.in_active') !!}
								</label>
							  </div>
						  </div>
						</div>
						<div class="clear"></div>
						<div class="col-md-4">
							<div class="form-group">
							 {!! Form::label('expiry_on',trans('messages.expiry_on'),['class' => 'control-label'])!!}
							 <div class="input-group">
							 {!! Form::input('number','expiry_on',($filter_data['expiry_on']) ? : '',['class'=>'form-control','placeholder'=>trans('messages.expiry_on')])!!}
							 <span class="input-group-addon">{!! trans('messages.day') !!}</span>
							 </div>
							</div>
						</div>
						<div class="clear"></div>
						{!! Form::submit(trans('messages.filter'),['class' => 'btn btn-primary pull-right']) !!}
					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-12">
				<div class="box-info">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.document') !!}
					</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

	@stop