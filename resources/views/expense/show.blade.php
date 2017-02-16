@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/expense">{!! trans('messages.expense') !!}</a></li>
		    <li class="active">{!! trans('messages.expense').' '.trans('messages.detail') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.expense').'</strong> '.trans('messages.detail') !!}</h2>
					<div class="table-responsive">
						<table class="table table-hover table-striped show-table">
							<tbody>
								<tr><th>{!! trans('messages.expense') !!} #</th><td>{!! str_pad($expense->id, 3, 0, STR_PAD_LEFT) !!}</td></tr>
								<tr><th>{!! trans('messages.employee') !!}</th><td>{!! $expense->User->full_name_with_designation !!}</td></tr>
								<tr><th>{!! trans('messages.expense_head') !!}</th><td>{!! $expense->ExpenseHead->head !!}</td></tr>
								<tr><th>{!! trans('messages.amount') !!}</th><td>{!! currency($expense->amount) !!}</td></tr>
								<tr><th>{!! trans('messages.date_of_request') !!}</th><td>{!! showDate($expense->date_of_expense) !!}</td></tr>
								<tr><th>{{ trans('messages.attachment') }}</th><td>{!! (($expense->attachments != null) ? '<a href="/expense/'.$expense->id.'/download" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.download').'"> <i class="fa fa-download"></i></a>' : '') !!}</td></tr>
							</tbody>
						</table>
					</div>
					<br />
					<div class="the-notes info">{!! $expense->remarks !!}</div>
				</div>
			</div>
			<div class="col-sm-8">
				@if(Entrust::can('change_expense_status') && $expense_status_enabled)
					<div class="box-info">
					{!! Form::model($expense,['method' => 'POST','route' => ['expense.update-status',$expense->id] ,'class' => 'expense-status-form','id' => 'expense-status-form','data-submit' => 'noAjax']) !!}
						<h2><strong>{!! trans('messages.update') !!}</strong> {!! trans('messages.status') !!}</h2>
						  <div class="form-group">
						    {!! Form::label('status',trans('messages.expense').' '.trans('messages.status'),[])!!}
							{!! Form::select('status', $status, isset($expense_status_detail->status) ?  $expense_status_detail->status : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one'),'id' => 'status'])!!}
						  </div>
						  <div class="form-group">
						    {!! Form::label('admin_remarks',trans('messages.remarks'),[])!!}
						    {!! Form::textarea('admin_remarks',isset($expense_status_detail->remarks) ? $expense_status_detail->remarks : '',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.remarks'),"data-show-counter" => 1,"data-limit" => config('config.textarea_limit'),'data-autoresize' => 1])!!}
						    <span class="countdown"></span>
						  </div>
						  {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
					{!! Form::close() !!}
					</div>
				@endif
				<div class="box-info full">
					<h2><strong>{!! trans('messages.expense') !!}</strong> {!! trans('messages.status') !!}</h2>
					<div class="table-responsive">
						<table class="table table-stripped table-hover">
							<thead>
								<tr>
									<th>Designation</th>
									<th>Status</th>
									<th>Remarks</th>
									<th>Date Updated</th>
								</tr>
							</thead>
							<tbody>
							@foreach($expense->ExpenseStatusDetail as $expense_status_detail)
								<tr>
									<td>{{$expense_status_detail->Designation->full_designation}}</td>
									<td>
										@if($expense_status_detail->status == 'pending')
											<span class="label label-info">{{trans('messages.pending')}}</span>
										@elseif($expense_status_detail->status == 'rejected')
											<span class="label label-danger">{{trans('messages.rejected')}}</span>
										@elseif($expense_status_detail->status == 'approved')
											<span class="label label-success">{{trans('messages.approved')}}</span>
										@endif
									</td>
									<td>{{$expense_status_detail->remarks}}</td>
									<td>{{($expense_status_detail->status != null && $expense_status_detail->status != 'pending') ? showDateTime($expense_status_detail->updated_at) : ''}}</td>
								</tr>
							@endforeach	
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.other') !!}</strong> {!! trans('messages.expense').' "'.$expense->User->full_name_with_designation.'"' !!}</h2>

					<div class="table-responsive">
						<table class="table table-hover table-striped">
							<thead>
								<tr>
									<th>{!! trans('messages.expense_head') !!}</th>
									<th>{!! trans('messages.date_of_expense') !!}</th>
									<th>{!! trans('messages.amount') !!}</th>
									<th>{!! trans('messages.remarks') !!}</th>
									<th>{!! trans('messages.status') !!}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($other_expenses as $other_expense)
									<tr>
										<td>{!! $other_expense->ExpenseHead->head !!}</td>
										<td>{!! showDate($other_expense->date_of_expense) !!}</td>
										<td>{!! currency($other_expense->amount) !!}</td>
										<td>{!! $other_expense->remarks !!}</td>
										<td>{!! trans('messages.'.$other_expense->status) !!}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	@stop