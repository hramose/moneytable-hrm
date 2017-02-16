<?php
namespace App\Http\Controllers;
use DB;
use Entrust;
use App\Award;
use App\AwardType;
use App\Classes\Helper;
use Illuminate\Http\Request;
use App\Http\Requests\AwardRequest;
use Auth;

Class AwardController extends Controller{
    use BasicController;

	protected $form = 'award-form';

	public function index(Award $award){

		if(!Entrust::can('list_award'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.award_type'),
        		trans('messages.employee'),
        		trans('messages.gift'),
        		trans('messages.cash'),
        		trans('messages.description'),
        		trans('messages.month_and_year'),
        		trans('messages.date')
        		);

        if(Entrust::can('manage_all_award'))
        	$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
        elseif(Entrust::can('manage_subordinate_award')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
        	$users = \App\User::whereIn('designation_id',$child_designations)->get()->pluck('full_name_with_designation','id');
        }
        else
        	$users = [];

        $award_types = AwardType::pluck('name','id')->all();
        $months = Helper::translateList(config('lists.month'));

		$award_types = AwardType::pluck('name','id')->all();
        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $menu = ['award'];
        $assets = ['rte'];
        $table_info = array(
			'source' => 'award',
			'title' => 'Award List',
			'id' => 'award_table'
		);

		return view('award.index',compact('col_heads','table_info','menu','award_types','users','months','assets'));
	}

	public function lists(){

		if(Entrust::can('manage_all_award'))
			$awards = Award::all();
		elseif(Entrust::can('manage_subordinate_award')) {
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = \App\User::whereIn('designation_id',$child_designations)->pluck('id');
			$awards = Award::with('user')->whereHas('user',function($q) use($child_users){
				$q->whereIn('user_id',$child_users);
			})->get();
		} else
			$awards = Award::with('user')->whereHas('user',function($q) {
				$q->whereUserId(Auth::user()->id);
			})->get();

        $rows=array();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);

        foreach($awards as $award){

        	if(count($award->User)){
	        	$user_name = "<ol class='nl'>";
	        	foreach($award->User as $user)
				    $user_name .= "<li>$user->full_name_with_designation</li>";
	        	$user_name .= "</ol>";
        	} else 
        	$user_name = trans('messages.all');

			$row = array(
				'<div class="btn-group btn-group-xs">'.
				((Entrust::can('edit_award') && $this->awardAccessible($award)) ? '<a href="#" data-href="/award/'.$award->id.'/edit" class="btn btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>' : '').
				((Entrust::can('delete_award') && $this->awardAccessible($award)) ? delete_form(['award.destroy',$award->id]) : '').
				'</div>',
				$award->AwardType->name,
				$user_name,
				$award->gift,
				currency($award->cash),
				$award->description,
				ucfirst($award->month)." ".$award->year,
				showDate($award->award_date)
				);	
			$id = $award->id;

			foreach($col_ids as $col_id)
				array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');
        	$rows[] = $row;
			
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function show(){
	}

	public function create(){

		if(!Entrust::can('create_award'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        if(Entrust::can('manage_all_award'))
        	$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
        elseif(Entrust::can('manage_subordinate_award')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
        	$users = \App\User::whereIn('designation_id',$child_designations)->get()->pluck('full_name_with_designation','id');
        }
        else
        	$users = [];

        $award_types = AwardType::pluck('name','id')->all();
        $months = Helper::translateList(config('lists.month'));
        $menu = ['award'];
        
		return view('award.create',compact('users','award_types','months','menu'));
	}

	public function edit(Award $award){

		if(!Entrust::can('edit_award') || !$this->awardAccessible($award))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$selected_user = array();

		foreach($award->User as $user){
			$selected_user[] = $user->id;
		}

		if(Entrust::can('manage_all_award'))
        	$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
        elseif(Entrust::can('manage_subordinate_award')){
			$child_designations = Helper::childDesignation($award->UserAdded->designation_id,1);
        	$users = \App\User::whereIn('designation_id',$child_designations)->get()->pluck('full_name_with_designation','id');
        }
        else
        	$users = [];

        $award_types = AwardType::pluck('name','id')->all();

		$custom_field_values = Helper::getCustomFieldValues($this->form,$award->id);
        $months = Helper::translateList(config('lists.month'));
        $menu = ['award'];
        $assets = ['rte'];

		return view('award.edit',compact('users','award','award_types','selected_user','custom_field_values','months','menu','assets'));
	}

	public function store(AwardRequest $request, Award $award){	

		if(!Entrust::can('create_award')){
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
	    $award->fill($data);
	    $award->description = clean($request->input('description'));
	    $award->user_id = Auth::user()->id;
		$award->save();
	    $award->user()->sync($request->input('user_id'));
		Helper::storeCustomField($this->form,$award->id, $data);
		$this->logActivity(['module' => 'award','unique_id' => $award->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.award').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.award').' '.trans('messages.added'));	
	}

	public function update(AwardRequest $request, Award $award){

		if(!Entrust::can('edit_award') || !$this->awardAccessible($award)){
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
		$award->fill($data);
	    $award->description = clean($request->input('description'));
		$award->save();
	    $award->user()->sync($request->input('user_id'));
		Helper::updateCustomField($this->form,$award->id, $data);
		$this->logActivity(['module' => 'award','unique_id' => $award->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.award').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/award')->withSuccess(trans('messages.award').' '.trans('messages.updated'));
	}

	public function destroy(Award $award,Request $request){
		if(!Entrust::can('delete_award') || !$this->awardAccessible($award)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		Helper::deleteCustomField($this->form, $award->id);
        
		$this->logActivity(['module' => 'award','unique_id' => $award->id,'activity' => 'activity_deleted']);
        $award->delete();
        
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.award').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/award')->withSuccess(trans('messages.award').' '.trans('messages.deleted'));
	}
}
?>