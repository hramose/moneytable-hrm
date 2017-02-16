@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/ticket">{!! trans('messages.ticket') !!}</a></li>
		    <li class="active">{!! trans('messages.add_new') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-8">
				<div class="box-info">
					<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.ticket') !!}
						<div class="additional-btn">
							@if(Entrust::can('manage_ticket'))
								<a href="/ticket"><button class="btn btn-sm btn-primary"><i class="fa fa-bars icon"></i> {!! trans('messages.list_all') !!}</button></a>
							@endif
						</div>
					</h2>
					{!! Form::open(['route' => 'ticket.store','role' => 'form', 'class'=>'ticket-form']) !!}
						@include('ticket._form')
					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-4">
				<div class="the-notes info"><h4>{!! trans('messages.help') !!}</h4>This module manages and maintains lists of issues, as needed by an organization. An employee or a manager can generate a ticket & it will be review further by higher authority.
				On resolving that issue, a ticket status can be closed by the reviewer. Tickets are solved on the basic of its priority.</div>
			</div>
		</div>

	@stop