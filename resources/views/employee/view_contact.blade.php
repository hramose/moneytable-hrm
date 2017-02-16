
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! $contact->User->full_name.' '.trans('messages.contact') !!}</h4>
	</div>
	<div class="modal-body">
		<h4>{{ trans('messages.name').': '.$contact->name }}</h4>
		{!! ($contact->is_primary) ? '<span class="label label-success">'.trans('messages.primary').'</span>' : '' !!}
		{!! ($contact->is_dependent) ? '<span class="label label-danger">'.trans('messages.dependent').'</span>' : '' !!}
		<br /><br />
		<address>
		  <i class="fa fa-map-marker icon"></i> <strong>{{ $contact->address_1 }}</strong><br>
		  {{ $contact->address_2 }}<br>
		  {{ $contact->city.', '.$contact->state.', '.$contact->zipcode.', '.$contact->country_id }}<br><br />
		  <i class="fa fa-phone-square icon"></i> {{ trans('messages.work').': '.$contact->work_phone.' '.trans('messages.ext').': '.$contact->work_phone_ext }} <br />
		  <i class="fa fa-mobile icon"></i> {{ trans('messages.mobile').' | '. $contact->mobile.' | '. trans('messages.home').': '.$contact->home }} <br /><br />
		  <i class="fa fa-envelope icon"></i> {{ trans('messages.work').': '.$contact->work_email.' | '.trans('messages.personal').': '.$contact->personal_email }}
		</address>
	</div>