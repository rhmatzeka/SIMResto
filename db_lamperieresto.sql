-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2025 at 04:54 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `konten` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal_post` timestamp NOT NULL DEFAULT current_timestamp()
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
  `id` int(11) NOT NULL,
  `kode_diskon` varchar(50) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `tipe_diskon` enum('persen','tetap') NOT NULL,
  `nilai_diskon` decimal(10,2) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('aktif','tidak aktif') NOT NULL DEFAULT 'aktif',
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_berakhir` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `kode_diskon`, `deskripsi`, `tipe_diskon`, `nilai_diskon`, `gambar`, `status`, `waktu_mulai`, `waktu_berakhir`) VALUES
(4, 'TEGUH', 'teguh', 'persen', 20.00, '6847cd6088450-Screenshot (1).png', 'tidak aktif', '2025-06-10 13:09:00', '2025-06-10 17:14:00'),
(7, 'DKV', 'discount mingguan', 'persen', 20.00, NULL, 'aktif', '2025-06-14 22:12:00', '2025-06-21 22:12:00');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `menu_item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`menu_item_id`, `item_name`, `description`, `price`, `category`, `image_url`) VALUES
(1, 'Steak Tenderloin 6oz', 'A cut of beef taken from the center of the back, specifically along the spine.', 43.00, 'Main Course', 'steak_tenderloin_6oz.jpg'),
(2, 'Wagyu A5 6oz', 'Imported Japanese Wagyu A5 steak served with sauce, vegetables, and potatoes.', 240.00, 'Main Course', 'wagyu_A5_6oz.jpg'),
(3, 'Garlic Salmon 5oz', 'Salmon perfectly combined with the strong and savory aroma and flavor of garlic.', 30.00, 'Main Course', 'garlic_salmon_5oz.jpg'),
(4, 'Sirloin Steak', 'A cut of beef taken from the lower back of a cow, specifically between the short loin and the hip (round).', 27.00, 'Main Course', 'sirloin_steak.jpg'),
(5, 'Spinach and Artichoke Dip', 'Served warm with tortilla chips, pita bread, or vegetable sticks for dipping.', 9.70, 'Appetizer', 'SpinachandArtichoke.png'),
(6, 'Buffalo Wings', 'Served with celery sticks and blue cheese or ranch dressing.', 10.70, 'Appetizer', 'BuffaloWings.png'),
(7, 'Mozzarella Sticks', 'Served with marinara sauce for dipping.', 8.70, 'Appetizer', 'MozzarellaSticks.png'),
(8, 'Shrimp Cocktail', 'Served in a glass with shrimp hooked on the rim and garnished with a lemon wedge.', 12.10, 'Appetizer', 'ShrimpCocktail.png'),
(9, 'Candy Bars', 'A long, thin sweet food covered in chocolate.', 1.10, 'Snacks', 'candy_bars.jpg'),
(10, 'Cookies Oreo', 'Chocolate sandwich biscuits with a sweet cream filling in between.', 4.30, 'Snacks', 'cookis_oreo.jpg'),
(11, 'Chips', 'Cheesecake with blueberry topping.', 1.60, 'Snacks', 'chips.jpg'),
(12, 'Chocolate Muffin', 'A small baked cake with a rich chocolate flavor. It has a dense and soft texture.', 10.80, 'Dessert', 'muffins.jpg'),
(13, 'Croissant', 'A buttery, flaky, viennoiserie pastry of Austrian and French origin, named for its historical crescent shape.', 5.40, 'Dessert', 'croisan.jpg'),
(14, 'Cheesecake', 'Made with a soft, fresh cheese, cottage cheese, eggs, and sugar.', 7.50, 'Dessert', 'Cheesecake.jpg'),
(15, 'Cinnamon Roll', 'A sweet roll made from a rolled sheet of yeast-leavened dough onto which a cinnamon and sugar mixture is sprinkled.', 5.40, 'Dessert', 'cinamonroll.jpg'),
(16, 'Hot Chocolate', 'A warm beverage made from shaved chocolate or cocoa powder melted with heated milk or water, and usually includes a sweetener.', 5.40, 'Non-Coffee', 'hot_choco.jpg'),
(17, 'Oreo Milkshake', 'A sweet and rich cold beverage made with a base of milk and ice cream blended with Oreo cookies.', 5.40, 'Non-Coffee', 'Oreo_Milkshake.jpg'),
(18, 'Vanilla Milkshake', 'A cold beverage made by blending milk, vanilla ice cream, and vanilla syrup.', 8.70, 'Non-Coffee', 'Vanilla_Milkshake.jpg'),
(19, 'Smoothie', 'A thick drink made by blending fruits or vegetables, often with yogurt, milk, or ice.', 5.40, 'Non-Coffee', 'Coffe_Latte.jpeg'),
(20, 'Americano', 'A coffee drink made by diluting an espresso shot with hot water.', 7.50, 'Coffee', 'Americano.jpg'),
(21, 'Espresso', 'Coffee brewed by forcing a small amount of nearly boiling water under pressure through finely-ground coffee beans.', 5.40, 'Coffee', 'Espresso.jpg'),
(22, 'Coffee Latte', 'Espresso with a blend of milk and a thin layer of milk foam on top.', 5.40, 'Coffee', 'Coffe-Latte.jpeg'),
(23, 'Cappuccino', 'Consists of a blend of espresso, steamed milk, and a dense milk foam.', 10.80, 'Coffee', 'Capuccino.jpg'),
(24, 'Orange Juice', 'Made from the pressing of carefully selected ripe oranges.', 3.50, 'Juice', 'orange-juice.jpg'),
(25, 'Apple Juice', 'Pure apple juice offers a crisp and refreshing sweetness from fresh apples.', 4.30, 'Juice', 'apple_juice.jpg'),
(26, 'Cranberry Juice', 'Often with a touch of natural sweetener or other fruit juices to balance its tartness.', 4.30, 'Juice', NULL),
(27, 'Pineapple Juice', 'A tropical sensation with a sweet, tangy, and very refreshing taste.', 4.90, 'Juice', NULL),
(28, 'apepe', 'makanan enak banget cihuy aslole geboy mujaer', 25.00, 'Main Course', '68458a3ddd2d9-Logo-Unpam-Universitas-Pamulang-Original-PNG-1.png'),
(29, 'Cranberry Juice', 'Often with a touch of natural sweetener or other fruit juices to balance its tartness.', 24.99, 'Juice', 'Cranberry Juice.jpg'),
(36, 'mie goreng', 'enak', 12.00, 'Main Course', 'images/menu/_3d5ac6aa-a64b-4b61-9fab-79a483b35555.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `table_number` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `discount_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `order_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `table_number`, `customer_name`, `order_date`, `total_amount`, `total_price`, `payment_method`, `discount_code`, `discount_amount`, `order_status`) VALUES
(7, 1, NULL, NULL, '2025-05-30 20:21:11', 0.00, 17.30, '', NULL, NULL, 'pending'),
(8, 2, NULL, NULL, '2025-05-30 20:28:48', 0.00, 30.50, '', NULL, NULL, 'pending'),
(9, 2, NULL, NULL, '2025-05-30 20:41:45', 0.00, 39.00, '', NULL, NULL, 'pending'),
(23, 3, NULL, NULL, '2025-06-01 12:03:27', 0.00, 4.90, 'COD', NULL, NULL, 'Pending'),
(24, 4, NULL, NULL, '2025-06-01 12:19:37', 0.00, 312.80, 'COD', NULL, NULL, 'Pending'),
(25, 4, NULL, NULL, '2025-06-01 12:23:25', 0.00, 397.00, 'Bank Transfer', NULL, NULL, 'Pending'),
(26, 1, NULL, NULL, '2025-06-01 22:26:22', 0.00, 116.00, 'Bank Transfer', NULL, NULL, 'Pending'),
(27, 8, NULL, NULL, '2025-06-05 10:21:20', 0.00, 48.40, 'Bank Transfer', NULL, NULL, 'Pending'),
(28, 8, NULL, NULL, '2025-06-05 11:11:16', 0.00, 25.80, 'Bank Transfer', NULL, NULL, 'Pending'),
(29, 8, NULL, NULL, '2025-06-07 09:17:48', 0.00, 76.00, 'COD', NULL, NULL, 'Pending'),
(30, 3, NULL, NULL, '2025-06-08 08:11:29', 0.00, 51.30, 'E-Wallet', NULL, NULL, 'Pending'),
(31, 3, NULL, NULL, '2025-06-09 06:08:49', 0.00, 507.00, 'Bank Transfer', NULL, NULL, 'Pending'),
(32, 10, NULL, NULL, '2025-06-09 14:24:11', 0.00, 433.00, 'E-Wallet', NULL, NULL, 'Pending'),
(33, 3, NULL, NULL, '2025-06-10 06:31:59', 0.00, 54.00, 'E-Wallet', NULL, NULL, 'Pending'),
(34, 9, NULL, NULL, '2025-06-10 08:30:58', 0.00, 16.20, 'E-Wallet', NULL, NULL, 'Pending'),
(35, 9, NULL, NULL, '2025-06-10 08:48:29', 0.00, 192.00, 'E-Wallet', NULL, NULL, 'Pending'),
(36, 3, NULL, NULL, '2025-06-10 08:50:07', 0.00, 57.45, 'E-Wallet', NULL, NULL, 'Pending'),
(37, 3, NULL, NULL, '2025-06-14 10:10:10', 0.00, 37.55, 'COD', NULL, NULL, 'Pending'),
(38, 3, NULL, NULL, '2025-06-14 15:28:26', 0.00, 313.00, 'E-Wallet', NULL, NULL, 'Pending'),
(40, 3, NULL, NULL, '2025-06-14 17:15:15', 0.00, 64.80, 'COD', '0', 16.20, 'Pending'),
(41, 3, NULL, NULL, '2025-06-14 17:44:37', 0.00, 576.00, 'Bank Transfer', '0', 144.00, 'Pending'),
(42, 3, NULL, NULL, '2025-06-14 17:51:37', 0.00, 487.20, 'COD', '0', 121.80, 'Pending'),
(43, 3, NULL, NULL, '2025-06-14 17:55:18', 0.00, 547.70, 'Bank Transfer', NULL, 0.00, 'Pending'),
(44, 3, NULL, NULL, '2025-06-14 18:00:06', 0.00, 1536.00, 'E-Wallet', '0', 384.00, 'Pending'),
(45, 3, NULL, NULL, '2025-06-14 18:05:15', 0.00, 2688.00, 'Bank Transfer', '0', 672.00, 'Pending'),
(46, 3, NULL, NULL, '2025-06-14 18:05:51', 0.00, 302.40, 'COD', '0', 75.60, 'Pending'),
(47, 3, NULL, NULL, '2025-06-14 18:10:36', 0.00, 429.04, 'COD', '0', 107.26, 'Pending'),
(48, 3, NULL, NULL, '2025-06-15 07:52:36', 0.00, 399.52, 'COD', '0', 99.88, 'Pending'),
(49, 3, NULL, NULL, '2025-06-15 07:53:26', 0.00, 172.00, 'E-Wallet', NULL, 0.00, 'Pending'),
(50, 3, NULL, NULL, '2025-06-15 08:09:42', 0.00, 64.80, 'Bank Transfer', '0', 16.20, 'Pending'),
(51, 3, NULL, NULL, '2025-06-15 08:12:57', 0.00, 480.00, 'Bank Transfer', NULL, 0.00, 'Pending'),
(54, 3, NULL, NULL, '2025-06-15 13:24:37', 0.00, 384.00, 'Bank Transfer', '0', 96.00, 'Pending'),
(55, 8, NULL, NULL, '2025-06-15 13:31:49', 0.00, 255.36, 'E-Wallet', '0', 63.84, 'Pending'),
(56, 16, NULL, NULL, '2025-06-15 21:49:21', 0.00, 32.40, 'Bank Transfer', NULL, 0.00, 'Pending'),
(57, 3, NULL, NULL, '2025-06-16 00:56:19', 0.00, 960.00, 'E-Wallet', '0', 240.00, 'Pending'),
(58, 3, NULL, NULL, '2025-06-16 01:08:22', 0.00, 576.00, 'E-Wallet', '0', 144.00, 'Pending'),
(59, 3, NULL, NULL, '2025-06-16 01:10:30', 0.00, 720.00, 'Bank Transfer', NULL, 0.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `status` enum('Pending','Preparing','Ready','Delivered') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `item_name`, `menu_item_id`, `quantity`, `price_per_item`, `subtotal`, `status`) VALUES
(1, 24, 'Wagyu A5 6oz', 2, 1, 240.00, 240.00, 'Pending'),
(2, 24, 'Sirloin Steak', 4, 1, 27.00, 27.00, 'Pending'),
(3, 24, 'Buffalo Wings', 6, 1, 10.70, 10.70, 'Pending'),
(4, 24, 'Mozzarella Sticks', 7, 1, 8.70, 8.70, 'Pending'),
(5, 24, 'Candy Bars', 9, 1, 1.10, 1.10, 'Pending'),
(6, 24, 'Chips', 11, 1, 1.60, 1.60, 'Pending'),
(7, 24, 'Chocolate Muffin', 12, 1, 10.80, 10.80, 'Pending'),
(8, 24, 'Cinnamon Roll', 15, 1, 5.40, 5.40, 'Pending'),
(9, 24, 'Americano', 20, 1, 7.50, 7.50, 'Pending'),
(10, 25, 'Steak Tenderloin 6oz', 1, 1, 43.00, 43.00, 'Pending'),
(11, 25, 'Wagyu A5 6oz', 2, 1, 240.00, 240.00, 'Pending'),
(12, 25, 'Garlic Salmon 5oz', 3, 2, 30.00, 60.00, 'Pending'),
(13, 25, 'Sirloin Steak', 4, 2, 27.00, 54.00, 'Pending'),
(14, 26, 'Garlic Salmon 5oz', 3, 2, 30.00, 60.00, 'Pending'),
(15, 26, 'Sirloin Steak', 4, 1, 27.00, 27.00, 'Pending'),
(16, 26, 'Buffalo Wings', 6, 1, 10.70, 10.70, 'Pending'),
(17, 26, 'Cheesecake', 14, 1, 7.50, 7.50, 'Pending'),
(18, 26, 'Hot Chocolate', 16, 1, 5.40, 5.40, 'Pending'),
(19, 26, 'Oreo Milkshake', 17, 1, 5.40, 5.40, 'Pending'),
(20, 27, 'Steak Tenderloin 6oz', 1, 1, 43.00, 43.00, 'Pending'),
(21, 27, 'Smoothie', 19, 1, 5.40, 5.40, 'Pending'),
(22, 28, 'Spinach and Artichoke Dip', 5, 1, 9.70, 9.70, 'Pending'),
(23, 28, 'Buffalo Wings', 6, 1, 10.70, 10.70, 'Pending'),
(24, 28, 'Coffee Latte', 22, 1, 5.40, 5.40, 'Pending'),
(25, 29, 'Sirloin Steak', 4, 2, 27.00, 54.00, 'Pending'),
(26, 29, 'Spinach and Artichoke Dip', 5, 1, 9.70, 9.70, 'Pending'),
(27, 29, 'Buffalo Wings', 6, 1, 10.70, 10.70, 'Pending'),
(28, 29, 'Chips', 11, 1, 1.60, 1.60, 'Pending'),
(29, 30, 'Spinach and Artichoke Dip', 5, 1, 9.70, 9.70, 'Pending'),
(30, 30, 'Mozzarella Sticks', 7, 2, 8.70, 17.40, 'Pending'),
(31, 30, 'Shrimp Cocktail', 8, 2, 12.10, 24.20, 'Pending'),
(32, 31, 'Wagyu A5 6oz', 2, 2, 240.00, 480.00, 'Pending'),
(33, 31, 'Spinach and Artichoke Dip', 5, 1, 9.70, 9.70, 'Pending'),
(34, 31, 'Candy Bars', 9, 1, 1.10, 1.10, 'Pending'),
(35, 31, 'Cinnamon Roll', 15, 1, 5.40, 5.40, 'Pending'),
(36, 31, 'Oreo Milkshake', 17, 2, 5.40, 10.80, 'Pending'),
(37, 32, 'Wagyu A5 6oz', 2, 1, 240.00, 240.00, 'Pending'),
(38, 32, 'Steak Tenderloin 6oz', 1, 3, 43.00, 129.00, 'Pending'),
(39, 32, 'Garlic Salmon 5oz', 3, 1, 30.00, 30.00, 'Pending'),
(40, 32, 'Mozzarella Sticks', 7, 1, 8.70, 8.70, 'Pending'),
(41, 32, 'Chips', 11, 1, 1.60, 1.60, 'Pending'),
(42, 32, 'Cheesecake', 14, 1, 7.50, 7.50, 'Pending'),
(43, 32, 'Hot Chocolate', 16, 2, 5.40, 10.80, 'Pending'),
(44, 32, 'Oreo Milkshake', 17, 1, 5.40, 5.40, 'Pending'),
(45, 33, 'Sirloin Steak', 4, 2, 27.00, 54.00, 'Pending'),
(46, 34, 'Croissant', 13, 1, 5.40, 5.40, 'Pending'),
(47, 34, 'Cinnamon Roll', 15, 1, 5.40, 5.40, 'Pending'),
(48, 34, 'Oreo Milkshake', 17, 1, 5.40, 5.40, 'Pending'),
(49, 35, 'Wagyu A5 6oz', 2, 1, 240.00, 240.00, 'Pending'),
(50, 36, 'apepe', 28, 1, 25.55, 25.55, 'Pending'),
(51, 36, 'Spinach and Artichoke Dip', 5, 1, 9.70, 9.70, 'Pending'),
(52, 36, 'Mozzarella Sticks', 7, 1, 8.70, 8.70, 'Pending'),
(53, 36, 'Candy Bars', 9, 1, 1.10, 1.10, 'Pending'),
(54, 36, 'Chips', 11, 1, 1.60, 1.60, 'Pending'),
(55, 36, 'Chocolate Muffin', 12, 1, 10.80, 10.80, 'Pending'),
(56, 37, 'apepe', 28, 1, 25.55, 25.55, 'Pending'),
(57, 37, 'mie goreng', 36, 1, 12.00, 12.00, 'Pending'),
(58, 38, 'Wagyu A5 6oz', 2, 1, 240.00, 240.00, 'Pending'),
(59, 38, 'Steak Tenderloin 6oz', 1, 1, 43.00, 43.00, 'Pending'),
(60, 38, 'Garlic Salmon 5oz', 3, 1, 30.00, 30.00, 'Pending'),
(61, 7, 'Candy Bars', 9, 1, 1.10, 1.10, 'Pending'),
(62, 7, 'Chocolate Muffin', 12, 1, 10.80, 10.80, 'Pending'),
(63, 7, 'Croissant', 13, 1, 5.40, 5.40, 'Pending'),
(64, 8, 'Spinach and Artichoke Dip', 5, 1, 9.70, 9.70, 'Pending'),
(65, 8, 'Mozzarella Sticks', 7, 1, 8.70, 8.70, 'Pending'),
(66, 8, 'Shrimp Cocktail', 8, 1, 12.10, 12.10, 'Pending'),
(67, 9, 'Buffalo Wings', 6, 1, 10.70, 10.70, 'Pending'),
(68, 9, 'Shrimp Cocktail', 8, 1, 12.10, 12.10, 'Pending'),
(69, 9, 'Cheesecake', 14, 1, 7.50, 7.50, 'Pending'),
(70, 9, 'Cinnamon Roll', 15, 1, 8.70, 8.70, 'Pending'),
(71, 54, 'Wagyu A5 6oz', 2, 2, 240.00, 480.00, 'Pending'),
(72, 55, 'Sirloin Steak', 4, 4, 27.00, 108.00, 'Pending'),
(73, 55, 'Buffalo Wings', 6, 2, 10.70, 21.40, 'Pending'),
(74, 55, 'Shrimp Cocktail', 8, 4, 12.10, 48.40, 'Pending'),
(75, 55, 'Candy Bars', 9, 2, 1.10, 2.20, 'Pending'),
(76, 55, 'Cookies Oreo', 10, 2, 4.30, 8.60, 'Pending'),
(77, 55, 'Chocolate Muffin', 12, 2, 10.80, 21.60, 'Pending'),
(78, 55, 'Croissant', 13, 2, 5.40, 10.80, 'Pending'),
(79, 55, 'Cheesecake', 14, 2, 7.50, 15.00, 'Pending'),
(80, 55, 'Cinnamon Roll', 15, 2, 5.40, 10.80, 'Pending'),
(81, 55, 'Oreo Milkshake', 17, 6, 5.40, 32.40, 'Pending'),
(82, 55, 'Cappuccino', 23, 2, 10.80, 21.60, 'Pending'),
(83, 55, 'Apple Juice', 25, 2, 4.30, 8.60, 'Pending'),
(84, 55, 'Pineapple Juice', 27, 2, 4.90, 9.80, 'Pending'),
(85, 56, 'Oreo Milkshake', 17, 6, 5.40, 32.40, 'Pending'),
(86, 57, 'Wagyu A5 6oz', 2, 5, 240.00, 1200.00, 'Pending'),
(87, 58, 'Wagyu A5 6oz', 2, 3, 240.00, 720.00, 'Pending'),
(88, 59, 'Wagyu A5 6oz', 2, 3, 240.00, 720.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_item_status`
--

