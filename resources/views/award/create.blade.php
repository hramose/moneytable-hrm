
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.award') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'award.store','role' => 'form', 'class'=>'award-form','id' => 'award-form','data-form-table' => 'award_table']) !!}
			@include('award._form')
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>