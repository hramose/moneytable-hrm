									<div class="col-sm-6">
										<div class="form-group">
										    {!! Form::label('company_name',trans('messages.company_name'))!!}
											{!! Form::input('text','company_name',isset($work_experience) ? $work_experience->company_name : '',['class'=>'form-control','placeholder'=>trans('messages.company_name')])!!}
										</div>
										<div class="form-group">
											<label class="" for="from_date">{!! trans('messages.from_date') !!}</label>
											<input type="text" class="form-control datepicker" id="from_date" name="from_date" placeholder="{!! trans('messages.from_date') !!}" readonly="true" value="{!! isset($work_experience->from_date) ? $work_experience->from_date : '' !!}">
									  	</div>
										<div class="form-group">
											<label class="" for="to_date">{!! trans('messages.to_date') !!}</label>
											<input type="text" class="form-control datepicker" id="to_date" name="to_date" placeholder="{!! trans('messages.to_date') !!}" readonly="true" value="{!! isset($work_experience->to_date) ? $work_experience->to_date : '' !!}">
									  	</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
										    {!! Form::label('post',trans('messages.post'))!!}
											{!! Form::input('text','post',isset($work_experience) ? $work_experience->post : '',['class'=>'form-control','placeholder'=>trans('messages.post')])!!}
										</div>
										<div class="form-group">
											{!! Form::label('description',trans('messages.description'),[])!!}
											{!! Form::textarea('description',isset($work_experience->description) ? $work_experience->description : '',['size' => '30x6', 'class' => 'form-control', 'placeholder' => trans('messages.description'),"data-show-counter" => 1,"data-limit" => config('config.textarea_limit'),'data-autoresize' => 1])!!}
											<span class="countdown"></span>
										</div>
										{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
									</div>
								<div class="clear"></div>