CREATE TABLE `order_item_status` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `status` enum('pending','preparing','finished') NOT NULL DEFAULT 'pending',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method`, `amount_paid`, `payment_date`) VALUES
(1, 7, 'E-Wallet', 17.30, '2025-05-30 20:21:11'),
(2, 8, 'E-Wallet', 30.50, '2025-05-30 20:28:48'),
(3, 9, 'Bank Transfer', 39.00, '2025-05-30 20:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `reservation_datetime` datetime NOT NULL,
  `num_of_people` int(11) NOT NULL,
  `table_number` varchar(10) DEFAULT NULL,
  `seated_at` datetime DEFAULT NULL,
  `special_request` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Arrived','Cancelled','Seated','No Show','Completed') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `customer_name`, `customer_email`, `reservation_datetime`, `num_of_people`, `table_number`, `seated_at`, `special_request`, `status`, `created_at`) VALUES
(1, 'teguh', 'teguh@gmail.com', '2025-06-20 15:52:00', 3, NULL, NULL, 'deket jendela', 'Pending', '2025-06-14 12:53:02'),
(2, 'teguh', 'teguh1@gmail.com', '2025-06-21 13:56:00', 2, NULL, NULL, 'deket kaca', 'Pending', '2025-06-14 12:56:32'),
(3, 'teguh', 'teguh1@gmail.com', '2025-06-21 13:56:00', 2, NULL, NULL, 'deket kaca', 'Pending', '2025-06-14 12:56:54'),
(4, 'teguh', 'teguh1@gmail.com', '2025-06-21 13:56:00', 2, NULL, NULL, 'deket kaca', 'Pending', '2025-06-14 12:59:19'),
(5, 'soso', 'soso@gmail.com', '2025-06-14 16:01:00', 2, '2', NULL, 'deket jendela', '', '2025-06-14 13:01:42'),
(6, 'soso', 'soso@gmail.com', '2025-06-19 20:31:00', 3, NULL, NULL, 'deket pintu', 'Pending', '2025-06-14 13:28:45'),
(7, 'soso', 'soso@gmail.com', '2025-06-14 20:39:00', 5, '5', NULL, '', '', '2025-06-14 13:40:08'),
(8, 'soso', 'soso@gmail.com', '2025-06-14 21:07:00', 1, '1', NULL, '', '', '2025-06-14 14:07:50'),
(9, 'deaa', 'dea@gmail.com', '2025-06-14 21:13:00', 2, '10000', NULL, '', '', '2025-06-14 14:13:42'),
(10, 'deaa', 'dea@gmail.com', '2025-06-14 00:14:00', 1, '10000', NULL, '', '', '2025-06-14 14:14:21'),
(11, 'deaa', 'dea@gmail.com', '2025-06-14 00:14:00', 2, '2', NULL, '', '', '2025-06-14 14:14:35'),
(12, 'deaa', 'dea@gmail.com', '2025-06-07 21:22:00', 1, NULL, NULL, '', 'Pending', '2025-06-14 14:22:31'),
(13, 'soso', 'soso@gmail.com', '2025-06-11 21:23:00', 2, NULL, NULL, '', 'Pending', '2025-06-14 14:23:10'),
(14, 'soso', 'soso@gmail.com', '2025-06-14 22:26:00', 1, '1', NULL, '', '', '2025-06-14 14:26:21'),
(15, 'dean', 'dean@gmail.com', '2025-06-16 09:11:00', 3, '3', '2025-06-16 02:54:41', '', 'Arrived', '2025-06-16 00:12:04'),
(16, 'soso', 'soso@gmail.com', '2025-06-20 09:20:00', 1, '5', NULL, '', 'Pending', '2025-06-16 02:21:01');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `table_id` int(11) NOT NULL,
  `table_number` varchar(10) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('available','reserved') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`table_id`, `table_number`, `capacity`, `status`) VALUES
(1, '1', 2, 'available'),
(2, '2', 4, 'available'),
(3, '3', 6, 'reserved'),
(4, '4', 2, 'available'),
(5, '5', 5, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user','kasir','manajer','kitchen','waiters') NOT NULL DEFAULT 'user',
  `member_id` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `created_at`, `role`, `member_id`, `profile_picture`) VALUES
(1, 'akbar ganteng bgt cihuy', 'akbargans@gmail.com', '$2y$10$5L263n0My.9Qaytf5C1lD.wuVpIsoUObga.epwY5KSTPVhhd7QiGi', '08654321891256', '2025-05-29 18:48:39', 'user', 'LP-2025-0001', NULL),
(2, 'teguh suandi', 'teguhcibinong@gmail.com', '$2y$10$GA5NwmFeaQjltzjgwXsAoO7LEVE0Pmqvhzrk5TbSv.z5tuyjHTe2S', '0812345457512', '2025-05-30 13:27:37', 'user', 'LP-2025-0002', NULL),
(3, 'soso', 'soso@gmail.com', '$2y$10$vMzbtcUr794no6EEE6JzyuLEJTHvQqw9HBMULwi/u8qZ2rFxybmz.', '081215432981', '2025-06-01 06:14:35', 'user', 'LP-2025-0003', 'images/upload/6847b5ccc68c0.jpeg'),
(4, 'Mikael Gatot (Gagah dan beroTot)', 'mikaeldepok@gmail.com', '$2y$10$AUmPmAjuiwJtLsgm7BG0i.Ycbr.RwFFdEhCvulQcbQX0ou3naY61m', '089765432341', '2025-06-01 10:17:48', 'user', 'LP-2025-0004', NULL),
(7, 'Admin LAMPERIE', 'admin@lamperie.com', '$2y$10$or4rObEtAhnPyiSbzbt6KeLF5eTJvG5hbFJbTuLYVNA3eh0xnKXjq', '081234567890', '2025-06-01 17:44:58', 'admin', 'LP-2025-0007', NULL),
(8, 'deaa', 'dea@gmail.com', '$2y$10$.W8/.9YKqUVJar0zYhWBTe6SlYcbvgUhv/wInnAdVueX3VTLwb6.O', '0857123', '2025-06-05 08:06:53', 'user', 'LP-2025-0008', 'images/upload/684247ca4ed22.jpg'),
(9, 'kasir', 'kasir@lamperie.com', '$2y$10$ObKhI6zg1t6Slm3Udb/EUeAjp0ZWRI3Vypt1nlTmIiPZlNtInsc7.', '081215432981', '2025-06-08 11:08:02', 'kasir', 'LP-2025-0009', NULL),
(10, 'arifin', 'teguh@gmail.com', '$2y$10$kgRbqw2Osopo/nc.s5Rum.IFzLWV0ODJ1eaZhYRwNe8eSU6bvycCS', '081215432981', '2025-06-09 12:20:44', 'user', 'LP-2025-0010', NULL),
(11, 'Manajer LAMPERIE', 'manajer@lamperie.com', '$2y$10$YZJFOkoj0fK0V9VtqwAAWetAXkpp4wRzSXiytW4irWu.MmktPjUu2', '081212323209', '2025-06-13 20:40:58', 'manajer', 'LP-2025-0011', NULL),
(14, 'kitchen', 'kitchen@lamperie.com', '$2y$10$xmDb3glRHisBzIEXIt3UluiP3z1rpR83qcPp9onlt3vz1rHtkWIRG', '0921391', '2025-06-14 11:50:41', 'kitchen', 'LP-2025-0012', NULL),
(15, 'waiters', 'waiters@lamperie.com', '$2y$10$awt8QuyxSCF0Qi9dmc4zheqDt1AAR.dWn9KfFfWBI.IEBqW6kpQyK', '02141204', '2025-06-14 12:06:10', 'waiters', 'LP-2025-0013', NULL),
(16, 'faldy', 'faldy@gmail.com', '$2y$10$qa6ffViOf156AiXvQCYEn.JqRBZaglv6i0dX77LjT88CySXVUUqQ6', '08123113', '2025-06-15 14:48:12', 'user', 'LP-2025-0016', NULL);

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
  ADD PRIMARY KEY (`table_id`),
  ADD UNIQUE KEY `table_number` (`table_number`),
  ADD UNIQUE KEY `table_number_2` (`table_number`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `menu_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `order_item_status`
--
ALTER TABLE `order_item_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
