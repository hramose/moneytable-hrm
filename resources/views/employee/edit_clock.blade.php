
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.attendance') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($clock,['method' => 'POST','route' => ['clock.clock-update',$clock->user_id,$clock->date,$clock->id] ,'class' => '','id' => 'update-clock-in-out','data-table-alter' => 'clock-list-table','data-submit' => 'noAjax']) !!}
			<div class="form-group row">
		  	  {!! Form::label('time',trans('messages.date_time'),['class' => 'col-md-2'])!!}
		  	  <div class="col-md-4">
			  {!! Form::input('text','clock_in',isset($clock->clock_in) ? date('Y-m-d h:i A',strtotime($clock->clock_in)) : '',['class'=>'form-control datetimepicker','placeholder'=>trans('messages.date_time'),'readonly' => true])!!}
			  </div>
			  <div class="col-md-4">
			  {!! Form::input('text','clock_out',isset($clock->clock_out) ? date('Y-m-d h:i A',strtotime($clock->clock_out)) : '',['class'=>'form-control datetimepicker','placeholder'=>trans('messages.clock_out'),'readonly' => true])!!}
			  </div>
			</div>
			{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary']) !!}
		{!! Form::close() !!}
	</div>
