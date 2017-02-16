<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\QualificationLanguageRequest;
use App\QualificationLanguage;
use App\Classes\Helper;

Class QualificationLanguageController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('qualification_language.create');
	}

	public function lists(){
		$qualification_languages = QualificationLanguage::all();

		$data = '';
		foreach($qualification_languages as $qualification_language){
			$data .= '<tr>
				<td>'.$qualification_language->name.'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/qualification-language/'.$qualification_language->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['qualification-language.destroy',$qualification_language->id],'qualification_language','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(QualificationLanguage $qualification_language){
		return view('qualification_language.edit',compact('qualification_language'));
	}

	public function store(QualificationLanguageRequest $request, QualificationLanguage $qualification_language){
		$qualification_language->fill($request->all())->save();

		$this->logActivity(['module' => 'qualification_language','unique_id' => $qualification_language->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $qualification_language->name,'id' => $qualification_language->id,'field' => 'qualification_language_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.qualification_language').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#qualification')->withSuccess(trans('messages.qualification_language').' '.trans('messages.added'));
	}

	public function update(QualificationLanguageRequest $request, QualificationLanguage $qualification_language){

		$qualification_language->fill($request->all())->save();

		$this->logActivity(['module' => 'qualification_language','unique_id' => $qualification_language->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.qualification_language').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#qualification')->withSuccess(trans('messages.qualification_language').' '.trans('messages.updated'));
	}

	public function destroy(QualificationLanguage $qualification_language,Request $request){
		$this->logActivity(['module' => 'qualification_language','unique_id' => $qualification_language->id,'activity' => 'activity_deleted']);

        $qualification_language->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.qualification_language').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect()->back()->withSuccess(trans('messages.qualification_language').' '.trans('messages.deleted'));
	}
}
?>