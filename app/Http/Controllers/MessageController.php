<?php
namespace App\Http\Controllers;
use DB;
use App\Classes\Helper;
use Auth;
use App\Message;
use Entrust;
use File;
use Illuminate\Http\Request;
use App\Http\Requests\MessageRequest;

Class MessageController extends Controller{
    use BasicController;

	public function inbox(){

		$messages = Message::whereToUserId(Auth::user()->id)
			->whereDeleteReceiver('0')->orderBy('created_at','desc')->get();
        $count_inbox = count($messages);
        $count_sent = Message::whereFromUserId(Auth::user()->id)
			->whereDeleteSender('0')
        	->count();

        $col_heads = [trans('messages.option'),trans('messages.from'),trans('messages.subject'),trans('messages.date_time'),''];
        $menu = ['message'];
        $table_info = array(
			'source' => 'message/inbox',
			'title' => 'Inbox List',
			'id' => 'message_table'
		);

		return view('message.inbox',compact('count_inbox','count_sent','col_heads','menu','table_info'));
	}

	public function sent(){

		$messages = Message::whereFromUserId(Auth::user()->id)
			->whereDeleteSender('0')->orderBy('created_at','desc')->get();

        $count_sent = count($messages);
        $count_inbox = Message::whereToUserId(Auth::user()->id)
			->whereDeleteReceiver('0')
        	->count();

        $col_heads = [trans('messages.option'),trans('messages.to'),trans('messages.subject'),trans('messages.date_time'),''];
        $menu = ['message'];
        $table_info = array(
			'source' => 'message/sent',
			'title' => 'Sent List',
			'id' => 'message_table'
		);

		return view('message.sent',compact('count_inbox','count_sent','col_heads','menu','table_info'));
	}

	public function lists($type, Request $request){

        $token = csrf_token();
		if($type == 'sent')
			$messages = Message::whereFromUserId(Auth::user()->id)
			->whereDeleteSender('0')->orderBy('created_at','desc')->get();
		else
			$messages = Message::whereToUserId(Auth::user()->id)
			->whereDeleteReceiver('0')->orderBy('created_at','desc')->get();

        $rows=array();
        foreach($messages as $message){

			$option = '<a href="/message/view/'.$message->id.'/'.$token.'" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-arrow-circle-right"></i></a><a href="/message/'.$message->id.'/delete/'.$token.'" class="btn btn-default btn-xs alert_delete"  data-toggle="tooltip" title="'.trans('messages.delete').'"> <i class="fa fa-trash-o"></i></a>';
			
			if($type != 'sent'){
				$source = $message->UserFrom->full_name_with_designation;
				if(!$message->is_read)
					$source = "<strong>".e($source)."</strong>";
			} else
				$source = $message->UserTo->full_name_with_designation;

			$rows[] = array('<div class="btn-group btn-group-xs">'.$option.'</div>', 
					$source,
					e($message->subject),
					showDateTime($message->created_at),
					($message->attachments != '') ? '<i class="fa fa-paperclip"></i>' : ''
					);	
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function compose(){

        $child_designation = Helper::childDesignation(Auth::user()->designation_id,1);
        $child_users = \App\User::whereIn('designation_id',$child_designation)->pluck('id')->all();
        
        if(Entrust::can('message_all_employee'))
            $users = \App\User::where('id','!=',Auth::user()->id)->get()->pluck('full_name_with_designation', 'id')->all();
        elseif(Entrust::can('message_subordinate'))
            $users = \App\User::whereIn('id',$child_users)->get()->pluck('full_name_with_designation', 'id')->all();
        else
            $users = [];

		$messages = Message::whereToUserId(Auth::user()->id)
			->whereDeleteReceiver('0')
			->get();
        $count_inbox = count($messages);
        $count_sent = Message::whereFromUserId(Auth::user()->id)
			->whereDeleteSender('0')
        	->count();

        $assets = ['rte'];
        $menu = ['message'];
		return view('message.compose',compact('users','count_inbox','count_sent','assets','menu'));
	}

	public function store(MessageRequest $request){	

		$data = $request->all();
		$filename = uniqid();
		
     	if ($request->hasFile('file')) {
	 		$extension = $request->file('file')->getClientOriginalExtension();
	 		$file = $request->file('file')->move(config('constants.upload_path.attachments'), $filename.".".$extension);
	 		$data['attachments'] = $filename.".".$extension;
		 }
		 else
		 	$data['attachments'] = '';

		$message = new Message;
	    $message->fill($data);
	    $message->body = clean($request->input('body'));
	    $message->from_user_id = Auth::user()->id;
	    $message->is_read = 0;
		$message->save();

		$this->logActivity(['module' => 'message','unique_id' => $request->input('to_user_id'),'activity' => 'activity_message_sent']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.message').' '.trans('messages.sent'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/message/compose')->withSuccess(trans('messages.message').' '.trans('messages.sent'));	
	}

	public function download($id){
		$message = Message::find($id);

		if(!$message)
			return redirect('/message')->withErrors(trans('messages.invalid_link'));

		if($message->attachments == null || $message->attachments == '')
			return redirect('/message')->withErrors(trans('messages.invalid_link'));

		$file = config('constants.upload_path.attachments').$message->attachments;
		if(File::exists($file))
			return response()->download($file);
		else
			return redirect()->back()->withErrors(trans('messages.file_not_found'));
	}

	public function view($id,$token){

	    if(!Helper::verifyCsrf($token))
	      return redirect('/dashboard')->withErrors(trans('messages.invalid_token'));

		$message = Message::find($id);

		if(!$message)
			return redirect('/message')->withErrors(trans('messages.invalid_link'));

		$query = \App\User::whereNotNull('id');
		if($message->from_user_id == Auth::user()->id){
			$message_type = 'sent';
			$query->where('id','=',$message->to_user_id);
		}
		elseif($message->to_user_id == Auth::user()->id){
			$message_type = 'inbox';
			$message->is_read = 1;
			$query->where('id','=',$message->from_user_id);
		}
		else
			return redirect('/message')->withErrors(trans('messages.invalid_link'));


    	$user = $query->first();

        $count_inbox = Message::whereToUserId(Auth::user()->id)
			->whereDeleteReceiver('0')
        	->count();
        $count_sent = Message::whereFromUserId(Auth::user()->id)
			->whereDeleteSender('0')
        	->count();

		$message->save();
        $menu = ['message'];

		return view('message.view',compact('message','user','count_inbox','count_sent','menu'));
	}

	public function delete($id,$token){

	    if(!Helper::verifyCsrf($token))
	      return redirect('/dashboard')->withErrors(trans('messages.invalid_token'));

		$message = Message::find($id);
		if(!$message || ($message->to_user_id != Auth::user()->id && $message->from_user_id != Auth::user()->id))
			return redirect('/message')->withErrors(trans('messages.invalid_link'));

		if($message->to_user_id == Auth::user()->id)
		$message->delete_receiver = 1;
		else
		$message->delete_sender = 1;	
		$message->save();

		return redirect('/message')->withSuccess(trans('messages.message').' '.trans('messages.deleted'));
		
	}
}
?>