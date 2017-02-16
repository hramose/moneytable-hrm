		
		<div class="row">
			<div class="col-sm-6">
				<div class="checkbox">
					<label>
						{!! Form::checkbox('hourly_payroll', 1,(isset($hourly_payroll) && $hourly_payroll) ? 'checked' : '',['id' => 'hourly_payroll_salary']) !!} {!! trans('messages.hourly_payroll') !!}
					</label>
				</div>
			</div>
			<div class="col-sm-6" id="hourly_rate_salary">
	  			<div class="form-group">
				    {!! Form::label('hourly',trans('messages.hourly'),[])!!}
					{!! Form::input('number','hourly',isset($hourly) ? $hourly : '',['min'=>'0','class'=>'form-control','placeholder'=>trans('messages.hourly'),'step' => $currency_decimal_value])!!}
				</div>
			</div>
		</div>
		<div id="monthly_rate_salary">
			<div class="row">
		  		<div class="col-sm-6">
		  			<h2>{!! trans('messages.earning_salary') !!}</h2>
		  			<div class="form-group">
					    {!! Form::label('overtime',trans('messages.overtime'),[])!!}
						{!! Form::input('number','overtime',isset($overtime) ? $overtime : '',['min'=>'0','class'=>'form-control','placeholder'=>trans('messages.overtime'),'step' => $currency_decimal_value])!!}
					</div>
		  		</div>
		  		<div class="col-sm-6">
		  			<h2>{!! trans('messages.deduction_salary') !!}</h2>
		  			<div class="form-group">
					    {!! Form::label('late',trans('messages.late'),[])!!}
						{!! Form::input('number','late',isset($late) ? $late : '',['min'=>'0','class'=>'form-control','placeholder'=>trans('messages.late'),'step' => $currency_decimal_value])!!}
					</div>
		  			<div class="form-group">
					    {!! Form::label('early_leaving',trans('messages.early_leaving'),[])!!}
						{!! Form::input('number','early_leaving',isset($early_leaving) ? $early_leaving : '',['min'=>'0','class'=>'form-control','placeholder'=>trans('messages.early_leaving'),'step' => $currency_decimal_value])!!}
					</div>
		  		</div>
		  	</div>
		  	<hr />
			<div class="row">
		  		<div class="col-sm-6">
				  	@foreach($earning_salary_types as $earning_salary_type)
				  	<div class="form-group">
					    {!! Form::label($earning_salary_type->id,$earning_salary_type->head,[])!!}
						{!! Form::input('number',$earning_salary_type->id,$salary_values[$earning_salary_type->id],['class'=>'form-control','placeholder'=>$earning_salary_type->head,'step' => $currency_decimal_value])!!}
					</div>
					@endforeach
				</div>
		  		<div class="col-sm-6">
				  	@foreach($deduction_salary_types as $deduction_salary_type)
				  	<div class="form-group">
					    {!! Form::label($deduction_salary_type->id,$deduction_salary_type->head,[])!!}
						{!! Form::input('number',$deduction_salary_type->id,$salary_values[$deduction_salary_type->id],['class'=>'form-control','placeholder'=>$deduction_salary_type->head,'step' => $currency_decimal_value])!!}
					</div>
					@endforeach
				</div>
			</div>
		</div>
		@if(config('config.payroll_contribution_field'))
		<h2>{!! trans('messages.contribution') !!}</h2>
		<div class="row">
			<div class="col-sm-4">
				<div class="form-group">
				    {!! Form::label('employee_contribution',trans('messages.employee_contribution'),[])!!}
					{!! Form::input('number','employee_contribution',isset($payroll_slip->employee_contribution) ? round($payroll_slip->employee_contribution,config('config.currency_decimal')) : '',['class'=>'form-control','placeholder'=>trans('messages.employee_contribution'),'step' => $currency_decimal_value])!!}
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
				    {!! Form::label('employer_contribution',trans('messages.employer_contribution'),[])!!}
					{!! Form::input('number','employer_contribution',isset($payroll_slip->employer_contribution) ? round($payroll_slip->employer_contribution,config('config.currency_decimal')) : '',['class'=>'form-control','placeholder'=>trans('messages.employer_contribution'),'step' => $currency_decimal_value])!!}
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					{!! Form::label('date_of_contribution',trans('messages.date_of_contribution'),[])!!}
					{!! Form::input('text','date_of_contribution',isset($payroll_slip->date_of_contribution) ? $payroll_slip->date_of_contribution : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.date'),'readonly' => 'true'])!!}
				</div>
			</div>
		</div>
		@endif
		{{ App\Classes\Helper::getCustomFields('payroll-form',$custom_field_values) }}
		{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}