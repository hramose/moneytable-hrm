
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.contract_type') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'contract-type.store','role' => 'form', 'class'=>'contract-type-form','id' => 'contract-type-form']) !!}
			@include('contract_type._form')
		{!! Form::close() !!}
	</div>
	<script>
	</script>
