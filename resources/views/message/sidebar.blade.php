
				<div class="col-md-2">
					<a href="/message/compose" class="btn btn-warning btn-block md-trigger"><i class="fa fa-edit"></i> {!! trans('messages.compose') !!}</a>
					<div class="list-group menu-message">
					  <a href="/message" class="list-group-item active">
						{!! trans('messages.inbox') !!} <strong>({!! $count_inbox !!})</strong>
					  </a>
					  <a href="/message/sent" class="list-group-item">{!! trans('messages.sent_box') !!} <strong>({!! $count_sent !!})</strong></a>
					</div>
				</div>