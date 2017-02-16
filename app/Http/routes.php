<?php
Route::get('/', 'Auth\AuthController@getLogin');
Route::get('/whats-new',function(){
	return view('whats_new');
});
Route::get('/apply', 'JobApplicationController@apply');
Route::post('/sidebar', 'DashboardController@sidebar');
Route::get('/test','DashboardController@test');
Route::post('/job-application', array('as' => 'job-application.store','uses' => 'JobApplicationController@store'));

Route::post('/clock/in', array('as' => 'clock.in', 'uses' => 'ClockController@in'));
Route::post('/clock/out', array('as' => 'clock.out', 'uses' => 'ClockController@out'));
	
Route::group(['middleware' => 'guest'], function () {
	Route::get('/login', 'Auth\AuthController@getLogin');
	Route::post('/login', 'Auth\AuthController@postLogin');
	Route::get('/email/reset','Auth\AuthController@getReset');
	Route::post('/email/reset','Auth\AuthController@postReset');
	Route::get('/password/email', 'Auth\PasswordController@getEmail');
	Route::post('/password/email', 'Auth\PasswordController@postEmail');
	Route::get('/password/reset/{token}', 'Auth\PasswordController@getReset');
	Route::post('/password/reset', 'Auth\PasswordController@postReset');
	Route::get('/verify-purchase', 'AccountController@verifyPurchase');
	Route::post('/verify-purchase', 'AccountController@postVerifyPurchase');
	Route::resource('/install', 'AccountController',['only' => ['index', 'store']]);
	Route::get('/update','AccountController@updateApp');
	Route::post('/update',array('as' => 'update-app','uses' => 'AccountController@postUpdateApp'));
});

Route::group(['middleware' => ['auth','license']], function () {
	Route::get('/account-invalid','EmployeeController@accountInvalid');
	Route::get('/logout', 'Auth\AuthController@getLogout');
});

