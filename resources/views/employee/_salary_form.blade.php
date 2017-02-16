									@if(isset($contract_lists))
									<div class="form-group">
									    {!! Form::label('salary_contract_id',trans('messages.contract'),['class' => ''])!!}
										{!! Form::select('salary_contract_id', [null=>trans('messages.select_one')] + $contract_lists,isset($contract) ? $contract->id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
									</div>
									@endif
									<div class="row">
										<div class="col-sm-6">
			    				  			<h6>({!! trans('messages.earning_salary') !!})</h6>
					    				  	@foreach($earning_salary_types as $earning_salary_type)
					    				  	<div class="form-group">
											    {!! Form::label($earning_salary_type->id,$earning_salary_type->head,[])!!}
												{!! Form::input('number',$earning_salary_type->id,(isset($contract) && array_key_exists($earning_salary_type->id,$salaries)) ? round($salaries[$earning_salary_type->id]) : '',['min'=>'0','class'=>'form-control','placeholder'=> trans('messages.salary_amount'),'step' => $currency_decimal_value])!!}
											</div>
											@endforeach
										</div>
			    				  		<div class="col-sm-6">
			    				  			<h6>({!! trans('messages.deduction_salary') !!})</h6>
					    				  	@foreach($deduction_salary_types as $deduction_salary_type)
					    				  	<div class="form-group">
											    {!! Form::label($deduction_salary_type->id,$deduction_salary_type->head,[])!!}
												{!! Form::input('number',$deduction_salary_type->id, (isset($contract) && array_key_exists($deduction_salary_type->id,$salaries)) ? round($salaries[$deduction_salary_type->id]) : '',['min'=>'0','class'=>'form-control','placeholder'=> trans('messages.salary_amount'),'step' => $currency_decimal_value])!!}
											</div>
											@endforeach
										</div>
									</div>
									@if(count($earning_salary_types) || count($deduction_salary_types))
									{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
									@endif
									<div class="clear"></div>