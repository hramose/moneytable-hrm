
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.change').' '.trans('messages.status') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($employee,['method' => 'POST','route' => ['employee.change-status',$employee] ,'class' => 'employee-change-status-form','id' => 'employee-change-status-form','data-form-table' => 'employee_table']) !!}
		  	<div class="form-group">
				<label for="to_date">{!! trans('messages.status') !!}</label>
				{!! Form::select('status', [null => trans('messages.select_one'),
				'active' => 'Active',
				'banned' => 'Banned'],$employee->status,['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
		  	</div>
			<div class="form-group">
			<button type="submit" class="btn btn-default btn-success pull-right">{!! trans('messages.save') !!}</button>
			</div>
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>