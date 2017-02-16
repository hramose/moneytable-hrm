@extends('layouts.default')
	@section('content')
		<div class="box-info box-messages">
			<div class="row">
				@include('message.sidebar')
				<div class="col-md-10">
					<p class="text-right"><strong>{!! date('d M Y, h:i A',strtotime($message->created_at)) !!}</strong></p>
					<table class="table">
						<tbody>
							<tr>
								<td colspan="2">
									<a href="{!! URL::to('/message/'.$message->id.'/delete/'.$token) !!}" class="btn btn-danger btn-sm alert_delete"><i class="fa fa-trash-o"></i> {!! trans('messages.trash') !!}</a>
								</td>
							</tr>
							<tr>
								<td style="width: 100px;"><strong>{!! trans('messages.from_to') !!}</strong></td>
								<td>{!! $user->full_name_with_designation !!}</td>
							</tr>
							<tr>
								<td><strong>{!! trans('messages.subject') !!}</strong></td>
								<td>{!! $message->subject !!}</td>
							</tr>
							<tr>
								<td colspan="2">
								<p style="text-align: justify">
								{!! $message->body !!}
								</p>
								</td>
							</tr>
							@if($message->attachments)
							<tr>
								<td><strong>{!! trans('messages.attachment') !!}</strong></td>
								<td><a href="/message/{{ $message->id }}/download"><strong>{!! trans('messages.download') !!}</strong></a></td>
							</tr>
							@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>
	@stop