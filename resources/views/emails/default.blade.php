@include('emails.head')
	<body bgcolor="#E1E1E1" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">

		<center style="background-color:#E1E1E1;">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="table-layout: fixed;max-width:100% !important;width: 100% !important;min-width: 100% !important;">
				<tr>
					<td align="center" valign="top" id="bodyCell">
					@include('emails.message')
						<table bgcolor="#FFFFFF"  border="0" cellpadding="0" cellspacing="0" width="500" id="emailBody">

							<tr>
								<td align="center" valign="top">
						            @if(File::exists(config('constants.upload_path.logo').config('config.logo')))
						            <a href="/"><img src="/{!! config('constants.upload_path.logo').config('config.logo') !!}" alt="Logo" width="200px;" style="margin:20px 0px;"></a>
						            @endif
								</td>
							</tr>
						</table>

						@yield('content')
						@include('emails.foot')
					</td>
				</tr>
			</table>
		</center>
	</body>
</html>