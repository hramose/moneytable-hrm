				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label {!! !isset($user_location) ? 'class="sr-only"' : '' !!} for="from_date">{!! trans('messages.from_date') !!}</label>
							<input type="text" class="form-control datepicker" id="from_date" name="from_date" placeholder="{!! trans('messages.from_date') !!}" readonly="true" value="{!! isset($user_location->from_date) ? $user_location->from_date : '' !!}">
					  	</div>
					</div>
					<div class="col-sm-4">
					  	<div class="form-group">
							<label {!! !isset($user_location) ? 'class="sr-only"' : '' !!} for="to_date">{!! trans('messages.to_date') !!}</label>
							<input type="text" class="form-control datepicker" id="to_date" name="to_date" placeholder="{!! trans('messages.to_date') !!}" readonly="true" value="{!! isset($user_location->to_date) ? $user_location->to_date : '' !!}">
					  	</div>
				  	</div>
					<div class="col-sm-4">
					  	<div class="form-group">
						    {!! Form::label('location_id',trans('messages.location'),['class' => !isset($user_location) ? 'sr-only' : ''])!!}
							{!! Form::select('location_id', [null=>trans('messages.select_one')] + $locations,isset($user_location->location_id) ? $user_location->location_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						</div>
					</div>
				  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
				</div>
				<div class="clear"></div>