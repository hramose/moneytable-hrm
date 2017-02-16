
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.ip_restriction') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($ip,['method' => 'PATCH','route' => ['ip.update',$ip->id] ,'class' => 'ip-form','id' => 'award-type-form-edit','data-table-alter' => 'ip-table']) !!}
			@include('ip._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
