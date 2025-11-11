-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 11, 2025 at 08:46 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_lamperieresto`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `konten` text COLLATE utf8mb4_general_ci NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_post` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id`, `judul`, `konten`, `gambar`, `tanggal_post`) VALUES
(5, 'beli 1', 'beli 1', '684658aed3e7e-Logo-Unpam-Universitas-Pamulang-Original-PNG-1.png', '2025-06-09 03:44:46'),
(6, 'beli 2', 'beli 2', '684659c9ec055-_3d5ac6aa-a64b-4b61-9fab-79a483b35555.jpeg', '2025-06-09 03:49:29'),
(7, 'beli 12', 'hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', '68465a4c01bcf-Logo-Unpam-Universitas-Pamulang-Original-PNG-1.png', '2025-06-09 03:51:40');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int NOT NULL,
  `kode_diskon` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipe_diskon` enum('persen','tetap') COLLATE utf8mb4_general_ci NOT NULL,
  `nilai_diskon` decimal(10,2) NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('aktif','tidak aktif') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aktif',
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_berakhir` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `kode_diskon`, `deskripsi`, `tipe_diskon`, `nilai_diskon`, `gambar`, `status`, `waktu_mulai`, `waktu_berakhir`) VALUES
(4, 'TEGUH', 'teguh', 'persen', '20.00', '6847cd6088450-Screenshot (1).png', 'tidak aktif', '2025-06-10 13:09:00', '2025-06-10 17:14:00'),
(7, 'DKV', 'discount mingguan', 'persen', '20.00', NULL, 'aktif', '2025-06-14 22:12:00', '2025-06-21 22:12:00');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `menu_item_id` int NOT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`menu_item_id`, `item_name`, `description`, `price`, `category`, `image_url`) VALUES
(1, 'Steak Tenderloin 6oz', 'A cut of beef taken from the center of the back, specifically along the spine.', '43.00', 'Main Course', 'steak_tenderloin_6oz.jpg'),
(2, 'Wagyu A5 6oz', 'Imported Japanese Wagyu A5 steak served with sauce, vegetables, and potatoes.', '240.00', 'Main Course', 'wagyu_A5_6oz.jpg'),
(3, 'Garlic Salmon 5oz', 'Salmon perfectly combined with the strong and savory aroma and flavor of garlic.', '30.00', 'Main Course', 'garlic_salmon_5oz.jpg'),
(4, 'Sirloin Steak', 'A cut of beef taken from the lower back of a cow, specifically between the short loin and the hip (round).', '27.00', 'Main Course', 'sirloin_steak.jpg'),
(5, 'Spinach and Artichoke Dip', 'Served warm with tortilla chips, pita bread, or vegetable sticks for dipping.', '9.70', 'Appetizer', 'SpinachandArtichoke.png'),
(6, 'Buffalo Wings', 'Served with celery sticks and blue cheese or ranch dressing.', '10.70', 'Appetizer', 'BuffaloWings.png'),
(7, 'Mozzarella Sticks', 'Served with marinara sauce for dipping.', '8.70', 'Appetizer', 'MozzarellaSticks.png'),
(8, 'Shrimp Cocktail', 'Served in a glass with shrimp hooked on the rim and garnished with a lemon wedge.', '12.10', 'Appetizer', 'ShrimpCocktail.png'),
(9, 'Candy Bars', 'A long, thin sweet food covered in chocolate.', '1.10', 'Snacks', 'candy_bars.jpg'),
(10, 'Cookies Oreo', 'Chocolate sandwich biscuits with a sweet cream filling in between.', '4.30', 'Snacks', 'cookis_oreo.jpg'),
(11, 'Chips', 'Cheesecake with blueberry topping.', '1.60', 'Snacks', 'chips.jpg'),
(12, 'Chocolate Muffin', 'A small baked cake with a rich chocolate flavor. It has a dense and soft texture.', '10.80', 'Dessert', 'muffins.jpg'),
(13, 'Croissant', 'A buttery, flaky, viennoiserie pastry of Austrian and French origin, named for its historical crescent shape.', '5.40', 'Dessert', 'croisan.jpg'),
(14, 'Cheesecake', 'Made with a soft, fresh cheese, cottage cheese, eggs, and sugar.', '7.50', 'Dessert', 'Cheesecake.jpg'),
(15, 'Cinnamon Roll', 'A sweet roll made from a rolled sheet of yeast-leavened dough onto which a cinnamon and sugar mixture is sprinkled.', '5.40', 'Dessert', 'cinamonroll.jpg'),
(16, 'Hot Chocolate', 'A warm beverage made from shaved chocolate or cocoa powder melted with heated milk or water, and usually includes a sweetener.', '5.40', 'Non-Coffee', 'hot_choco.jpg'),
(17, 'Oreo Milkshake', 'A sweet and rich cold beverage made with a base of milk and ice cream blended with Oreo cookies.', '5.40', 'Non-Coffee', 'Oreo_Milkshake.jpg'),
(18, 'Vanilla Milkshake', 'A cold beverage made by blending milk, vanilla ice cream, and vanilla syrup.', '8.70', 'Non-Coffee', 'Vanilla_Milkshake.jpg'),
(19, 'Smoothie', 'A thick drink made by blending fruits or vegetables, often with yogurt, milk, or ice.', '5.40', 'Non-Coffee', 'Coffe_Latte.jpeg'),
(20, 'Americano', 'A coffee drink made by diluting an espresso shot with hot water.', '7.50', 'Coffee', 'Americano.jpg'),
(21, 'Espresso', 'Coffee brewed by forcing a small amount of nearly boiling water under pressure through finely-ground coffee beans.', '5.40', 'Coffee', 'Espresso.jpg'),
(22, 'Coffee Latte', 'Espresso with a blend of milk and a thin layer of milk foam on top.', '5.40', 'Coffee', 'Coffe-Latte.jpeg'),
(23, 'Cappuccino', 'Consists of a blend of espresso, steamed milk, and a dense milk foam.', '10.80', 'Coffee', 'Capuccino.jpg'),
(24, 'Orange Juice', 'Made from the pressing of carefully selected ripe oranges.', '3.50', 'Juice', 'orange-juice.jpg'),
(25, 'Apple Juice', 'Pure apple juice offers a crisp and refreshing sweetness from fresh apples.', '4.30', 'Juice', 'apple_juice.jpg'),
(26, 'Cranberry Juice', 'Often with a touch of natural sweetener or other fruit juices to balance its tartness.', '4.30', 'Juice', NULL),
(27, 'Pineapple Juice', 'A tropical sensation with a sweet, tangy, and very refreshing taste.', '4.90', 'Juice', NULL),
(28, 'apepe', 'makanan enak banget cihuy aslole geboy mujaer', '255555.00', 'Main Course', '68458a3ddd2d9-Logo-Unpam-Universitas-Pamulang-Original-PNG-1.png'),
(29, 'Cranberry Juice', 'Often with a touch of natural sweetener or other fruit juices to balance its tartness.', '24.99', 'Juice', 'Cranberry Juice.jpg'),
(36, 'mie goreng', 'enak', '12.00', 'Main Course', 'images/menu/_3d5ac6aa-a64b-4b61-9fab-79a483b35555.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `table_number` int DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `discount_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `order_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `table_number`, `customer_name`, `order_date`, `total_amount`, `total_price`, `payment_method`, `discount_code`, `discount_amount`, `order_status`) VALUES
(7, 1, NULL, NULL, '2025-05-30 20:21:11', '0.00', '17.30', '', NULL, NULL, 'pending'),
(26, 1, NULL, NULL, '2025-06-01 22:26:22', '0.00', '116.00', 'Bank Transfer', NULL, NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int NOT NULL,
  `order_id` int NOT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `menu_item_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `status` enum('Pending','Preparing','Ready','Delivered') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `item_name`, `menu_item_id`, `quantity`, `price_per_item`, `subtotal`, `status`) VALUES
