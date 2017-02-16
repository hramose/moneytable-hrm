@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.payroll') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.payroll') !!}
					@if(Entrust::can('generate_payroll'))
					<div class="additional-btn">
						<a href="/payroll/create"><button class="btn btn-sm btn-primary"><i class="fa fa-plus icon"></i> {!! trans('messages.generate_new_payroll') !!}</button></a>
					</div>
					@endif
					</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

	@stop