
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.location') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($user_location,['method' => 'PATCH','route' => ['user-location.update',$user_location->id] ,'class' => 'user-location-form','id' => 'user-location-form-edit','data-table-alter' => 'user-location-table']) !!}
			@include('employee._user_location_form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>