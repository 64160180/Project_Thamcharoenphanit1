-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Nov 21, 2024 at 05:34 PM
-- Server version: 5.7.44
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_member`
--

CREATE TABLE `tbl_member` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `title_name` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `role` varchar(10) NOT NULL COMMENT 'admin, user',
  `dateCreate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_member`
--

INSERT INTO `tbl_member` (`id`, `username`, `password`, `title_name`, `name`, `surname`, `role`, `dateCreate`) VALUES
(1, 'admin@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'นาย', 'เกรียงไกร', 'ธนูธรรม', 'admin', '2024-09-16 08:09:35'),
(2, 'user@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'นาย', 'ปัณณทัต', 'ธนูธรรม', 'user', '2024-09-16 08:10:21'),
(8, 'admin4@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'นาง', 'yada', 'sss', 'admin', '2024-10-30 11:03:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_newproduct`
--

CREATE TABLE `tbl_newproduct` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `newproduct_name` varchar(200) NOT NULL,
  `newcost_price` decimal(10,2) NOT NULL,
  `newproduct_price` decimal(10,2) NOT NULL,
  `newproduct_qty` int(3) NOT NULL,
  `dateCreate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_newproduct`
--

INSERT INTO `tbl_newproduct` (`id`, `product_id`, `newproduct_name`, `newcost_price`, `newproduct_price`, `newproduct_qty`, `dateCreate`) VALUES
(128, NULL, 'คอม', 100.00, 120.00, 10, '2024-11-21 14:28:32'),
(129, NULL, 'คอม', 90.00, 120.00, 10, '2024-11-21 14:29:06'),
(130, NULL, 'คอม', 120.00, 150.00, 10, '2024-11-21 14:38:01'),
(131, NULL, 'com', 1000.00, 1500.00, 10, '2024-11-21 14:57:38'),
(132, NULL, 'com', 1200.00, 1800.00, 10, '2024-11-21 14:58:08'),
(133, NULL, 'com', 900.00, 1500.00, 10, '2024-11-21 15:59:09');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_order`
--

CREATE TABLE `tbl_order` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `sell_price` decimal(10,2) DEFAULT NULL,
  `date_out` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `quantity` int(5) NOT NULL,
  `historical_cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_order`
--

INSERT INTO `tbl_order` (`id`, `product_id`, `product_name`, `cost_price`, `sell_price`, `date_out`, `quantity`, `historical_cost`) VALUES
(251, 89, 'คอม', 100.00, 120.00, '2024-11-21 14:28:50', 1, 100.00),
(252, 89, 'คอม', 90.00, 120.00, '2024-11-21 14:29:18', 1, 95.00),
(253, 89, 'คอม', 120.00, 150.00, '2024-11-21 14:38:07', 1, 103.33),
(254, 90, 'com', 1000.00, 1500.00, '2024-11-21 14:57:47', 1, 1000.00),
(255, 90, 'com', 1200.00, 1800.00, '2024-11-21 14:58:23', 1, 1100.00),
(256, 90, 'com', 900.00, 1500.00, '2024-11-21 15:59:16', 1, 1033.33);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

CREATE TABLE `tbl_product` (
  `id` int(11) NOT NULL,
  `ref_type_id` int(11) NOT NULL COMMENT 'tbl_type type_id',
  `product_name` varchar(200) NOT NULL,
  `product_qty` int(3) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_image` varchar(100) NOT NULL,
  `dateCrate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `newproduct_id` int(11) DEFAULT NULL,
  `product_minimum` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_product`
--

