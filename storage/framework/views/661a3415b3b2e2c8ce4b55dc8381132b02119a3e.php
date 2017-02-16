<!DOCTYPE html>
<html>
	<head>
	<title><?php echo config('config.application_name') ? : config('constants.default_title'); ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">

	<?php echo HTML::style('assets/css/bootstrap.min.css'); ?>

	<?php echo HTML::style('assets/third/select2/css/select2.css'); ?>

	<?php echo HTML::style('assets/css/style.css'); ?>

	<?php echo HTML::style('assets/css/style-responsive.css'); ?>

	<?php echo HTML::style('assets/css/animate.css'); ?>

	<?php echo HTML::style('assets/third/toastr/toastr.min.css'); ?>

	<?php echo HTML::style('assets/third/font-awesome/css/font-awesome.min.css'); ?>

	<?php echo HTML::style('assets/third/icheck/skins/flat/blue.css'); ?>

	<?php echo HTML::style('assets/css/custom.css'); ?>


	<body class="tooltips full-content">
	
	<div class="container">
	
		<?php echo $__env->yieldContent('content'); ?>

		<p style="text-align:center;"><a href="<?php echo URL::to('/'); ?>"><?php echo config('config.application_name').' ' .config('code.version'); ?></a> <?php echo config('config.credit'); ?></p>
	</div> 
	
	<?php echo HTML::script('assets/js/jquery-1.11.3.min.js'); ?>

	<?php echo HTML::script('assets/js/bootstrap.min.js'); ?> 
	<?php echo HTML::script('assets/js/jquery.validate.min.js'); ?>

	<?php echo HTML::script('assets/third/toastr/toastr.min.js'); ?>


	<?php echo $__env->make('notification', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	
	<?php echo HTML::script('assets/third/slimscroll/jquery.slimscroll.min.js'); ?>

	<?php echo HTML::script('assets/third/select/bootstrap-select.min.js'); ?>

	<?php echo HTML::script('assets/third/input/bootstrap.file-input.js'); ?>

	<?php echo HTML::script('assets/third/datepicker/js/bootstrap-datepicker.js'); ?>

	<?php echo HTML::script('assets/third/icheck/icheck.min.js'); ?>

	<?php echo HTML::script('assets/js/validation-form.js'); ?>

	
    <script>
	$(document).ready(function() { 
		// Validate.init();
		$('input').iCheck({
		checkboxClass: 'icheckbox_flat-blue',
		radioClass: 'iradio_flat-blue',
		increaseArea: '20%' 
		}); 
	});
	</script>
	<?php echo HTML::script('assets/js/wmlab.js'); ?>


	<?php if(!config('code.mode')): ?>
	<script type='text/javascript'>var fc_CSS=document.createElement('link');fc_CSS.setAttribute('rel','stylesheet');var fc_isSecured = (window.location && window.location.protocol == 'https:');var fc_lang = document.getElementsByTagName('html')[0].getAttribute('lang'); var fc_rtlLanguages = ['ar','he']; var fc_rtlSuffix = (fc_rtlLanguages.indexOf(fc_lang) >= 0) ? '-rtl' : '';fc_CSS.setAttribute('type','text/css');fc_CSS.setAttribute('href',((fc_isSecured)? 'https://d36mpcpuzc4ztk.cloudfront.net':'http://assets1.chat.freshdesk.com')+'/css/visitor'+fc_rtlSuffix+'.css');document.getElementsByTagName('head')[0].appendChild(fc_CSS);var fc_JS=document.createElement('script'); fc_JS.type='text/javascript'; fc_JS.defer=true;fc_JS.src=((fc_isSecured)?'https://d36mpcpuzc4ztk.cloudfront.net':'http://assets.chat.freshdesk.com')+'/js/visitor.js';(document.body?document.body:document.getElementsByTagName('head')[0]).appendChild(fc_JS);window.livechat_setting= 'eyJ3aWRnZXRfc2l0ZV91cmwiOiJ3bWxhYi5mcmVzaGRlc2suY29tIiwicHJvZHVjdF9pZCI6bnVsbCwibmFtZSI6IlN1cHBvcnQgUG9ydGFsIiwid2lkZ2V0X2V4dGVybmFsX2lkIjpudWxsLCJ3aWRnZXRfaWQiOiI0ODYxZjZhNS1lYzZjLTRiOTUtODc0Ny1jOTlhZjZmOTI0MDMiLCJzaG93X29uX3BvcnRhbCI6dHJ1ZSwicG9ydGFsX2xvZ2luX3JlcXVpcmVkIjpmYWxzZSwibGFuZ3VhZ2UiOiJlbiIsInRpbWV6b25lIjoiTmV3IERlbGhpIiwiaWQiOjE3MDAwMDExNzgxLCJtYWluX3dpZGdldCI6MSwiZmNfaWQiOiI4Mjc0YTVkYzQxZGNhOGVmNjNjYmVmNDE4ZjQ2NmExNCIsInNob3ciOjEsInJlcXVpcmVkIjoyLCJoZWxwZGVza25hbWUiOiJXTSBMYWIiLCJuYW1lX2xhYmVsIjoiTmFtZSIsIm1lc3NhZ2VfbGFiZWwiOiJNZXNzYWdlIiwicGhvbmVfbGFiZWwiOiJQaG9uZSIsInRleHRmaWVsZF9sYWJlbCI6IlRleHRmaWVsZCIsImRyb3Bkb3duX2xhYmVsIjoiRHJvcGRvd24iLCJ3ZWJ1cmwiOiJ3bWxhYi5mcmVzaGRlc2suY29tIiwibm9kZXVybCI6ImNoYXQuZnJlc2hkZXNrLmNvbSIsImRlYnVnIjoxLCJtZSI6Ik1lIiwiZXhwaXJ5IjoxNDcyNTM2MDAwMDAwLCJlbnZpcm9ubWVudCI6InByb2R1Y3Rpb24iLCJlbmRfY2hhdF90aGFua19tc2ciOiJUaGFuayB5b3UhISEiLCJlbmRfY2hhdF9lbmRfdGl0bGUiOiJFbmQiLCJlbmRfY2hhdF9jYW5jZWxfdGl0bGUiOiJDYW5jZWwiLCJzaXRlX2lkIjoiODI3NGE1ZGM0MWRjYThlZjYzY2JlZjQxOGY0NjZhMTQiLCJhY3RpdmUiOjEsInJvdXRpbmciOm51bGwsInByZWNoYXRfZm9ybSI6MSwiYnVzaW5lc3NfY2FsZW5kYXIiOm51bGwsInByb2FjdGl2ZV9jaGF0IjoxLCJwcm9hY3RpdmVfdGltZSI6MTA1LCJzaXRlX3VybCI6IndtbGFiLmZyZXNoZGVzay5jb20iLCJleHRlcm5hbF9pZCI6bnVsbCwiZGVsZXRlZCI6MCwibW9iaWxlIjoxLCJhY2NvdW50X2lkIjpudWxsLCJjcmVhdGVkX2F0IjoiMjAxNi0wOC0xNVQwNDoyNjozOS4wMDBaIiwidXBkYXRlZF9hdCI6IjIwMTYtMDgtMTVUMDU6NDk6NDAuMDAwWiIsImNiRGVmYXVsdE1lc3NhZ2VzIjp7ImNvYnJvd3Npbmdfc3RhcnRfbXNnIjoiWW91ciBzY3JlZW5zaGFyZSBzZXNzaW9uIGhhcyBzdGFydGVkIiwiY29icm93c2luZ19zdG9wX21zZyI6IllvdXIgc2NyZWVuc2hhcmluZyBzZXNzaW9uIGhhcyBlbmRlZCIsImNvYnJvd3NpbmdfZGVueV9tc2ciOiJZb3VyIHJlcXVlc3Qgd2FzIGRlY2xpbmVkIiwiY29icm93c2luZ192aWV3aW5nX3NjcmVlbiI6IllvdSBhcmUgdmlld2luZyB0aGUgdmlzaXRvcuKAmXMgc2NyZWVuIiwiY29icm93c2luZ19jb250cm9sbGluZ19zY3JlZW4iOiJZb3UgYXJlIGNvbnRyb2xsaW5nIHRoZSB2aXNpdG9y4oCZcyBzY3JlZW4iLCJjb2Jyb3dzaW5nX3JlcXVlc3RfY29udHJvbCI6IlJlcXVlc3QgdmlzaXRvciBmb3IgY29udHJvbCIsImNvYnJvd3Npbmdfc3RvcF9yZXF1ZXN0IjoiRW5kIHlvdXIgc2NyZWVuc2hhcmluZyBzZXNzaW9uIiwiY29icm93c2luZ19yZXF1ZXN0X2NvbnRyb2xfcmVqZWN0ZWQiOiJZb3VyIHJlcXVlc3Qgd2FzIGRlY2xpbmVkIiwiY29icm93c2luZ19jYW5jZWxfdmlzaXRvcl9tc2ciOiJTY3JlZW5zaGFyaW5nIGlzIGN1cnJlbnRseSB1bmF2YWlsYWJsZSIsImNiX3ZpZXdpbmdfc2NyZWVuX3ZpIjoiQWdlbnQgY2FuIHZpZXcgeW91ciBzY3JlZW4gIiwiY2JfY29udHJvbGxpbmdfc2NyZWVuX3ZpIjoiQWdlbnQgY2FuIGNvbnRyb2wgeW91ciBzY3JlZW4iLCJjYl9naXZlX2NvbnRyb2xfdmkiOiJBbGxvdyBhZ2VudCB0byBjb250cm9sIHlvdXIgc2NyZWVuIiwiY2JfdmlzaXRvcl9zZXNzaW9uX3JlcXVlc3QiOiJDYW4gYWdlbnQgY29udHJvbCB5b3VyIGN1cnJlbnQgc2NyZWVuPyJ9fQ==';</script>
	<?php endif; ?>
	
	</body>
</html>