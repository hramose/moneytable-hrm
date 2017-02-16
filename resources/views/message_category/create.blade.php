
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.category') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'message-category.store','role' => 'form', 'class'=>'message-category-form','id' => 'message-category-form']) !!}
			@include('message_category._form')
		{!! Form::close() !!}
	</div>
	<script>
	</script>
