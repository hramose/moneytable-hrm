
							<li {!! (in_array('dashboard',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'dashboard') !!}><a href="{!! URL::to('/') !!}"><i class="fa fa-home icon"></i> {!! trans('messages.dashboard') !!}</a></li>
							@if(Entrust::can('list_employee'))
							<li {!! (in_array('employee',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'employee') !!}><a href="{!! URL::to('/employee') !!}"><i class="fa fa-users icon"></i> {!! trans('messages.employee') !!}</a></li>
							@endif
							<li {!! (in_array('attendance',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'attendance') !!}><a href=""><i class="fa fa-book icon"></i><i class="fa fa-angle-double-down i-right"></i> {!! trans('messages.attendance') !!}</a>
								<ul {!! (in_array('daily_attendance',$menu) ||
											in_array('date_wise_attendance',$menu) ||
											in_array('date_wise_summary_attendance',$menu) ||
											in_array('shift_detail',$menu) ||
											in_array('update_attendance',$menu)
								) ? 'class="visible"' : '' !!}>
									<li {!! (in_array('daily_attendance',$menu)) ? 'class="active"' : '' !!} class="no-sort"><a href="{!! URL::to('/attendance') !!}"><i class="fa fa-angle-right"></i> {!! trans('messages.daily_attendance') !!} </a></li>
									<li {!! (in_array('date_wise_attendance',$menu)) ? 'class="active"' : '' !!} class="no-sort"><a href="{!! URL::to('/date-wise-attendance') !!}"><i class="fa fa-angle-right"></i> {!! trans('messages.date_wise').' '.trans('messages.attendance') !!} </a></li>
									<li {!! (in_array('date_wise_summary_attendance',$menu)) ? 'class="active"' : '' !!} class="no-sort"><a href="{!! URL::to('/date-wise-summary-attendance') !!}"><i class="fa fa-angle-right"></i> <small>{!! trans('messages.date_wise').' '.trans('messages.summary').' '.trans('messages.attendance') !!}</small> </a></li>
									<li {!! (in_array('shift_detail',$menu)) ? 'class="active"' : '' !!} class="no-sort"><a href="{!! URL::to('/shift-detail') !!}"><i class="fa fa-angle-right"></i> {!! trans('messages.shift_detail') !!} </a></li>
									@if(Entrust::can('update_attendance'))
									<li {!! (in_array('update_attendance',$menu)) ? 'class="active"' : '' !!} class="no-sort"><a href="{!! URL::to('/update-attendance') !!}"><i class="fa fa-angle-right"></i> {!! trans('messages.update_attendance') !!} </a></li>
									@endif
								</ul>
							</li>
							@if(Entrust::can('list_holiday'))
							<li {!! (in_array('holiday',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'holiday') !!}><a href="{!! URL::to('/holiday') !!}"><i class="fa fa-fighter-jet icon"></i> {!! trans('messages.holiday') !!}</a></li>
							@endif
							<li {!! (in_array('leave',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'leave') !!}><a href="{!! URL::to('/leave') !!}"><i class="fa fa-coffee icon"></i> {!! trans('messages.leave') !!}</a></li>
							<li {!! (in_array('payroll',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'payroll') !!}><a href="{!! URL::to('/payroll') !!}"><i class="fa fa-money icon"></i> {!! trans('messages.payroll') !!}</a></li>
							@if(Entrust::can('list_announcement'))
							<li {!! (in_array('announcement',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'announcement') !!}><a href="{!! URL::to('/announcement') !!}"><i class="fa fa-list-alt icon"></i> {!! trans('messages.announcement') !!}</a></li>
							@endif
							@if(Entrust::can('list_award'))
							<li {!! (in_array('award',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'award') !!}><a href="{!! URL::to('/award') !!}"><i class="fa fa-trophy icon"></i> {!! trans('messages.award') !!}</a></li>
							@endif
							@if(Entrust::can('list_expense'))
							<li {!! (in_array('expense',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'expense') !!}><a href="{!! URL::to('/expense') !!}"><i class="fa fa-credit-card icon"></i> {!! trans('messages.expense') !!}</a></li>
							@endif
							@if(Entrust::can('list_task'))
							<li {!! (in_array('task',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'task') !!}><a href="{!! URL::to('/task') !!}"><i class="fa fa-tasks icon"></i> {!! trans('messages.task') !!}</a></li>
							@endif
							@if(Entrust::can('list_ticket'))
							<li {!! (in_array('ticket',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'ticket') !!}><a href="{!! URL::to('/ticket') !!}"><i class="fa fa-ticket icon"></i> {!! trans('messages.ticket') !!}</a></li>
							@endif
							@if(Entrust::can('manage_message'))
							<li {!! (in_array('message',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'message') !!}><a href="{!! URL::to('/message') !!}"><i class="fa fa-envelope icon"></i> {!! trans('messages.message') !!}</a></li>
							@endif
							@if(Entrust::can('list_job'))
								@if(Entrust::can('create_job'))
									<li {!! (in_array('list_job',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'job') !!}><a href="{!! URL::to('/job') !!}"><i class="fa fa-bullhorn icon"></i> {!! trans('messages.list_all').' '.trans('messages.job') !!}</a></li>
								@endif
								<li {!! (in_array('job_application',$menu)) ? 'class="active"' : '' !!} {!! menuAttr($menus,'job_application') !!}><a href="{!! URL::to('/job-application') !!}"><i class="fa fa-file-text-o icon"></i> {!! trans('messages.job').' '.trans('messages.application') !!}</a></li>
							@endif