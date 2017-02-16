-- Activity Log Table --
rename table `activity_log` to `activities`;
alter table `activities` change `text` `activity` varchar(255) default null;
alter table `activities` change `ip_address` `ip` varchar(64) default null;
alter table `activities` drop `user_agent`;
alter table `activities` add `module` varchar(100) default null after `user_id`;
alter table `activities` add `unique_id` int(11) default null after `module`;
alter table `activities` add `secondary_id` int(11) default null after `unique_id`;
alter table `activities` modify `user_id` int(10) unsigned default null;
alter table `activities` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `activities` modify `updated_at` timestamp default null;
alter table `activities` add index (`user_id`);
alter table `activities` add constraint `activities_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade on update cascade;

-- Allowed IP --

CREATE TABLE IF NOT EXISTS `allowed_ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `end` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Announcements --

rename table `notice` to `announcements`;
alter table `announcements` modify `title` varchar(1000) default null;
alter table `announcements` change `content` `description` text default null;
alter table `announcements` modify `from_date` date default null;
alter table `announcements` modify `to_date` date default null;
alter table `announcements` add `attachments` text default null after `to_date`;
alter table `announcements` drop foreign key `notice_username_foreign`;
alter table `announcements` drop index `username`;
alter table `announcements` drop `username`;
alter table `announcements` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `announcements` modify `updated_at` timestamp default null;
alter table `announcements` add index (`user_id`);
alter table `announcements` add constraint `announcements_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade on update cascade;

-- Announcement Designation --

rename table `notice_designation` to `announcement_designation`;
alter table `announcement_designation` drop foreign key `notice_designation_notice_id_foreign`;
alter table `announcement_designation` drop foreign key `notice_designation_designation_id_foreign`;
alter table `announcement_designation` change `notice_id` `announcement_id` int(11) default null;
alter table `announcement_designation` drop index `notice_id`;
alter table `announcement_designation` add index (`announcement_id`);
alter table `announcement_designation`
  add constraint `announcement_designation_announcement_id_foreign` foreign key (`announcement_id`) references `announcements` (`id`) on delete cascade on update cascade,
  add constraint `announcement_designation_designation_id_foreign` foreign key (`designation_id`) references `designations` (`id`) on delete cascade on update cascade;

-- Award Table --

alter table `awards` modify `award_type_id` int(11) default null;
alter table `awards` modify `gift` text default null;
alter table `awards` modify `cash` decimal(25,5) default null;
alter table `awards` modify `month` varchar(15) default null;
alter table `awards` modify `year` year(4) default null;
alter table `awards` change `award_description` `description` text default null;
alter table `awards` change `award_date` `date_of_award` date default null;
alter table `awards` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `awards` modify `updated_at` timestamp default null;

-- Award Type Table --
alter table `award_types` change `award_name` `name` varchar(100) default null;
alter table `award_types` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `award_types` modify `updated_at` timestamp default null;

-- Award User Table --
rename table `assigned_awards` to `award_user`;
alter table `award_user` modify `user_id` int(10) unsigned default null;
alter table `award_user` modify `award_id` int(11) default null;
alter table `award_user` drop foreign key `assigned_awards_award_id_foreign`;
alter table `award_user` drop foreign key `assigned_awards_user_id_foreign`;
ALTER TABLE `award_user`
  ADD CONSTRAINT `award_user_award_id_foreign` FOREIGN KEY (`award_id`) REFERENCES `awards` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `award_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Backup Table --

