<?php
namespace App\Http\Controllers;
use App\TicketNote;
use Illuminate\Http\Request;
use Auth;

Class TicketNoteController extends Controller{
    use BasicController;

	public function store(Request $request,$id){

		$note = TicketNote::firstOrNew(['ticket_id' => $id,'user_id' => Auth::user()->id]);
		$note->note = $request->input('note');
	    $note->save();
		$this->logActivity(['module' => 'ticket_note','unique_id' => $note->id,'secondary_id' => $id, 'activity' => 'activity_saved']);
        
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.note').' '.trans('messages.saved'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
	    return redirect('/ticket/'.$id."#note")->withSuccess(trans('messages.note').' '.trans('messages.saved'));
	}
}
?>