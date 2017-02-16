			<div class="col-sm-6">
			  <div class="form-group">
			    {!! Form::label('sid',trans('messages.sid'),[])!!}
				{!! Form::input('text','sid',(config('twilio.sid')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.sid')])!!}
			  </div>
			  <div class="form-group">
			    {!! Form::label('token',trans('messages.token'),[])!!}
				{!! Form::input('text','token',(config('twilio.token')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.token')])!!}
			  </div>
			  <div class="form-group">
			    {!! Form::label('from',trans('messages.sender_id'),[])!!}
				{!! Form::input('text','from',(config('twilio.from')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.sender_id')])!!}
			  </div>
			  {!! Form::hidden('config_type','sms')!!}
			{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
			</div>
			<div class="col-sm-6">
			</div>
			<div class="clear"></div>
