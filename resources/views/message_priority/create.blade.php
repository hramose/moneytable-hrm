
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.priority') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'message-priority.store','role' => 'form', 'class'=>'message-priority-form','id' => 'message-priority-form']) !!}
			@include('message_priority._form')
		{!! Form::close() !!}
	</div>
	<script>
	</script>
