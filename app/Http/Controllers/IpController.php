<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\IpRequest;
use App\Ip;
use App\Classes\Helper;

Class IpController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('ip.create');
	}

	public function lists(){
		$ips = Ip::all();

		$data = '';
		foreach($ips as $ip){
			$data .= '<tr>
				<td>'.$ip->start.'</td>
				<td>'.(($ip->end) ? : '-').'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/ip/'.$ip->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['ip.destroy',$ip->id],'ip','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(Ip $ip){
		return view('ip.edit',compact('ip'));
	}

	public function store(IpRequest $request, Ip $ip){
		$ip->fill($request->all())->save();

		$this->logActivity(['module' => 'ip','unique_id' => $ip->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
            $response = ['message' => trans('messages.ip_restriction').' '.trans('messages.added'), 'status' => 'success','data' => $data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#ip')->withSuccess(trans('messages.ip_restriction').' '.trans('messages.added'));
	}

	public function update(IpRequest $request, Ip $ip){

		$ip->fill($request->all())->save();

		$this->logActivity(['module' => 'ip','unique_id' => $ip->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.ip_restriction').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#ip')->withSuccess(trans('messages.ip').' '.trans('messages.updated'));
	}

	public function destroy(Ip $ip,Request $request){
		$this->logActivity(['module' => 'ip','unique_id' => $ip->id,'activity' => 'activity_deleted']);

        $ip->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.ip_restriction').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect()->back()->withSuccess(trans('messages.ip_restriction').' '.trans('messages.deleted'));
	}
}
?>