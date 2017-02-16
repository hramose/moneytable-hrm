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

	{!! HTML::style('assets/css/bootstrap.min.css') !!}
	{!! HTML::style('assets/third/select2/css/select2.css') !!}
	{!! HTML::style('assets/css/style.css') !!}
	{!! HTML::style('assets/css/style-responsive.css') !!}
	{!! HTML::style('assets/third/toastr/toastr.min.css') !!}
	{!! HTML::style('assets/css/animate.css') !!}
	{!! HTML::style('assets/third/font-awesome/css/font-awesome.min.css') !!}
	{!! HTML::style('assets/third/icheck/skins/flat/blue.css') !!}
	{!! HTML::style('assets/css/custom.css') !!}

	<body class="tooltips full-content">
	
	<div class="container">
	
		@yield('content')

	</div>  
	
	{!! HTML::script('assets/js/jquery-1.11.3.min.js') !!}
	{!! HTML::script('assets/js/bootstrap.min.js') !!} 
	{!! HTML::script('assets/js/jquery.validate.min.js') !!}
	{!! HTML::script('assets/third/toastr/toastr.min.js') !!}

	@include('notification')
	
	{!! HTML::script('assets/third/slimscroll/jquery.slimscroll.min.js') !!}
	{!! HTML::script('assets/third/select/bootstrap-select.min.js') !!}
	{!! HTML::script('assets/third/input/bootstrap.file-input.js') !!}
	{!! HTML::script('assets/third/datepicker/js/bootstrap-datepicker.js') !!}
	{!! HTML::script('assets/third/icheck/icheck.min.js') !!}
	{!! HTML::script('assets/third/wizard/jquery.snippet.js') !!}
	{!! HTML::script('assets/third/wizard/jquery.easyWizard.js') !!}
	{!! HTML::script('assets/js/validation-form.js') !!}
	
    <script>
	$(document).ready(function() { 
		$('input').iCheck({
		checkboxClass: 'icheckbox_flat-blue',
		radioClass: 'iradio_flat-blue',
		increaseArea: '20%' 
		});
		Validate.init(); 
		$('#myWizard').easyWizard({
		buttonsClass: 'btn btn-default',
		submitButtonClass: 'btn btn-primary',
		showSteps: true,
	    showButtons: true,
	    submitButton: false
		});
	});
	</script>

	</body>
</html>