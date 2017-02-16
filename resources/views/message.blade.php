		@if(!config('code.mode'))
		<div class="row">
        		<div class="col-md-12">
	        		<div class="box-info">
					<div class="alert alert-info">You are free to perform all actions. It gets reset in every 30 minutes.</div>
				</div>
			</div>
		</div>
		@endif
        @if(config('config.application_setup_info') && defaultRole())
        	<div class="row" id="setup_panel">
        		<div class="col-md-12">
	        		<div class="box-info">
	        			<h2>
	    					<strong>{!! trans('messages.application_setup_info') !!}</strong>
	    					<div class="additional-btn">
							{!! Form::open(['route' => 'setup-complete','role' => 'form', 'class'=>'form-inline','id' => 'setup-complete-form','data-setup-complete' => 1]) !!}
							<button type="submit" class="btn btn-danger btn-sm">{{ trans('messages.hide') }}</button>
							{!! Form::close() !!}
							</div>
	        			</h2>
	        			<div id="setup_info">
        					{!! $setup_info !!}
        				</div>
        			</div>
        		</div>
			</div>
        @endif