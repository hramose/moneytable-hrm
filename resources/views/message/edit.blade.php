
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.message') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($message,['method' => 'PATCH','route' => ['message.update',$message] ,'class' => 'message-form','id' => 'message-form-edit','data-form-table' => 'message_table']) !!}
		  <div class="form-group">
		    {!! Form::label('message_priority_id',trans('messages.priority'),[])!!}
			{!! Form::select('message_priority_id', $message_priorities,isset($message->message_priority_id) ? $message->message_priority_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
		  </div>
		<div class="form-group">
			{!! Form::select('status', $status, $message->status,['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
		</div>
		<div class="form-group">
		  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
		</div>
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>