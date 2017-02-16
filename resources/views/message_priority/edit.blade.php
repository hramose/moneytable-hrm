
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.priority') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($message_priority,['method' => 'PATCH','route' => ['message-priority.update',$message_priority->id] ,'class' => 'message-priority-form','id' => 'message-priority-form-edit','data-table-alter' => 'message-priority-table']) !!}
			@include('message_priority._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
