
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.announcement') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($announcement,['method' => 'PATCH','route' => ['announcement.update',$announcement] ,'class' => 'announcement-form','id' => 'announcement-form-edit','data-form-table' => 'announcement_table']) !!}
			@include('announcement._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>