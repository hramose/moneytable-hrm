<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\TicketRequest;
use DB;
use Entrust;
use App\Ticket;
use App\User;
use Auth;
use App\Classes\Helper;

Class TicketController extends Controller{
    use BasicController;

	protected $form = 'ticket-form';

	public function index(Ticket $ticket){

		if(!Entrust::can('list_ticket'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.employee'),
        		trans('messages.subject'),
        		trans('messages.priority'),
        		trans('messages.status'),
        		trans('messages.date')
        		);

		$priorities = Helper::translateList(config('lists.ticket_priority'));
        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $menu = ['ticket'];
        $table_info = array(
			'source' => 'ticket',
			'title' => 'Ticket List',
			'id' => 'ticket_table'
		);

		return view('ticket.index',compact('col_heads','menu','table_info','priorities'));
	}

	public function lists(Request $request){

    	if(Entrust::can('manage_all_ticket'))
    		$tickets = Ticket::all();
    	elseif(Entrust::can('manage_subordinate_ticket')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
			array_push($child_users, Auth::user()->id);
			$tickets = Ticket::whereIn('user_id',$child_users)->get();
    	} else
    		$tickets = Ticket::where('user_id','=',Auth::user()->id)->get();
    	
        $rows = array();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);

        foreach($tickets as $ticket){
			$row = array(
					'<div class="btn-group btn-group-xs">'.
					'<a href="/ticket/'.$ticket->id.'" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-arrow-circle-right"></i></a> '.
					((Entrust::can('edit_ticket') && $this->ticketAccessible($ticket)) ? '<a href="#" data-href="/ticket/'.$ticket->id.'/edit" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
					((Entrust::can('delete_ticket') && $this->ticketAccessible($ticket)) ? delete_form(['ticket.destroy',$ticket->id]) : '').'</div>',
					$ticket->UserAdded->full_name_with_designation,
					$ticket->subject,
					trans('messages.'.$ticket->priority),
					trans('messages.'.$ticket->status),
					showDateTime($ticket->created_at)
					);	
			$id = $ticket->id;

			foreach($col_ids as $col_id)
				array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');
        	$rows[] = $row;
        }

        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function assignTicket(Request $request,$id){

		if(!Entrust::can('assign_ticket')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$ticket = Ticket::find($id);

		if(!$ticket){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/ticket')->withErrors(trans('messages.invalid_link'));
		}

	    $ticket->user()->sync(($request->input('user_id')) ? : []);
		$this->logActivity(['module' => 'ticket','unique_id' => $ticket->id,'activity' => 'activity_user_assigned']);

        if($request->has('ajax_submit')){
			$new_data = '';
			foreach($ticket->User as $user){
				$new_data .= '<li class="media">'.Helper::getAvatar($user->id).
				'<div class="media-body" style="vertical-align:middle; padding-left:10px;">
				  <h4 class="media-heading"><a href="#">'.$user->full_name.'</a> <br /> <small>'.$user->Designation->full_designation.'</small></h4>';
				  if($user->id == $ticket->user_id)
					$new_data .= '<span class="label label-danger pull-right">Admin</span>';
				$new_data .= '</div>
			  </li>';
			}
			$new_data .= '<script>$("#ticket-assigned-user .textAvatar").nameBadge();</script>';
            $response = ['message' => trans('messages.ticket').' '.trans('messages.user').' '.trans('messages.assigned'), 'status' => 'success','new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.ticket').' '.trans('messages.user').' '.trans('messages.assigned'));	
	}


	public function updateTicketStatus(Request $request,$id){

		if(!Entrust::can('update_ticket_status')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$ticket = Ticket::find($id);

		if(!$ticket){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/ticket')->withErrors(trans('messages.invalid_link'));
		}

		$ticket->status = $request->input('status');
		if($request->input('status') == 'open')
			$ticket->closed_at = null;
		else
			$ticket->closed_at = new \DateTime;
		$ticket->admin_remarks = $request->input('admin_remarks');
		$ticket->save();
		$this->logActivity(['module' => 'ticket','unique_id' => $ticket->id,'activity' => 'activity_status_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.ticket').' '.trans('messages.status').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.ticket').' '.trans('messages.status').' '.trans('messages.updated'));
	}

	public function show(Ticket $ticket){

		$assigned_to = array();
		foreach($ticket->User as $user)
			$assigned_to[] = $user->id;

		if(!in_array(Auth::user()->id,$assigned_to) && $ticket->user_id != Auth::user()->id && !$this->ticketAccessible($ticket))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$status = Helper::translateList(config('lists.ticket_status'));

		if(defaultRole())
			$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_all_ticket'))
			$users = \App\User::whereIsHidden(0)->get()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_ticket')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$users = \App\User::whereIn('designation_id',$child_designations)->orWhere('id','=',Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();
		} else 
			$users = \App\User::whereId(Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();
		
		$selected_user = array();
		foreach($ticket->User as $user)
			$selected_user[] = $user->id;
        $menu = ['ticket'];
        $assets = ['rte'];

		return view('ticket.show',compact('selected_user','users','ticket','status','menu','assets'));
	}

	public function create(){

		if(!Entrust::can('create_ticket'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$priorities = Helper::translateList(config('lists.ticket_priority'));
        $menu = ['ticket'];

		return view('ticket.create',compact('priorities','menu'));
	}

	public function edit(Ticket $ticket){

		if(!Entrust::can('edit_ticket') || !$this->ticketAccessible($ticket))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$custom_field_values = Helper::getCustomFieldValues($this->form,$ticket->id);
		$priorities = Helper::translateList(config('lists.ticket_priority'));
        $menu = ['ticket'];
		return view('ticket.edit',compact('ticket','custom_field_values','priorities','menu'));
	}

	public function store(TicketRequest $request, Ticket $ticket){	

		if(!Entrust::can('create_ticket')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

		$data = $request->all();
	    $ticket->fill($data);
	    $ticket->status = 'open';
	    $ticket->user_id = Auth::user()->id;
		$ticket->save();
		$this->logActivity(['module' => 'ticket','unique_id' => $ticket->id,'activity' => 'activity_added']);
		
		Helper::storeCustomField($this->form,$ticket->id, $data);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.ticket').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.ticket').' '.trans('messages.added'));		
	}

	public function update(TicketRequest $request, Ticket $ticket){

		if(!Entrust::can('edit_ticket') || !$this->ticketAccessible($ticket)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }
        
		$data = $request->all();
		$ticket->fill($data);
		$ticket->save();
		Helper::updateCustomField($this->form,$ticket->id, $data);
		$this->logActivity(['module' => 'ticket','unique_id' => $ticket->id,'activity' => 'activity_updated']);
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.ticket').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/ticket')->withSuccess(trans('messages.ticket').' '.trans('messages.updated'));
	}

	public function destroy(Ticket $ticket,Request $request){
		
		if(!Entrust::can('delete_ticket') || !$this->ticketAccessible($ticket)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		Helper::deleteCustomField($this->form, $ticket->id);
		$this->logActivity(['module' => 'ticket','unique_id' => $ticket->id,'activity' => 'activity_deleted']);
        
		$ticket->delete();
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.ticket').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/ticket')->withSuccess(trans('messages.ticket').' '.trans('messages.deleted'));
	}
}
?>