<?php
namespace App\Http\Controllers;
use App\Classes\Helper;
use Auth;
use File;

Class ResetController extends Controller{
    use BasicController;

    public function test(){
    	$languages = config('language');

    	$message = array();
    	// foreach($languages as $key => $language)
    	// 	$message[$key] = $language['value'];

  //   	$locale = 'hi';
  //       $translations = File::getRequire(base_path().'/resources/lang/'.$locale.'/messages.php');
  //       return count($translations);

  //       foreach($translations as $key => $translation)
  //           if(!array_key_exists($key, $languages))
  //               unset($translations[$key]);

		// $filename = base_path().'/resources/lang/'.$locale.'/messages.php';
		// File::put($filename,var_export($translations, true));
		// File::prepend($filename,'<?php return ');
		// File::append($filename, ';');

		return 1;

    }
}