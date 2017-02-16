<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\JobSearchRequest;

Class JobSearchController extends Controller{
    use BasicController;

	protected $form = 'job-search-form';

	public function search(Request $request){

		$jobs = array();

		if($request->input('country')){
			$url = 'http://api.indeed.com/ads/apisearch?publisher='.config('config.indeed_publisher_id').'&q='.$request->input('query').'&co='.$request->input('country').'&format=json&v=2';

			if($request->input('job_type'))
				$url .= '&jt='.$request->input('job_type');

			if($request->input('location'))
				$url .= '&l='.$request->input('location');

			$ch = curl_init($url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_USERAGENT, 'WM Lab');
		    $data = curl_exec($ch);
	    	$jobs = json_decode($data,true);
		}

		$menu = ['job_search'];

		return view('job_search.index',compact('jobs','request','menu'));
	}
}