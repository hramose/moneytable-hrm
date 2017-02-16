<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\DocumentTypeRequest;
use App\DocumentType;
use App\Classes\Helper;

Class DocumentTypeController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('document_type.create');
	}

	public function lists(){
		$document_types = DocumentType::all();

		$data = '';
		foreach($document_types as $document_type){
			$data .= '<tr>
				<td>'.$document_type->name.'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/document-type/'.$document_type->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['document-type.destroy',$document_type->id],'document_type','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(DocumentType $document_type){
		return view('document_type.edit',compact('document_type'));
	}

	public function store(DocumentTypeRequest $request, DocumentType $document_type){	

		$document_type->fill($request->all())->save();

		$this->logActivity(['module' => 'document_type','unique_id' => $document_type->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $document_type->name,'id' => $document_type->id,'field' => 'document_type_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.document_type').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

		return redirect('/configuration#document')->withSuccess(trans('messages.document_type').' '.trans('messages.added'));				
	}

	public function update(DocumentTypeRequest $request, DocumentType $document_type){

		$document_type->fill($request->all())->save();

		$this->logActivity(['module' => 'document_type','unique_id' => $document_type->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.document_type').' '.trans('messages.updated'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#document')->withSuccess(trans('messages.document_type').' '.trans('messages.updated'));
	}

	public function destroy(DocumentType $document_type,Request $request){

		$this->logActivity(['module' => 'document_type','unique_id' => $document_type->id,'activity' => 'activity_deleted']);

        $document_type->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.document_type').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect('/configuration#document')->withSuccess(trans('messages.document_type').' '.trans('messages.deleted'));
	}
}
?>