
				<div class="col-md-2">
					<a href="/message/compose" class="btn btn-warning btn-block md-trigger"><i class="fa fa-edit"></i> {!! trans('messages.compose') !!}</a>
					<div class="list-group menu-message">
					  <a href="/message" class="list-group-item">
						{!! trans('messages.inbox') !!} <strong>({!! $count_inbox !!})</strong>
					  </a>
					  <a href="/message/sent" class="list-group-item">{!! trans('messages.sent_box') !!} <strong>({!! $count_sent !!})</strong></a>
					</div>

					@if(in_array('search',$assets))
					{!! Form::open(['route' => 'message.search','role' => 'form','class'=>'','id' => 'message_search','data-form-table' => 'message_table','data-no-form-clear' => 1]) !!}
						<div class="form-group">
							{!! Form::select('user_id', [null => 'Employee'] + $users, '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						</div>
						<div class="form-group">
							{!! Form::select('message_priority_id', [null => 'Priority'] + $message_priorities, '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						</div>
						<div class="form-group">
							{!! Form::select('message_category_id', [null => 'Category'] + $message_categories, '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						</div>
						<div class="form-group">
							{!! Form::select('location_id', [null => 'Location'] + $locations, '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						</div>
						<div class="form-group">
							{!! Form::select('status', [null => 'Status'] + $status, '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						</div>
						<div class="form-group">
						  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.filter'),['class' => 'btn btn-primary pull-right']) !!}
						</div>
					{!! Form::close() !!}
					@endif

				</div>