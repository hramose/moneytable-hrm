<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\OfficeShiftRequest;
use App\OfficeShift;
use App\Classes\Helper;
use Form;
use HTML;

Class OfficeShiftController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
	}

	public function lists(){
		$office_shifts = OfficeShift::all();
		$data = '';
		foreach($office_shifts as $office_shift){
			$data .= '<tr>
				<td>'.$office_shift->name.' '.(($office_shift->is_default) ? '<span class="badge badge-success">1</span>' : '').'</td>
				<td>
					<div class="btn-group btn-group-xs">'.((!$office_shift->is_default) ?
					'<a href="/office-shift/'.$office_shift->id.'/default" class="btn btn-xs btn-default"><i class="fa fa-clock-o" data-toggle="tooltip" title="'.trans('messages.make_default').'"></i></a>' : '').
					'<a href="#" data-href="/office-shift/'.$office_shift->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['office-shift.destroy',$office_shift->id],'office_shift','1').'
					</div>
				</td>';
				foreach($office_shift->OfficeShiftDetail as $day_detail){
					$data .= '<td>';
					if($day_detail->in_time == $day_detail->out_time)
						$data .= '-';
					else
						$data .= (($day_detail->overnight) ? '<strong>(O)</strong>' : '').' '.showTime($day_detail->in_time).' '.trans('messages.to').' '.showTime($day_detail->out_time);
					$data .= '</td>';
				}
			$data .= '</tr>';
		}
		return $data;
	}

	public function edit(OfficeShift $office_shift){
		$office_shift_details = $office_shift->OfficeShiftDetail;

		$week = array();
		foreach($office_shift_details as $office_shift_detail){
			$week['in_time'][$office_shift_detail->day] = date('h:i a',strtotime($office_shift_detail->in_time));
			$week['out_time'][$office_shift_detail->day] = date('h:i a',strtotime($office_shift_detail->out_time));
		}

		return view('office_shift.edit',compact('office_shift','week'));
	}

	public function store(OfficeShiftRequest $request, OfficeShift $office_shift){	

		$total_office_shift = OfficeShift::count();
        if(config('limit.office_shift') && $total_office_shift >= config('limit.office_shift')){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.crossed_max_office_shift_limit'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/dashboard')->withErrors(trans('messages.crossed_max_office_shift_limit'));
        }

		$office_shift->name = $request->input('name');
		$week = $request->input('week');
		$overnight = $request->input('overnight');

		$error = array();
		foreach(config('lists.week') as $key => $day){
			if(strtotime($week['in_time'][$key]) > strtotime($week['out_time'][$key]) && $overnight[$key] != 1)
				$error[] = trans('messages.'.$key);
		}	

		if(count($error)){
			if($request->has('ajax_submit')){
		        $response = ['message' => implode(',',$error).' '.trans('messages.out_time_not_less_than_in_time'), 'status' => 'error']; 
		        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
			}

			return redirect()->back()->withInput()->withErrors(implode(',',$error).' '.trans('messages.out_time_not_less_than_in_time'));
		}

		$office_shift->save();

		foreach(config('lists.week') as $key => $day){
			$office_shift_detail = new \App\OfficeShiftDetail;
			$office_shift_detail->office_shift_id = $office_shift->id;
			$office_shift_detail->overnight = isset($overnight[$key]) ? 1 : 0;
			$office_shift_detail->day = $key;
			$office_shift_detail->in_time = ($week['in_time'][$key]) ? date('H:i:s',strtotime($week['in_time'][$key])) : '00:00:00';
			$office_shift_detail->out_time = ($week['out_time'][$key]) ? date('H:i:s',strtotime($week['out_time'][$key])) : '00:00:00';
			$office_shift_detail->save();
		}

        if(\App\Setup::whereModule('office_shift')->whereCompleted(0)->first())
        	\App\Setup::whereModule('office_shift')->whereCompleted(0)->update(['completed' => 1]);

		$this->logActivity(['module' => 'office_shift','unique_id' => $office_shift->id,'activity' => 'activity_added']);
			
        if($request->has('ajax_submit')){
        	$data = $this->lists();
            $response = ['message' => trans('messages.office_shift').' '.trans('messages.added'), 'status' => 'success','data' => $data]; 
	        if(config('config.application_setup_info') && defaultRole()){
	        	$setup_data = Helper::setupInfo();
	        	$response['setup_data'] = $setup_data;
	        }
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

		return redirect('/configuration#office-shift')->withSuccess(trans('messages.office_shift').' '.trans('messages.added'));		
	}

	public function makeDefault($id){
		$office_shift = OfficeShift::find($id);

		if(!$office_shift){
			if(isset($request) && $request->has('ajax_submit')){
		        $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
		        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		    }

			return redirect('/')->withErrors(trans('messages.permission_denied'));
		}

		if($office_shift->is_default == 1){
			if(isset($request) && $request->has('ajax_submit')){
		        $response = ['message' => trans('messages.shift_already_marked_default'), 'status' => 'error']; 
		        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		    }

			return redirect('/configuration#office_shift')->withErrors(trans('messages.shift_already_marked_default'));
		}

		$affected_rows = OfficeShift::whereNotNull('id')->update(['is_default' => 0]);
		$office_shift->is_default = 1;
		$office_shift->save();

		$this->logActivity(['module' => 'office_shift','unique_id' => $office_shift->id,'activity' => 'activity_made_default']);
		
        if(isset($request) && $request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.office_shift').' '.trans('messages.made_default'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#office-shift')->withSuccess(trans('messages.office_shift').' '.trans('messages.made_default'));	
	}

	public function update(OfficeShiftRequest $request, OfficeShift $office_shift){

		$week = $request->input('week');
		$overnight = $request->input('overnight');

		$error = array();
		foreach(config('lists.week') as $key => $day){
			if(strtotime($week['in_time'][$key]) > strtotime($week['out_time'][$key]) && $overnight[$key] != 1)
				$error[] = trans('messages.'.$key);
		}	
	

		if(count($error)){
			if($request->has('ajax_submit')){
		        $response = ['message' => implode(',',$error).' '.trans('messages.out_time_not_less_than_in_time'), 'status' => 'error']; 
		        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		    }

			return redirect()->back()->withInput()->withErrors(implode(',',$error).' '.trans('messages.out_time_not_less_than_in_time'));
		}

		$office_shift->fill(['name' => $request->input('name')])->save();

		foreach(config('lists.week') as $key => $day){
			\App\OfficeShiftDetail::whereOfficeShiftId($office_shift->id)
				->where('day','=',$key)
				->update(['in_time' => ($week['in_time'][$key]) ? date('H:i:s',strtotime($week['in_time'][$key])) : '00:00:00',
					'out_time' => ($week['out_time'][$key]) ? date('H:i:s',strtotime($week['out_time'][$key])) : '00:00:00', 'overnight' => isset($overnight[$key]) ? 1 : 0]);
		}

		$this->logActivity(['module' => 'office_shift','unique_id' => $office_shift->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.office_shift').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#office-shift')->withSuccess(trans('messages.office_shift').' '.trans('messages.updated'));
	}

	public function destroy(OfficeShift $office_shift,Request $request){

		if($office_shift->is_default){
			if($request->has('ajax_submit')){
		        $response = ['message' => trans('messages.primary_shift_cannot_delete'), 'status' => 'error'];
		        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		    }
			return redirect()->back()->withInput()->withErrors(trans('messages.primary_shift_cannot_delete'));
		}

        $this->logActivity(['module' => 'office_shift','unique_id' => $office_shift->id,'activity' => 'activity_deleted']);

        $office_shift->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.office_shift').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect()->back()->withSuccess(trans('messages.office_shift').' '.trans('messages.deleted'));
	}
}
?>