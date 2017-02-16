
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.award_type') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'award-type.store','role' => 'form', 'class'=>'award-type-form','id' => 'award-type-form']) !!}
			@include('award_type._form')
		{!! Form::close() !!}
	</div>
	<script>
	</script>
