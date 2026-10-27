CREATE TABLE `roles` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(50) UNIQUE NOT NULL
);

CREATE TABLE `users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) UNIQUE NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
);

CREATE TABLE `bike_types` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(50) UNIQUE NOT NULL
);

CREATE TABLE `bikes` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `bike_type_id` int NOT NULL,
  `brand` varchar(100) NOT NULL,
  `frame_size` smallint NOT NULL COMMENT 'in cm',
  `photo` varchar(255) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `deleted_at` datetime COMMENT 'soft delete, terug te zetten'
);

CREATE TABLE `reservations` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `bike_id` int NOT NULL,
  `pickup_date` date NOT NULL,
  `return_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL COMMENT 'prijs op moment van reserveren',
  `returned_on` date COMMENT 'inleverdatum, nog onbekend onderweg',
  `remark` text
);

CREATE TABLE `logs` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
);

ALTER TABLE `users` ADD FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

ALTER TABLE `bikes` ADD FOREIGN KEY (`bike_type_id`) REFERENCES `bike_types` (`id`);

ALTER TABLE `reservations` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `reservations` ADD FOREIGN KEY (`bike_id`) REFERENCES `bikes` (`id`);

ALTER TABLE `logs` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
