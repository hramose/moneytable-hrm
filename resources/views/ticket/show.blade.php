@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/ticket">{!! trans('messages.ticket') !!}</a></li>
		    <li class="active">{!! $ticket->subject !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.ticket').'</strong> '.trans('messages.detail') !!}</h2>
					<div class="table-responsive">
						<table class="table table-hover table-striped show-table">
							<tbody>
								<tr><th>{!! trans('messages.subject') !!}</th><td>{!! $ticket->subject !!}</td></tr>
								<tr><th>{!! trans('messages.employee') !!}</th><td>{!! $ticket->UserAdded->full_name_with_designation !!}</td></tr>
								<tr><th>{!! trans('messages.date') !!}</th><td>{!! showDate($ticket->created_at) !!}</td></tr>
								<tr><th>{!! trans('messages.priority') !!}</th><td>{!! trans('messages.'.$ticket->priority) !!}</td></tr>
							</tbody>
						</table>
					</div>
				</div>

				@if(Entrust::can('assign_ticket'))
				<div class="box-info">
					<h2><strong>{!! trans('messages.assigned_to') !!}</strong></h2>
					  {!! Form::model($ticket,['method' => 'POST','route' => ['ticket.assign-ticket',$ticket->id] ,'class' => 'ticket-assign-form','id' => 'ticket-assign-form','data-div-alter' => 'ticket-assigned-user','data-no-form-clear' => 1]) !!}
					  	<div class="form-group">
						    {!! Form::label('user_id',trans('messages.employee'),[])!!}
						    {!! Form::select('user_id[]', [''=>'']+$users
						    	, $selected_user,['class'=>'form-control input-xlarge select2me','multiple'=>true,'placeholder'=>trans('messages.select_one')])!!}
					    </div>
					    {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
					  {!! Form::close() !!}
				</div>
				@endif
			</div>
			<div class="col-sm-8">
				<div class="box-info full">
					<ul class="nav nav-tabs nav-justified">
					  <li class="active"><a href="#detail" data-toggle="tab"><i class="fa fa-home"></i> {!! trans('messages.detail') !!}</a></li>
					  <li><a href="#comment" data-toggle="tab"><i class="fa fa-comment"></i> {!! trans('messages.comment') !!}</a></li>
					  <li><a href="#note" data-toggle="tab"><i class="fa fa-pencil"></i> {!! trans('messages.note') !!}</a></li>
					  <li><a href="#attachment" data-toggle="tab"><i class="fa fa-paperclip"></i> {!! trans('messages.attachment') !!}</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane animated active fadeInRight" id="detail">
							<div class="user-profile-content">
								<div class="the-notes info">{!! $ticket->description !!}</div>
								<div class="col-md-6">
									<h2><strong>{!! trans('messages.assigned_to') !!}</strong></h2>
									<ul class="media-list" id="ticket-assigned-user">
									  @foreach($ticket->User as $user)
									  <li class="media">
										{!! \App\Classes\Helper::getAvatar($user->id) !!}
										<div class="media-body" style="vertical-align:middle; padding-left:10px;">
										  <h4 class="media-heading"><a href="#">{{ $user->full_name }}</a> <br /> <small>{{ $user->Designation->full_designation }}</small></h4>
										  @if($user->id == $ticket->user_id)
											<span class="label label-danger pull-right">Admin</span>
										  @endif
										</div>
									  </li>
									  @endforeach
									</ul>
								</div>
								<div class="col-md-6">
									@if(Entrust::can('update_ticket_status'))
									<h2><strong>{!! trans('messages.update').' </strong> '.trans('messages.status') !!}</h2>
									  {!! Form::model($ticket,['method' => 'POST','route' => ['ticket.update-ticket-status',$ticket->id] ,'class' => 'ticket-status-form','id' => 'ticket-status-form','data-no-form-clear' => 1]) !!}
									  	<div class="form-group">
										    {!! Form::label('status',trans('messages.status'),[])!!}
										    {!! Form::select('status', [null=>trans('messages.select_one')] + $status
										    	, isset($ticket->status) ? $ticket->status : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
									    </div>
										  <div class="form-group">
										    {!! Form::label('admin_remarks',trans('messages.remarks'),[])!!}
										    {!! Form::textarea('admin_remarks',isset($ticket->admin_remarks) ? $ticket->admin_remarks : '',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.admin_remarks')])!!}
										    <span class="countdown"></span>
										  </div>
									    {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
									  {!! Form::close() !!}
									@else
										<h2><strong>{!! trans('messages.ticket').' </strong> '.trans('messages.status') !!}</h2>
										<div class="table-responsive">
											<table class="table table-hover table-striped show-table">
												<tbody>
													<tr><th>{!! trans('messages.status') !!}</th><td>
														@if($ticket->status == 'open')
															<span class="badge badge-danger">{!! trans('messages.open') !!}</span>
														@else
															<span class="badge badge-success">{!! trans('messages.close') !!}</span>
														@endif
													</td></tr>
													@if($ticket->status == 'close')
													<tr><th>{!! trans('messages.closed_at') !!}</th><td>{!! showDate($ticket->closed_at) !!}</td></tr>
													<tr><th>{!! trans('messages.remarks') !!}</th><td>{!! $ticket->admin_remarks !!}</td></tr>
													@endif
												</tbody>
											</table>
											</div>
									@endif
								</div>
								<div class="clear"></div>
							</div>
						</div>
						<div class="tab-pane animated fadeInRight" id="comment">
							<div class="user-profile-content">
								{!! Form::model($ticket,['method' => 'POST','route' => ['ticket-comment.store',$ticket->id] ,'class' => 'ticket-comment-form','id' => 'ticket-comment-form','data-list-alter' => 'ticket-comment-lists']) !!}
								  <div class="form-group">
								    {!! Form::textarea('comment','',['size' => '30x3', 'class' => 'form-control ', 'placeholder' => trans('messages.comment'),'data-autoresize' => 1,'style' => 'border:0px;border-bottom:1px solid #cccccc;'])!!}
								    <span class="countdown"></span>
								  </div>
								  {!! Form::submit(trans('messages.post'),['class' => 'btn btn-primary pull-right btn-sm']) !!}
								{!! Form::close() !!}
								<div class="clear"></div>

								<h2><strong>{!! trans('messages.comment') !!}</strong> {!! trans('messages.list') !!}</h2>
								<div class="scroll-widget" id="ticket-comment-lists">
									<ul class="media-list">
									@if($ticket->TicketComment)
										@foreach($ticket->TicketComment->sortByDesc('id') as $ticket_comment)
										  <li class="media">
											<a class="pull-left" href="#">
											  {!! App\Classes\Helper::getAvatar($ticket_comment->user_id) !!}
											</a>
											<div class="media-body">
											  <h4 class="media-heading"><a href="#">{!! $ticket_comment->User->full_name !!}</a> <small>{!! showDateTime($ticket_comment->created_at) !!}</small>
											  @if(Auth::user()->id == $ticket_comment->user_id)
												<div class="pull-right">{!! delete_form(['ticket-comment.destroy',$ticket_comment->id]) !!}</div>
											  </h4>
										      @endif
											  <div class="the-notes danger" style="margin-top:10px; background-color:#f1f1f1;">{!! $ticket_comment->comment !!}</div>
											</div>
										  </li>
										@endforeach
									@endif
									</ul>
								</div>
							</div>
						</div>
						<div class="tab-pane animated fadeInRight" id="note">
							<div class="user-profile-content">
								{!! Form::model($ticket,['method' => 'POST','route' => ['ticket-note.store',$ticket->id] ,'class' => 'ticket-note-form','id' => 'ticket-note-form','data-no-form-clear' => 1]) !!}
								   <div class="form-group">
								    {!! Form::textarea('note',(count($ticket->TicketNote) && $ticket->TicketNote->whereLoose('user_id',Auth::user()->id)) ? $ticket->TicketNote->whereLoose('user_id',Auth::user()->id)->first()->note : '',['size' => '30x10', 'class' => 'form-control notebook', 'placeholder' => trans('messages.note')])!!}
								    <span class="countdown"></span>
								   </div>
							 	{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
								{!! Form::close() !!}
								<div class="clear"></div>
							</div>
						</div>
						<div class="tab-pane animated fadeInRight" id="attachment">
							<div class="user-profile-content">
								<h2><strong>{!! trans('messages.attachment') !!}</strong></h2>
								{!! Form::model($ticket,['files'=>'true','method' => 'POST','route' => ['ticket-attachment.store',$ticket->id] ,'class' => 'ticket-attachment-form','id' => 'ticket-attachment-form','data-table-alter' => 'ticket-attachment-table']) !!}
								  <div class="form-group">
								    {!! Form::label('title',trans('messages.title'),[])!!}
									{!! Form::input('text','title','',['class'=>'form-control','placeholder'=>trans('messages.title')])!!}
								  </div>
								  <div class="form-group">
								  	<input type="file" name="attachments" id="attachments" class="btn btn-default" title="{!! trans('messages.select').' '.trans('messages.file') !!}">
								  </div>
								  <div class="form-group">
								    {!! Form::textarea('description','',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.description')])!!}
								    <span class="countdown"></span>
								  </div>
								  {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}	
								{!! Form::close() !!}
								<div class="clear"></div>
								<h2><strong>{!! trans('messages.attachment') !!}</strong> {!! trans('messages.list') !!}</h2>
								<div class="table-responsive">
									<table class="table table-hover table-striped table-bordered table-ajax-load"  id="ticket-attachment-table" data-source="/ticket-attachment/{{$ticket->id}}/lists">
										<thead>
											<tr>
												<th>{!! trans('messages.option') !!}</th>
												<th>{!! trans('messages.title') !!}</th>
												<th>{!! trans('messages.description') !!}</th>
												<th>{!! trans('messages.date_time') !!}</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
				
	@stop