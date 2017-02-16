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

        $child_designation = Helper::childDesignation(Auth::user()->designation_id,1);
        $child_users = \App\User::whereIn('designation_id',$child_designation)->pluck('id')->all();
        array_push($child_users,Auth::user()->id);
		$messages = Message::whereToUserId(Auth::user()->id)
			->whereDeleteReceiver('0')->orderBy('created_at','desc')->get();

        $count_inbox = count($messages);
        $count_sent = Message::whereFromUserId(Auth::user()->id)
			->whereDeleteSender('0')
        	->count();

        $col_heads = [trans('messages.option'),trans('messages.from'),trans('messages.category'),trans('messages.priority'),trans('messages.subject'),trans('messages.date_time'),''];
        $menu = ['message'];
        $table_info = array(
			'source' => 'message/inbox',
			'title' => 'Inbox List',
			'id' => 'message_table',
			'form' => 'message_search'
		);

        if(Entrust::can('message_all_employee'))
            $users = \App\User::where('id','!=',Auth::user()->id)->get()->pluck('full_name_with_designation', 'id')->all();
        elseif(Entrust::can('message_subordinate'))
            $users = \App\User::whereIn('id',$child_users)->get()->pluck('full_name_with_designation', 'id')->all();
        else
            $users = [];

        $message_categories = \App\MessageCategory::all()->pluck('name','id')->all();
        $message_priorities = \App\MessagePriority::all()->pluck('name','id')->all();
        $locations = \App\Location::all()->pluck('name','id')->all();
        $status = ['open' => 'Open','close' => 'Close'];
        $assets = ['search'];
        $type = 'inbox';

		return view('message.inbox',compact('count_inbox','count_sent','col_heads','menu','table_info','users','message_categories','message_priorities','assets','locations','status','type'));
	}

	public function sent(){

        $child_designation = Helper::childDesignation(Auth::user()->designation_id,1);
        $child_users = \App\User::whereIn('designation_id',$child_designation)->pluck('id')->all();
        array_push($child_users,Auth::user()->id);
		$messages = Message::whereFromUserId(Auth::user()->id)
			->whereDeleteSender('0')->orderBy('created_at','desc')->get();

        $count_sent = count($messages);
        $count_inbox = Message::whereToUserId(Auth::user()->id)
			->whereDeleteReceiver('0')
        	->count();

        $col_heads = [trans('messages.option'),trans('messages.to'),trans('messages.category'),trans('messages.priority'),trans('messages.subject'),trans('messages.date_time'),''];
        $menu = ['message'];
        $table_info = array(
			'source' => 'message/sent',
			'title' => 'Sent List',
			'id' => 'message_table',
			'form' => 'message_search'
		);

        if(Entrust::can('message_all_employee'))
            $users = \App\User::where('id','!=',Auth::user()->id)->get()->pluck('full_name_with_designation', 'id')->all();
        elseif(Entrust::can('message_subordinate'))
            $users = \App\User::whereIn('id',$child_users)->get()->pluck('full_name_with_designation', 'id')->all();
        else
            $users = [];

        $message_categories = \App\MessageCategory::all()->pluck('name','id')->all();
        $message_priorities = \App\MessagePriority::all()->pluck('name','id')->all();
        $locations = \App\Location::all()->pluck('name','id')->all();
        $status = ['open' => 'Open','close' => 'Close'];

        $assets = ['search'];
        $type = 'sent';

		return view('message.sent',compact('count_inbox','count_sent','col_heads','menu','table_info','assets','users','message_categories','message_priorities','locations','status','type'));
	}

	public function search(){
        $response = ['message' => trans('messages.request_submit'), 'status' => 'success']; 
        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	}

	public function lists($type, Request $request){

        $child_designation = Helper::childDesignation(Auth::user()->designation_id,1);
        $child_users = \App\User::whereIn('designation_id',$child_designation)->pluck('id')->all();
        array_push($child_users,Auth::user()->id);

        $token = csrf_token();

		if($type == 'sent'){
			$query = Message::whereFromUserId(Auth::user()->id)
			->whereDeleteSender('0');
		}
		else{
			$query = Message::whereToUserId(Auth::user()->id)
			->whereDeleteReceiver('0');
		}

		if($request->has('message_category_id'))
			$query->whereMessageCategoryId($request->input('message_category_id'));

		if($request->has('message_priority_id'))
			$query->whereMessageCategoryId($request->input('message_priority_id'));

		if($request->has('status'))
			$query->whereStatus($request->input('status'));

		if($request->has('user_id') && $type == 'sent')
			$query->whereToUserId($request->input('user_id'));
		elseif($request->has('user_id') && $type == 'inbox')
			$query->whereFromUserId($request->input('user_id'));

		$messages = $query->orderBy('created_at','desc')->get();

		$location = ($request->has('location_id')) ? \App\Location::find($request->input('location_id')) : null;

        $rows=array();
        foreach($messages as $message){

			$option = '<a href="/message/view/'.$message->id.'/'.$token.'" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-arrow-circle-right"></i></a><a href="#" data-href="/message/'.$message->id.'/edit" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>';

			if(($type == 'sent' && $message->from_user_id == Auth::user()->id) || ($type == 'inbox' && $message->to_user_id == Auth::user()->id))
			$option .= '<a href="/message/'.$message->id.'/delete/'.$token.'" class="btn btn-default btn-xs alert_delete"  data-toggle="tooltip" title="'.trans('messages.delete').'"> <i class="fa fa-trash-o"></i></a>';
			
			if($type != 'sent'){
				$source = $message->UserFrom->full_name_with_designation;
				if(!$message->is_read)
					$source = "<strong>".e($source)."</strong>";
			} else
				$source = $message->UserTo->full_name_with_designation;

			if($type == 'sent')
				$user_location = Helper::getLocation(date('Y-m-d',strtotime($message->created_at)),$message->to_user_id);
			else
				$user_location = Helper::getLocation(date('Y-m-d',strtotime($message->created_at)),$message->from_user_id);

			if(!$location || ($location && $location->name == $user_location))
			$rows[] = array('<div class="btn-group btn-group-xs">'.$option.'</div>', 
					$source.' '.(($message->status == 'open') ? '<span class="label label-danger">Open</span>': '<span class="label label-success">Close</span>'),
					$message->MessageCategory->name,
					$message->MessagePriority->name,
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

        $message_categories = \App\MessageCategory::all()->pluck('name','id')->all();
        $message_priorities = \App\MessagePriority::all()->pluck('name','id')->all();

        $assets = ['rte'];
        $menu = ['message'];
		return view('message.compose',compact('users','count_inbox','count_sent','assets','menu','message_categories','message_priorities'));
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
	    $message->message_category_id = $request->input('message_category_id');
	    $message->message_priority_id = $request->input('message_priority_id');
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

		$message = Message::whereId($id)->where(function($query){
			$query->where('from_user_id','=',\Auth::user()->id)
				->orWhere('to_user_id','=',\Auth::user()->id);
		})->first();

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

	public function edit($id){

		$message = Message::whereId($id)->where(function($query){
			$query->where('from_user_id','=',\Auth::user()->id)
				->orWhere('to_user_id','=',\Auth::user()->id);
		})->first();

		if(!$message)
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$message_priorities = \App\MessagePriority::all()->pluck('name','id')->all();
        $status = ['open' => 'Open','close' => 'Close'];

		return view('message.edit',compact('message','message_priorities','status'));
	}

	public function update(Request $request, $id){

		$message = Message::whereId($id)->where(function($query){
			$query->where('from_user_id','=',\Auth::user()->id)
				->orWhere('to_user_id','=',\Auth::user()->id);
		})->first();

		if(!$message){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/message')->withErrors(trans('messages.invalid_link'));
		}

		$message->status = $request->input('status');
		$message->message_priority_id = $request->input('message_priority_id');
		$message->save();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.status').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/message')->withSuccess(trans('messages.status').' '.trans('messages.updated'));
	}

	public function view($id,$token){

	    if(!Helper::verifyCsrf($token))
	      return redirect('/dashboard')->withErrors(trans('messages.invalid_token'));

		$message = Message::whereId($id)->where(function($query){
			$query->where('from_user_id','=',\Auth::user()->id)
				->orWhere('to_user_id','=',\Auth::user()->id);
		})->first();

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

		$this->logActivity(['module' => 'message','unique_id' => $message->id,'activity' => 'activity_deleted']);

		if($message->to_user_id == Auth::user()->id)
		$message->delete_receiver = 1;
		else
		$message->delete_sender = 1;	
		$message->save();

		return redirect('/message')->withSuccess(trans('messages.message').' '.trans('messages.deleted'));
		
	}
}
?>