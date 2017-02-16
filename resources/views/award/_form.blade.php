			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('user_id',trans('messages.employee'),['class' => 'control-label'])!!}
						{!! Form::select('user_id[]', $users, isset($selected_user) ? $selected_user : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one'),'multiple' => true])!!}
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								{!! Form::label('award_type_id',trans('messages.award_type'),['class' => 'control-label'])!!}
								{!! Form::select('award_type_id', [''=> ''] + $award_types, isset($award->award_type_id) ? $award->award_type_id : '',['class'=>'form-control input-xlarge select2me','id'=>'award_type_id','placeholder'=>trans('messages.select_one')])!!}
								@if((defaultRole() || Entrust::can('manage_configuration')) && !isset($award))
									<p class="help-block"><a href="/award-type/create" target="_blank" data-toggle='modal' data-target='#myModal'>{!! trans('messages.add_new') !!}</a></p>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								{!! Form::label('date_of_award',trans('messages.date'),[])!!}
								{!! Form::input('text','date_of_award',isset($award->date_of_award) ? $award->date_of_award : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.date'),'readonly' => 'true'])!!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								{!! Form::label('month',trans('messages.month'),[])!!}
								{!! Form::select('month', [null=> trans('messages.select_one')] + $months, isset($award->month) ? $award->month : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								{!! Form::label('year',trans('messages.year'),[])!!}
								{!! Form::select('year', [null=> trans('messages.select_one')] + App\Classes\Helper::getYears(), isset($award->year) ? $award->year : date('Y'),['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								{!! Form::label('gift',trans('messages.gift'),[])!!}
								{!! Form::input('text','gift',isset($award->gift) ? $award->gift : '',['class'=>'form-control','placeholder'=>trans('messages.gift')])!!}
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								{!! Form::label('cash',trans('messages.cash'),[])!!}
								{!! Form::input('number','cash',isset($award->cash) ? round($award->cash,config('config.currency_decimal')) : '',['class'=>'form-control','placeholder'=>trans('messages.cash'),'step' => $currency_decimal_value,'min' => 0])!!}
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('description',trans('messages.description'),[])!!}
						{!! Form::textarea('description',isset($award->description) ? $award->description : '',['size' => '30x6', 'class' => 'form-control summernote-big', 'placeholder' => trans('messages.description')])!!}
					</div>
				</div>
			</div>
		  	{{ App\Classes\Helper::getCustomFields('award-form',$custom_field_values) }}
		  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
