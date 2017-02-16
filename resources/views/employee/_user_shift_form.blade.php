				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label {!! !isset($user_shift) ? 'class="sr-only"' : '' !!} for="from_date">{!! trans('messages.from_date') !!}</label>
							<input type="text" class="form-control datepicker" id="from_date" name="from_date" placeholder="{!! trans('messages.from_date') !!}" readonly="true" value="{!! isset($user_shift->from_date) ? $user_shift->from_date : '' !!}">
					  	</div>
					</div>
					<div class="col-sm-4">
					  	<div class="form-group">
							<label {!! !isset($user_shift) ? 'class="sr-only"' : '' !!} for="to_date">{!! trans('messages.to_date') !!}</label>
							<input type="text" class="form-control datepicker" id="to_date" name="to_date" placeholder="{!! trans('messages.to_date') !!}" readonly="true" value="{!! isset($user_shift->to_date) ? $user_shift->to_date : '' !!}">
					  	</div>
				  	</div>
					<div class="col-sm-4">
					  	<div class="form-group">
						    {!! Form::label('office_shift_id',trans('messages.office_shift'),['class' => !isset($user_shift) ? 'sr-only' : ''])!!}
							{!! Form::select('office_shift_id', [null=>trans('messages.select_one')] + $office_shifts,isset($user_shift->office_shift_id) ? $user_shift->office_shift_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						</div>
					</div>
				</div>
				<div class="form-group">
			  		{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
			  	</div>
				<div class="clear"></div>