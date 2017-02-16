@include('layouts.head')
	
<div class="container">
	<div class="logo-brand header sidebar rows">
		<div class="logo">
			<h1><a href="{!! URL::to('/') !!}">{!! config('config.application_name').' '.config('constants.version') !!}</a></h1>
		</div>
	</div>
	
    <div class="content-page">
        <div class="body content rows scroll-y">
			
			@yield('content')
		
			@include('layouts.footer')	
        </div>
    </div>
</div>
@include('layouts.foot')	

		
	
	
	