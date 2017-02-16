	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.designation') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($designation,['method' => 'PATCH','route' => ['designation.update',$designation] ,'class' => 'designation-form','id' => 'designation-form-edit','data-form-table' => 'designation_table']) !!}
			@include('designation._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>