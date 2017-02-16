<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\HolidayRequest;
use Entrust;
use App\Classes\Helper;
use App\Holiday;

Class HolidayController extends Controller{
    use BasicController;

	protected $form = 'holiday-form';

	public function index(Holiday $holiday){

		if(!Entrust::can('list_holiday'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.date'),
        		trans('messages.description'));

        $col_heads = Helper::putCustomHeads($this->form, $col_heads);

        $holiday_count = array();
        $month_holiday = array();
        $month_holiday_count = array();

        foreach(config('lists.month') as $month)
        	$month_holiday[ucfirst($month)] = 0;
        $holidays = Holiday::where('date','>=',date('Y').'-01-01')->where('date','<=',date('Y').'-12-31')->get();
    	foreach($holidays as $holiday)
    		$month_holiday[date('F',strtotime($holiday->date))]++;

    	foreach($month_holiday as $key => $value)
    		$month_holiday_count[] = array($key,$value,$value.' '.trans('messages.holiday').' '.trans('messages.in').' '.trans('messages.'.strtolower($key)));

        $menu = ['holiday'];
        $assets = ['graph'];
        $table_info = array(
			'source' => 'holiday',
			'title' => 'Holiday List',
			'id' => 'holiday_table'
		);

		return view('holiday.index',compact('col_heads','assets','menu','table_info','month_holiday_count'));
	}

	public function lists(Request $request){

        $holidays = Holiday::all();

        $rows=array();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);

        foreach($holidays as $holiday){

			$row = array('<div class="btn-group btn-group-xs">'.
					(Entrust::can('edit_holiday') ? '<a href="#" data-href="/holiday/'.$holiday->id.'/edit" class="btn btn-default btn-xs md-trigger" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
					(Entrust::can('delete_holiday') ? delete_form(['holiday.destroy',$holiday->id]) : '').
					'</div>',
					showDate($holiday->date),
					$holiday->description);		
			$id = $holiday->id;

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

		if(!Entrust::can('create_holiday'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $menu = ['holiday'];
		return view('holiday.create',compact('menu'));
	}

	public function edit(Holiday $holiday){

		if(!Entrust::can('edit_holiday'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$custom_field_values = Helper::getCustomFieldValues($this->form,$holiday->id);
        $menu = ['holiday'];
		return view('holiday.edit',compact('holiday','custom_field_values','menu'));
	}

	public function store(HolidayRequest $request,Holiday $holiday){	

		if(!Entrust::can('create_holiday'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

		$dates = explode(',',$request->input('date'));
		$data = $request->all();
		foreach($dates as $date){
			$holiday_exists = Holiday::where('date','=',$date)->count();
			if(!$holiday_exists){
				$holiday = new Holiday;
				$holiday->date = $date;
				$holiday->description = $request->input('description');
				$holiday->save();
				Helper::storeCustomField($this->form,$holiday->id, $data);
			}
		}
		$this->logActivity(['module' => 'holiday','activity' => 'activity_added']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.holiday').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.holiday').' '.trans('messages.added'));		
	}

	public function update(HolidayRequest $request,Holiday $holiday){

		if(!Entrust::can('edit_holiday'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }
        
		$dates = explode(',',$request->input('date'));
		if(count($dates) > 1){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.one_holiday_edit_one_time'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.one_holiday_edit_one_time'));
		}
		
	    $data = $request->all();
		$holiday->fill($data);
		$holiday->save();
		Helper::updateCustomField($this->form,$holiday->id, $data);
		$this->logActivity(['module' => 'holiday','activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.holiday').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/holiday')->withSuccess(trans('messages.holiday').' '.trans('messages.updated'));
	}

	public function destroy(Holiday $holiday,Request $request){
		if(!Entrust::can('delete_holiday')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$this->logActivity(['module' => 'holiday','activity' => 'activity_deleted']);
		Helper::deleteCustomField($this->form, $holiday->id);
        $holiday->delete();
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.holiday').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/holiday')->withSuccess(trans('messages.holiday').' '.trans('messages.deleted'));
	}
}
?>