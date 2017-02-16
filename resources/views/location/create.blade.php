	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.location') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'location.store','role' => 'form', 'class'=>'location-form','id' => 'location-form','data-form-table' => 'location_table']) !!}
			@include('location._form')
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>