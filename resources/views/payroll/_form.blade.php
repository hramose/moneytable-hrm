		<div class="row">
	  		<div class="col-sm-6">
	  			<h2>{!! trans('messages.earning_salary') !!}</h2>
			  	@foreach($earning_salary_types as $earning_salary_type)
			  	<div class="form-group">
				    {!! Form::label($earning_salary_type->id,$earning_salary_type->head,[])!!}
					{!! Form::input('number',$earning_salary_type->id,((isset($payroll) && array_key_exists($earning_salary_type->id, $payroll)) ? round($payroll[$earning_salary_type->id],config('config.currency_decimal')) : 0),['class'=>'form-control','placeholder'=>$earning_salary_type->head,'step' => $currency_decimal_value])!!}
				</div>
				@endforeach
			</div>
	  		<div class="col-sm-6">
	  			<h2>{!! trans('messages.deduction_salary') !!}</h2>
			  	@foreach($deduction_salary_types as $deduction_salary_type)
			  	<div class="form-group">
				    {!! Form::label($deduction_salary_type->id,$deduction_salary_type->head,[])!!}
					{!! Form::input('number',$deduction_salary_type->id,((isset($payroll) && array_key_exists($deduction_salary_type->id, $payroll)) ? round($payroll[$deduction_salary_type->id],config('config.currency_decimal')) : 0),['class'=>'form-control','placeholder'=>$deduction_salary_type->head,'step' => $currency_decimal_value])!!}
				</div>
				@endforeach
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