(14, 26, 'Garlic Salmon 5oz', 3, 2, '30.00', '60.00', 'Pending'),
(15, 26, 'Sirloin Steak', 4, 1, '27.00', '27.00', 'Pending'),
(16, 26, 'Buffalo Wings', 6, 1, '10.70', '10.70', 'Pending'),
(17, 26, 'Cheesecake', 14, 1, '7.50', '7.50', 'Pending'),
(18, 26, 'Hot Chocolate', 16, 1, '5.40', '5.40', 'Pending'),
(19, 26, 'Oreo Milkshake', 17, 1, '5.40', '5.40', 'Pending'),
(61, 7, 'Candy Bars', 9, 1, '1.10', '1.10', 'Pending'),
(62, 7, 'Chocolate Muffin', 12, 1, '10.80', '10.80', 'Pending'),
(63, 7, 'Croissant', 13, 1, '5.40', '5.40', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_item_status`
--

CREATE TABLE `order_item_status` (
  `id` int NOT NULL,
  `order_item_id` int NOT NULL,
  `status` enum('pending','preparing','finished') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item_status`
--

INSERT INTO `order_item_status` (`id`, `order_item_id`, `status`, `last_updated`) VALUES
(1, 56, 'finished', '2025-06-14 13:13:44'),
(2, 57, 'finished', '2025-06-14 13:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int NOT NULL,
  `order_id` int NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method`, `amount_paid`, `payment_date`) VALUES
(1, 7, 'E-Wallet', '17.30', '2025-05-30 20:21:11');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `reservation_datetime` datetime NOT NULL,
  `num_of_people` int NOT NULL,
  `table_number` int DEFAULT NULL,
  `special_request` text COLLATE utf8mb4_general_ci,
  `status` enum('Pending','Confirmed','Cancelled','Seated','No Show') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `seated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `customer_name`, `customer_email`, `reservation_datetime`, `num_of_people`, `table_number`, `special_request`, `status`, `seated_at`, `created_at`) VALUES
(1, 'teguh', 'teguh@gmail.com', '2025-06-20 15:52:00', 3, NULL, 'deket jendela', 'Pending', NULL, '2025-06-14 12:53:02'),
(2, 'teguh', 'teguh1@gmail.com', '2025-06-21 13:56:00', 2, NULL, 'deket kaca', 'Pending', NULL, '2025-06-14 12:56:32'),
(3, 'teguh', 'teguh1@gmail.com', '2025-06-21 13:56:00', 2, NULL, 'deket kaca', 'Pending', NULL, '2025-06-14 12:56:54'),
(4, 'teguh', 'teguh1@gmail.com', '2025-06-21 13:56:00', 2, NULL, 'deket kaca', 'Pending', NULL, '2025-06-14 12:59:19'),
(5, 'soso', 'soso@gmail.com', '2025-06-14 16:01:00', 2, 2, 'deket jendela', '', NULL, '2025-06-14 13:01:42'),
(6, 'soso', 'soso@gmail.com', '2025-06-19 20:31:00', 3, NULL, 'deket pintu', 'Pending', NULL, '2025-06-14 13:28:45'),
(8, 'soso', 'soso@gmail.com', '2025-06-14 21:07:00', 1, 1, '', '', NULL, '2025-06-14 14:07:50'),
(9, 'deaa', 'dea@gmail.com', '2025-06-14 21:13:00', 2, 10000, '', '', NULL, '2025-06-14 14:13:42'),
(10, 'deaa', 'dea@gmail.com', '2025-06-14 00:14:00', 1, 10000, '', '', NULL, '2025-06-14 14:14:21'),
(11, 'deaa', 'dea@gmail.com', '2025-06-14 00:14:00', 2, 2, '', '', NULL, '2025-06-14 14:14:35'),
(12, 'deaa', 'dea@gmail.com', '2025-06-07 21:22:00', 1, NULL, '', 'Pending', NULL, '2025-06-14 14:22:31'),
(13, 'soso', 'soso@gmail.com', '2025-06-11 21:23:00', 2, NULL, '', 'Pending', NULL, '2025-06-14 14:23:10');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` int NOT NULL,
  `table_number` varchar(255) NOT NULL,
  `is_available` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `table_number`, `is_available`) VALUES
(1, '1', 1),
(2, '2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('admin','user','kasir','manajer','kitchen','waiters') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `member_id` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `created_at`, `role`, `member_id`, `profile_picture`) VALUES
(1, 'rahmat', 'rahmat@gmail.com', '123', '08654321891256', '2025-05-29 18:48:39', 'admin', 'LP-2025-0001', NULL),
(16, 'rahmat eka', 'matsganz@gmail.com', '$2y$10$N98tNAGM8TxHi6rTpJvDOeV5cGGeqZx0yaW2kdM92eDtnic6sDt/u', '089514509392', '2025-11-03 15:25:17', 'admin', 'LP-2025-0016', NULL),
(17, 'user', 'user@gmail.com', '$2y$10$hgjZkqTpQc5qmdzvTFofK.43t7wTTmlxTMulz.KZ9.SSQfGEaHE2i', '089555555555', '2025-11-03 15:33:17', 'user', 'LP-2025-0017', NULL),
(18, 'kasir', 'kasir@kasir', '$2y$10$9sezQ4fAMHUTNhChyfymNOn.Et8v1QJBwJBeBU43GYFEylTrbyOpa', '09876543', '2025-11-11 01:11:21', 'kasir', 'LP-2025-0018', NULL),
(19, 'manager', 'manager@manager', '$2y$10$mYqFSHWShk0lHyg0xjKLpu9OBnIHaAaAU127t8EVQh0SlQI2AAu/m', '09876543', '2025-11-11 01:12:39', 'manajer', 'LP-2025-0019', NULL),
(20, 'kitchen', 'kitchen@kitchen', '$2y$10$29ckgGejyQXzCo6qQd9Yfex5asGzZ7FiH2LgwGvOAFR6BhHyrHaui', '09876543', '2025-11-11 01:13:36', 'kitchen', 'LP-2025-0020', NULL),
(21, 'waiters', 'waiters@waiters', '$2y$10$bIBK0BM3O.dRE4Eodr0YzOrGZ.ZtuGWBpNKg74hqqrcxksU6mOOV6', '09876543', '2025-11-11 01:14:29', 'waiters', 'LP-2025-0021', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_diskon` (`kode_diskon`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`menu_item_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_users` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `fk_order_items_menu_items` (`menu_item_id`),
  ADD KEY `fk_order_items_orders` (`order_id`);

--
-- Indexes for table `order_item_status`
--
ALTER TABLE `order_item_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_item_status_order_items` (`order_item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payments_orders` (`order_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `member_id` (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `menu_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `order_item_status`
--
ALTER TABLE `order_item_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_menu_items` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`menu_item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
