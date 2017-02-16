<title>{!! config('config.application_name') ? : config('constants.default_title') !!}</title>
<style>
*{font-family:helvetica; font-size:12px;}
table.fancy {  font-size:12px; border-collapse: collapse;  width:100%;  margin:0 auto;  margin-bottom:10px; margin-top:10px;}
table.fancy th{  border: 1px #2e2e2e solid;  padding: 0.5em;  padding-left:10px; vertical-align:top;text-align: left;}
table.fancy td {  text-align: left;padding: 0.5em;  }
table.fancy caption {  margin-left: inherit;  margin-right: inherit;}
table.fancy tr:hover{}

table.fancy-detail {  font-size:12px; border-collapse: collapse;  width:100%;  margin:0 auto;  margin-bottom:10px; margin-top:10px;}
table.fancy-detail-detail-detail th{  border: 1px #2e2e2e solid;  padding: 0.5em;  padding-left:10px; vertical-align:top;text-align: left;}
table.fancy-detail-detail th, table.fancy-detail td  {  padding: 0.5em;  padding-left:10px; border:1px solid #2e2e2e;text-align: left;}
table.fancy-detail caption {  margin-left: inherit;  margin-right: inherit;}
table.fancy-detail tr:hover{}

</style>
<table border="0" style="width:100%;">
	<tr>
		<td style="width:40%;">
			@if(File::exists(config('constants.upload_path.logo').config('config.logo')))
            <a href="/"><img src="{{url(config('constants.upload_path.logo').config('config.logo'))}}" class="" alt="Logo"></a>
            @endif
		</td>
		<td style="text-align:right;width:60%;">
			<p style='font-size:16px; font-weight:bold;'>{!! config('config.company_name') !!}</h2>
			<p style='font-size:14px; font-weight:bold;'>{!! config('config.address') !!}
			{!! config('config.zipcode') !!}</p>
			<p style=''>{!! trans('messages.email') !!} : {!! config('config.email') !!} | {!! trans('messages.phone') !!} : {!! config('config.phone') !!}</p>
		</td>
	</tr>
</table>
<table class="fancy">
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
</table>
@if(config('config.payroll_include_day_summary') && !$payroll_slip->hourly_payroll)
<table class="fancy">
	<tr>
		<th>{!! trans('messages.absent') !!}</th>
		<th>{!! trans('messages.holiday') !!}</th>
		<th>{!! trans('messages.present') !!}</th>
		<th>{!! trans('messages.leave') !!}</th>
		<th>{!! trans('messages.late') !!}</th>
		<th>{!! trans('messages.overtime') !!}</th>
		<th>{!! trans('messages.early').' '.trans('messages.leaving') !!}</th>
	</tr>
	<tr>
		<th>{!! $att_summary['A'] !!}</th>
		<th>{!! $att_summary['H'] !!}</th>
		<th>{!! $att_summary['P'] !!}</th>
		<th>{!! $att_summary['L'] !!}</th>
		<th>{!! $att_summary['Late'] !!}</th>
		<th>{!! $att_summary['Overtime'] !!}</th>
		<th>{!! $att_summary['Early'] !!}</th>
	</tr>
</table>
@endif
@if(config('config.payroll_include_hour_summary'))
<table class="fancy">
	<tr>
		<th>{!! trans('messages.total_late') !!}</th>
		<th>{!! trans('messages.total_early') !!}</th>
		<th>{!! trans('messages.total_rest') !!}</th>
		<th>{!! trans('messages.total_overtime') !!}</th>
		<th>{!! trans('messages.total_work') !!}</th>
	</tr>
	<tr>
		<th>{!! array_key_exists('total_late',$summary) ? $summary['total_late'] : '-' !!}</th>
		<th>{!! array_key_exists('total_early',$summary) ? $summary['total_early'] : '-' !!}</th>
		<th>{!! array_key_exists('total_rest',$summary) ? $summary['total_rest'] : '-' !!}</th>
		<th>{!! array_key_exists('total_overtime',$summary) ? $summary['total_overtime'] : '-' !!}</th>
		<th>{!! array_key_exists('total_working',$summary) ? $summary['total_working'] : '-' !!}</th>
	</tr>
</table>
@endif
@if(config('config.payroll_include_leave_summary') && !$payroll_slip->hourly_payroll)
<table class="fancy">
	<tr>
		@foreach($leave_types as $leave_type)
			<th>{!!$leave_type->name!!}</th>
		@endforeach
	</tr>
	<tr>
		@foreach($leave_types as $leave_type)
			<th>{!!$used[$leave_type->id].'/'.$allotted[$leave_type->id]!!}</th>
		@endforeach
	</tr>
</table>
@endif

<table class="fancy">
	@if(!$payroll_slip->hourly_payroll)
	<tr>
		<td colspan="2" valign="top" width="50%">
			<table class="fancy-detail">
				<tr>
					<th style="width:60%;">{!! trans('messages.earning_salary') !!} </th>
					<th style="text-align:right;width:40%;">{!! trans('messages.amount') !!} </th>
				</tr>
				@foreach($earning_salary_types as $earning_salary_type)
				<tr>
					<td style="width:60%;">{!! $earning_salary_type->head !!}</td>
					<td style="text-align:right;width:40%;">{!! array_key_exists($earning_salary_type->id, $payroll) ? currency($payroll[$earning_salary_type->id]) : 0 !!}</td>
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
			</table>
		</td>
		<td colspan="2" valign="top">
			<table class="fancy-detail">
				<tr>
					<th style="width:60%;">{!! trans('messages.deduction_salary') !!} </th>
					<th style="text-align:right;width:40%;">{!! trans('messages.amount') !!} </th>
				</tr>
				@foreach($deduction_salary_types as $deduction_salary_type)
				<tr>
					<td style="width:60%;">{!! $deduction_salary_type->head !!}</td>
					<td style="text-align:right;width:40%;">{!! array_key_exists($deduction_salary_type->id, $payroll) ? currency($payroll[$deduction_salary_type->id]) : 0 !!}</td>
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
			</table>
		</td>
	</tr>
	<tr>
		<td colspan = "2">
			<table class="fancy-detail">
				<tr>
					<th style="width:60%;">{!! trans('messages.total_earning') !!} </th>
					<th style="text-align:right;width:40%;">{!! currency($total_earning) !!}</th>
				</tr>
			</table>
		</td>
		<td colspan = "2">
			<table class="fancy-detail">
				<tr>
					<th style="width:60%;">{!! trans('messages.total_deduction') !!} </th>
					<th style="text-align:right;width:40%;">{!! currency($total_deduction) !!}</th>
				</tr>
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
		<th colspan="3" style="text-align:right;">{!! currency($total_earning-$total_deduction)." (".ucwords(App\Classes\Helper::inWords(round(($total_earning-$total_deduction),config('config.currency_decimal')))).")" !!} </th>
	</tr>
</table>
<p style='text-align:right;margin-top:30px;'>{!! trans('messages.authorised_signatory') !!}</p>