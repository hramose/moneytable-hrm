	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.designation') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'designation.store','role' => 'form', 'class'=>'designation-form','id' => 'designation-form','data-form-table' => 'designation_table']) !!}
			@include('designation._form')
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>