@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.language') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.language') !!}</h2>
					{!! Form::open(['route' => 'language.store','role' => 'form', 'class'=>'language-form','id' => 'language-form','data-form-table' => 'language_table']) !!}
						@include('language._form')
					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-8">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.language') !!}</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>

		</div>

	@stop