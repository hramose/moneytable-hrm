<?php
namespace App\Http\Controllers;
use App\User;
use App\Document;
use Entrust;
use File;
use Auth;
use DB;
use App\Classes\Helper;
use Illuminate\Http\Request;
use App\Http\Requests\DocumentRequest;

Class DocumentController extends Controller{
    use BasicController;

    public function lists(Request $request){
        $data = '';

        $employee = User::find($request->input('employee_id'));

        if(!$employee)
            return $data;

		foreach($employee->Document as $document)
		{
			$data .= '<tr>
				<td>'.$document->DocumentType->name.'</td>
				<td>'.$document->title.'</td>
				<td>'.showDate($document->date_of_expiry).'</td>
				<td>'.$document->description.'</td>
				<td>'.(($document->status) ? '<span class="badge badge-success">'.trans('messages.active').'</span>' : '<span class="badge badge-danger">'.trans('messages.in_active').'</span>').'</td>';

				if(config('config.employee_manage_own_document') || $request->has('show_option')){
					$data .='<td>
						<div class="btn-group btn-group-xs">
						<a href="/document/download/'.$document->id.'" class="btn btn-xs btn-default" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-download"></i></a>'.
						delete_form(['document.destroy',$document->id]).
						'</div>
					</td>';
				}
			$data .='</tr>';
		}
		return $data;
    }

	public function store(DocumentRequest $request, $id){

        $employee = User::find($id);

        if(!$employee){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
            return redirect('/employee')->withErrors(trans('messages.invalid_link'));
        }

		if(!$this->employeeAccessible($employee)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$filename = uniqid();
		$data = $request->all();
		$data['date_of_expiry'] = ($request->input('date_of_expiry')) ? : null;

		$document = new Document;
     	if ($request->hasFile('attachments')) {
	 		$extension = $request->file('attachments')->getClientOriginalExtension();
	 		$file = $request->file('attachments')->move(config('constants.upload_path.attachments'), $filename.".".$extension);
	 		$data['attachments'] = $filename.".".$extension;
		}
	    $document->fill($data);

	    $document->status = 1;
        $employee->document()->save($document);
        $this->logActivity(['module' => 'document','unique_id' => $document->id,'activity' => 'activity_added','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.document').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee/'.$id."#document")->withSuccess(trans('messages.document').' '.trans('messages.added'));			
	}

	public function destroy(Document $document,Request $request){

		if(!$this->employeeAccessible($document->User)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$id = $document->User->id;
        $this->logActivity(['module' => 'document','unique_id' => $document->id,'activity' => 'activity_deleted','secondary_id' => $id]);
		File::delete(config('constants.upload_path.attachments').$document->attachments);
		$document->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.document').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/employee/'.$id."#document")->withSuccess(trans('messages.document').' '.trans('messages.deleted'));
	}

	public function filter(Request $request){

        $query = Document::whereNotNull('id');

        if(defaultRole())
          $user_lists = User::all()->pluck('full_name_with_designation', 'id')->all();
        elseif(Entrust::can('manage_all_employee'))
          $user_lists = User::whereIsHidden(0)->get()->pluck('full_name_with_designation', 'id')->all();
        elseif(Entrust::can('manage_subordinate_employee')){
          $child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
          $childs = User::whereIn('designation_id',$child_designations)->pluck('id');
          		$query->whereIn('user_id',$childs);
          $user_lists = User::whereIn('designation_id',$child_designations)->get()->pluck('full_name_with_designation', 'id')->all();
        } else
          $user_lists = [];

        if($request->has('user_id'))
        	$query->whereUserId($request->input('user_id'));
        if($request->input('expired') == 'expired')
			$query->where('date_of_expiry','<',date('Y-m-d'));
		elseif($request->input('expired') == 'valid')
			$query->where('date_of_expiry','>=',date('Y-m-d'));
        if($request->input('status') == 'active')
			$query->whereStatus(1);
        elseif($request->input('status') == 'inactive')
			$query->whereStatus(0);
		if($request->has('expiry_on'))
			$query->where('date_of_expiry','>=',date('Y-m-d'))->where('date_of_expiry','<=',date('Y-m-d', strtotime(' +'.$request->input('expiry_on').' day')));
        
        $documents = $query->get();

		$filter_data = [
			'expired' => $request->input('expired'),
			'user_id' => $request->input('user_id'),
			'status' => $request->input('status'),
			'expiry_on' => $request->input('expiry_on')
			];
         
        $col_data=array();
        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.employee'),
        		trans('messages.department'),
        		trans('messages.document_type'),
        		trans('messages.date_of_expiry'),
        		trans('messages.title'),
        		trans('messages.status')
        		);

        foreach($documents as $document){
			$col_data[] = array(
				'<div class="btn-group btn-group-xs">'.
				'<a href="/document/download/'.$document->id.'" class="btn btn-xs btn-default" data-toggle="tooltip" title="'.trans('messages.download').'") !!}"> <i class="fa fa-download"></i></a> '.
				(($document->status) ? 
				'<a href="/document/status/'.$document->id.'" class="btn btn-xs btn-default" data-toggle="tooltip" title="'.trans('messages.make_in_active').'"> <i class="fa fa-ban"></i></a> ' : 
				'<a href="/document/status/'.$document->id.'" class="btn btn-xs btn-default" data-toggle="tooltip" title="'.trans('messages.make_active').'"> <i class="fa fa-check"></i></a> ').
				delete_form(['document.destroy',$document->id]).
				'</div>',
				$document->User->full_name,
				$document->User->Designation->full_designation,
				$document->DocumentType->name,
				($document->date_of_expiry < date('Y-m-d')) ? '<span class="badge badge-danger">'.showDate($document->date_of_expiry).'</span>' : '<span class="badge badge-success">'.showDate($document->date_of_expiry).'</span>',
				$document->title,
				($document->status) ? '<span class="badge badge-success">'.trans('messages.active').'</span>' : '<span class="badge badge-danger">'.trans('messages.in_active').'</span>'
				);
        }
        Helper::writeResult($col_data);
        $menu = ['employee'];

		return view('documents.list',compact('col_heads','user_lists','filter_data','menu'));
	}

	public function download($id){
		$document = Document::find($id);
		if(!$document)
			return redirect()->withErrors(trans('messages.invalid_link'));

		if(!$this->employeeAccessible($document->User))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$file = config('constants.upload_path.attachments').$document->attachments;

		if(File::exists($file))
			return response()->download($file);
		else
			return redirect()->back()->withErrors(trans('messages.file_not_found'));
	}
}
?>