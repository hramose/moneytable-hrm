			<div class="col-sm-6">
			  <div class="form-group">
			    {!! Form::label('driver',trans('messages.driver'),[])!!}
				{!! Form::select('driver', [
					null=>'Please Select',
					'smtp' => 'smtp',
					'mailgun' => 'mailgun',
					'mandrill' => 'mandrill',
					'log' => 'log'
					],(config('mail.driver')) ? : '',['id' => 'mail_driver', 'class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
			  </div>
			  <div class="form-group">
			    {!! Form::label('from_address',trans('messages.from_address'),[])!!}
				{!! Form::input('email','from_address',config('mail.from.address') ? : '',['class'=>'form-control','placeholder'=>trans('messages.from_address')])!!}
			  </div>
			  <div class="form-group">
			    {!! Form::label('from_name',trans('messages.from_name'),[])!!}
				{!! Form::input('text','from_name',config('mail.from.name') ? : '',['class'=>'form-control','placeholder'=>trans('messages.from_name')])!!}
			  </div>
			  {!! Form::hidden('config_type','mail')!!}
			{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
			</div>
			<div class="col-sm-6">
				<div id="mail_configuration" class="mail_config">
					<div class="the-notes info"><h4>{!! trans('messages.help') !!}</h4>
					You may not be able to send, if you are using this application on your local server. Once uploaded to 
					live webserver, you will be able to send mail by this mail driver. It is one of the easiest mail driver to send mail with zero configuration.
					</div>
				</div>
				<div id="sendmail_configuration" class="mail_config">
					<div class="the-notes info"><h4>{!! trans('messages.help') !!}</h4>
					You may not be able to send, if you are using this application on your local server. Once uploaded to 
					live webserver, you will be able to send mail by this mail driver. It is one of the easiest mail driver to send mail with zero configuration.
					</div>
				</div>
				<div id="log_configuration" class="mail_config">
					<div class="the-notes info"><h4>{!! trans('messages.help') !!}</h4>
					You won't be able to send mail by using this driver, but all your sent mail will be logged into laravel log file.
					</div>
				</div>
				<div id="smtp_configuration" class="mail_config">
				  <div class="form-group">
				    {!! Form::label('host',trans('messages.smtp_host'),[])!!}
					{!! Form::input('text','host',(config('mail.host')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.smtp_host')])!!}
				  </div>
				  <div class="form-group">
				    {!! Form::label('port',trans('messages.smtp_port'),[])!!}
					{!! Form::input('text','port',(config('mail.port')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.smtp_port')])!!}
				  </div>
				  <div class="form-group">
				    {!! Form::label('username',trans('messages.smtp_username'),[])!!}
					{!! Form::input('text','username',(config('mail.username')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.smtp_username')])!!}
				  </div>
				  <div class="form-group">
				    {!! Form::label('password',trans('messages.smtp_password'),[])!!}
					{!! Form::input('password','password',(config('mail.password')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.smtp_password')])!!}
				  </div>
				  <div class="form-group">
				    {!! Form::label('encryption',trans('messages.encryption'),[])!!}
					{!! Form::input('text','encryption',(config('mail.encryption')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.encryption')])!!}
				  </div>
					<div class="the-notes info"><h4>{!! trans('messages.help') !!}</h4>
					You may send email from local server as well as live web server by using this mail driver.
					If you want to use gmail setting, then you have to configure some of the preferences in your gmail account.
					</div>
				</div>
				<div id="mandrill_configuration" class="mail_config">
				  <div class="form-group">
				    {!! Form::label('mandrill_secret',trans('messages.secret_key'),[])!!}
					{!! Form::input('text','mandrill_secret',(config('services.mandrill.secret')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.secret_key')])!!}
				  </div>
					<div class="the-notes info"><h4>{!! trans('messages.help') !!}</h4>
					You may send email from local server as well as live web server by using this mail driver.
					You must have a working mandrill account for using this driver.
					</div>
				</div>
				<div id="mailgun_configuration" class="mail_config">
				  <div class="form-group">
				    {!! Form::label('mailgun_domain',trans('messages.domain'),[])!!}
					{!! Form::input('text','mailgun_domain',(config('services.mailgun.domain')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.domain')])!!}
				  </div>
				  <div class="form-group">
				    {!! Form::label('mailgun_secret',trans('messages.secret_key'),[])!!}
					{!! Form::input('text','mailgun_secret',(config('services.mailgun.secret')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.secret_key')])!!}
				  </div>
				  <div class="form-group">
				    {!! Form::label('mailgun_host',trans('messages.smtp_host'),[])!!}
					{!! Form::input('text','mailgun_host',(config('mail.host')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.smtp_host')])!!}
				  </div>
				  <div class="form-group">
				    {!! Form::label('mailgun_port',trans('messages.smtp_port'),[])!!}
					{!! Form::input('text','mailgun_port',(config('mail.port')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.smtp_port')])!!}
				  </div>
				  <div class="form-group">
				    {!! Form::label('mailgun_username',trans('messages.smtp_username'),[])!!}
					{!! Form::input('text','mailgun_username',(config('mail.username')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.smtp_username')])!!}
				  </div>
				  <div class="form-group">
				    {!! Form::label('mailgun_password',trans('messages.smtp_password'),[])!!}
					{!! Form::input('text','mailgun_password',(config('mail.password')) ? : '',['class'=>'form-control','placeholder'=>trans('messages.smtp_password')])!!}
				  </div>
					<div class="the-notes info"><h4>{!! trans('messages.help') !!}</h4>
					You may send email from local server as well as live web server by using this mail driver.
					You must have a working mailgun account for using this driver.
					</div>
				</div>
			</div>
			<div class="clear"></div>
