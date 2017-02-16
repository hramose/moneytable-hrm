@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.expense').' '.trans('messages.statistics') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.expense').' '.trans('messages.statistics') !!}
					<div class="additional-btn">
						<a href="/expense"><button class="btn btn-sm btn-primary"><i class="fa fa-credit-card icon"></i> {!! trans('messages.expense').' '.trans('messages.request') !!}</button></a>
					</div>
					</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>
	@stop