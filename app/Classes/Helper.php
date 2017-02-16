<?php
namespace App\Classes;
use File;
use Form;
use Session;
use DB;
use App\CustomField;
use App\CustomFieldValue;
use App\Designation;
use Validator;
use Auth;
use Entrust;
use Services_Twilio;
use Services_Twilio_RestException;

	Class Helper{

		public static function getUsedStorage(){
			$file_size = 0;
			if(!File::exists(config('constants.upload_path.attachments')))
				File::makeDirectory(config('constants.upload_path.attachments'));
    		foreach(File::allFiles(config('constants.upload_path.attachments')) as $file)
        	$file_size += $file->getSize();
        	return number_format(($file_size / 1048576), 2, '.', '');
		}

		public static function updateAttendanceRecord($user,$date){
			
		}

		public static function getRatingStar($rating,$only = 0){
			$rating = round($rating * 2,0) / 2;
			$full_star = floor($rating);
			$half_star = $rating - $full_star;
			$star = '<div>';
			for($i = 1; $i <= $full_star; $i++)
				$star .= '<i class="fa fa-lg fa-star icon rating-star" ></i>';

			if($half_star)
				$star .= '<i class="fa fa-lg fa-star-half icon rating-star" ></i>';
			$star .= '</div>';

			if(!$only)
				return $star;
			else
				return ($rating) ? $rating : '';
		}

		public static function getSubTaskRating($task_id,$user_id,$only = 0){
			$task = \App\Task::find($task_id);
			$sub_tasks = $task->SubTask->pluck('id')->all();
			$rating = \App\SubTaskRating::where('user_id','=',$user_id)->whereIn('sub_task_id',$sub_tasks)->avg('rating');
			if(!$only)
				return Helper::getRatingStar($rating);
			else
				return round(($rating*2),0)/2;
		}

		public static function setConfig($config_vars){

	        foreach($config_vars as $config_var){
	            config(['config.'.$config_var->name => (isset($config_var->value) && $config_var->value != '' && $config_var->value != null) ? $config_var->value : config('config.'.$config_var->name)]);
	        }

			config([
            'mail.driver' => ($config_vars->whereLoose('name','driver')->first()) ? $config_vars->whereLoose('name','driver')->first()->value : '',
            'mail.from.address' => ($config_vars->whereLoose('name','from_address')->first()) ? $config_vars->whereLoose('name','from_address')->first()->value : '',
            'mail.from.name' => ($config_vars->whereLoose('name','from_name')->first()) ? $config_vars->whereLoose('name','from_name')->first()->value : '',
            'mail.encryption' => ($config_vars->whereLoose('name','encryption')->first()) ? $config_vars->whereLoose('name','encryption')->first()->value : ''
            ]);

	        if(config('mail.driver') == 'smtp'){
	            config([
	            'mail.host' => ($config_vars->whereLoose('name','host')->first()) ? $config_vars->whereLoose('name','host')->first()->value : '',
	            'mail.port' => ($config_vars->whereLoose('name','port')->first()) ? $config_vars->whereLoose('name','port')->first()->value : '',
	            'mail.username' => ($config_vars->whereLoose('name','username')->first()) ? $config_vars->whereLoose('name','username')->first()->value : '',
	            'mail.password' => ($config_vars->whereLoose('name','password')->first()) ? $config_vars->whereLoose('name','password')->first()->value : ''
	            ]);
	        }

	        if(config('mail.driver') == 'mailgun'){
	            config([
	            'mail.host' => ($config_vars->whereLoose('name','mailgun_host')->first()) ? $config_vars->whereLoose('name','mailgun_host')->first()->value : '',
	            'mail.port' => ($config_vars->whereLoose('name','mailgun_port')->first()) ? $config_vars->whereLoose('name','mailgun_port')->first()->value : '',
	            'mail.username' => ($config_vars->whereLoose('name','mailgun_username')->first()) ? $config_vars->whereLoose('name','mailgun_username')->first()->value : '',
	            'mail.password' => ($config_vars->whereLoose('name','mailgun_password')->first()) ? $config_vars->whereLoose('name','mailgun_password')->first()->value : '',
	            'services.mailgun.domain' => ($config_vars->whereLoose('name','mailgun_domain')->first()) ? $config_vars->whereLoose('name','mailgun_domain')->first()->value : '',
	            'services.mailgun.secret' => ($config_vars->whereLoose('name','mailgun_secret')->first()) ? $config_vars->whereLoose('name','mailgun_secret')->first()->value : '',
	            ]);
	        }

	        if(config('mail.driver') == 'mandrill'){
	            config([
	                'services.mandrill.secret' => ($config_vars->whereLoose('name','mandrill_secret')->first()) ? $config_vars->whereLoose('name','mandrill_secret')->first()->value : ''
	            ]);
	        }	        
		}

		public static function setDefaultEmail(){
			config(['mail.driver' => config('constants.mail_default.driver'),
					'mail.from.address' => config('constants.mail_default.from_address'),
					'mail.from.name' => config('constants.mail_default.from_name'),]);
		}

		public static function validateIp(){

			$ip = \Request::getClientIp();

			$wl_ips = \App\Ip::all();
			$allowedIps = array();
			foreach($wl_ips as $wl_ip){
				if($wl_ip->end)
					$allowedIps[] = $wl_ip->start.'-'.$wl_ip->end;
				else
					$allowedIps[] = $wl_ip->start;
			}

			foreach ($allowedIps as $allowedIp) 
	        {
	            if (strpos($allowedIp, '*')) 
	            {
	                $range = [ 
	                    str_replace('*', '0', $allowedIp),
	                    str_replace('*', '255', $allowedIp)
	                ];
	                if(Helper::ipExistsInRange($range, $ip)) return true;
	            } 
	            else if(strpos($allowedIp, '-'))
	            {
	                $range = explode('-', str_replace(' ', '', $allowedIp));
	                if(Helper::ipExistsInRange($range, $ip)) return true;
	            }
	            else 
	            {
	                if (ip2long($allowedIp) === ip2long($ip)) return true;
	            }
	        }
	        return false;
		}

		public static function ipExistsInRange(array $range, $ip)
	    {
	        if (ip2long($ip) >= ip2long($range[0]) && ip2long($ip) <= ip2long($range[1])) 
	            return true;
	        return false;
	    }

		public static function setupInfo(){

			$url = \Request::path();
			$con = is_numeric(strpos($url, 'configuration'));

	        $setup = \App\Setup::orderBy('id','asc')->get();
	        $setup_total = 0;
	        $setup_completed = 0;
	        foreach($setup as $value){
	            $setup_total += config('setup.'.$value->module.'.weightage');
	            if($value->completed)
	                $setup_completed += config('setup.'.$value->module.'.weightage');
	        }
	        $setup_percentage = ($setup_total) ? round(($setup_completed/$setup_total) * 100) : 0;

	        $data = '<div class="progress">
	                  <div class="progress-bar progress-bar-'.Helper::activityTaskProgressColor( $setup_percentage).'" role="progressbar" aria-valuenow="'.$setup_percentage.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$setup_percentage.'%;">'.
	                    $setup_percentage.'%
	                  </div>
	                </div>';
	                foreach ($setup->chunk(4) as $chunk)
	                {
	                    $data .= '<div class="row" style="padding:5px;">';
	                        foreach ($chunk as $setup_info)
	                        {
	                            $data .= '<div class="col-xs-3">';
	                                if($setup_info->completed)
	                                    $data .= '<i class="fa fa-check-circle success fa-2x" style="vertical-align:middle;"></i> '.trans('messages.'.$setup_info->module);
	                                else
	                                    $data .= '<i class="fa fa-times-circle danger fa-2x" style="vertical-align:middle;"></i><a href="/'.config('setup.'.$setup_info->module.'.link').'" '.($con && config('setup.'.$setup_info->module.'.tab') ? 'data-toggle="tab"' : '').'> '.trans('messages.'.$setup_info->module).'</a>';
	                            $data .= '</div>';
	                        }
	                    $data .= '</div>';
	                }

	                if($setup_percentage == 100)
	                $data .= '<p class="alert alert-success">Great! You have setup the application completely and ready to use. </p>';

	        return $data;
	    }

		public static function sendSMS($to,$message){
	        $client = new Services_Twilio(config('twilio.sid'), config('twilio.token'));
	        try{
	          $message = $client->account->messages->create(array(
	              "From" => config('twilio.from'),
	              "To" => $to,
	              "Body" => $message
	          ));
	          return 1;
	        } catch (Services_Twilio_RestException $e) {
	          return $e->getMessage();
	        }
		}

		public static function callCurl($url,$postData){
			$ch = curl_init($url);
	        curl_setopt_array($ch, array(
	            CURLOPT_URL => $url,
	            CURLOPT_RETURNTRANSFER => true,
	            CURLOPT_POST => true,
	            CURLOPT_POSTFIELDS => $postData
	        ));
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	        $data = curl_exec($ch);
	        if(curl_errno($ch))
	            return response()->json(['error' => '108']);

	        curl_close($ch);

	        return json_decode($data, true);
		}

		public static function translateList($lists){
			$translated_list = array();
			foreach($lists as $key => $list)
				$translated_list[$key] = trans('messages.'.$key);

			return $translated_list;
		}

		public static function createSlug($string){
		   if(Helper::checkUnicode($string))
		   		$slug = str_replace(' ', '-', $string);
		   else
		   		$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
		   return $slug;
		}
		
		public static function checkUnicode($string)
		{
			if(strlen($string) != strlen(utf8_decode($string)))
			return true;
			else
			return false;
		}

		public static function getContract($user_id = null, $date = null){
			if($user_id == null)
				$user_id = Auth::user()->id;
			if($date == null)
				$date = date('Y-m-d');

			$contract = \App\Contract::whereUserId($user_id)
                ->where('from_date','<=',$date)
                ->where('to_date','>=',$date)
                ->first();

            return $contract;
		}

		public static function getShift($date, $user_id = ''){
			if($user_id == '')
				$user_id = Auth::user()->id;
	        $default_shift = \App\OfficeShift::whereIsDefault(1)->first();

	        if(!$default_shift)
	        	return null;
			
			$shift = \App\UserShift::whereUserId($user_id)->where('from_date','<=',$date)->where('to_date','>=',$date)->first();

			if(!$shift)
				$shift = \App\UserShift::whereUserId($user_id)->where('from_date','<=',$date)->whereNull('to_date')->orderBy('from_date','desc')->first();

	        if(!$shift)
	            $my_shift = $default_shift->OfficeShiftDetail->whereLoose('day',strtolower(date('l',strtotime($date))))->first();
	        else
	            $my_shift = $shift->OfficeShift->OfficeShiftDetail->whereLoose('day',strtolower(date('l',strtotime($date))))->first();

	        return $my_shift;
		}

		public static function getLocation($date, $user_id = ''){
			if($user_id == '')
				$user_id = Auth::user()->id;
			
			$user_location = \App\UserLocation::whereUserId($user_id)->where('from_date','<=',$date)->where('to_date','>=',$date)->first();

			if(!$user_location)
				$user_location = \App\UserLocation::whereUserId($user_id)->where('from_date','<=',$date)->whereNull('to_date')->orderBy('from_date','desc')->first();

			if($user_location)
				return $user_location->Location->name;
			else
				return;
		}

		public static function createLineTreeView($array, $currentParent = 1, $currLevel = 0, $prevLevel = -1) {
			foreach ($array as $categoryId => $category) {
			if ($currentParent == $category['parent_id']) {                       
			    if ($currLevel > $prevLevel) echo " <ul class='tree'> "; 
			    if ($currLevel == $prevLevel) echo " </li> ";
			    
			    	echo '<li>'.$category['name'];

			    if ($currLevel > $prevLevel) { $prevLevel = $currLevel; }
			    $currLevel++; 
			    Helper::createLineTreeView ($array, $categoryId, $currLevel, $prevLevel);
			    $currLevel--;               
			    }   
			}
			if ($currLevel == $prevLevel) echo " </li>  </ul> ";
		}

		public static function getChilds($array, $currentParent = 1, $id = 0, $currLevel = 0, $prevLevel = -1) {
			STATIC $child = array();
			foreach ($array as $categoryId => $category) {
			if ($currentParent == $category['parent_id']) {  
				if ($currLevel > $prevLevel){} 
				if ($currLevel == $prevLevel){}
				if($id == 0)
					$child[$categoryId] = $category['name'];
				else
					$child[] = $categoryId;
			    if ($currLevel > $prevLevel) { $prevLevel = $currLevel; }
			    $currLevel++; 
			    Helper::getChilds($array, $categoryId, $id, $currLevel, $prevLevel);
			    $currLevel--;               
			    }   
			}
			if ($currLevel == $prevLevel){}
			return $child;
		}

		public static function childLocation($location_id = '', $id = 0){

            $tree = array();
      		$locations = \App\Location::whereNotNull('top_location_id')->get();
            foreach($locations as $location){
                $tree[$location->id] = array(
                    'parent_id' => $location->top_location_id,
                    'name' => $location->name
                );
            }
            return Helper::getChilds($tree,$location_id,$id);
		}

		public static function childDesignation($designation_id = '', $id = 0){
			if($designation_id == '')
				$designation_id = Auth::user()->designation_id;

			if(!config('config.subordinate')){
				return ($id) ? Designation::whereTopDesignationId($designation_id)->get()->pluck('id')->all() :
				Designation::whereTopDesignationId($designation_id)->get()->pluck('full_designation','id')->all();
			}

            $tree = array();
      		$designations = Designation::whereNotNull('top_designation_id')->get();
            foreach($designations as $designation){
                $tree[$designation->id] = array(
                    'parent_id' => $designation->top_designation_id,
                    'name' => $designation->full_designation
                );
            }
            return Helper::getChilds($tree,$designation_id,$id);
		}

		public static function getParent($designation_id){
        	$designations = \App\Designation::all()->pluck('top_designation_id','id')->all();
        	return Helper::getParentDesignation($designation_id,$designations);
		}

		public static function getParentDesignation($designation_id, $data, $parents=array()) {
            $parent_id = isset($data[$designation_id]) ? $data[$designation_id] : null;
            if ($parent_id != null) {
            	$parents[] = $parent_id;
                return Helper::getParentDesignation($parent_id, $data, $parents);
            }
            return $parents;
        }

		public static function isChild($child_designation_id,$parent_designation_id = ''){
			if($parent_designation_id == '')
				$parent_designation_id = Auth::user()->designation_id;

			$childs = Helper::childDesignation($parent_designation_id, 1);
			if(in_array($child_designation_id,$childs))
				return true;
			else
				return false;
		}

		public static function toWord($word){
			$word = str_replace('_', ' ', $word);
			$word = str_replace('-', ' ', $word);
			$word = ucwords($word);
			return $word;
		}

		public static function activityShow(){
			$side = ['left','right'];
			$index=array_rand($side);
			echo $side[$index];
		}

		public static function verifyCsrf($token){
			if($token == Session::token())
				return 1;
			else
				return 0;
		}

		public static function getCustomFields($form, $custom_field_values = array()){
			
			$custom_fields = CustomField::whereForm($form)->get();
			
			foreach($custom_fields as $custom_field){
			  
			  $c_values = (array_key_exists($custom_field->name, $custom_field_values)) ? $custom_field_values[$custom_field->name] : '';
			  $options = explode(',',$custom_field->options);
			  
			  $required = '';
			  
			  // if($custom_field->is_required)
			  // 	$required = 'required';
		      
		      echo '<div class="form-group">';
			  echo '<label for="'.$custom_field->name.'">'.$custom_field->title.'</label>';
			  
			  if($custom_field->type == 'select'){
			  	echo '<select class="form-control input-xlarge select2me" placeholder="'.trans('messages.select_one').'" id="'.$custom_field->name.'" name="'.$custom_field->name.'"'.$required.'>
			  	<option value=""></option>';
			  	foreach($options as $option){
			  		if($option == $c_values)
			  			echo '<option value="'.$option.'" selected>'.ucfirst($option).'</option>';
			  		else
			  			echo '<option value="'.$option.'">'.ucfirst($option).'</option>';
			  	}
			  	echo '</select>';
			  }
			  elseif($custom_field->type == 'radio'){
			  	echo '<div>
					<div class="radio">';
					foreach($options as $option){
						if($option == $c_values)
							$checked = "checked";
						else
							$checked = "";
						echo '<label><input type="radio" name="'.$custom_field->name.'" id="'.$custom_field->name.'" value="'.$option.'" '.$required.' '.$checked.' > '.ucfirst($option).'</label> ';
					}
				echo '</div>
				</div>';
			  }
			  elseif($custom_field->type == 'checkbox'){
			  	echo '<div>
					<div class="checkbox">';
					foreach($options as $option){
						if(in_array($option,explode(',',$c_values)))
							$checked = "checked";
						else
							$checked = "";
						echo '<label><input type="checkbox" name="'.$custom_field->name.'[]" id="'.$custom_field->name.'" value="'.$option.'" '.$checked.' '.$required.'> '.ucfirst($option).'</label> ';
					}
				echo '</div>
				</div>';
			  }
			  elseif($custom_field->type == 'textarea')
			   echo '<textarea class="form-control" data-limit="'.config('config.textarea_limit').'" placeholder="'.$custom_field->title.'" name="'.$custom_field->name.'" cols="30" rows="3" id="'.$custom_field->name.'"'.$required.' data-show-counter=1 data-autoresize=1>'.$c_values.'</textarea><span class="countdown"></span>';
			  else
			  	echo '<input class="form-control '.(($custom_field->type == 'date') ? 'datepicker' : '').'" value="'.$c_values.'" placeholder="'.$custom_field->title.'" name="'.$custom_field->name.'" type="text" value="" id="'.$custom_field->name.'"'.$required.' '.(($custom_field->type == 'date') ? 'readonly' : '').'>';
			  echo '</div>';
			}
		}

		public static function putCustomHeads($form, $col_heads){
			$custom_fields = CustomField::whereForm($form)->get();
			foreach($custom_fields as $custom_field)
				array_push($col_heads, $custom_field->title);
			return $col_heads;
		}

		public static function validateCustomField($form,$request){
	        $custom_validation = array();
			$custom_fields = CustomField::whereForm($form)->get();
			$friendly_names = array();
			foreach($custom_fields as $custom_field){
				if($custom_field->is_required){
					$custom_validation[$custom_field->name] = 'required'.(($custom_field->type == 'date') ? '|date' : '').(($custom_field->type == 'number') ? '|numeric' : '').(($custom_field->type == 'email') ? '|email' : '').(($custom_field->type == 'url') ? '|url' : '');
					$friendly_names[$custom_field->name] = $custom_field->title;
				}
	       }

	       $validation = Validator::make($request->all(),$custom_validation);
	       $validation->setAttributeNames($friendly_names);
	       return $validation;
		}

		public static function fetchCustomValues($form){
			$rows = DB::table('custom_fields')
        	->join('custom_field_values','custom_field_values.field_id','=','custom_fields.id')
			->where('form','=',$form)
			->select(DB::raw('unique_id,field_id,value,type'))
			->get();
			$values = array();
			foreach($rows as $row){
				$field_values = [];
				$value = '';
				if($row->type == 'checkbox'){
					$field_values = explode(',',$row->value);
					$value .= '<ol>';
					foreach($field_values as $fv)
						$value .= '<li>'.Helper::toWord($fv).'</li>';
					$value .= '</ol>';
				} else
				$value = Helper::toWord($row->value);

				$values[$row->unique_id][$row->field_id] = $value;
			}
			return $values;
		}

		public static function getCustomFieldValues($form,$id){
			return DB::table('custom_fields')
			->join('custom_field_values','custom_field_values.field_id','=','custom_fields.id')
			->where('form','=',$form)
			->where('unique_id','=',$id)
			->pluck('value','name');
		}

		public static function getCustomColId($form){
			return CustomField::whereForm($form)->pluck('id')->all();
		}

		public static function storeCustomField($form, $id, $request){
			$custom_fields = CustomField::whereForm($form)->get();
			foreach($custom_fields as $custom_field){
				$custom_field_value = new CustomFieldValue;
				$value = $request[$custom_field->name];
				if(is_array($value))
					$value = implode(',',$value);
				$custom_field_value->value = $value;
				$custom_field_value->field_id = $custom_field->id;
				$custom_field_value->unique_id = $id;
				$custom_field_value->save();
			}
		}

		public static function updateCustomField($form, $id, $request){
			$custom_fields = CustomField::whereForm($form)->get();
			foreach($custom_fields as $custom_field){
				$value = array_key_exists($custom_field->name, $request) ? $request[$custom_field->name] : '';

				if(is_array($value))
					$value = implode(',',$value);

				$custom = DB::table('custom_fields')
					->join('custom_field_values','custom_field_values.field_id','=','custom_fields.id')
					->where('form','=',$form)
					->where('name','=',$custom_field->name)
					->where('unique_id','=',$id)
					->select(DB::raw('custom_field_values.id'))
					->first();

				if($custom)
					$custom_field_value = CustomFieldValue::find($custom->id);
				else
					$custom_field_value = new CustomFieldValue;
				$custom_field_value->value = $value;
				$custom_field_value->field_id = $custom_field->id;
				$custom_field_value->unique_id = $id;
				$custom_field_value->save();
			}
		}

		public static function deleteCustomField($form, $id){
			$data = DB::table('custom_field_values')
				->join('custom_fields','custom_fields.id','=','custom_field_values.field_id')
				->where('form','=',$form)
				->where('unique_id','=',$id)
				->delete();
		}

		public static function activityColorShow(){
			$color = ['warning','danger','success','info','primary',''];
			$index=array_rand($color);
			echo $color[$index];
		}

		public static function activityTaskProgressColor($task_progress){
			if($task_progress <= 20)
				return 'danger';
			elseif($task_progress>20  && $task_progress <=50)
				return 'warning';
			elseif($task_progress>50  && $task_progress <=75)
				return 'info';
			else
				return 'success';
		}

		public static function storageColor($used){
			if($used <= 20)
				return 'info';
			elseif($used>20  && $used <=50)
				return 'success';
			elseif($used>50  && $used <=75)
				return 'warning';
			else
				return 'danger';
		}

		public static function getYears($start_year=1980,$end_year=2020){
			for($i=$start_year;$i<=$end_year;$i++)
        	$years[$i] = $i; 
        	return $years;
		}

		public static function getEnumValues($table, $column)
		{
		  $type = DB::select( DB::raw("SHOW COLUMNS FROM $table WHERE Field = '$column'") )[0]->Type;
		  preg_match('/^enum\((.*)\)$/', $type, $matches);
		  $enum = array();
		  foreach( explode(',', $matches[1]) as $value )
		  {
		    $v = trim( $value, "'" );
		    $enum = array_add($enum, $v, ucwords($v));
		  }
		  return $enum;
		}

		public static function writeResult($col_data){
			$datatable['aaData'] = $col_data;
			$fp = fopen('data.txt', 'w');
			fwrite($fp, json_encode($datatable));
			fclose($fp);
		}

		public static function getAvatar($id, $size = null){
			$user = \App\User::find($id);
			$profile = $user->Profile;
			$name = ($user->full_name) ? : $user->username;
			$tooltip = $name.' : '.$user->Designation->full_designation;
			if($size == null)
				$size = '40px';
			if(isset($profile->photo))
				return '<img src="/'.config('constants.upload_path.profile_image').$profile->photo.'" class="media-object img-circle profile-avatar" style="width:'.$size.';float:left;" alt="User avatar" data-toggle="tooltip" title="'.$tooltip.'">';
			else 
				return '<div class="textAvatar profile-avatar" data-toggle="tooltip" title="'.$tooltip.'">'.$name.'</div>';
		}
		
		public static function br2nl($string)
		{
		    return preg_replace('/\<br(\s*)?\/?\>/i', "", $string);
		}

		public static function mynl2br($string)
		{
		    $string=str_replace("'", "&#039;", $string);
		    $string=nl2br($string);
		    return($string);
		}

		public static function showDuration($second){
			$hour = floor($second / 3600);
			$minute = floor(($second / 60) % 60);
			return str_pad($hour, 2, '0', STR_PAD_LEFT).':'.str_pad($minute, 2, '0', STR_PAD_LEFT);
		}

		public static function getAttendanceTag($type){
			if($type == 'overtime')
				return '<span class="badge badge-success" data-toggle="tooltip" title="'.trans('messages.overtime').'">O</span> ';
			elseif($type == 'late')
				return '<span class="badge badge-danger" data-toggle="tooltip" title="'.trans('messages.late').'">L</span> ';
			elseif($type == 'early')
				return '<span class="badge badge-warning" data-toggle="tooltip" title="'.trans('messages.early').'">E</span> ';
			else
				return;
		}

		public static function showDetailDuration($second){
			// $day = floor($second / 86400);
			$hour = floor($second / 3600);
			$minute = floor(($second / 60) % 60);
			$duration = (isset($day) ? $day.' day ' : '').(($hour) ? $hour.' hour ' : '').(($minute) ? $minute.' minute ' : '');
			return ($duration) ? : '-';
			// return str_pad($hour, 2, '0', STR_PAD_LEFT).':'.str_pad($minute, 2, '0', STR_PAD_LEFT);
		}

		public static function inWords($number){
		    $hyphen      = '-';
		    $conjunction = ' and ';
		    $separator   = ', ';
		    $negative    = 'negative ';
		    $decimal     = ' point ';
		    $dictionary  = array(
		        0                   => 'zero',
		        1                   => 'one',
		        2                   => 'two',
		        3                   => 'three',
		        4                   => 'four',
		        5                   => 'five',
		        6                   => 'six',
		        7                   => 'seven',
		        8                   => 'eight',
		        9                   => 'nine',
		        10                  => 'ten',
		        11                  => 'eleven',
		        12                  => 'twelve',
		        13                  => 'thirteen',
		        14                  => 'fourteen',
		        15                  => 'fifteen',
		        16                  => 'sixteen',
		        17                  => 'seventeen',
		        18                  => 'eighteen',
		        19                  => 'nineteen',
		        20                  => 'twenty',
		        30                  => 'thirty',
		        40                  => 'fourty',
		        50                  => 'fifty',
		        60                  => 'sixty',
		        70                  => 'seventy',
		        80                  => 'eighty',
		        90                  => 'ninety',
		        100                 => 'hundred',
		        1000                => 'thousand',
		        1000000             => 'million',
		        1000000000          => 'billion',
		        1000000000000       => 'trillion',
		        1000000000000000    => 'quadrillion',
		        1000000000000000000 => 'quintillion'
		    );
		    
		    if (!is_numeric($number)) {
		        return false;
		    }
		    
		    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		        // overflow
		        trigger_error(
		            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
		            E_USER_WARNING
		        );
		        return false;
		    }

		    if ($number < 0) {
		        return $negative . Helper::inWords(abs($number));
		    }
		    
		    $string = $fraction = null;
		    
		    if (strpos($number, '.') !== false) {
		        list($number, $fraction) = explode('.', $number);
		    }
		    
		    switch (true) {
		        case $number < 21:
		            $string = $dictionary[$number];
		            break;
		        case $number < 100:
		            $tens   = ((int) ($number / 10)) * 10;
		            $units  = $number % 10;
		            $string = $dictionary[$tens];
		            if ($units) {
		                $string .= $hyphen . $dictionary[$units];
		            }
		            break;
		        case $number < 1000:
		            $hundreds  = $number / 100;
		            $remainder = $number % 100;
		            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
		            if ($remainder) {
		                $string .= $conjunction . Helper::inWords($remainder);
		            }
		            break;
		        default:
		            $baseUnit = pow(1000, floor(log($number, 1000)));
		            $numBaseUnits = (int) ($number / $baseUnit);
		            $remainder = $number % $baseUnit;
		            $string = Helper::inWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
		            if ($remainder) {
		                $string .= $remainder < 100 ? $conjunction : $separator;
		                $string .= Helper::inWords($remainder);
		            }
		            break;
		    }
		    
		    if (null !== $fraction && is_numeric($fraction)) {
		        $string .= $decimal;
		        $words = array();
		        foreach (str_split((string) $fraction) as $number) {
		            $words[] = $dictionary[$number];
		        }
		        $string .= implode(' ', $words);
		    }
		    
		    return $string;
		}
	}
?>