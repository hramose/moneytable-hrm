@extends('layouts.default')
	@section('content')
		<div class="box-info box-messages">
			<div class="row">
				@include('message.sidebar')
				<div class="col-md-10">
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>
	@stop