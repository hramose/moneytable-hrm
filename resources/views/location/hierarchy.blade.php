@foreach(\App\Location::whereNull('top_location_id')->get() as $location)
	<h4>{!! $location->name !!}</h4>
	{!! App\Classes\Helper::createLineTreeView($tree,$location->id) !!}
@endforeach