INSERT INTO `tbl_product` (`id`, `ref_type_id`, `product_name`, `product_qty`, `cost_price`, `product_price`, `product_image`, `dateCrate`, `newproduct_id`, `product_minimum`) VALUES
(6, 3, 'ท่อPVC 1/2*8.5', 39, 45.00, 60.00, '185429536220240818_180904.jpg', '2024-08-17 11:46:11', NULL, 20),
(9, 3, 'ท่อ PVC 1/2*13.5', 18, 49.00, 65.00, '85219110820240909_103103.jpg', '2024-09-09 01:31:03', NULL, 10),
(14, 3, 'ท่อ PVC 1*8.5', 19, 65.00, 80.00, '71642284620240909_113413.jpg', '2024-09-09 02:34:13', NULL, 10),
(15, 3, 'ท่อ PVC 1*13.5', 20, 97.00, 120.00, '167399066820240909_113452.jpg', '2024-09-09 02:34:52', NULL, 10),
(21, 3, 'ท่อPVC 1/4*5', 10, 57.00, 100.00, '212399322420240915_105117.jpg', '2024-09-15 01:51:17', NULL, 10),
(22, 3, 'ท่อPVC 1/2*5', 10, 53.00, 120.00, '198412970320240915_105139.jpg', '2024-09-15 01:51:39', NULL, 10),
(23, 3, 'ท่อ PVC 2*5', 10, 79.00, 130.00, '147745388920240915_105411.jpg', '2024-09-15 01:54:11', NULL, 10),
(24, 3, 'ท่อ PVC 3*5', 10, 105.00, 270.00, '78095768220240915_105649.jpg', '2024-09-15 01:56:49', NULL, 10),
(25, 3, 'ท่อ PVC 4*5', 15, 28.00, 38.00, '165130528620240915_105716.jpg', '2024-09-15 01:57:16', NULL, 10),
(26, 3, 'ท่อ PVC 3/4*8.5', 11, 49.00, 65.00, '192995548920240915_105821.jpg', '2024-09-15 01:58:21', NULL, 10),
(27, 3, 'ท่อ PVC 3/4*13.5', 24, 60.00, 80.00, '156858420120240915_105851.jpg', '2024-09-15 01:58:51', NULL, 10),
(28, 1, 'ข้อต่อตรงเกลียวใน ท/ล 1/2', 69, 24.00, 40.00, '7998771720240915_113550.jpg', '2024-09-15 02:02:16', NULL, 50),
(29, 1, 'ข้องอ 90 เกลียวใน ท/ล 1/2', 79, 38.00, 50.00, '24860394420240915_113638.jpg', '2024-09-15 02:03:37', NULL, 50),
(30, 1, 'สามทางเกลียวใน ท/ล 1/2', 51, 45.00, 60.00, '61317957620240915_113737.jpg', '2024-09-15 02:05:11', NULL, 50),
(31, 1, 'ข้อต่อตรงเกลียวใน 3/4', 70, 40.00, 50.00, '63756312220240915_110626.jpg', '2024-09-15 02:06:26', NULL, 50),
(32, 1, 'ข้องอเกลียวใน 3/4', 55, 43.00, 65.00, '166174911520240915_110810.jpg', '2024-09-15 02:08:10', NULL, 50),
(33, 1, 'ข้อต่อตรงเกลียวใน 1 1/2', 145, 70.00, 100.00, '15698737920240915_112611.jpg', '2024-09-15 02:26:11', NULL, 50),
(34, 1, 'ข้องอ90 เกลียวใน 1 1/2', 78, 69.00, 100.00, '204229060420240915_112759.jpg', '2024-09-15 02:27:59', NULL, 50),
(35, 1, 'ข้อต่อตรง1/2', 185, 2.00, 6.00, '121039633620240915_112952.png', '2024-09-15 02:29:52', NULL, 50),
(36, 1, 'ข้อต่อตรงเกลียวใน 1/2', 100, 3.00, 8.00, '100588976320240915_113840.png', '2024-09-15 02:38:40', NULL, 50),
(37, 1, 'ข้อต่อตรงเกลียวนอก 1/2', 190, 3.00, 8.00, '130428159420240915_113943.jpg', '2024-09-15 02:39:43', NULL, 50),
(38, 1, 'ข้องอ 90 1/2', 160, 3.00, 7.00, '181297205620240915_114052.png', '2024-09-15 02:40:52', NULL, 50),
(39, 1, 'ข้องอ 45 1/2', 180, 4.00, 8.00, '197253724020240915_114142.jpg', '2024-09-15 02:41:42', NULL, 50),
(40, 1, 'ข้องอเกลียวใน 1/2', 100, 6.00, 10.00, '211838488020240915_114304.png', '2024-09-15 02:43:04', NULL, 50),
(41, 1, 'ข้องอเกลียวนอก 1/2', 180, 7.00, 12.00, '16265035520240915_114356.jpg', '2024-09-15 02:43:56', NULL, 50),
(42, 1, 'สามทาง 1/2', 100, 5.00, 10.00, '179778774220240915_114458.jpg', '2024-09-15 02:44:58', NULL, 50),
(43, 1, 'สามทางเกลียวใน 1/2', 54, 10.00, 15.00, '46252919220240915_114543.jpg', '2024-09-15 02:45:43', NULL, 50),
(45, 4, 'หลอดไฟ Panasonic LED', 9, 69.00, 120.00, '139936493620240918_182919.jpg', '2024-09-18 09:21:10', NULL, 10),
(67, 17, 'โค้ก 1.5 ลิตร', 8, 15.00, 40.00, '147692499620241108_120258.png', '2024-11-08 05:02:58', NULL, 5),
(68, 17, 'น้ำแดง', 17, 12.00, 15.00, '30618203720241108_120309.png', '2024-11-08 05:03:09', NULL, 5),
(70, 17, 'น้ำเขียว', 4, 15.00, 25.00, '209104978820241110_152627.png', '2024-11-10 15:26:27', NULL, 5),
(89, 18, 'คอม', 27, 120.00, 150.00, '111650816520241121_142820.png', '2024-11-21 14:28:20', NULL, 10),
(90, 18, 'com', 27, 900.00, 1500.00, '28327337120241121_145713.jpg', '2024-11-21 14:57:13', NULL, 10);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_type`
--

CREATE TABLE `tbl_type` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_type`
--

INSERT INTO `tbl_type` (`type_id`, `type_name`) VALUES
(18, 'IT'),
(16, 'pong'),
(14, 'yada'),
(3, 'ท่อ'),
(9, 'น้ำมันเครื่อง'),
(17, 'น้ำอัดลม'),
(11, 'ป้อง'),
(2, 'สีน้ำมัน'),
(4, 'หลอดไฟ'),
(1, 'อุปกรณ์ต่อปะปา');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_member`
--
ALTER TABLE `tbl_member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `tbl_newproduct`
--
ALTER TABLE `tbl_newproduct`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tbl_order`
--
ALTER TABLE `tbl_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ref_type_id` (`ref_type_id`);

--
-- Indexes for table `tbl_type`
--
ALTER TABLE `tbl_type`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_member`
--
ALTER TABLE `tbl_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_newproduct`
--
ALTER TABLE `tbl_newproduct`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `tbl_order`
--
ALTER TABLE `tbl_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT for table `tbl_product`
--
ALTER TABLE `tbl_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `tbl_type`
--
ALTER TABLE `tbl_type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_newproduct`
--
ALTER TABLE `tbl_newproduct`
  ADD CONSTRAINT `tbl_newproduct_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `tbl_product` (`id`);

--
-- Constraints for table `tbl_order`
--
ALTER TABLE `tbl_order`
  ADD CONSTRAINT `tbl_order_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `tbl_product` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD CONSTRAINT `tbl_product_ibfk_1` FOREIGN KEY (`ref_type_id`) REFERENCES `tbl_type` (`type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
