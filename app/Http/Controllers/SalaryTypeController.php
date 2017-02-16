<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\SalaryTypeRequest;
use App\SalaryType;
use App\Classes\Helper;

Class SalaryTypeController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('salary_type.create');
	}

	public function lists(){
		$salary_types = SalaryType::all();

		$data = '';
		foreach($salary_types as $salary_type){
			$data .= '<tr>
				<td>'.$salary_type->head.'</td>
				<td>'.trans('messages.'.$salary_type->salary_type).'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/salary-type/'.$salary_type->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['salary-type.destroy',$salary_type->id],'salary_type','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(SalaryType $salary_type){
		return view('salary_type.edit',compact('salary_type'));
	}

	public function store(SalaryTypeRequest $request, SalaryType $salary_type){	

		$salary_type->fill($request->all())->save();

		$this->logActivity(['module' => 'salary_type','unique_id' => $salary_type->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $salary_type->head,'id' => $salary_type->id,'field' => 'salary_type_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.salary_type').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

		return redirect('/configuration#salary')->withSuccess(trans('messages.salary_type').' '.trans('messages.added'));				
	}

	public function update(SalaryTypeRequest $request, SalaryType $salary_type){

		$salary_type->fill($request->all())->save();

		$this->logActivity(['module' => 'salary_type','unique_id' => $salary_type->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.salary_type').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#salary')->withSuccess(trans('messages.salary_type').' '.trans('messages.updated'));
	}

	public function destroy(SalaryType $salary_type,Request $request){

		$this->logActivity(['module' => 'salary_type','unique_id' => $salary_type->id,'activity' => 'activity_deleted']);

        $salary_type->delete();
        
        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.salary_type').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect('/configuration#salary')->withSuccess(trans('messages.salary_type').' '.trans('messages.deleted'));
	}
}
?>