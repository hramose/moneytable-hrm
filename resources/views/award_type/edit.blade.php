
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.award_type') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($award_type,['method' => 'PATCH','route' => ['award-type.update',$award_type->id] ,'class' => 'award-type-form','id' => 'award-type-form-edit','data-table-alter' => 'award-type-table']) !!}
			@include('award_type._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
