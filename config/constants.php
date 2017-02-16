<?php
return [
    'default_title' => 'Employer Zone | Ultimate Human Resource Manager',
    'path' => [
        'country' => '/config/country.php',
        'timezone' => '/config/timezone.php',
        'language' => '/config/language.php',
        'lang' => '/config/lang.php',
        'verifier' => 'http://envato.wmlab.in/',
        'config' => '/config/config.php',
        'mail' => '/config/mail.php',
        'service' => '/config/services.php',
    ],
    'item_code' => 'EZ0303',
    'ignore_var' => array('_token','config_type','ajax_submit','domain'),
    'upload_path' => [
        'logo' => 'uploads/logo/',
        'attachments' => 'uploads/attachments/',
        'profile_image' => 'uploads/profile_image/',
        'backup' => 'uploads/backup/',
    ],
    'system_default' => [
        'application_name' => 'Employer Zone',
        'timezone_id' => '266',
        'default_currency' => 'Dollar',
        'default_currency_symbol' => '$',
        'default_language' => 'en',
        'direction' => 'ltr',
        'allowed_upload_file' => 'pdf,doc,docx,xls,xlsx,jpg,jpeg',
        'error_display' => 1,
        'currency_decimal' => '2',
        'currency_position' => 'prefix',
        'logo' => 'logo.png',
        'application_setup_info' => '1',
        'textarea_limit' => '300',
        'notification_position' => 'toast-bottom-right',
        'installation_path' => 1,
        'celebration_days' => 30
    ],
    'mail_default' => [
        'driver' => 'log',
        'from_name' => 'Mailer Employer Zone',
        'from_address' => 'mail@yourcompany.com'
    ],
    'default_role' => 'admin',
    'default_department' => 'System Administration',
    'default_designation' => 'System Administrator',
];