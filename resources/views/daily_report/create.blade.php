
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.daily_report') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'daily-report.store','role' => 'form', 'class'=>'daily-report-form','id' => 'daily-report-form','data-form-table' => 'daily_report_table']) !!}
			@include('daily_report._form')
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>