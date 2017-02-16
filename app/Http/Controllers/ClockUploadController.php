<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Entrust;
use App\Classes\Helper;
use File;

Class ClockUploadController extends Controller{
    use BasicController;

	public function index(){

		if(!Entrust::can('upload_attendance'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.employee'),
        		trans('messages.total'),
        		trans('messages.uploaded_count'),
        		trans('messages.rejected'),
        		trans('messages.date')
        		);

        $menu = ['attendance'];
        $table_info = array(
			'source' => 'attendance-upload',
			'title' => 'Attendance Upload List',
			'id' => 'attendance_upload'
		);
		return view('clock_upload.index',compact('col_heads','menu','table_info'));
	}

	public function showFails($id){
		$clock_upload = \App\ClockUpload::find($id);

		if(!$clock_upload)
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$clock_fails = \App\ClockFail::whereClockUploadId($id)->get();

		return view('clock_upload.show',compact('clock_upload','clock_fails'));
	}

	public function lists(Request $request){

		$clock_uploads = \App\ClockUpload::all();
        $rows=array();

        foreach($clock_uploads as $clock_upload){
			$rows[] = array(
					'<div class="btn-group btn-group-xs">
					<a href="/attendance-upload-log/'.$clock_upload->id.'/download" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.download').'"> <i class="fa fa-download"></i></a>'.
					delete_form(['clock-upload.destroy',$clock_upload->id]).
					'</div>',
					$clock_upload->User->full_name_with_designation,
					$clock_upload->total,
					$clock_upload->uploaded,
					(($clock_upload->rejected) ? '<a href="#" data-href="/attendance-upload-log/'.$clock_upload->id.'" data-toggle="modal" data-target="#myModal">'.$clock_upload->rejected.'</a>' : $clock_upload->rejected),
					showDate($clock_upload->created_at)
					);	
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function download($id){
		if(!Entrust::can('upload_attendance'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$clock_upload = \App\ClockUpload::find($id);

		if(!$clock_upload)
			return redirect('/dashboard')->withErrors(trans('messages.invalid_link'));

		$file = config('constants.upload_path.attendance').$clock_upload->filename;

		if(File::exists($file))
			return response()->download($file);
		else
			return redirect()->back()->withErrors(trans('messages.file_not_found'));
	}

	public function destroy($id,Request $request){

		$clock_upload = \App\ClockUpload::find($id);

		if(!$clock_upload){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
		}

		$this->logActivity(['module' => 'clock','unique_id' => $clock_upload->id,'activity' => 'activity_deleted']);

        $clock_upload->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.attendance').' '.trans('messages.upload').' '.trans('messages.log').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.attendance').' '.trans('messages.upload').' '.trans('messages.log').' '.trans('messages.deleted'));
	}
}