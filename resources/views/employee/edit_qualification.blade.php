
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.qualification') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($qualification,['method' => 'PATCH','route' => ['qualification.update',$qualification->id] ,'class' => 'qualification-form', 'role' => 'form','id' => 'qualification-edit-form','data-table-alter' => 'qualification-table']) !!}
		  	@include('employee._qualification_form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
