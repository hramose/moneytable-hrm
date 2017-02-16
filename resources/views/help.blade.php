<div class="col-sm-{{ isset($col) ? $col : '4' }}">
	<div class="the-notes info">
	<h4>{!! trans('messages.help') !!}</h4>
	@if($section == 'department_create')
		Departments are divison of a large organization into parths with specific responsibility.
		For example, an organization can have separate account department, human resource department
		etc. You can create department here; every department should have a unique name. Once you create department, you can move to create designations.
	
	@elseif($section == 'department_edit')
		Here you can edit the department name & its description. Keep in mind that department name cannot be same as another department name.
		Change in department name will be reflected everywhere.
	
	@elseif($section == 'award_create')
		Here you can award employee with cash or gift prize. It maintains the record of date of award, month & year, type of award given and name of employee to whom award is given.
		An award can be given to multiple employee at a same time. To add new award type, you can move to setting->award type.

	@elseif($section == 'award_edit')
		Editing an award is easy as editing any other entity. You can update any of the given fields and it will be saved in database for further use.

	@elseif($section == 'designation_create')
		Designations are post at various department that can be allotted to an employee.
		For example, account department can have designation of Sr Account Manager, Account Manager etc.
		You can create designation here; every designation should have a unique name in a department. Once you create designation, you can move to create employee.

	@elseif($section == 'designation_edit')
		Here you can edit the designation name & its description. Keep in mind that designation name cannot be same as designation in another department.
		Change in designation name will be reflected everywhere.

	@elseif($section == 'employee_create')
		This module enables you to add unlimited staff in your organization. Once completed this form
		he/she will be able to login with given username & password. Email id mentioned here should be
		unique & must contain only alphabet or underscore. In case when password is lost, this email
		can be used to reset password. Username should also be unique. Password should be strong enough
		so that no one can guess it.

	@elseif($section == 'update_attendance')
		You can easily update attendance of any employee, even if he/she has not marked his/her attendance. You can left blank clock in or clock out if you only want to update one of the field. Please note that
		in every case clock in time is always less than clock out time.
	@endif
	</div>
</div>