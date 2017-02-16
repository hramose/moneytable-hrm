<?php
namespace App\Http\Controllers;
use App\TaskNote;
use Illuminate\Http\Request;
use Auth;

Class TaskNoteController extends Controller{
    use BasicController;

	public function __construct()
	{
		if(!in_array('utilities', config('limit.available_module')))
			$this->middleware('feature_available');
	}
	
	public function store(Request $request,$id){

		$note = TaskNote::firstOrNew(['task_id' => $id,'user_id' => Auth::user()->id]);
		$note->note = $request->input('note');
	    $note->save();
		$this->logActivity(['module' => 'task_note','unique_id' => $note->id,'secondary_id' => $id, 'activity' => 'activity_saved']);
	    
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.note').' '.trans('messages.saved'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
	    return redirect('/task/'.$id."#note")->withSuccess(trans('messages.note').' '.trans('messages.saved'));
	}
}
?>