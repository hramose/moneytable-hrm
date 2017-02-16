
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.daily_report') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($daily_report,['method' => 'PATCH','route' => ['daily-report.update',$daily_report] ,'class' => 'daily-report-form','id' => 'daily-report-form-edit','data-form-table' => 'daily_report_table']) !!}
			@include('daily_report._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>
