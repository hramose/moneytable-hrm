
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.work_experience') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($work_experience,['method' => 'PATCH','route' => ['work-experience.update',$work_experience->id] ,'class' => 'work-experience-form', 'role' => 'form','id' => 'work-experience-edit-form','data-table-alter' => 'work-experience-table']) !!}
		  	@include('employee._work_experience_form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
