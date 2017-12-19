-- 'users'
CREATE TABLE `users` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

-- 'bookings'
CREATE TABLE `bookings` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `reference_number` varchar(255) NOT NULL,
  `booked_by_id` INTEGER NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

-- 'tickets'
CREATE TABLE `tickets` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `reference_number` varchar(255) NOT NULL,
  `reported_by_id` INTEGER NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`)
VALUES
	(1, 'Chet', 'Walker', 'fstanton@example.net', '$2y$10$iUmBjMHNchcFkWUr.8jXIOndw8fkmqf4iBTkkpB8Gf.CK7MakiKzm', 'hkcB2eGBvE', '2017-12-11 14:50:42', '2017-12-11 14:50:42'),
	(2, 'Wilford', 'Ritchie', 'serena.oberbrunner@example.com', '$2y$10$ggg82heV/RZkGY/C9/RsWOiWXcVQdS8q8cTHZXzb6pqqgBI9ALpgq', 'ZK9BPXh8HS', '2017-12-12 16:31:15', '2017-12-12 16:31:15');

INSERT INTO `bookings` (`id`, `reference_number`, `booked_by_id`, `created_at`, `updated_at`)
VALUES
	(1, 'B1234567890', 1, '2017-12-11 14:50:42', '2017-12-11 14:50:42'),
	(2, 'BXYZRGYER10', 2, '2017-12-12 16:31:15', '2017-12-12 16:31:15');

INSERT INTO `tickets` (`id`, `reference_number`, `reported_by_id`, `created_at`, `updated_at`)
VALUES
	(1, 'T1234567890', 1, '2017-12-11 14:50:42', '2017-12-11 14:50:42'),
	(2, 'TXYZRGYER10', 2, '2017-12-12 16:31:15', '2017-12-12 16:31:15');
