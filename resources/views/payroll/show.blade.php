@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/payroll">{!! trans('messages.payroll') !!}</a></li>
		    <li class="active">{!! $user->full_name_with_designation.' '.trans('messages.payslip').' '.trans('messages.from').' '.showDate($payroll_slip->from_date).' '.trans('messages.to').' '.showDate($payroll_slip->to_date) !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info full">
					<h2><strong>{!!trans('messages.attendance').' </strong>'.trans('messages.summary') !!}</h2>
					<div class="table-responsive">
						<table class="table table-hover table-striped show-table">
							<tbody>
								<tr>
									<th>{!! trans('messages.absent') !!}</th>
									<td>{!! $att_summary['A'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.holiday') !!}</th>
									<td>{!! $att_summary['H'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.present') !!}</th>
									<td>{!! $att_summary['P'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.leave') !!}</th>
									<td>{!! $att_summary['L'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.late') !!}</th>
									<td>{!! $att_summary['Late'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.overtime') !!}</th>
									<td>{!! $att_summary['Overtime'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.early').' '.trans('messages.leaving') !!}</th>
									<td>{!! $att_summary['Early'] !!}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="box-info full">
					<h2><strong>{!! trans('messages.hour').' </strong>'.trans('messages.summary') !!}</h2>
					<div class="table-responsive">
						<table class="table table-hover table-striped show-table">
							<tbody>
								<tr>
									<th>{!! trans('messages.total_late') !!}</th>
									<td>{!! array_key_exists('total_late',$summary) ? $summary['total_late'] : '-' !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.total_early') !!}</th>
									<td>{!! array_key_exists('total_early',$summary) ? $summary['total_early'] : '-' !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.total_rest') !!}</th>
									<td>{!! array_key_exists('total_rest',$summary) ? $summary['total_rest'] : '-' !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.total_overtime') !!}</th>
									<td>{!! array_key_exists('total_overtime',$summary) ? $summary['total_overtime'] : '-' !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.total_work') !!}</th>
									<td>{!! array_key_exists('total_working',$summary) ? $summary['total_working'] : '-' !!}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-sm-8">
				<div class="box-info">
					<h2><strong>{{trans('messages.payslip')}}</strong>
					<div class="additional-btn">
						<a href="/payroll/generate/mail/{{$payroll_slip->id}}" data-toggle="tooltip" title="{{trans('messages.mail')}}"><button class="btn btn-xs btn-success"><i class="fa fa-envelope icon"></i></button></a>
						<a href="/payroll/generate/print/{{$payroll_slip->id}}" target="_blank" data-toggle="tooltip" title="{{trans('messages.print')}}"><button class="btn btn-xs btn-primary"><i class="fa fa-print icon"></i></button></a>
						<a href="/payroll/generate/pdf/{{$payroll_slip->id}}" data-toggle="tooltip" title="{{trans('messages.generate_pdf')}}"><button class="btn btn-xs btn-warning"><i class="fa fa-file-pdf-o icon"></i></button></a>
						<a href="#" data-href="/payroll/{{$payroll_slip->id}}/edit" data-toggle="modal" data-target="#myModal"><button class="btn btn-xs btn-default" data-toggle="tooltip" title="{{trans('messages.edit')}}"><i class="fa fa-edit icon"></i></button></a>
						{!!delete_form(['payroll.destroy',$payroll_slip->id])!!}
					</div>
					</h2>
					<div class="table-responsive">
						<table class="table table-stripped table-hover table-bordered">
							<tr>
								<th>{!! trans('messages.name') !!} </th>
								<th>{!! $user->full_name !!}</th>
								<th>{!! trans('messages.employee_code') !!} </th>
								<th>{!! $user->Profile->employee_code !!}</th>
							</tr>
							<tr>
								<th>{!! trans('messages.department') !!} </th>
								<th>{!! $user->Designation->Department->name !!}</th>
								<th>{!! trans('messages.designation') !!} </th>
								<th>{!! $user->Designation->name !!}</th>
							</tr>
							<tr>
								<th>{!! trans('messages.duration') !!} </td>
								<th>{!! showDate($payroll_slip->from_date).' '.trans('messages.to').' '.showDate($payroll_slip->to_date) !!}</th>
								<th>{!! trans('messages.payslip_no') !!} </td>
								<th>{!! str_pad($payroll_slip->id, 3, 0, STR_PAD_LEFT) !!}</th>
							</tr>
							@if(!$payroll_slip->hourly_payroll)
							<tr>
								<td colspan = "2" valign="top" style="padding:0px;">
									<table class="table" style="border:0px">
										<thead>
											<tr>
												<th>{!! trans('messages.earning_salary') !!} </th>
												<td align="right">{!! trans('messages.amount') !!} </td>
											</tr>
										</thead>
										<tbody>
										@foreach($earning_salary_types as $earning_salary_type)
										<tr>
											<td>{!! $earning_salary_type->head !!}</td>
											<td align="right">{!! array_key_exists($earning_salary_type->id, $payroll) ? currency($payroll[$earning_salary_type->id]) : 0 !!}</td>
										</tr>
										<?php $total_earning += array_key_exists($earning_salary_type->id, $payroll) ? ($payroll[$earning_salary_type->id]) : 0; ?>
										@endforeach
										@if($contract->overtime_hourly_rate)
										<tr>
											<td style="width:60%;">{!! trans('messages.overtime') !!}</td>
											<td style="text-align:right;width:40%;">{!! currency($payroll_slip->overtime) !!}</td>
										</tr>
										<?php $total_earning += $payroll_slip->overtime; ?>
										@endif
										</tbody>
									</table>
								</td>
								<td colspan = "2" valign="top" style="padding:0px;">
									<table class="table">
										<thead>
										<tr>
											<th>{!! trans('messages.deduction_salary') !!} </th>
											<td align="right">{!! trans('messages.amount') !!} </td>
										</tr>
										</thead>
										<tbody>
										@foreach($deduction_salary_types as $deduction_salary_type)
										<tr>
											<td>{!! $deduction_salary_type->head !!}</td>
											<td align="right">{!! array_key_exists($deduction_salary_type->id, $payroll) ? currency($payroll[$deduction_salary_type->id]) : 0 !!}</td>
										</tr>
										<?php $total_deduction += array_key_exists($deduction_salary_type->id, $payroll) ? ($payroll[$deduction_salary_type->id]) : 0; ?>
										@endforeach
										@if($contract->late_hourly_rate)
										<tr>
											<td style="width:60%;">{!! trans('messages.late') !!}</td>
											<td style="text-align:right;width:40%;">{!! currency($payroll_slip->late) !!}</td>
										</tr>
										<?php $total_deduction += $payroll_slip->late; ?>
										@endif
										@if($contract->early_leaving_hourly_rate)
										<tr>
											<td style="width:60%;">{!! trans('messages.early_leaving') !!}</td>
											<td style="text-align:right;width:40%;">{!! currency($payroll_slip->early_leaving) !!}</td>
										</tr>
										<?php $total_deduction += $payroll_slip->early_leaving; ?>
										@endif
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="padding:0px;">
									<table class="table">
										<thead>
											<tr>
												<td class="strong-text">{!! trans('messages.total_earning') !!} </td>
												<td class="pull-right strong-text">{!! currency($total_earning) !!}</td>
											</tr>
										</thead>
									</table>
								</td>
								<td colspan="2" style="padding:0px;">
									<table class="table">
										<thead>
											<tr>
												<td class="strong-text">{!! trans('messages.total_deduction') !!} </td>
												<td class="pull-right strong-text">{!! currency($total_deduction) !!}</td>
											</tr>
										</thead>
									</table>
								</td>
							</tr>
							@else
							<tr>
								<?php $total_earning = $payroll_slip->hourly; ?>
								<th>{!! trans('messages.hourly').' '.trans('messages.salary') !!}</th>
								<th colspan="3" style="text-align:right;">{!! currency($total_earning) !!}</th>
							</tr>
							@endif
							<tr>
								<th>{!! trans('messages.net_salary') !!} </th>
								<th colspan="3">{!! currency($total_earning-$total_deduction)." (".ucwords(App\Classes\Helper::inWords(round(($total_earning-$total_deduction),config('config.currency_decimal')))).")" !!} </th>
							</tr>
						</table>
						<p class="pull-right" style="margin-top:20px;margin-right:10px;">{!! trans('messages.authorised_signatory') !!}</p>
					</div>
				</div>
				<div class="box-info full">
					<h2><strong>{!! trans('messages.monthly') !!}</strong> {!! trans('messages.salary') !!} {!! $user->full_name_with_designation !!}</h2>
					@if(count($salaries))
					<div class="table-responsive">
						<table class="table table-hover table-striped">
							<thead>
								<tr>
									<th>{!! trans('messages.salary_head') !!}</th>
									<th>{!! trans('messages.type') !!}</th>
									<th>{!! trans('messages.amount') !!}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($salaries as $salary)
									<tr>
										<td>{!! $salary->SalaryType->head !!}</td>
										<td>{!! ucfirst($salary->SalaryType->salary_type) !!}</td>
										<td>{!! currency($salary->amount) !!}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					@else
						@include('common.notification',['message' => trans('messages.salary_not_set'),'type' => 'danger'])
					@endif
				</div>
			</div>
		</div>
	@stop