	{!! HTML::script('assets/js/jquery-1.11.3.min.js') !!}
	{!! HTML::script('assets/js/bootstrap.min.js') !!} 
	{!! HTML::script('assets/js/jquery.validate.min.js') !!}
	{!! HTML::script('assets/js/textAvatar.js') !!}
	{!! HTML::script('assets/js/sidemenu.js') !!}
	{!! HTML::script('assets/third/toastr/toastr.min.js') !!}
	{!! HTML::script('assets/third/sortable/sortable.min.js') !!}
	{!! HTML::script('assets/js/jquery.knob.min.js') !!}
	@include('notification')
	{!! HTML::script('assets/js/bootbox.js') !!}
	{!! HTML::script('assets/third/slimscroll/jquery.slimscroll.min.js') !!}
    @if(in_array('calendar',$assets))
	{!! HTML::script('assets/third/fullcalendar/moment.min.js') !!}
	{!! HTML::script('assets/third/fullcalendar/fullcalendar.min.js') !!}
	{!! HTML::script('assets/third/fullcalendar/lang-all.js') !!}
	@endif
    @if(in_array('graph',$assets))
	{!! HTML::script('https://www.gstatic.com/charts/loader.js') !!}
	@endif
    @if(in_array('rte',$assets))
	{!! HTML::script('assets/third/summernote/summernote.js') !!}
	@endif
    @if(in_array('timepicker',$assets))
	{!! HTML::script('assets/third/timepicker/bootstrap-clockpicker.min.js') !!}
	@endif
    @if(in_array('tour',$assets))
	{!! HTML::script('assets/third/bootstrap-tour/bootstrap-tour.min.js') !!}
	@endif
	{!! HTML::script('assets/third/jquery-ui/jquery-ui.min.js') !!}
	{!! HTML::script('assets/third/select2/js/select2.min.js') !!}
	{!! HTML::script('assets/third/datatable/datatables.min.js') !!}
	{!! HTML::script('assets/third/nifty-modal/js/classie.js') !!}
	{!! HTML::script('assets/third/nifty-modal/js/modalEffects.js') !!}
	{!! HTML::script('assets/third/select/bootstrap-select.min.js') !!}
	{!! HTML::script('assets/third/input/bootstrap.file-input.js') !!}
	{!! HTML::script('assets/third/datepicker/js/bootstrap-datepicker.js') !!}
	{!! HTML::script('assets/third/icheck/icheck.min.js') !!}

    @if(config('lang.'.session('lang').'.datepicker') != 'en')
	{!! HTML::script('assets/third/datepicker/locale/bootstrap-datepicker.'.config('lang.'.session('lang').'.datepicker').'.js',array('charset' => 'UTF-8')) !!}
    @endif

    @if(in_array('datetimepicker',$assets) || in_array('timepicker',$assets))
	{!! HTML::script('assets/third/datetimepicker/bootstrap-datetimepicker.js') !!}
	@if(config('lang.'.session('lang').'.datetimepicker') != 'en')
	{!! HTML::script('assets/third/datetimepicker/locale/bootstrap-datetimepicker.'.config('lang.'.session('lang').'.datetimepicker').'.js',array('charset' => 'UTF-8')) !!}
	@endif
    @endif

    @if(config('lang.'.session('lang').'.validation') != 'en')
	{!! HTML::script('assets/js/validation-localization/messages_'.config('lang.'.session('lang').'.validation').'.js',array('charset' => 'UTF-8')) !!}
    @endif
	{!! HTML::script('assets/js/validation-form.js') !!}
    <script>
    	$.ajaxSetup({
		   headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
		});
		var datatable_language = "//cdn.datatables.net/plug-ins/1.10.9/i18n/{!! config('lang.'.session('lang').'.datatable') !!}.json";
		var datetimepicker_language = "{{ config('lang.'.session('lang').'.datetimepicker') }}";
		var datepicker_language = "{{ config('lang.'.session('lang').'.datepicker') }}";
		var calendar_language = "{!! config('lang.'.session('lang').'.calendar') !!}";
		var character_remaining = "{{ trans('messages.character_remaining') }}";
		var present_employee = "{!! trans('messages.present_employee') !!}";
		var something_error_message = 'Bug: Something went wrong, Please try again.';
		var page_not_found = 'Bug: Page not found. Please contact your administrator.';
		var toastr_position = "{{ config('config.notification_position') }}";
		var assets = {!! json_encode($assets) !!};
		@if(isset($events))
			var calendar_events = {!! json_encode($events) !!};
		@endif
		@if(isset($daily_employee_attendance))
			var daily_employee_attendance_data = {!! json_encode($daily_employee_attendance) !!};
		@endif
		@if(isset($employee_graph_data))
			var employee_graph_data = {!! json_encode($employee_graph_data) !!};
		@endif
		@if(isset($month_holiday_count))
			var month_holiday_count = {!! json_encode($month_holiday_count) !!};
		@endif
		@if(isset($leave_graph))
			var leave_graph = {!! json_encode($leave_graph) !!};
		@endif
    	@if(in_array('graph',$assets))
    		var enable_graph = 1;
    	@else
    		var enable_graph = 0;
    	@endif
    	var availableDates = {!! json_encode($available_date) !!};
    	var defaultDatepickerDate = {!! isset($default_datepicker_date) ? json_encode(datepickerDefaultDate($default_datepicker_date)) : json_encode(datepickerDefaultDate()) !!};
    	var default_datetimepicker_date = {!! isset($default_datetimepicker_date) ? json_encode($default_datetimepicker_date) : json_encode(date('Y-m-d')) !!};

		@if(in_array('mail_config',$assets))
			$('.mail_config').hide();
			@if(config('mail.driver') == 'mail')
			$('#mail_configuration').show();
			@elseif(config('mail.driver') == 'sendmail')
			$('#sendmail_configuration').show();
			@elseif(config('mail.driver') == 'log')
			$('#log_configuration').show();
			@elseif(config('mail.driver') == 'smtp')
			$('#smtp_configuration').show();
			@elseif(config('mail.driver') == 'mandrill')
			$('#mandrill_configuration').show();
			@elseif(config('mail.driver') == 'mailgun')
			$('#mailgun_configuration').show();
			@endif
			$(document).on('change', '#mail_driver', function(){
				$('.mail_config').hide();
			 	var field = $('#mail_driver').val();
				if(field == 'smtp')
					$('#smtp_configuration').show();
				else if(field == 'mandrill')
					$('#mandrill_configuration').show();
				else if(field == 'mailgun')
					$('#mailgun_configuration').show();
				else if(field == 'mail')
					$('#mail_configuration').show();
				else if(field == 'sendmail')
					$('#sendmail_configuration').show();
				else if(field == 'log')
					$('#log_configuration').show();
			});
		@endif
	</script>
	{!! HTML::script('assets/js/wmlab.js') !!}
	</body>
</html>