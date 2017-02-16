<!DOCTYPE html>
<html>
	<head>
	<title>{!! config('config.application_name') ? : config('constants.default_title') !!}</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="csrf-token" content="{{ csrf_token() }}" />

	{!! HTML::style('assets/css/bootstrap.min.css') !!}
	{!! HTML::style('assets/third/select2/css/select2.css') !!}
	{!! HTML::style('assets/css/bootstrap.vertical-tabs.css') !!}
	{!! HTML::style('assets/css/style.css') !!}
	{!! HTML::style('assets/css/style-responsive.css') !!}
	{!! HTML::style('assets/css/sidemenu.css') !!}
	{!! HTML::style('assets/third/sortable/sortable-theme-bootstrap.css') !!}
	@if($direction == 'rtl')
	{!! HTML::style('assets/css/bootstrap-rtl.css') !!}
	{!! HTML::style('assets/css/bootstrap-flipped.css') !!}
	@endif
	
    @if(in_array('tour',$assets))
	{!! HTML::style('assets/third/bootstrap-tour/bootstrap-tour.min.css') !!}
    @endif

    @if(in_array('timepicker',$assets))
	{!! HTML::style('assets/third/timepicker/bootstrap-clockpicker.min.css') !!}
    @endif

    @if(in_array('calendar',$assets))
	{!! HTML::style('assets/third/fullcalendar/fullcalendar.min.css') !!}
	{!! HTML::style('assets/third/fullcalendar/fullcalendar.print.css', array('media' => 'print')) !!}
    @endif
    
    @if(in_array('datetimepicker',$assets) || in_array('timepicker',$assets))
	{!! HTML::style('assets/third/datetimepicker/bootstrap-datetimepicker.css') !!}
    @endif

    @if(in_array('rte',$assets))
	{!! HTML::style('assets/third/summernote/summernote.css') !!}
    @endif

	{!! HTML::style('assets/third/toastr/toastr.min.css') !!}
	{!! HTML::style('assets/third/jquery-ui/jquery-ui.min.css') !!}

	{!! HTML::style('assets/css/animate.css') !!}
	{!! HTML::style('assets/third/font-awesome/css/font-awesome.min.css') !!}
	{!! HTML::style('assets/third/nifty-modal/css/component.css') !!}
	{!! HTML::style('assets/third/icheck/skins/flat/blue.css') !!}
	{!! HTML::style('assets/third/select/bootstrap-select.min.css') !!}
	{!! HTML::style('assets/third/datepicker/css/datepicker.css') !!}
	{!! HTML::style('assets/third/datatable/datatables.min.css') !!}
	{!! HTML::style('assets/css/custom.css') !!}

	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
	
	<link rel="shortcut icon" href="{!! URL::to('assets/img/favicon.ico') !!}">
	<script> var public_path = "{!! URL::to('/'); !!}/"; </script>
	</head>
	<body class="tooltips k-rtl">
	
	