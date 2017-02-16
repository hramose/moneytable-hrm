
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.payroll') !!}</h4>
	</div>
	<div class="modal-body">
		<p>{!! $payroll_slip->User->full_name_with_designation.' '.trans('messages.payslip').' '.trans('messages.from').' '.showDate($payroll_slip->from_date).' '.trans('messages.to').' '.showDate($payroll_slip->to_date) !!}</p>
		{!! Form::model('',['method' => 'PATCH','route' => ['payroll.update',$payroll_slip->id] ,'class' => 'payroll-form','id' => 'payroll-form-edit','data-submit' => 'noAjax']) !!}
			@include('payroll._form')
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>