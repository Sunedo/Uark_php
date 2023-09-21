CREATE TABLE `orgs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(50) NOT NULL,
  `org_no` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `org_id` INT NOT NULL,
  `name` varchar(50) NOT NULL,
  `birthday` datetime,
  `email` varchar(50) NOT NULL,
  `account` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `status` varchar(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

ALTER TABLE `users`
ADD FOREIGN KEY (`org_id`) REFERENCES `orgs`(`id`) ON DELETE CASCADE;


CREATE TABLE `apply_file` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `file_path` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

ALTER TABLE `apply_file`
ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;

