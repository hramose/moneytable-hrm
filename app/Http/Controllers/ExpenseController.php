<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\ExpenseRequest;
use Entrust;
use App\Classes\Helper;
use App\Expense;
use App\ExpenseHead;
use Auth;
use File;

Class ExpenseController extends Controller{
    use BasicController;

	protected $form = 'expense-form';

	public function index(){

		if(!Entrust::can('list_expense'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.employee'),
        		trans('messages.expense_head'),
        		trans('messages.amount'),
        		trans('messages.date'),
        		trans('messages.status'),
        		trans('messages.remarks')
        		);

		$expense_heads = ExpenseHead::pluck('head','id')->all();
        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $menu = ['expense'];
        $table_info = array(
			'source' => 'expense',
			'title' => 'Expense List',
			'id' => 'expense_table'
		);
		return view('expense.index',compact('col_heads','menu','table_info','expense_heads'));
	}

	public function lists(Request $request){

		$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
		$child_users = \App\User::whereIn('designation_id',$child_designations)->pluck('id')->all();
		array_push($child_users, Auth::user()->id);
		
		if(Entrust::can('manage_all_expense'))
        	$expenses = Expense::all();
        elseif(Entrust::can('manage_subordinate_expense')){
	    	$expenses = Expense::with('user')->whereHas('user', function($q) use ($child_users) {
	            $q->whereIn('user_id',$child_users);
	        })->get();
        } else
        	$expenses = Expense::whereUserId(Auth::user()->id)->get();

        $rows=array();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);

        foreach($expenses as $expense){
			$row = array(
					'<div class="btn-group btn-group-xs">'.
					((Entrust::can('edit_expense') && $this->expenseAccessible($expense)) ? '<a href="#" data-href="/expense/'.$expense->id.'/edit" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
					((Entrust::can('change_expense_status') && $this->expenseAccessible($expense)) ? '<a href="#" data-href="/expense/'.$expense->id.'/update-status" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal"> <i class="fa fa-lock" data-toggle="tooltip" title="'.trans('messages.change_status').'"></i></a> ' : '').
					(($expense->attachments != null) ? '<a href="/expense/'.$expense->id.'/download" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.download').'"> <i class="fa fa-download"></i></a>' : '').
					((Entrust::can('edit_expense') && $this->expenseAccessible($expense)) ? delete_form(['expense.destroy',$expense->id]) : '').
					'</div>',
					$expense->User->full_name_with_designation,
					$expense->ExpenseHead->head,
					currency($expense->amount),
					showDate($expense->date_of_expense),
					trans('messages.'.$expense->status),
					$expense->remarks
					);	
			$id = $expense->id;

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

		if(!Entrust::can('create_expense'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$expense_heads = ExpenseHead::pluck('head','id')->all();
        $menu = ['expense'];

		return view('expense.create',compact('expense_heads','menu'));
	}

	public function edit(Expense $expense){

		if(!Entrust::can('edit_expense') || !$this->expenseAccessible($expense))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$expense_heads = ExpenseHead::pluck('head','id')->all();

		$custom_field_values = Helper::getCustomFieldValues($this->form,$expense->id);
        $menu = ['expense'];

		return view('expense.edit',compact('expense','expense_heads','custom_field_values','menu'));
	}

	public function store(ExpenseRequest $request, Expense $expense){	

		if(!Entrust::can('create_expense')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
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

		$expense = new Expense;
		$data = $request->all();
	    $expense->fill($data);
	    $expense->status = 'pending';
		$expense->user_id = Auth::user()->id;

        if ($request->hasFile('attachments')) {
            $extension = $request->file('attachments')->getClientOriginalExtension();
            $filename = uniqid();
            $file = $request->file('attachments')->move(config('constants.upload_path.attachments'), $filename.".".$extension);
            $expense->attachments = $filename.".".$extension;
        }

		$expense->save();
		Helper::storeCustomField($this->form,$expense->id, $data);
		$this->logActivity(['module' => 'expense','unique_id' => $expense->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.expense').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.expense').' '.trans('messages.added'));		
	}

	public function update(ExpenseRequest $request, Expense $expense){

		$data = $request->all();
		$expense->fill($data);

        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }
        
        if ($request->hasFile('attachments') && $request->input('remove') != 1) {
            $extension = $request->file('attachments')->getClientOriginalExtension();
            $filename = uniqid();
            $file = $request->file('attachments')->move(config('constants.upload_path.attachments'), $filename.".".$extension);
            $expense->attachments = $filename.".".$extension;
        } elseif($request->input('remove') == 1){
            File::delete(config('constants.upload_path.attachments').$expense->attachments);
            $expense->attachments = null;
        }
        else
        $expense->attachments = $expense->attachments;

    	$expense->save();

		Helper::updateCustomField($this->form,$expense->id, $data);
		$this->logActivity(['module' => 'expense','unique_id' => $expense->id,'activity' => 'activity_updated']);
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.expense').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/expense')->withSuccess(trans('messages.expense').' '.trans('messages.updated'));
	}

	public function editStatus($id){
		$expense = Expense::find($id);

		if(!$expense || !$this->expenseAccessible($expense))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$expense_status = Helper::translateList(config('lists.expense_status'));

		return view('expense.update_status',compact('expense','expense_status'));
	}

	public function updateStatus(Request $request, $id){
		$expense = Expense::find($id);

		if(!$expense || !$this->expenseAccessible($expense)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.invalid_link'));
		}

		$expense->status = $request->input('status');
		$expense->admin_remarks = $request->input('admin_remarks');
		$expense->save();
		$this->logActivity(['module' => 'expense','unique_id' => $expense->id,'activity' => 'activity_status_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.expense').' '.trans('messages.status').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.expense').' '.trans('messages.status').' '.trans('messages.updated'));

	}

	public function download($id){
		$expense = Expense::find($id);

		if(!$expense || !$this->expenseAccessible($expense))
			return redirect()->back()->withErrors(trans('messages.invalid_link'));

		$file = config('constants.upload_path.attachments').$expense->attachments;

		if(File::exists($file))
			return response()->download($file);
		else
			return redirect()->back()->withErrors(trans('messages.file_not_found'));
	}

	public function destroy(Expense $expense,Request $request){
		if(!Entrust::can('delete_expense') || !$this->expenseAccessible($expense)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$file = config('constants.upload_path.attachments').$expense->attachments;

		if(File::exists($file))
			File::delete($file);

		$this->logActivity(['module' => 'expense','unique_id' => $expense->id,'activity' => 'activity_deleted']);
		
		Helper::deleteCustomField($this->form, $expense->id);
        $expense->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.expense').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/expense')->withSuccess(trans('messages.expense').' '.trans('messages.deleted'));
	}
}
?>