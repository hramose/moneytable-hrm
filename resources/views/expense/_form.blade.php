			<div class="row">
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								{!! Form::label('expense_head_id',trans('messages.expense_head'),['class' => 'control-label'])!!}
								{!! Form::select('expense_head_id', [''=>''] + ($expense_heads), isset($expense->expense_head_id) ? $expense->expense_head_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
								@if((defaultRole() || Entrust::can('manage_configuration')) && !isset($expense))
									<p class="help-block"><a href="#" data-href="/expense-head/create" data-toggle="modal" data-target="#myModal">{!! trans('messages.add_new') !!}</a></p>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							{!! Form::label('date_of_expense',trans('messages.date_of_expense'),[])!!}
							{!! Form::input('text','date_of_expense',isset($expense->date_of_expense) ? $expense->date_of_expense : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.date_of_expense'),'readonly' => 'true'])!!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								{!! Form::label('amount',trans('messages.amount'),[])!!}
								{!! Form::input('number','amount',isset($expense->amount) ? round($expense->amount,config('config.currency_decimal')) : '',['class'=>'form-control','step' => $currency_decimal_value, 'placeholder'=>trans('messages.amount'),'min' => 0])!!}
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								{!! Form::input('file','attachments','',['class'=>'btn btn-default','title' => trans('messages.select').' '.trans('messages.file')])!!}
								@if(isset($expense) && $expense->attachments != null)
									<div class="checkbox">
										<label>
											{!! Form::checkbox('remove', 1) !!} {!! trans('messages.remove') !!}
										</label>
									</div>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('remarks',trans('messages.remarks'),[])!!}
						{!! Form::textarea('remarks',isset($expense->remarks) ? $expense->remarks : '',['size' => '30x6', 'class' => 'form-control', 'placeholder' => trans('messages.remarks'),"data-show-counter" => 1,"data-limit" => config('config.textarea_limit'),'data-autoresize' => 1])!!}
						<span class="countdown"></span>
					</div>
				</div>
			</div>
		  	{{ App\Classes\Helper::getCustomFields('expense-form',$custom_field_values) }}
		  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
