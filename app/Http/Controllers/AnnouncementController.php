<?php
namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests\AnnouncementRequest;
use Entrust;
use App\Announcement;
use App\Classes\Helper;
use Auth;

Class AnnouncementController extends Controller{
    use BasicController;

	protected $form = 'announcement-form';

	public function index(Announcement $announcement){

		if(!Entrust::can('list_announcement'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.title'),
        		trans('messages.published_for'),
        		trans('messages.from_date'),
        		trans('messages.to_date'),
        		trans('messages.published_by')
        		);

        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $menu = ['announcement'];
        $table_info = array(
			'source' => 'announcement',
			'title' => 'Announcement List',
			'id' => 'announcement_table'
		);

        if(Entrust::can('manage_all_announcement')){
        	$designations = \App\Designation::all()->pluck('full_designation','id')->all();
        }
        elseif(Entrust::can('manage_subordinate_announcement'))
        	$designations = Helper::childDesignation(Auth::user()->designation_id);
        else
        	$designations = [];
        $assets = ['rte'];

		return view('announcement.index',compact('col_heads','menu','table_info','designations','assets'));
	}

	public function lists(Request $request){

		$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
		array_push($child_designations, Auth::user()->designation_id);

		if(Entrust::can('manage_all_announcement'))
			$announcements = Announcement::all();
		elseif(Entrust::can('manage_subordinate_announcement'))
			$announcements = Announcement::with('designation')->whereHas('designation', function($q) use ($child_designations) {
	            $q->whereIn('designation_id',$child_designations);
	        })->get();
		else
			$announcements = Announcement::with('designation')->whereHas('designation',function($q) {
				$q->whereDesignationId(Auth::user()->designation_id);
			})->get();

        $rows=array();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);

        foreach($announcements as $announcement){

        	if($announcement->belongsToMany('App\Designation','announcement_designation')->count()){
	        	$designation_name = "<ol class='nl'>";
	        	foreach($announcement->Designation as $designation)
				    $designation_name .= "<li>$designation->full_designation</li>";
	        	$designation_name .= "</ol>";
        	} else
        		$designation_name = trans('messages.all');

			$row = array(
				'<div class="btn-group btn-group-xs">'.
				((Entrust::can('edit_announcement') && $this->announcementAccessible($announcement)) ? '<a href="#" data-href="announcement/'.$announcement->id.'/edit" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
				((Entrust::can('delete_announcement') && $this->announcementAccessible($announcement)) ? delete_form(['announcement.destroy',$announcement->id]) : '').
				'</div>',
				$announcement->title,
				$designation_name,
				showDate($announcement->from_date),
				showDate($announcement->to_date),
				$announcement->User->full_name_with_designation
				);	
			$id = $announcement->id;

			foreach($col_ids as $col_id)
				array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');
        	$rows[] = $row;
			
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function show(Announcement $announcement){

		$announcement = \App\Announcement::with('designation')->whereId($announcement->id)->whereHas('designation',function($query) {
                    $query->where('designation_id','=',Auth::user()->designation_id);
                })->orWhere(function ($query)  { $query->doesntHave('designation'); })->first();

		if(!$announcement)
			return redirect('/dashboard')->withErrors(trans('messages.invalid_link'));

        $announcements = \App\Announcement::with('designation')->where('id','!=',$announcement->id)->whereHas('designation',function($query) {
                    $query->where('designation_id','=',Auth::user()->designation_id);
                })->orWhere(function ($query)  { $query->doesntHave('designation'); })->orderBy('id','desc')->take(5)->get();

		return view('announcement.show',compact('announcement','announcements'));
	}

	public function create(){

		if(!Entrust::can('create_announcement'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        if(Entrust::can('manage_all_announcement')){
        	$designations = \App\Designation::all()->pluck('full_designation','id')->all();
        }
        elseif(Entrust::can('manage_subordinate_announcement'))
        	$designations = Helper::childDesignation(Auth::user()->designation_id);
        else
        	$designation = [];
        $menu = ['announcement'];

		return view('announcement.create',compact('designations','menu'));
	}

	public function edit(Announcement $announcement){

		if(!Entrust::can('edit_announcement') || !$this->announcementAccessible($announcement))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$selected_designation = array();

		foreach($announcement->Designation as $designation){
			$selected_designation[] = $designation->id;
		}

        if(Entrust::can('manage_all_announcement')){
        	$designations = \App\Designation::all()->pluck('full_designation','id')->all();
        }
        elseif(Entrust::can('manage_subordinate_announcement'))
    	$designations = Helper::childDesignation($announcement->User->designation_id);
        else
        	$designation = [];

		$custom_field_values = Helper::getCustomFieldValues($this->form,$announcement->id);
        $menu = ['announcement'];
        $assets = ['rte'];

		return view('announcement.edit',compact('designations','announcement','selected_designation','custom_field_values','menu','assets'));
	}

	public function store(AnnouncementRequest $request, Announcement $announcement){

		if(!Entrust::can('create_announcement')){
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

		$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
		array_push($child_designations, Auth::user()->designation_id);
		$designations = ($request->input('designation_id')) ? : $child_designations;
		$data = $request->all();
	    $announcement->fill($data);
	    $announcement->description = clean($request->input('description'));
		$announcement->user_id = Auth::user()->id;
		$announcement->save();
	    $announcement->designation()->sync($designations);
		Helper::storeCustomField($this->form,$announcement->id, $data);
		$this->logActivity(['module' => 'announcement','unique_id' => $announcement->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.announcement').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.announcement').' '.trans('messages.added'));		
	}

	public function update(AnnouncementRequest $request, Announcement $announcement){

		if(!Entrust::can('edit_announcement') || !$this->announcementAccessible($announcement)){
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
        
		$child_designations = Helper::childDesignation($announcement->User->designation_id,1);
		array_push($child_designations, Auth::user()->designation_id);
		$designations = ($request->input('designation_id')) ? : $child_designations;

		$data = $request->all();
		$announcement->fill($data);
	    $announcement->description = clean($request->input('description'));
		$announcement->save();
	    $announcement->designation()->sync($designations);
		Helper::updateCustomField($this->form,$announcement->id, $data);
		$this->logActivity(['module' => 'announcement','unique_id' => $announcement->id,'activity' => 'activity_updated']);
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.announcement').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/announcement')->withSuccess(trans('messages.announcement').' '.trans('messages.updated'));
	}

	public function destroy(Announcement $announcement,Request $request){

		if(!Entrust::can('delete_announcement') || !$this->announcementAccessible($announcement)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$this->logActivity(['module' => 'announcement','unique_id' => $announcement->id,'activity' => 'activity_deleted']);

		Helper::deleteCustomField($this->form, $announcement->id);
        $announcement->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.announcement').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/announcement')->withSuccess(trans('messages.announcement').' '.trans('messages.deleted'));
	}
}
?>