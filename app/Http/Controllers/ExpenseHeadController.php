<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\ExpenseHeadRequest;
use App\ExpenseHead;
use App\Classes\Helper;

Class ExpenseHeadController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('expense_head.create');
	}

	public function lists(){
		$expense_heads = ExpenseHead::all();

		$data = '';
		foreach($expense_heads as $expense_head){
			$data .= '<tr>
				<td>'.$expense_head->head.'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/expense-head/'.$expense_head->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['expense-head.destroy',$expense_head->id],'expense_head','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(ExpenseHead $expense_head){
		return view('expense_head.edit',compact('expense_head'));
	}

	public function store(ExpenseHeadRequest $request, ExpenseHead $expense_head){	

		$expense_head->fill($request->all())->save();

		$this->logActivity(['module' => 'expense_head','unique_id' => $expense_head->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $expense_head->head,'id' => $expense_head->id,'field' => 'expense_head_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.expense_head').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

		return redirect('/configuration#expense')->withSuccess(trans('messages.expense_head').' '.trans('messages.added'));				
	}

	public function update(ExpenseHeadRequest $request, ExpenseHead $expense_head){

		$expense_head->fill($request->all())->save();

		$this->logActivity(['module' => 'expense_head','unique_id' => $expense_head->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.expense_head').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#expense')->withSuccess(trans('messages.expense_head').' '.trans('messages.updated'));
	}

	public function destroy(ExpenseHead $expense_head,Request $request){

		$this->logActivity(['module' => 'expense_head','unique_id' => $expense_head->id,'activity' => 'activity_deleted']);

        $expense_head->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.expense_head').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect('/configuration#expense')->withSuccess(trans('messages.expense_head').' '.trans('messages.deleted'));
	}
}
?>