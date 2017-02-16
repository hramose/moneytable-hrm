
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.daily_report').' of '.showDate($daily_report->date) !!}</h4>
	</div>
	<div class="modal-body">
		<h4>{!! $daily_report->User->full_name_with_designation !!}</h4>
		{!! $daily_report->description !!}
	</div>
	<div class="modal-footer">
	</div>
