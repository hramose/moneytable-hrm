<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\MessageCategoryRequest;
use App\MessageCategory;
use App\Classes\Helper;
use Entrust;

Class MessageCategoryController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('message_category.create');
	}

	public function lists(){
		$message_categories = MessageCategory::all();

		$data = '';
		foreach($message_categories as $message_category){
			$data .= '<tr>
				<td>'.$message_category->name.'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/message-category/'.$message_category->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['message-category.destroy',$message_category->id],'message_category','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(MessageCategory $message_category){
		return view('message_category.edit',compact('message_category'));
	}

	public function store(MessageCategoryRequest $request, MessageCategory $message_category){	

		$message_category->fill($request->all())->save();

		$this->logActivity(['module' => 'message_category','unique_id' => $message_category->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $message_category->name,'id' => $message_category->id,'field' => 'message_category_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.message_category').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#leave')->withSuccess(trans('messages.message_category').' '.trans('messages.added'));				
	}

	public function update(MessageCategoryRequest $request, MessageCategory $message_category){

		$message_category->fill($request->all())->save();

		$this->logActivity(['module' => 'message_category','unique_id' => $message_category->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.message_category').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }
		return redirect('/configuration#leave')->withSuccess(trans('messages.message_category').' '.trans('messages.updated'));
	}

	public function destroy(MessageCategory $message_category,Request $request){

		$this->logActivity(['module' => 'message_category','unique_id' => $message_category->id,'activity' => 'activity_deleted']);

        $message_category->delete();
        
        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.message_category').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect('/configuration#leave')->withSuccess(trans('messages.message_category').' '.trans('messages.deleted'));
	}
}
?>