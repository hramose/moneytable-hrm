					@if(isset($contract_lists))
					<div class="form-group">
					    {!! Form::label('leave_contract_id',trans('messages.contract'),['class' => ''])!!}
						{!! Form::select('leave_contract_id', [null=>trans('messages.select_one')] + $contract_lists,'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					</div>
					@endif
				  	@foreach($leave_types->chunk(3) as $chunk)
				  		<div class="row">
	        				@foreach ($chunk as $leave_type)
	        				<div class="col-sm-4">
							  	<div class="form-group">
									<label for="to_date">{!! $leave_type->name !!}</label>
									<input type="number" class="form-control" name="leave[{!! $leave_type->id !!}]" placeholder="{!! $leave_type->leave_name !!}" value="{{ (isset($contract) && array_key_exists($leave_type->id,$leaves)) ? $leaves[$leave_type->id] : 0}}" required min="0">
							  	</div>
						  	</div>
						  	@endforeach
					  	</div>
				  	@endforeach
				  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
				  	<div class="clear"></div>