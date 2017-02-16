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
        		trans('messages.remarks'),
        		trans('messages.status')
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

		$expense_status_details = \App\ExpenseStatusDetail::whereDesignationId(Auth::user()->designation_id)->get()->pluck('expense_id')->all();

		if(Entrust::can('manage_all_expense'))
        	$expenses = Expense::all();
        elseif(Entrust::can('manage_subordinate_expense')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = \App\User::whereIn('designation_id',$child_designations)->pluck('id')->all();
			array_push($child_users, Auth::user()->id);

			$subordinate_expenses = Expense::whereIn('user_id',$child_users)->get()->pluck('id')->all();
			$all_expenses = array_unique(array_merge($expense_status_details,$subordinate_expenses));
    		$expenses = Expense::whereIn('id',$all_expenses)->get();
        } else {
    		$my_expenses = Expense::where('user_id','=',Auth::user()->id)->get()->pluck('id')->all();
			$all_expenses = array_unique(array_merge($expense_status_details,$my_expenses));
			$expenses = Expense::whereIn('id',$all_expenses)->get();
        }

        $rows=array();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);

        foreach($expenses as $expense){
			$row = array(
					'<div class="btn-group btn-group-xs">'.
					'<a href="/expense/'.$expense->id.'" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-arrow-circle-right"></i></a> '.
					((Entrust::can('edit_expense') && $this->expenseAccessible($expense)) ? '<a href="#" data-href="/expense/'.$expense->id.'/edit" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
					(($expense->attachments != null) ? '<a href="/expense/'.$expense->id.'/download" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.download').'"> <i class="fa fa-download"></i></a>' : '').
					((Entrust::can('edit_expense') && $this->expenseAccessible($expense)) ? delete_form(['expense.destroy',$expense->id]) : '').
					'</div>',
					$expense->User->full_name_with_designation,
					$expense->ExpenseHead->head,
					currency($expense->amount),
					showDate($expense->date_of_expense),
					$expense->remarks,
					trans('messages.'.$expense->status)
					);	
			$id = $expense->id;

			foreach($col_ids as $col_id)
				array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');
        	$rows[] = $row;
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function expenseStatistics(){

        $col_heads = array(
        		trans('messages.employee'),
        		trans('messages.designation'),
        		trans('messages.applied'),
        		trans('messages.pending'),
        		trans('messages.rejected'),
        		trans('messages.approved'),
        		);

        $menu = ['expense'];
        $table_info = array(
			'source' => 'expense-statistics',
			'title' => 'All Expense List',
			'id' => 'expense_statistics_table'
		);

		return view('expense.statistics',compact('col_heads','menu','table_info'));
	}

	public function postExpenseStatistics(Request $request){

		if(defaultRole())
          $users = \App\User::all();
        elseif(Entrust::can('manage_all_employee'))
          $users = \App\User::whereIsHidden(0)->get();
        elseif(Entrust::can('manage_subordinate_employee')){
          $childs = Helper::childDesignation(Auth::user()->designation_id,1);
		  $child_users = \App\User::whereIn('designation_id',$childs)->pluck('id')->all();
          array_push($child_users, Auth::user()->id);
          $users = \App\User::whereIn('id',$child_users)->get();
        } else
          $users = \App\User::whereId(Auth::user()->id)->get();

        $rows=array();

        $expenses = \App\Expense::all();

        foreach($users as $user){
        	$rows[] = array(
        		$user->full_name,
        		$user->Designation->full_designation,
        		$expenses->whereLoose('user_id',$user->id)->count(),
        		$expenses->whereLoose('user_id',$user->id)->whereLoose('status','pending')->count(),
        		$expenses->whereLoose('user_id',$user->id)->whereLoose('status','rejected')->count(),
        		$expenses->whereLoose('user_id',$user->id)->whereLoose('status','approved')->count(),
        	);
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function show(Expense $expense){

		$expense_status_detail = \App\ExpenseStatusDetail::whereExpenseId($expense->id)->whereDesignationId(Auth::user()->designation_id)->first();

		if(!$this->expenseAccessible($expense) && !$expense_status_detail)
          	return redirect('/expense')->withErrors(trans('messages.permission_denied'));

    	$other_expenses = Expense::where('id','!=',$expense->id)
    		->whereUserId($expense->user_id)
    		->get();

    	$status = Helper::translateList(config('lists.expense_status'));

        $expense_status_detail = \App\ExpenseStatusDetail::whereExpenseId($expense->id)->whereDesignationId(Auth::user()->designation_id)->first();
        $expense_status_enabled = $this->getExpenseStatus($expense);

        $menu = ['expense'];

		return view('expense.show',compact('expense','other_expenses','status','menu','expense_status_enabled','expense_status_detail'));
	}

	public function getExpenseStatus($expense){

        $expense_status_detail = \App\ExpenseStatusDetail::whereExpenseId($expense->id)->whereDesignationId(Auth::user()->designation_id)->first();

        if($expense_status_detail)
        	$previous_expense_status_detail = \App\ExpenseStatusDetail::whereExpenseId($expense->id)->where('id','<',$expense_status_detail->id)->orderBy('id','desc')->first();
        
        if($expense_status_detail && $previous_expense_status_detail)
        	$previous_expense_status = $previous_expense_status_detail->status;
        else
        	$previous_expense_status = null;

        if($expense_status_detail)
        	$next_expense_status_detail = \App\ExpenseStatusDetail::whereExpenseId($expense->id)->where('id','>',$expense_status_detail->id)->first();
        
        if($expense_status_detail && $next_expense_status_detail)
        	$next_expense_status = $next_expense_status_detail->status;
        else
        	$next_expense_status = null;

		$last_expense_status_detail = \App\ExpenseStatusDetail::whereExpenseId($expense->id)->orderBy('id','desc')->first();

		if(!$expense_status_detail)
			$expense_status_enabled = 0;
		elseif($previous_expense_status == 'rejected' || $previous_expense_status == 'pending')
			$expense_status_enabled = 0;
		elseif($last_expense_status_detail && $last_expense_status_detail->designation_id == Auth::user()->designation_id)
			$expense_status_enabled = 1;
		elseif($next_expense_status == 'pending' || $next_expense_status == null)
			$expense_status_enabled = 1;
		elseif($expense_status_detail == 'pending' || $expense_status_detail == null)
			$expense_status_enabled = 1;
		else
			$expense_status_enabled = 0;

		return $expense_status_enabled;
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

        $expense_status_detail = \App\ExpenseStatusDetail::whereExpenseId($expense->id)->first();

		if($expense_status_detail && $expense_status_detail->status != 'pending')
            return view('common.error',['message' => trans('messages.expense_cannot_edit')]);

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

		$parents = Helper::getParent(Auth::user()->designation_id);

		if(config('config.expense_approval_level') != 'designation' && !count($parents)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.expense_approver_unavailable'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withInput()->withErrors(trans('messages.expense_approver_unavailable'));
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

		if(config('config.expense_approval_level') == 'designation')
			$expense_status_insert[] = array('expense_id' => $expense->id,'designation_id' => config('config.expense_approval_level_detail'),'status' => 'pending');

		if(count($parents)){
			if(config('config.expense_approval_level') == 'single')
				$expense_status_insert[] = array('expense_id' => $expense->id,'designation_id' => $parents[0],'status' => 'pending');
			elseif(config('config.expense_approval_level') == 'multiple'){
				$i = 1;
				foreach($parents as $parent){
					if($i <= config('config.expense_approval_level_multiple') && $parent != null)
					$expense_status_insert[] = array('expense_id' => $expense->id,'designation_id' => $parent,'status' => (($i == 1) ? 'pending' : null));
					$i++;
				}
			}
			elseif(config('config.expense_approval_level') == 'last'){
				$i = 1;
				foreach($parents as $parent){
					$expense_status_insert[] = array('expense_id' => $expense->id,'designation_id' => $parent,'status' => (($i == 1) ? 'pending' : null));
					$i++;
				}
			}
		}
		
		\App\ExpenseStatusDetail::insert($expense_status_insert);

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
        
        $expense_status_detail = \App\ExpenseStatusDetail::whereExpenseId($expense->id)->first();

		if($expense_status_detail && $expense_status_detail->status != 'pending'){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.expense_cannot_edit'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors(trans('messages.expense_cannot_edit'));
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

		if(!$expense){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.invalid_link'));
		}

		$expense_status_detail = \App\ExpenseStatusDetail::whereExpenseId($expense->id)->whereDesignationId(Auth::user()->designation_id)->first();

		if(!$this->expenseAccessible($expense) && !$expense_status_detail)
          	return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $expense_status_enabled = $this->getExpenseStatus($expense);

		if($expense_status_enabled == 0){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
          	return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$expense_status_detail->status = $request->input('status');
		$expense_status_detail->remarks = $request->input('admin_remarks');
		$expense_status_detail->save();

        $next_expense_status_detail = \App\ExpenseStatusDetail::whereExpenseId($expense->id)->where('id','>',$expense_status_detail->id)->first();
        if($next_expense_status_detail){
        	$next_expense_status_detail->status = ($request->input('status') == 'pending') ? null : 'pending';
        	$next_expense_status_detail->save();
        }
		$last_expense_status_detail = \App\ExpenseStatusDetail::whereExpenseId($expense->id)->orderBy('id','desc')->first();

        if($request->input('status') == 'rejected'){
        	$expense->status = 'rejected';
        	$expense->admin_remarks = $request->input('admin_remarks');
        	$expense->save();
        	\App\ExpenseStatusDetail::where('id','>',$expense_status_detail->id)->update(['status' => null]);
        } else {
        	$expense->status = 'pending';
        	$expense->admin_remarks = null;
        	$expense->save();
        }

		if($last_expense_status_detail && $last_expense_status_detail->designation_id == Auth::user()->designation_id){
			$expense->status = ($request->input('status')) ? : 'pending';
			$expense->admin_remarks = ($request->input('status') != 'pending') ? $request->input('admin_remarks') : null;
			$expense->save();
		}
		
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