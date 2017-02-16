
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.employee') !!}</h4>
	</div>
	<div class="modal-body">

		{!! Form::open(['route' => 'auth.register','role' => 'form', 'class'=>'employee-form','id' => 'employee-form','data-form-table' => 'employee_table']) !!}
		<div class="col-md-6">
			<div class="form-group">
			    {!! Form::label('first_name',trans('messages.first_name'),['class' => 'control-label'])!!}
				{!! Form::input('text','first_name','',['class'=>'form-control','placeholder'=>trans('messages.first_name')])!!}
			</div>
			<div class="form-group">
			    {!! Form::label('last_name',trans('messages.last_name'),['class' => 'control-label'])!!}
				{!! Form::input('text','last_name','',['class'=>'form-control','placeholder'=>trans('messages.last_name')])!!}
			</div>
			<div class="form-group">
			    {!! Form::label('employee_code',trans('messages.employee_code'),['class' => 'control-label'])!!}
				{!! Form::input('text','employee_code','',['class'=>'form-control','placeholder'=>trans('messages.employee_code')])!!}
			</div>	
			<div class="form-group">
			    {!! Form::label('designation_id',trans('messages.designation'),['class' => 'control-label'])!!}
				{!! Form::select('designation_id', [''=>''] + $designations,'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
			</div>	
			<div class="form-group">
			    {!! Form::label('role_id',trans('messages.role'),['class' => 'control-label'])!!}
				{!! Form::select('role_id', [''=>''] + $roles,'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
			</div>	
		</div>
		<div class="col-md-6">
			<div class="form-group">
			    {!! Form::label('username',trans('messages.username'),['class' => 'control-label'])!!}
				{!! Form::input('text','username','',['class'=>'form-control','placeholder'=>trans('messages.username')])!!}
			</div>
			<div class="form-group">
			    {!! Form::label('email',trans('messages.email'),['class' => 'control-label'])!!}
				{!! Form::input('text','email','',['class'=>'form-control','placeholder'=>trans('messages.email')])!!}
			</div>
			<div class="form-group">
			    {!! Form::label('password',trans('messages.password'),['class' => 'control-label'])!!}
				{!! Form::input('password','password','',['class'=>'form-control','placeholder'=>trans('messages.password')])!!}
			</div>
			<div class="form-group">
			    {!! Form::label('password_confirmation',trans('messages.confirm_password'),['class' => 'control-label'])!!}
				{!! Form::input('password','password_confirmation','',['class'=>'form-control','placeholder'=>trans('messages.confirm_password')])!!}
			</div>
			<div class="form-group">
				<div class="radio">
					<label>
						{!! Form::checkbox('send_welcome_email', '1', '').' '.trans('messages.send_welcome_email') !!}
					</label>
				</div>
			</div>
			{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
		</div>
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>