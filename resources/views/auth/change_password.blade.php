
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.change').' '.trans('messages.password') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'change-password','role' => 'form', 'class' => 'form-horizontal change-password-form','id' => "change-password-form"]) !!}
			<div class="form-group">
		    {!! Form::label('old_password',trans('messages.current_password'),['class' => 'col-sm-2 control-label'])!!}
		    <div class="col-sm-10">
				{!! Form::input('password','old_password','',['class'=>'form-control','placeholder'=>trans('messages.current_password')])!!}
			</div>
		  </div>
		  <div class="form-group">
		    {!! Form::label('new_password',trans('messages.new_password'),['class' => 'col-sm-2 control-label'])!!}
		    <div class="col-sm-10">
				{!! Form::input('password','new_password','',['class'=>'form-control','placeholder'=>trans('messages.new_password')])!!}
			</div>
		  </div>
		  <div class="form-group">
		    {!! Form::label('new_password_confirmation',trans('messages.confirm_password'),['class' => 'col-sm-2 control-label'])!!}
		    <div class="col-sm-10">
				{!! Form::input('password','new_password_confirmation','',['class'=>'form-control','placeholder'=>trans('messages.confirm_password')])!!}
			</div>
		  </div>
		  <div class="col-sm-offset-2 col-sm-10">
		  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
		  </div>
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>