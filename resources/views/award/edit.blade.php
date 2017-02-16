
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.award') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($award,['method' => 'PATCH','route' => ['award.update',$award] ,'class' => 'award-form','id' => 'award-form-edit','data-form-table' => 'award_table']) !!}
			@include('award._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>
