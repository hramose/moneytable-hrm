			<div class="col-sm-12">
			  <div class="form-group">
			    {!! Form::label('allow_multiple_review_per_user',trans('messages.allow_multiple_review_per_user'),['class' => 'control-label'])!!}
				<div class="radio">
					<label>
						{!! Form::radio('allow_multiple_review_per_user', '1', (config('config.allow_multiple_review_per_user') == '1') ? 'checked' : '') !!} {!! trans('messages.yes') !!}
					</label>
					<label>
						{!! Form::radio('allow_multiple_review_per_user', '0', (config('config.allow_multiple_review_per_user') != '1') ? 'checked' : '') !!} {!! trans('messages.no') !!}
					</label>
				</div>
			  </div>
			  {!! Form::hidden('config_type','review')!!}
			{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
			</div>
			<div class="clear"></div>
