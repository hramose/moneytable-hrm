<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\ContractTypeRequest;
use App\ContractType;
use App\Classes\Helper;
use Entrust;

Class ContractTypeController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('contract_type.create');
	}

	public function lists(){
		$contract_types = ContractType::all();

		$data = '';
		foreach($contract_types as $contract_type){
			$data .= '<tr>
				<td>'.$contract_type->name.'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/contract-type/'.$contract_type->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['contract-type.destroy',$contract_type->id],'contract_type','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(ContractType $contract_type){
		return view('contract_type.edit',compact('contract_type'));
	}

	public function store(ContractTypeRequest $request, ContractType $contract_type){	

		$contract_type->fill($request->all())->save();

		$this->logActivity(['module' => 'contract_type','unique_id' => $contract_type->id,'activity' => 'activity_added']);

        if(\App\Setup::whereModule('contract')->whereCompleted(0)->first())
            \App\Setup::whereModule('contract')->whereCompleted(0)->update(['completed' => 1]);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $contract_type->name,'id' => $contract_type->id,'field' => 'contract_type_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.contract_type').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
	        if(config('config.application_setup_info') && defaultRole()){
	        	$setup_data = Helper::setupInfo();
	        	$response['setup_data'] = $setup_data;
	        }
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#contract-type')->withSuccess(trans('messages.contract_type').' '.trans('messages.added'));		
	}

	public function update(ContractTypeRequest $request, ContractType $contract_type){

		$contract_type->fill($request->all())->save();

		$this->logActivity(['module' => 'contract_type','unique_id' => $contract_type->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.contract_type').' '.trans('messages.updated'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#contract-type')->withSuccess(trans('messages.contract_type').' '.trans('messages.updated'));
	}

	public function destroy(ContractType $contract_type,Request $request){
		$this->logActivity(['module' => 'contract_type','unique_id' => $contract_type->id,'activity' => 'activity_deleted']);

        $contract_type->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.contract_type').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect()->back()->withSuccess(trans('messages.contract_type').' '.trans('messages.deleted'));
	}
}
?>