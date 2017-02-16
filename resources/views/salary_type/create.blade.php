
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.salary_type') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'salary-type.store','role' => 'form', 'class'=>'salary-type-form','id' => 'salary-type-form']) !!}
			@include('salary_type._form')
		{!! Form::close() !!}
	</div>
	<script>
	</script>
