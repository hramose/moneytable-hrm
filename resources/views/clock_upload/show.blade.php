
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.attendance').' '.trans('messages.upload').' '.trans('messages.log') !!} ({{ trans('messages.rejected').' '.trans('messages.attendance') }})</h4>
	</div>
	<div class="modal-body">
		@if(count($clock_fails))
			<div class="table-responsive">
				<table class="table table-stripped table-hover table-bordered">
					<thead>
						<tr>
							<th>{{trans('messages.employee_code')}}</th>
							<th>{{trans('messages.date')}}</th>
							<th>{{trans('messages.clock_in')}}</th>
							<th>{{trans('messages.clock_out')}}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($clock_fails as $clock_fail)
							<tr>
								<td>{{$clock_fail->employee_code}}</td>
								<td>{{showDate($clock_fail->date)}}</td>
								<td>{{showDateTime($clock_fail->clock_in)}}</td>
								<td>{{showDateTime($clock_fail->clock_out)}}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@else
			@include('common.notification',['message' => trans('messages.no_data_found'),'type' => 'danger'])
		@endif
	</div>