CREATE TABLE IF NOT EXISTS `backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Bank Accounts Table --
alter table `bank_accounts` add `is_primary` tinyint(4) default '0' after `user_id`;
alter table `bank_accounts` modify `bank_name` varchar(100) default null;
alter table `bank_accounts` modify `account_name` varchar(100) default null;
alter table `bank_accounts` modify `account_number` varchar(100) default null;
alter table `bank_accounts` change `ifsc_code` `bank_code` varchar(100) default null;
alter table `bank_accounts` modify `bank_branch` varchar(100) default null;
alter table `bank_accounts` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `bank_accounts` modify `updated_at` timestamp default null;

-- Clocks Table --
rename table `clock` to `clocks`;
alter table `clocks` modify `user_id` int(10) unsigned default null;
alter table `clocks` modify `date` date default null;
alter table `clocks` modify `clock_in` timestamp default null;
alter table `clocks` modify `clock_out` timestamp default null;
alter table `clocks` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `clocks` modify `updated_at` timestamp default null;

-- Clock Summaries --

CREATE TABLE IF NOT EXISTS `clock_summaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `in_time` timestamp NULL DEFAULT NULL,
  `out_time` timestamp NULL DEFAULT NULL,
  `late` bigint(20) DEFAULT NULL,
  `early` bigint(20) DEFAULT NULL,
  `overtime` bigint(20) DEFAULT NULL,
  `rest` bigint(20) DEFAULT NULL,
  `working` bigint(20) DEFAULT NULL,
  `status` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tag` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `clock_summaries`
  ADD CONSTRAINT `clock_summaries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Contacts Table --

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `is_primary` tinyint(4) NOT NULL DEFAULT '0',
  `is_dependent` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `relation` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `work_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `work_phone` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `work_phone_extension` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `home` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_1` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_2` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Contract Type Table --

CREATE TABLE IF NOT EXISTS `contract_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Contract Table --

