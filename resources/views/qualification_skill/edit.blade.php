
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.skill') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($qualification_skill,['method' => 'PATCH','route' => ['qualification-skill.update',$qualification_skill->id] ,'class' => 'qualification-skill-form','id' => 'qualification-skill-form-edit','data-table-alter' => 'qualification-skill-table']) !!}
			@include('qualification_skill._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
