
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.ip_restriction') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'ip.store','role' => 'form', 'class'=>'ip-form','id' => 'ip-form']) !!}
			@include('ip._form')
		{!! Form::close() !!}
	</div>
	<script>
	</script>