Route::group(['middleware' => ['auth','license','account_valid']], function () {

	Route::get('/release-license','AccountController@releaseLicense');
	Route::get('/dashboard','DashboardController@index');
	Route::post('/recent-activity','DashboardController@recentActivity');
	Route::post('/setup-complete',array('as' => 'setup-complete','uses' => 'ConfigController@setupComplete'));

	Route::model('todo','\App\Todo');
	Route::resource('/todo', 'TodoController'); 

	Route::get('/change-password', 'EmployeeController@changePassword');
	Route::post('/change-password',array('as'=>'change-password','uses' =>'EmployeeController@doChangePassword'));
	
	Route::group(['middleware' => ['permission:manage_email_log']], function () {
		Route::model('email','\App\Email');
		Route::post('/email/lists','EmailController@lists');
		Route::resource('/email', 'EmailController',['only' => ['index','show']]); 
	});

	Route::group(['middleware' => ['permission:manage_backup']], function () {
		Route::model('backup','\App\Backup');
		Route::post('/backup/lists','BackupController@lists');
		Route::resource('/backup', 'BackupController',['only' => ['index','show','store','destroy']]); 
	});

	Route::group(['middleware' => ['config_accessible']], function () {
		Route::get('/configuration', 'ConfigController@index'); 
		Route::get('/permission', 'ConfigController@permission'); 
		Route::get('/check-update','AccountController@checkUpdate');
		Route::post('/configuration', array('as' => 'configuration.store','uses' => 'ConfigController@store')); 
		Route::post('/sms-store', array('as' => 'configuration.sms-store','uses' => 'ConfigController@smsStore')); 
		Route::post('/mail-store', array('as' => 'configuration.mail-store','uses' => 'ConfigController@mailStore')); 
		Route::post('/logo-store', array('as' => 'configuration.logo-store','uses' => 'ConfigController@logoStore')); 
		Route::post('/menu-store', array('as' => 'configuration.menu-store','uses' => 'ConfigController@menuStore')); 
		Route::post('/save-permission',array('as' => 'configuration.save-permission','uses' => 'ConfigController@savePermission'));
		Route::post('/api-store',array('as' => 'configuration.api','uses' => 'ConfigController@api'));
		
		Route::model('role','\App\Role');
		Route::post('/role/lists','RoleController@lists');
		Route::resource('/role', 'RoleController'); 

		Route::model('office_shift','\App\OfficeShift');
		Route::post('/office-shift/lists','OfficeShiftController@lists');
		Route::resource('/office-shift', 'OfficeShiftController'); 
		Route::get('/office-shift/{id}/default','OfficeShiftController@makeDefault');

		Route::model('contract_type','\App\ContractType');
		Route::post('/contract-type/lists','ContractTypeController@lists');
		Route::resource('/contract-type', 'ContractTypeController'); 

		Route::model('award_type','\App\AwardType');
		Route::post('/award-type/lists','AwardTypeController@lists');
		Route::resource('/award-type', 'AwardTypeController'); 

		Route::model('ip','\App\Ip');
		Route::post('/ip/lists','IpController@lists');
		Route::resource('/ip', 'IpController'); 

		Route::model('leave_type','\App\LeaveType');
		Route::post('/leave-type/lists','LeaveTypeController@lists');
		Route::resource('/leave-type', 'LeaveTypeController'); 
		
		Route::model('document_type','\App\DocumentType');
		Route::post('/document-type/lists','DocumentTypeController@lists');
		Route::resource('/document-type', 'DocumentTypeController'); 
		
		Route::model('salary_type','\App\SalaryType');
		Route::post('/salary-type/lists','SalaryTypeController@lists');
		Route::resource('/salary-type', 'SalaryTypeController'); 
		
		Route::model('expense_head','\App\ExpenseHead');
		Route::post('/expense-head/lists','ExpenseHeadController@lists');
		Route::resource('/expense-head', 'ExpenseHeadController'); 
	});

	Route::model('department','\App\Department');
	Route::post('/department/lists','DepartmentController@lists');
	Route::resource('/department', 'DepartmentController'); 
	
	Route::model('designation','\App\Designation');
	Route::post('/designation/lists','DesignationController@lists');
	Route::resource('/designation', 'DesignationController'); 
	
	Route::group(['middleware' => ['permission:manage_custom_field']], function () {
		Route::model('custom_field','\App\CustomField');
		Route::post('/custom-field/lists','CustomFieldController@lists');
		Route::resource('/custom-field', 'CustomFieldController'); 
	});
	
	Route::group(['middleware' => ['permission:manage_template']], function () {
		Route::model('template','\App\Template');
		Route::post('/template/lists','TemplateController@lists');
		Route::resource('/template', 'TemplateController'); 
	});

	Route::group(['middleware' => ['permission:manage_language']], function () {
		Route::post('/language/lists','LanguageController@lists');
		Route::resource('/language', 'LanguageController'); 
		Route::post('/language/addWords',array('as'=>'language.add-words','uses'=>'LanguageController@addWords'));
		Route::patch('/language/plugin/{locale}',array('as'=>'language.plugin','uses'=>'LanguageController@plugin'));
		Route::patch('/language/updateTranslation/{id}', ['as' => 'language.update-translation','uses' => 'LanguageController@updateTranslation']);
	});

	Route::get('/set-language/{locale}','LanguageController@setLanguage');

	Route::get('/employee/create', 'Auth\AuthController@getRegister');
	Route::get('/profile','EmployeeController@profile');
	Route::get('/profile/{id}','EmployeeController@profile');
	Route::post('/auth/register',array('as' => 'auth.register','uses' => 'Auth\AuthController@postRegister'));
	Route::model('employee','\App\User');
	Route::post('/employee/lists','EmployeeController@lists');
	Route::post('/employee/email/{id}',array('as' => 'employee.email', 'uses' => 'EmployeeController@email'));
	Route::resource('/employee', 'EmployeeController',['except' => ['create', 'store']]);
	Route::patch('/users/profile/{id}',['as' => 'employee.profile-update', 'uses' => 'EmployeeController@profileUpdate']);
	Route::patch('/users/sms/{id}', ['as' => 'employee.send-employee-SMS', 'uses' => 'SMSController@sendEmployeeSMS']);
	Route::post('/template/content','TemplateController@content');
	
	Route::model('contact','\App\Contact');
	Route::post('/contact/lists','ContactController@lists');
	Route::resource('/contact', 'ContactController'); 
	Route::post('/contact/{id}',array('uses' => 'ContactController@store','as' => 'contact.store'));

	Route::model('bank_account','\App\BankAccount');
	Route::post('/bank-account/lists','BankAccountController@lists');
	Route::resource('/bank-account', 'BankAccountController'); 
	Route::post('/bank-account/{id}',array('uses' => 'BankAccountController@store','as' => 'bank-account.store'));
	
	Route::model('document','\App\Document');
	Route::post('/document/lists',array('as' => 'document.lists','uses' => 'DocumentController@lists'));
	Route::post('/document/{id}',array('uses' => 'DocumentController@store','as' => 'document.store'));
	Route::resource('/document', 'DocumentController',['only' => ['destroy']]); 
	Route::get('/document/download/{id}','DocumentController@download');
	Route::get('/documents','DocumentController@filter');
	Route::post('/filter-documents',['as' => 'document.filter','uses' => 'DocumentController@filter']);
	Route::get('/document/status/{id}','DocumentController@changeStatus');
	
	Route::post('/salary/lists','SalaryController@lists');
	Route::post('/salary/{id}',array('uses' => 'SalaryController@store','as' => 'salary.store'));
	Route::get('/salary/{id}/edit','SalaryController@edit');
	Route::patch('/salary/{id}/edit',array('uses' => 'SalaryController@update','as' => 'salary.update'));
	Route::delete('/salary/{id}',array('uses' => 'SalaryController@destroy','as' => 'salary.destroy'));

	Route::post('/user-shift/lists','UserShiftController@lists');
	Route::post('/user-leave/lists','UserLeaveController@lists');
	Route::post('/user-leave/{id}',array('uses' => 'UserLeaveController@store','as' => 'user-leave.store'));
	Route::resource('/user-leave', 'UserLeaveController',['only' => ['edit','update','destroy']]); 
	Route::model('user_shift','\App\UserShift');
	Route::post('/user-shift/{id}',array('uses' => 'UserShiftController@store','as' => 'user-shift.store'));
	Route::resource('/user-shift', 'UserShiftController',['except' => ['store']]); 

	Route::model('contract','\App\Contract');
	Route::post('/contract/lists','ContractController@lists');
	Route::resource('/contract', 'ContractController'); 
	Route::post('/contract/{id}',array('uses' => 'ContractController@store','as' => 'contract.store'));
	Route::post('/get-user-leave','ProfileController@getLeave');
	
	Route::patch('/change-employee-password/{id}',array('as'=>'change-employee-password','uses' =>'EmployeeController@doChangeEmployeePassword'));
	
	Route::model('holiday','\App\Holiday');
	Route::post('/holiday/lists','HolidayController@lists');
	Route::resource('/holiday', 'HolidayController'); 
	
	Route::model('award','\App\Award');
	Route::post('/award/lists','AwardController@lists');
	Route::resource('/award', 'AwardController'); 

	Route::model('expense','\App\Expense');
	Route::post('/expense/lists','ExpenseController@lists');
	Route::resource('/expense', 'ExpenseController'); 
	Route::get('/expense/{id}/download','ExpenseController@download');
	Route::get('/expense/{id}/update-status','ExpenseController@editStatus');
	Route::patch('/expense/{id}/update-status',array('as' => 'expense.update-status','uses' => 'ExpenseController@updateStatus'));

	Route::model('announcement','\App\Announcement');
	Route::post('/announcement/lists','AnnouncementController@lists');
	Route::resource('/announcement', 'AnnouncementController'); 

	Route::model('task','\App\Task');
	Route::post('/task/lists','TaskController@lists');
	Route::resource('/task', 'TaskController'); 
	Route::post('/update-task-progress/{id}', ['as' => 'task.update-task-progress', 'uses' => 'TaskController@updateTaskProgress']);
	Route::post('/assign-task/{id}', ['as' => 'task.assign-task', 'uses' => 'TaskController@assignTask']);
	Route::post('/task-comment/{id}',array('uses' => 'TaskCommentController@store','as' => 'task-comment.store'));
	Route::delete('/task-comment/{id}',array('uses' => 'TaskCommentController@destroy','as' => 'task-comment.destroy'));
	Route::post('/task-note/{id}',array('uses' => 'TaskNoteController@store','as' => 'task-note.store'));
	
	Route::get('/task-attachment/{id}/lists',['uses' => 'TaskAttachmentController@lists','middleware' => 'ajax']);
	Route::post('/task-attachment/{id}',array('uses' => 'TaskAttachmentController@store','as' => 'task-attachment.store'));
	Route::delete('/task-attachment/{id}',array('uses' => 'TaskAttachmentController@destroy','as' => 'task-attachment.destroy'));
	Route::get('/task-attachment/download/{id}','TaskAttachmentController@download');

	Route::model('ticket','\App\Ticket');
	Route::post('/ticket/lists','TicketController@lists');
	Route::resource('/ticket', 'TicketController'); 
	Route::post('/update-ticket-status/{id}', ['as' => 'ticket.update-ticket-status', 'uses' => 'TicketController@updateTicketStatus']);
	Route::post('/assign-ticket/{id}', ['as' => 'ticket.assign-ticket', 'uses' => 'TicketController@assignTicket']);
	Route::post('/ticket-comment/{id}',array('uses' => 'TicketCommentController@store','as' => 'ticket-comment.store'));
	Route::delete('/ticket-comment/{id}',array('uses' => 'TicketCommentController@destroy','as' => 'ticket-comment.destroy'));
	Route::post('/ticket-note/{id}',array('uses' => 'TicketNoteController@store','as' => 'ticket-note.store'));
	
	Route::get('/ticket-attachment/{id}/lists',['uses' => 'TicketAttachmentController@lists']);
	Route::post('/ticket-attachment/{id}',array('uses' => 'TicketAttachmentController@store','as' => 'ticket-attachment.store'));
	Route::delete('/ticket-attachment/{id}',array('uses' => 'TicketAttachmentController@destroy','as' => 'ticket-attachment.destroy'));
	Route::get('/ticket-attachment/download/{id}','TicketAttachmentController@download');
	
	Route::get('/sms', 'SMSController@index'); 
	Route::get('/sms/{type}', 'SMSController@index'); 
	Route::post('/sms', array('as'=>'sms.store','uses'=>'SMSController@store')); 

	Route::model('leave','\App\Leave');
	Route::post('/leave/lists','LeaveController@lists');
	Route::resource('/leave', 'LeaveController'); 
	Route::post('/update-leave-status/{id}', ['as' => 'leave.update-status', 'uses' => 'LeaveController@updateStatus']);

	Route::model('clock','\App\Clock');
	Route::post('/my-clock/lists','ClockController@lists');
	Route::resource('/clock', 'ClockController'); 

	Route::get('/attendance','ClockController@attendance');
	Route::post('/attendance',array('as'=>'clock.attendance','uses'=>'ClockController@postAttendance'));
	Route::post('/daily-attendance/lists','ClockController@listDailyAttendance');

	Route::get('/date-wise-attendance', 'ClockController@dateWiseAttendance');
	Route::post('/date-wise-attendance', array('as'=>'clock.date-wise-attendance','uses'=>'ClockController@postDateWiseAttendance'));
	Route::post('/date-wise-attendance/lists',array('uses' => 'ClockController@listDateWiseAttendance','as' => 'clock.list-date-wise-attendance'));

	Route::get('/date-wise-summary-attendance', 'ClockController@dateWiseSummaryAttendance');
	Route::post('/date-wise-summary-attendance', array('as'=>'clock.date-wise-summary-attendance','uses'=>'ClockController@postDateWiseSummaryAttendance'));
	Route::post('/date-wise-summary-attendance/lists','ClockController@listDateWiseSummaryAttendance');

	Route::post('/upload-attendance',array('as' => 'clock.upload-attendance','uses' => 'ClockController@uploadAttendance'));

	Route::get('/update-attendance','ClockController@updateAttendance');
	Route::post('/update-attendance',array('as' => 'clock.update-attendance','uses' => 'ClockController@updateAttendance'));
	Route::post('/clock/{user_id}/{date}',array('as' => 'clock.clock-update','uses' => 'ClockController@clock'));
	Route::post('/clock/{user_id}/{date}/{clock_id?}',array('as' => 'clock.clock-update','uses' => 'ClockController@clock'));

	Route::get('/shift-detail','ClockController@shift');
	Route::post('/shift-detail',array('as' => 'clock.shift','uses' => 'ClockController@postShift'));
	Route::post('/shift-detail/lists','ClockController@shiftDetailList');
	
	Route::post('/payroll/lists','PayrollController@lists');
	Route::post('/payroll/store',array('as' => 'payroll.store','uses' => 'PayrollController@store'));
	Route::get('/payroll/generate/{action}/{payroll_slip_id}','PayrollController@generate');
	Route::get('/payroll','PayrollController@index'); 
	Route::get('/payroll/create','PayrollController@create');
	Route::get('/payroll/{id}','PayrollController@show');
	Route::post('/payroll/create',array('as' => 'payroll.create','uses' => 'PayrollController@create'));
	Route::delete('/payroll/{id}',array('uses' => 'PayrollController@destroy', 'as' => 'payroll.destroy'));	
	Route::get('/payroll/{id}/edit','PayrollController@edit');
	Route::patch('/payroll/{id}/update',array('as' => 'payroll.update','uses' => 'PayrollController@update'));
	
	Route::post('/copy-template',array('as' => 'copy-template','uses' => 'MailController@copyTemplate'));
	Route::post('/mail',array('as' => 'mail.index','uses' => 'MailController@index'));

	Route::model('job','\App\Job');
	Route::post('/job/lists','JobController@lists');
	Route::resource('/job', 'JobController'); 
	Route::post('/job-application/lists','JobApplicationController@lists');
	Route::get('/job-application/{id}/resume','JobApplicationController@resume');
	Route::model('job_application','\App\JobApplication');
	Route::resource('/job-application', 'JobApplicationController',['except' => ['store']]); 
	Route::patch('/job-application/{id}/update-status',array('as' => 'job-application.update-status','uses' => 'JobApplicationController@updateStatus'));

	Route::group(['middleware' => ['permission:manage_message']], function () {
		Route::get('/message/compose', 'MessageController@compose'); 
		Route::post('/message/{type}/lists','MessageController@lists');
		Route::post('/message', ['as' => 'message.store', 'uses' => 'MessageController@store']);
		Route::get('/message/sent','MessageController@sent'); 
		Route::get('/message','MessageController@inbox'); 
		Route::get('/message/{id}/download','MessageController@download');
		Route::get('/message/view/{id}/{token}', array('as' => 'message.view', 'uses' => 'MessageController@view'));
		Route::get('/message/{id}/delete/{token}', array('as' => 'message.delete', 'uses' => 'MessageController@delete'));
	});
});
	
Route::get('/api/list-employee/{auth_token}','ApiController@listEmployee');
Route::get('/api/clock-in/{auth_token?}/{emp_code?}','ApiController@clockIn');
Route::get('/api/clock-out/{auth_token?}/{emp_code?}','ApiController@clockOut');
