<?php

function delete_form($param, $name = '', $data_table_row_delete = 0, $label = ''){
	$form = Form::open(['method' => 'DELETE','route' => $param,'class' => 'form-inline','id' => $name.'_'.$param[1],'data-table-row-delete' => $data_table_row_delete]);
	$form .= HTML::decode(Form::button('<i class="fa fa-trash-o"></i> '.$label,['data-toggle' => 'tooltip','title' => trans('messages.delete'),'class' => 'btn btn-danger btn-xs','data-submit-confirm-text' => 'Yes','type' => 'submit']));
	return $form .= Form::close();
}

function checkDBConnection(){
	$link = @mysqli_connect(config('database.connections.primary.host'),
		config('database.connections.primary.username'),
		config('database.connections.primary.password'));

	if($link)
		return mysqli_select_db($link,config('database.connections.primary.database'));
	else
		return false;
}

function datepickerDefaultDate($date = null){
    $date = ($date) ? : date('Y-m-d');
    return array('year' => explode('-',$date)[0],'month' => explode('-',$date)[1],'day' => explode('-',$date)[2]);
}

function randomString($length,$type = 'token'){
    if($type == 'password')
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    elseif($type == 'username')
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    else
         $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $token = substr( str_shuffle( $chars ), 0, $length );
    return $token;
}

function menuAvailable($menus,$menu){
    $menu_item = $menus->whereLoose('name',$menu)->first();
    return $menu_item->visible;
}

function menuAttr($menus,$menu){
    $menu_item = $menus->whereLoose('name',$menu)->first();

    if($menu_item)
        return 'data-position="'.(($menu_item->order == null) ? $menu_item->id : $menu_item->order).'" data-visible="'.$menu_item->visible.'"';
    else
        return '';
}

function backupDatabase(){
    try {
        $db_export = \App\Classes\Shuttle_Dumper::create(array(
            'host' => config('database.connections.primary.host'),
            'username' => config('database.connections.primary.username'),
            'password' => config('database.connections.primary.password'),
            'db_name' => config('database.connections.primary.database'),
        ));
        $filename = 'backup_'.date('Y_m_d_H_i_s').'.sql.gz';
        $db_export->dump($filename);
        return ['status' => 'success','filename' => $filename];
    } catch(\App\Classes\Shuttle_Exception $e) {
        $message = $e->getMessage();
        return ['status' => 'error'];
    }
}

function withEmpty($selectList, $emptyLabel='') {
	return array(''=>$emptyLabel) + $selectList->toArray();
}

function getMode(){
	return config('code.mode');
}

function currenyDecimalValue(){
	return (config('config.currency_decimal')) ? '.'.str_pad(1, config('config.currency_decimal'), '0', STR_PAD_LEFT) : 2;
}

function defaultDB(){
    config([
        'database.connections.primary.host' => config('db.hostname'),
        'database.connections.primary.username' => config('db.username'),
        'database.connections.primary.password' => config('db.password'),
        'database.connections.primary.database' => config('db.database')
        ]);
}

function installPurchase($purchase_code = '',$envato_username = '',$email = ''){
		$data['status'] = 'success';
    return $data;
}

function complete($purchase_code = ''){
	$data['status'] = 'success';
    return $data;
}

function verifyPurchase($purchase_code = ''){
	$data['status'] = 'success';
	return $data;
}

function releaseLicense(){
	$purchase_code = config('code.purchase_code');
    $url = config('constants.path.verifier')."license-release";
    $postData = array(
        'purchase_code' => $purchase_code,
        'install_url' => \Request::url()
    );
	$ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData
    ));
    $data = curl_exec($ch);
    return json_decode($data,true);
}

function getUpdate(){
    $url = config('constants.path.verifier')."update";
    $postData = array(
    	'purchase_code' => config('code.purchase_code'),
        'build' => config('code.build'),
        'install_url' => \Request::url()
    );
	$ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData
    ));
    $data = curl_exec($ch);
    return $data;
    return json_decode($data,true);
}


function write2Config($data,$file){
	$filename = base_path().'/config/'.$file.'.php';
	File::put($filename,var_export($data, true));
	File::prepend($filename,'<?php return ');
	File::append($filename, ';');
}

function is_connected()
{
    $connected = @fsockopen("www.google.com", 80);
    if ($connected){
        $is_conn = true;
        fclose($connected);
    }else{
        $is_conn = false;
    }
    return $is_conn;
}

function currency($amount){
	$currency_decimal_value = currenyDecimalValue();
	$amount = round($amount,(config('config.currency_decimal')) ? : '2');
	if(config('config.currency_position') == 'suffix')
		return round($amount,config('config.currency_decimal')).' '.config('config.default_currency_symbol');
	else
		return config('config.default_currency_symbol').' '.round($amount,config('config.currency_decimal'));
}

function showMessage() {
	if (Session::has('errors')) {

		$error = Session::get('errors')->First();
		echo "<div class='alert alert-danger alert-dismissable'>
                <i class='fa fa-ban'></i>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <b>$error</b>
            </div>";

	}
	elseif (Session::has('success')) {

		$success = Session::get('success');
		echo "<div class='alert alert-success alert-dismissable'>
                <i class='fa fa-check'></i>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <b>$success</b>
            </div>";
	}
}

function getDateDiff($date){
	$difference = date('z',strtotime($date)) - date('z') + 1;
	if($difference == 0)
		return trans('messages.today');
	elseif($difference == 1)
		return trans('messages.tomorrow');
	elseif($difference == -1)
		return trans('messages.yesterday');
	else
		return 0;
}

function showDateTime($time = ''){
	if($time == '')
		return;

	if(config('config.date_format') == 'dd MM YYYY')
		$format = 'd-M-Y';
	elseif(config('config.date_format') == 'mm dd YYYY')
		$format = 'm-d-Y';
	elseif(config('config.date_format') == 'MM dd YYYY')
		$format = 'M-d-Y';
	else
		$format = 'd-m-Y';

	if(config('config.time_format') == '24hrs')
		return date($format.',H:i',strtotime($time));
	else
		return date($format.',h:i a',strtotime($time));
}

function showTime($time = ''){
	if($time == '' || $time == null)
		return;
	if(config('config.time_format') == '24hrs')
		return date('H:i',strtotime($time));
	else
		return date('h:i a',strtotime($time));
}

function defaultRole(){
	if(Entrust::hasRole(DEFAULT_ROLE))
		return 1;
	else
		return 0;
}

function showDate($date = ''){
	if($date == '' || $date == null)
		return;

	if(config('config.date_format') == 'dd MM YYYY')
		$format = 'd-M-Y';
	elseif(config('config.date_format') == 'mm dd YYYY')
		$format = 'm-d-Y';
	elseif(config('config.date_format') == 'MM dd YYYY')
		$format = 'M-d-Y';
	else
		$format = 'd-m-Y';

	return date($format,strtotime($date));
}

function dateDiff($date1,$date2){
	if($date2 > $date1)
		return date_diff(date_create($date1),date_create($date2))->days + 1;
	else
		return date_diff(date_create($date2),date_create($date1))->days + 1;
}

function daySuffix($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return 'st';
            case 2: return 'nd';
            case 3: return 'rd';
        }
    }
    return 'th';
}
