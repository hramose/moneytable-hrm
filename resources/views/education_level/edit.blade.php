
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.education_level') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($education_level,['method' => 'PATCH','route' => ['education-level.update',$education_level->id] ,'class' => 'education-level-form','id' => 'education-level-form-edit','data-table-alter' => 'education-level-table']) !!}
			@include('education_level._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
