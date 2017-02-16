
CREATE TABLE `clock_fails` (
  `id` int(11) NOT NULL,
  `clock_upload_id` int(11) DEFAULT NULL,
  `employee_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `clock_fails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clock_upload_id` (`clock_upload_id`);

ALTER TABLE `clock_fails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `clock_uploads` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `filename` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total` int(11) NOT NULL DEFAULT '0',
  `uploaded` int(11) NOT NULL DEFAULT '0',
  `rejected` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `clock_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `clock_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

alter table `contracts` add `late_hourly_rate` decimal(25,5) after `to_date`;
alter table `contracts` add `early_leaving_hourly_rate` decimal(25,5) after `late_hourly_rate`;
alter table `contracts` add `overtime_hourly_rate` decimal(25,5) after `early_leaving_hourly_rate`;
alter table `contracts` add `hourly_payroll` int(11) default '0' after `overtime_hourly_rate`;
alter table `contracts` add `hourly_rate` decimal(25,5) after `hourly_payroll`;

CREATE TABLE `daily_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `is_locked` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `daily_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `daily_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

alter table `designations` add `is_default` int(11) default '0' after `is_hidden`;

CREATE TABLE `education_levels` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `education_levels`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `education_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `expense_status_details` (
  `id` int(11) NOT NULL,
  `expense_id` int(11) DEFAULT NULL,
  `designation_id` int(11) DEFAULT NULL,
  `status` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `expense_status_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_id` (`expense_id`),
  ADD KEY `designation_id` (`designation_id`);

ALTER TABLE `expense_status_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `leave_status_details` (
  `id` int(11) NOT NULL,
  `leave_id` int(11) DEFAULT NULL,
  `designation_id` int(11) DEFAULT NULL,
  `approved_date` text COLLATE utf8_unicode_ci,
  `status` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `leave_status_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_id` (`leave_id`),
  ADD KEY `designation_id` (`designation_id`);

ALTER TABLE `leave_status_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `top_location_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `top_location_id` (`top_location_id`);

ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

alter table `messages` add `message_category_id` int(11) default NULL after `id`;
alter table `messages` add index (`message_category_id`);
alter table `messages` add `message_priority_id` int(11) default NULL after `message_category_id`;
alter table `messages` add index (`message_priority_id`);
alter table `messages` add `token` varchar(100) default NULL after `message_priority_id`;
alter table `messages` add `is_draft` int(11) default '0' after `token`;
alter table `messages` add `status` enum('open','close') default 'open' after `body`;
alter table `messages` add `is_starred_sender` int(11) default '0' after `status`;
alter table `messages` add `is_starred_receiver` int(11) default '0' after `is_starred_sender`;

CREATE TABLE `message_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `message_categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `message_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  
CREATE TABLE `message_priorities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `message_priorities`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `message_priorities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

alter table `payroll_slip` add `hourly_payroll` int(11) default '0' after `to_date`;
alter table `payroll_slip` add `hourly` decimal(25,5) after `employer_contribution`;
alter table `payroll_slip` add `late` decimal(25,5) after `hourly`;
alter table `payroll_slip` add `early_leaving` decimal(25,5) after `late`;
alter table `payroll_slip` add `overtime` decimal(25,5) after `early_leaving`;

CREATE TABLE `qualifications` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `institute_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `from_year` year(4) DEFAULT NULL,
  `to_year` year(4) DEFAULT NULL,
  `education_level_id` int(11) DEFAULT NULL,
  `qualification_language_id` int(11) DEFAULT NULL,
  `qualification_skill_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `qualifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `education_level_id` (`education_level_id`),
  ADD KEY `qualification_language_id` (`qualification_language_id`),
  ADD KEY `qualification_skill_id` (`qualification_skill_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `qualifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `qualification_languages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `qualification_languages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `qualification_languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `qualification_skills` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `qualification_skills`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `qualification_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `q_failed_jobs` (
  `id` int(10) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `q_failed_jobs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `q_failed_jobs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `q_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `q_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved`,`reserved_at`);

ALTER TABLE `q_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

alter table `roles` add `is_default` int(11) default '0' after `is_hidden`;

alter table `salary_types` add `is_fixed` int(11) default '0' after `salary_type`;

CREATE TABLE `sub_tasks` (
  `id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `sub_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

CREATE TABLE `sub_task_ratings` (
  `id` int(11) NOT NULL,
  `sub_task_id` int(11) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `comment` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `sub_task_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_task_id` (`sub_task_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `sub_task_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `sub_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

alter table `task_user` add `rating` int(11) default NULL after `task_id`;
alter table `task_user` add `comment` text default NULL after `rating`;
alter table `task_user` add `updated_at` timestamp default NULL after `comment`;

alter table `users` add `activation_token` varchar(255) default NULL after `remember_token`;

CREATE TABLE `user_locations` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `user_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `location_id` (`location_id`);

CREATE TABLE `work_experiences` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `post` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `work_experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `work_experiences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `user_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `clock_fails`
  ADD CONSTRAINT `clock_fails_clock_upload_id_foreign` FOREIGN KEY (`clock_upload_id`) REFERENCES `clock_uploads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `clock_uploads`
  ADD CONSTRAINT `clock_uploads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `daily_reports`
  ADD CONSTRAINT `daily_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `expense_status_details`
  ADD CONSTRAINT `expense_status_details_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `expense_status_details_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `leaves` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `leave_status_details`
  ADD CONSTRAINT `leave_status_details_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `leave_status_details_leave_id_foreign` FOREIGN KEY (`leave_id`) REFERENCES `leaves` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `messages`
  ADD CONSTRAINT `messages_message_category_id_foreign` FOREIGN KEY (`message_category_id`) REFERENCES `message_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messages_message_priority_id_foreign` FOREIGN KEY (`message_priority_id`) REFERENCES `message_priorities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sub_tasks`
  ADD CONSTRAINT `sub_tasks_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sub_task_ratings`
  ADD CONSTRAINT `sub_task_ratings_sub_task_id_foreign` FOREIGN KEY (`sub_task_id`) REFERENCES `sub_tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_task_ratings_sub_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `user_locations`
  ADD CONSTRAINT `user_locations_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_locations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `locations`
  ADD CONSTRAINT `locations_top_location_id_foreign` FOREIGN KEY (`top_location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
