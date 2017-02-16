
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.announcement') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'announcement.store','role' => 'form', 'class'=>'announcement-form','id' => 'announcement-form','data-form-table' => 'announcement_table']) !!}
			@include('announcement._form')
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>