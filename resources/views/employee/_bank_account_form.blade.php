									<div class="col-sm-6">
										<div class="checkbox">
											<label>
											  {!! Form::checkbox('is_primary', 1,(isset($bank_account) && $bank_account->is_primary) ? 'checked' : '') !!} {!! trans('messages.primary_account') !!}
											</label>
										</div>
										<div class="form-group">
										    {!! Form::label('account_name',trans('messages.account_name'))!!}
											{!! Form::input('text','account_name',isset($bank_account) ? $bank_account->account_name : '',['class'=>'form-control','placeholder'=>trans('messages.account_name')])!!}
										</div>
										<div class="form-group">
										    {!! Form::label('account_number',trans('messages.account_number'))!!}
											{!! Form::input('text','account_number',isset($bank_account) ? $bank_account->account_number : '',['class'=>'form-control','placeholder'=>trans('messages.account_number')])!!}
										</div>
									</div>
									<div class="col-sm-6">
				    				  	<div class="form-group">
										    {!! Form::label('bank_name',trans('messages.bank_name'))!!}
											{!! Form::input('text','bank_name',isset($bank_account) ? $bank_account->bank_name : '',['class'=>'form-control','placeholder'=>trans('messages.bank_name')])!!}
										</div>
										<div class="form-group">
										    {!! Form::label('bank_code',trans('messages.bank_code'))!!}
											{!! Form::input('text','bank_code',isset($bank_account) ? $bank_account->bank_code : '',['class'=>'form-control','placeholder'=>trans('messages.bank_code')])!!}
										</div>
										<div class="form-group">
										    {!! Form::label('bank_branch',trans('messages.bank_branch'))!!}
											{!! Form::input('text','bank_branch',isset($bank_account) ? $bank_account->bank_branch : '',['class'=>'form-control','placeholder'=>trans('messages.bank_branch')])!!}
										</div>
										{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
									</div>
								<div class="clear"></div>

							  <script>
								$(function() {
							  	 	Validate.init();
							    });
								$('input').iCheck({
								checkboxClass: 'icheckbox_flat-blue',
								radioClass: 'iradio_flat-blue',
								increaseArea: '20%' // optional
								});
							  </script>