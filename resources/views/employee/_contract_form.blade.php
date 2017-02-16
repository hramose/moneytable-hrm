				<div class="col-md-6">
				  	<div class="form-group">
					    {!! Form::label('contract_type_id',trans('messages.contract_type'),['class' => ''])!!}
						{!! Form::select('contract_type_id', [null=>trans('messages.select_one')] + $contract_types,isset($contract->contract_type_id) ? $contract->contract_type_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						@if(defaultRole() || Entrust::can('manage_configuration'))
							<div class="help-block"><a href="#" data-href="/contract-type/create" data-toggle="modal" data-target="#myModal">{!! trans('messages.add_new') !!}</a></div>
						@endif
					</div>
					<div class="form-group">
						<label class="" for="from_date">{!! trans('messages.from_date') !!}</label>
						<input type="text" class="form-control datepicker" id="from_date" name="from_date" placeholder="{!! trans('messages.from_date') !!}" readonly="true" value="{!! isset($contract->from_date) ? $contract->from_date : '' !!}">
				  	</div>
				  	<div class="form-group">
					    {!! Form::label('new_designation_id',trans('messages.designation'),['class' => ''])!!}
						{!! Form::select('new_designation_id', [null=>trans('messages.select_one')] + $designations,isset($contract->designation_id) ? $contract->designation_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
					    {!! Form::label('title',trans('messages.contract').' '.trans('messages.title'),['class' => ''])!!}
						{!! Form::input('text','title',isset($contract->title) ? $contract->title : '',['class'=>'form-control','placeholder'=>trans('messages.contract').' '.trans('messages.title')])!!}
					</div>
				  	<div class="form-group">
						<label class="" for="to_date">{!! trans('messages.to_date') !!}</label>
						<input type="text" class="form-control datepicker" id="to_date" name="to_date" placeholder="{!! trans('messages.to_date') !!}" readonly="true" value="{!! isset($contract->to_date) ? $contract->to_date : '' !!}">
				  	</div>
					<div class="form-group">
					    {!! Form::label('description',trans('messages.description'),[])!!}
					    {!! Form::textarea('description',isset($contract->description) ? $contract->description : '',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.description'),"data-show-counter" => 1,"data-limit" => config('config.textarea_limit'),'data-autoresize' => 1])!!}
					    <span class="countdown"></span>
					</div>
				</div>
				  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
				<div class="clear"></div>