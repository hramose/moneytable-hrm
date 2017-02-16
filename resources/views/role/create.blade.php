
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.role') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'role.store','role' => 'form', 'class'=>'role-form','id' => 'role-form']) !!}
			@include('role._form')
		{!! Form::close() !!}
	</div>
	<script>
	</script>
