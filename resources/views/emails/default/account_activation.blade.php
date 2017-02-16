@extends('emails.default')

	@section('content')
	<table bgcolor="#FFFFFF"  border="0" cellpadding="0" cellspacing="0" width="500" id="emailBody">

		<tr>
			<td align="center" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" style="color:#FFFFFF;" bgcolor="#0F8AD0">
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="0" cellspacing="0" width="500" class="flexibleContainer">
								<tr>
									<td align="center" valign="top" width="500" class="flexibleContainerCell">

										<table border="0" cellpadding="10" cellspacing="0" width="100%">
											<tr>
												<td align="center" valign="top" class="textContent">
													<h1 style="color:#FFFFFF;line-height:100%;font-family:Helvetica,Arial,sans-serif;font-size:35px;font-weight:normal;margin-bottom:5px;text-align:center;">Hi there, </h1>
													<h2 style="color:#FFFFFF;line-height:100%;font-family:Helvetica,Arial,sans-serif;font-size:25px;font-weight:normal;margin-bottom:5px;text-align:center;">Thank you for registering an account with us.</h2>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr style="padding-top:0;">
						<td align="center" valign="top">
							<table border="0" cellpadding="10" cellspacing="0" width="500" class="flexibleContainer">
								<tr>
									<td style="padding-top:0;" align="center" valign="top" width="500" class="flexibleContainerCell">
									<p style="font-size:12px;">To activate your account, Click on the below link</p>
										<table border="0" cellpadding="0" cellspacing="0" width="auto" class="emailButton" style="background-color: #0F8AD0;">
											<tr>
												<td align="center" valign="middle" class="buttonContent" style="padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;">
													<a style="color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:20px;line-height:135%;" href="{{ url('activate-account/'.$user->activation_token) }}" target="_blank">Activate Account</a>
												</td>
											</tr>
										</table>
									<p style="font-size:12px;">For any kind of support, don't hesitate to write us at support@wmlab.in</p>

									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top">
				<p style="font-size:12px;">If you haven't signup with us, just delete this email & take a deep breath.</p>
			</td>
		</tr>
	</table>
	@stop