CREATE TABLE IF NOT EXISTS `contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `contract_type_id` int(11) DEFAULT NULL,
  `designation_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `title` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `contract_type_id` (`contract_type_id`),
  KEY `designation_id` (`designation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_contract_type_id_foreign` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contracts_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contracts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Custom Fields Table --

alter table `custom_fields` modify `form` varchar(200) default null;
alter table `custom_fields` change `field_name` `name` varchar(200) default null;
alter table `custom_fields` change `field_title` `title` varchar(200) default null;
alter table `custom_fields` change `field_type` `type` varchar(50) default null;
alter table `custom_fields` change `field_values` `options` text default null;
alter table `custom_fields` change `field_required` `is_required` int(11) default '0';
alter table `custom_fields` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `custom_fields` modify `updated_at` timestamp default null;

-- Custom Field Values Table --

alter table `custom_field_values` modify `field_id` int(11) default null;
alter table `custom_field_values` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `custom_field_values` modify `updated_at` timestamp default null;

-- Departments Table --

alter table `departments` change `department_name` `name` varchar(200) default null;
alter table `departments` change `department_description` `description` text default null;
alter table `departments` add `is_hidden` tinyint(4) default '0' after `description`;
alter table `departments` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `departments` modify `updated_at` timestamp default null;

-- Designations Table --

alter table `designations` modify `department_id` int(11) default null;
alter table `designations` change `designation` `name` varchar(100) default null;
alter table `designations` add `is_hidden` tinyint(4) default '0' after `name`;
alter table `designations` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `designations` modify `updated_at` timestamp default null;

-- Documents Table --

alter table `documents` change `expiry_date` `date_of_expiry` date default null;
alter table `documents` change `document_title` `title` varchar(500) default null;
alter table `documents` change `document_description` `description` text default null;
alter table `documents` change `document` `attachments` text default null;
alter table `documents` add `status` int(11) default null after `attachments`;
alter table `documents` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `documents` modify `updated_at` timestamp default null;
alter table `documents` drop foreign key `documents_user_id_foreign`;
alter table `documents` drop index `user_id`;
alter table `documents` modify `user_id` int(10) unsigned default null;
alter table `documents` add index (`user_id`);
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Document Types Table --

alter table `document_types` change `document_type_name` `name` varchar(100) default null;
alter table `document_types` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `document_types` modify `updated_at` timestamp default null;

-- Emails Table --

CREATE TABLE IF NOT EXISTS `emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_address` text COLLATE utf8_unicode_ci,
  `from_address` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` text COLLATE utf8_unicode_ci,
  `body` text COLLATE utf8_unicode_ci,
  `attachments` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Expenses Table --

alter table `expenses` drop foreign key `expenses_expense_head_id_foreign`;
alter table `expenses` drop index `expense_head_id`;
alter table `expenses` modify `expense_head_id` int(11) default null;
alter table `expenses` add index (`expense_head_id`);
alter table `expenses` drop foreign key `expenses_user_id_foreign`;
alter table `expenses` drop index `user_id`;
alter table `expenses` modify `user_id` int(10) unsigned default null;
alter table `expenses` add index (`user_id`);
alter table `expenses` modify `amount` decimal(25,5) default null;
alter table `expenses` change `expense_date` `date_of_expense` date default null;
alter table `expenses` add `attachments` text default null after `date_of_expense`;
alter table `expenses` add `status` varchar(100) default null after `attachments`;
alter table `expenses` add `admin_remarks` text default null after `status`;
alter table `expenses` modify `remarks` text default null;
alter table `expenses` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `expenses` modify `updated_at` timestamp default null;

ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_expense_head_id_foreign` FOREIGN KEY (`expense_head_id`) REFERENCES `expense_heads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Expense Heads Table --

alter table `expense_heads` change `expense_head` `head` varchar(100) default null;
alter table `expense_heads` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `expense_heads` modify `updated_at` timestamp default null;

-- Holidays Table --

alter table `holidays` modify `date` date default null;
alter table `holidays` change `holiday_description` `description` text default null;
alter table `holidays` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `holidays` modify `updated_at` timestamp default null;

-- Jobs Table --

alter table `jobs` change `job_title` `title` varchar(1000) default null;
alter table `jobs` drop foreign key `jobs_designation_id_foreign`;
alter table `jobs` drop index `designation_id`;
alter table `jobs` modify `designation_id` int(11) default null;
alter table `jobs` add index (`designation_id`);
alter table `jobs` add `location` varchar(100) default null after `designation_id`;
alter table `jobs` add `date_of_closing` date default null after `location`;
alter table `jobs` change `numbers` `no_of_post` int(11) default null;
alter table `jobs` add `job_type` varchar(100) default null after `no_of_post`;
alter table `jobs` change `job_description` `description` text default null;
alter table `jobs` drop foreign key `jobs_user_id_foreign`;
alter table `jobs` drop index `user_id`;
alter table `jobs` modify `user_id` int(10) unsigned default null;
alter table `jobs` add index (`user_id`);
alter table `jobs` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `jobs` modify `updated_at` timestamp default null;
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Job Applications Table --

rename table `applications` to `job_applications`;
alter table `job_applications` drop foreign key `job_applications_job_id_foreign`;
alter table `job_applications` drop index `job_id`;
alter table `job_applications` modify `job_id` int(11) default null;
alter table `job_applications` add index (`job_id`);
alter table `job_applications` modify `name` varchar(100) default null;
alter table `job_applications` modify `email` varchar(50) default null;
alter table `job_applications` modify `contact_number` varchar(20) default null;
alter table `job_applications` add `source` varchar(100) default null after `contact_number`;
alter table `job_applications` modify `resume` text default null;
alter table `job_applications` modify `status` varchar(100) default null;
alter table `job_applications` add `remarks` text default null after `status`;
alter table `job_applications` add `date_of_application` date default null after `remarks`;
alter table `job_applications` add `date_of_joining` date default null after `date_of_application`;
alter table `job_applications` add `salary` decimal(25,5) default null after `date_of_application`;
alter table `job_applications` drop foreign key `applications_user_id_foreign`;
alter table `job_applications` drop index `username`;
alter table `job_applications` modify `user_id` int(10) unsigned default null;
alter table `job_applications` add index (`user_id`);
alter table `job_applications` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `job_applications` modify `updated_at` timestamp default null;

ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `job_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Leaves Table --
alter table `leaves` drop foreign key `leaves_user_id_foreign`;
alter table `leaves` drop index `user_id`;
alter table `leaves` modify `user_id` int(10) unsigned default null;
alter table `leaves` add index (`user_id`);
alter table `leaves` drop foreign key `leaves_leave_type_id_foreign`;
alter table `leaves` drop index `leaves_leave_type_id_foreign`;
alter table `leaves` modify `leave_type_id` int(11) default null;
alter table `leaves` add index (`leave_type_id`);
alter table `leaves` modify `from_date` date default null;
alter table `leaves` modify `to_date` date default null;
alter table `leaves` change `leave_description` `description` text default null;
alter table `leaves` change `leave_status` `status` varchar(100) default null;
alter table `leaves` change `leave_comment` `remarks` text default null;
alter table `leaves` add `approved_date` text default null after `remarks`;
alter table `leaves` add `admin_remarks` text default null after `approved_date`;
alter table `leaves` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `leaves` modify `updated_at` timestamp default null;

ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `leaves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Leave Types Table --

alter table `leave_types` change `leave_name` `name` varchar(100) default null;
alter table `leave_types` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `leave_types` modify `updated_at` timestamp default null;

-- Menu Table --

CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `visible` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Messages Table --

alter table `messages` drop foreign key `messages_from_user_id_foreign`;
alter table `messages` drop index `from_user_id`;
alter table `messages` modify `from_user_id` int(10) unsigned default null;
alter table `messages` add index (`from_user_id`);
alter table `messages` drop foreign key `messages_to_user_id_foreign`;
alter table `messages` drop index `to_user_id`;
alter table `messages` modify `to_user_id` int(10) unsigned default null;
alter table `messages` add index (`to_user_id`);
alter table `messages` modify `subject` varchar(1000) default null;
alter table `messages` change `content` `body` text default null;
alter table `messages` change `read` `is_read` int(11) default '0';
alter table `messages` add `reply_id` int(11) default null after `delete_receiver`;
alter table `messages` add index (`reply_id`);
alter table `messages` change `attachment` `attachments` varchar(100) default null;
alter table `messages` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `messages` modify `updated_at` timestamp default null;
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messages_reply_id_foreign` FOREIGN KEY (`reply_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messages_to_user_id_foreign` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Notifications Table --

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `user` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Office Shifts Table --

CREATE TABLE IF NOT EXISTS `office_shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_default` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Office Shift Details Table --

CREATE TABLE IF NOT EXISTS `office_shift_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `office_shift_id` int(11) DEFAULT NULL,
  `day` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `in_time` time DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `overnight` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `office_shift_id` (`office_shift_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `office_shift_details`
  ADD CONSTRAINT `office_shift_detail_office_shift_id_foreign` FOREIGN KEY (`office_shift_id`) REFERENCES `office_shifts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Payroll Table --

alter table `payroll` drop foreign key `payroll_payroll_slip_id_foreign`;
alter table `payroll` drop index `payroll_slip_id`;
alter table `payroll` modify `payroll_slip_id` int(11) default null;
alter table `payroll` add index (`payroll_slip_id`);
alter table `payroll` drop foreign key `payroll_salary_type_id_foreign`;
alter table `payroll` drop index `salary_type_id`;
alter table `payroll` modify `salary_type_id` int(11) default null;
alter table `payroll` add index (`salary_type_id`);
alter table `payroll` modify `amount` decimal(25,5) default null;
alter table `payroll` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `payroll` modify `updated_at` timestamp default null;

ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_payroll_slip_id_foreign` FOREIGN KEY (`payroll_slip_id`) REFERENCES `payroll_slip` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_salary_type_id_foreign` FOREIGN KEY (`salary_type_id`) REFERENCES `salary_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Payroll Slip Table --

alter table `payroll_slip` drop foreign key `payroll_slip_user_id_foreign`;
alter table `payroll_slip` drop index `user_id`;
alter table `payroll_slip` modify `user_id` int(10) unsigned default null;
alter table `payroll_slip` add index (`user_id`);
alter table `payroll_slip` change `month` `from_date` date default null;
alter table `payroll_slip` change `year` `to_date` date default null;
alter table `payroll_slip` modify `employee_contribution` decimal(25,5) default null;
alter table `payroll_slip` modify `employer_contribution` decimal(25,5) default null;
alter table `payroll_slip` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `payroll_slip` modify `updated_at` timestamp default null;

ALTER TABLE `payroll_slip`
  ADD CONSTRAINT `payroll_slip_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Permissions Table --

alter table `permissions` modify `category` varchar(100) default null;
alter table `permissions` modify `name` varchar(255) default null;
alter table `permissions` drop `display_name`;
alter table `permissions` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `permissions` modify `updated_at` timestamp default null;

-- Profile Table --

alter table `profile` drop foreign key `profile_user_id_foreign`;
alter table `profile` drop index `user_id`;
alter table `profile` modify `user_id` int(10) unsigned default null;
alter table `profile` add index (`user_id`);
alter table `profile` modify `employee_code` varchar(100) default null;
alter table `profile` add `gender` varchar(100) default null after `employee_code`;
alter table `profile` add `marital_status` varchar(100) default null after `gender`;
alter table `profile` modify `date_of_birth` date default null;
alter table `profile` modify `date_of_joining` date default null;
alter table `profile` modify `date_of_leaving` date default null;
alter table `profile` modify `date_of_retirement` date default null;
alter table `profile` modify `contact_number` varchar(100) default null;
alter table `profile` modify `photo` varchar(100) default null;
alter table `profile` modify `facebook_link` varchar(100) default null;
alter table `profile` modify `twitter_link` varchar(100) default null;
alter table `profile` modify `blogger_link` varchar(100) default null;
alter table `profile` modify `linkedin_link` varchar(100) default null;
alter table `profile` modify `googleplus_link` varchar(100) default null;
alter table `profile` drop `father_name`;
alter table `profile` drop `mother_name`;
alter table `profile` drop `alternate_contact_number`;
alter table `profile` drop `alternate_email`;
alter table `profile` drop `present_address`;
alter table `profile` drop `permanent_address`;
alter table `profile` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `profile` modify `updated_at` timestamp default null;
ALTER TABLE `profile`
  ADD CONSTRAINT `profile_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Roles Table --

alter table `roles` modify `name` varchar(255) default null;
alter table `roles` add `is_hidden` tinyint(4) NOT NULL DEFAULT '0' after `name`;
alter table `roles` drop `display_name`;
alter table `roles` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `roles` modify `updated_at` timestamp default null;

-- Sessions Table --

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payload` text COLLATE utf8_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Setup Table --

CREATE TABLE IF NOT EXISTS `setup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `completed` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Templates Table --

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_default` int(11) NOT NULL DEFAULT '0',
  `name` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` text COLLATE utf8_unicode_ci,
  `body` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- User Leaves Table --

CREATE TABLE IF NOT EXISTS `user_leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_type_id` int(11) DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `leave_count` int(11) NOT NULL DEFAULT '0',
  `leave_used` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_type_id` (`leave_type_id`),
  KEY `contract_id` (`contract_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `user_leaves`
  ADD CONSTRAINT `user_leaves_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_leaves_leave_type_id` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- User Shifts Table --

CREATE TABLE IF NOT EXISTS `user_shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `office_shift_id` int(11) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`office_shift_id`),
  KEY `office_shift_id` (`office_shift_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `user_shifts`
  ADD CONSTRAINT `user_shifts_office_shift_id_foreign` FOREIGN KEY (`office_shift_id`) REFERENCES `office_shifts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_shifts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Salary Table --

alter table `salary` drop foreign key `salary_user_id_foreign`;
alter table `salary` drop index `user_id`;
alter table `salary` drop `user_id`;
alter table `salary` add `contract_id` int(10) default null after `id`;
alter table `salary` add index (`contract_id`);
alter table `salary` drop foreign key `salary_salary_type_id_foreign`;
alter table `salary` drop index `salary_type_id`;
alter table `salary` modify `salary_type_id` int(11) default null;
alter table `salary` add index (`salary_type_id`);
alter table `salary` modify `amount` decimal(25,5) default null;
alter table `salary` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `salary` modify `updated_at` timestamp default null;

ALTER TABLE `salary`
  ADD CONSTRAINT `salary_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `salary_salary_type_id_foreign` FOREIGN KEY (`salary_type_id`) REFERENCES `salary_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Salary Types Table --

alter table `salary_types` change `salary_head` `head` varchar(100) default null;
alter table `salary_types` modify `salary_type` varchar(10) default null;
alter table `salary_types` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `salary_types` modify `updated_at` timestamp default null;

-- Task Table --

alter table `tasks` change `task_title` `title` varchar(500) default null;
alter table `tasks` change `task_description` `description` text default null;
alter table `tasks` modify `start_date` date default null;
alter table `tasks` modify `due_date` date default null;
alter table `tasks` change `task_progress` `progress` int(11) default null;
alter table `tasks` drop foreign key `tasks_task_username_foreign`;
alter table `tasks` drop index `task_username`;
alter table `tasks` drop `task_username`;
alter table `tasks` add index (`user_id`);
alter table `tasks` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `tasks` modify `updated_at` timestamp default null;
ALTER TABLE `tasks`
  ADD CONSTRAINT `task_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Task Attachments Table --

rename table `attachments` to `task_attachments`;
alter table `task_attachments` change `attachment_title` `title` varchar(100) default null;
alter table `task_attachments` change `attachment_description` `description` text default null;
alter table `task_attachments` drop foreign key `attachments_task_id_foreign`;
alter table `task_attachments` drop index `task_id`;
alter table `task_attachments` modify `task_id` int(11) default null;
alter table `task_attachments` add index (`task_id`);
alter table `task_attachments` drop foreign key `attachments_attachment_username_foreign`;
alter table `task_attachments` drop index `attachment_username`;
alter table `task_attachments` drop `attachment_username`;
alter table `task_attachments` add index (`user_id`);
alter table `task_attachments` change `file` `attachments` text default null;
alter table `task_attachments` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `task_attachments` modify `updated_at` timestamp default null;
ALTER TABLE `task_attachments`
  ADD CONSTRAINT `task_attachments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Task Comments Table --

rename table `comments` to `task_comments`;
alter table `task_comments` drop foreign key `comment_task_id_foreign`;
alter table `task_comments` drop index `task_id`;
alter table `task_comments` modify `task_id` int(11) default null;
alter table `task_comments` add index (`task_id`);
alter table `task_comments` modify `comment` text default null;
alter table `task_comments` drop foreign key `comment_comment_username_foreign`;
alter table `task_comments` drop index `comment_username`;
alter table `task_comments` drop `comment_username`;
alter table `task_comments` add index (`user_id`);
alter table `task_comments` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `task_comments` modify `updated_at` timestamp default null;
ALTER TABLE `task_comments`
  ADD CONSTRAINT `task_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_comment_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Task Notes Table --

rename table `notes` to `task_notes`;
alter table `task_notes` drop foreign key `notes_task_id_foreign`;
alter table `task_notes` drop index `unique_id`;
alter table `task_notes` modify `task_id` int(11) default null;
alter table `task_notes` add index (`task_id`);
alter table `task_notes` change `note_content` `note` text default null;
alter table `task_notes` drop foreign key `notes_note_username_foreign`;
alter table `task_notes` drop index `username`;
alter table `task_notes` drop `note_username`;
alter table `task_notes` add index (`user_id`);
alter table `task_notes` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `task_notes` modify `updated_at` timestamp default null;
ALTER TABLE `task_notes`
  ADD CONSTRAINT `task_notes_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Task User Table --

rename table `assigned_tasks` to `task_user`;
alter table `task_user` drop foreign key `assigned_tasks_task_id_foreign`;
alter table `task_user` drop index `task_id`;
alter table `task_user` modify `task_id` int(11) default null;
alter table `task_user` add index (`task_id`);
alter table `task_user` drop foreign key `assigned_tasks_user_id_foreign`;
alter table `task_user` drop index `user_id`;
alter table `task_user` modify `user_id` int(10) unsigned default null;
alter table `task_user` add index (`user_id`);

ALTER TABLE `task_user`
  ADD CONSTRAINT `task_user_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Tickets Table --

alter table `tickets` drop foreign key `tickets_user_id_foreign`;
alter table `tickets` drop index `user_id`;
alter table `tickets` modify `user_id` int(10) unsigned default null;
alter table `tickets` add index (`user_id`);
alter table `tickets` change `ticket_subject` `tickets` varchar(500) default null;
alter table `tickets` change `ticket_description` `description` varchar(500) default null;
alter table `tickets` change `ticket_priority` `priority` varchar(100) default null;
alter table `tickets` change `ticket_status` `status` varchar(100) default null;
alter table `tickets` add `admin_remarks` text default null after `status`;
alter table `tickets` add `closed_at` timestamp default null after `admin_remarks`;
alter table `tickets` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `tickets` modify `updated_at` timestamp default null;
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Ticket Attachments Table --

alter table `ticket_attachments` change `attachment_title` `title` varchar(100) default null;
alter table `ticket_attachments` change `attachment_description` `description` text default null;
alter table `ticket_attachments` drop foreign key `ticket_attachments_ticket_id_foreign`;
alter table `ticket_attachments` drop index `ticket_id`;
alter table `ticket_attachments` modify `ticket_id` int(11) default null;
alter table `ticket_attachments` add index (`ticket_id`);
alter table `ticket_attachments` drop foreign key `ticket_attachments_attachment_username_foreign`;
alter table `ticket_attachments` drop index `attachment_username`;
alter table `ticket_attachments` drop `attachment_username`;
alter table `ticket_attachments` add index (`user_id`);
alter table `ticket_attachments` change `file` `attachments` text default null;
alter table `ticket_attachments` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `ticket_attachments` modify `updated_at` timestamp default null;
ALTER TABLE `ticket_attachments`
  ADD CONSTRAINT `ticket_attachments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Ticket Comments Table --

alter table `ticket_comments` drop foreign key `ticket_comments_ticket_id_foreign`;
alter table `ticket_comments` drop index `ticket_id`;
alter table `ticket_comments` modify `ticket_id` int(11) default null;
alter table `ticket_comments` add index (`ticket_id`);
alter table `ticket_comments` modify `comment` text default null;
alter table `ticket_comments` drop foreign key `ticket_comments_comment_username_foreign`;
alter table `ticket_comments` drop index `comment_username`;
alter table `ticket_comments` drop `comment_username`;
alter table `ticket_comments` add index (`user_id`);
alter table `ticket_comments` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `ticket_comments` modify `updated_at` timestamp default null;
ALTER TABLE `ticket_comments`
  ADD CONSTRAINT `ticket_comments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Ticket Notes Table --

alter table `ticket_notes` drop foreign key `ticket_notes_ticket_id_foreign`;
alter table `ticket_notes` drop index `ticket_id`;
alter table `ticket_notes` modify `ticket_id` int(11) default null;
alter table `ticket_notes` add index (`ticket_id`);
alter table `ticket_notes` change `note_content` `note` text default null;
alter table `ticket_notes` drop foreign key `ticket_notes_note_username_foreign`;
alter table `ticket_notes` drop index `username`;
alter table `ticket_notes` drop `note_username`;
alter table `ticket_notes` add index (`user_id`);
alter table `ticket_notes` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `ticket_notes` modify `updated_at` timestamp default null;
ALTER TABLE `ticket_notes`
  ADD CONSTRAINT `ticket_notes_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Ticket User Table --

CREATE TABLE IF NOT EXISTS `ticket_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `ticket_user`
  ADD CONSTRAINT `ticket_user_ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- To do Table --

alter table `todos` drop foreign key `todos_user_id_foreign`;
alter table `todos` drop index `user_id`;
alter table `todos` modify `user_id` int(10) unsigned default null;
alter table `todos` add index (`user_id`);
alter table `todos` modify `visibility` varchar(10) default null;
alter table `todos` change `todo_title` `title` text default null;
alter table `todos` change `todo_description` `description` text default null;
alter table `todos` modify `date` date default null;
alter table `todos` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `todos` modify `updated_at` timestamp default null;
ALTER TABLE `todos`
  ADD CONSTRAINT `todos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Users Table --

alter table `users` add `is_hidden` tinyint(4) default '0' after `id`;
alter table `users` modify `first_name` varchar(100) default null;
alter table `users` modify `last_name` varchar(100) default null;
alter table `users` drop foreign key `users_designation_id_foreign`;
alter table `users` drop index `designation_id`;
alter table `users` modify `designation_id` int(11) default null;
alter table `users` add index (`designation_id`);
alter table `users` modify `username` varchar(100) default null;
alter table `users` modify `email` varchar(255) default null;
alter table `users` add `status` varchar(100) default 'active' after `email`;
alter table `users` modify `password` varchar(255) default null;
alter table `users` add `auth_token` varchar(255) default null after `remember_token`;
alter table `users` modify `created_at` timestamp not null default CURRENT_TIMESTAMP;
alter table `users` modify `updated_at` timestamp default null;
ALTER TABLE `users`
  ADD CONSTRAINT `users_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

