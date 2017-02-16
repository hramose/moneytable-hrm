<?php return array (
    'welcome_email' => array(
        'path' => 'emails/default/welcome',
        'default_subject' => 'Welcome Email',
        'fields' => 'NAME, USERNAME, EMAIL, PASSWORD, DESIGNATION, DEPARTMENT'
      ),
    'payslip_email' => array(
        'path' => 'emails/default/payslip',
        'default_subject' => 'Payslip',
        'fields' => 'NAME, USERNAME, EMAIL, DESIGNATION, DEPARTMENT, FROM_DATE, TO_DATE, DATE_GENERATED'
      ),
);