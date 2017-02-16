
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.ticket') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($ticket,['method' => 'PATCH','route' => ['ticket.update',$ticket] ,'class' => 'ticket-form','id' => 'ticket-form-edit','data-form-table' => 'ticket_table']) !!}
			@include('ticket._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>