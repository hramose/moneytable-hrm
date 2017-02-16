
<title>{!! config('config.application_name') ? : config('constants.default_title') !!}</title>
<meta name="viewport" content="width=device-width">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	
	<table class="body-wrap" bgcolor="#f6f6f6" style="width: 100%; margin: 0;font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em;">
		<tbody>
			<tr>
				<td style="background-color:#fff;clear: both !important; display: block !important; max-width: 600px !important; margin: 0 auto; padding: 20px; border: 1px solid #f0f0f0;">
					<div style="display: block; max-width: 600px; margin: 0 auto; padding: 0;">
						
						<center>
				            @if(File::exists(config('constants.upload_path.logo').config('config.logo')))
				            <a href="/"><img src="/{!! config('constants.upload_path.logo').config('config.logo') !!}" class="" alt="Logo"></a>
				            @endif
						</center>
								
						<p style="font-size: 14px; line-height: 22.4px;text-align:justify;">Hi there,<br /> It seems you have forgot your login password. We have received a password change request from your account.</p>

						<center><a href="http://wmlab.in" style="color: #ffffff; border-radius: 25px; display: inline-block; cursor: pointer; text-decoration: none; background: #348eda; margin: 0; padding: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">Reset My Password</a></center>
						<p style="font-size: 14px; line-height: 22.4px;text-align:center;color:#000;">If you haven't requested password reset email, please ignore this & take a deep breath.</p>
						<p style="font-size: 14px; line-height: 22.4px;text-align:center;color:#9e9e9e;">For any kind of support, don't hesitate to write us at support@wmlab.in</p>
						<p style="font-size: 14px; line-height: 22.4px;text-align:center;color:#9e9e9e;">&copy; 2016 WM Lab</p>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
