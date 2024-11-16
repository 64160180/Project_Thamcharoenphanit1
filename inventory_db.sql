-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Nov 16, 2024 at 07:38 AM
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
  `newproduct_name` varchar(200) NOT NULL,
  `newcost_price` decimal(10,2) NOT NULL,
  `newproduct_price` decimal(10,2) NOT NULL,
  `newproduct_qty` int(3) NOT NULL,
  `dateCreate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_newproduct`
--

INSERT INTO `tbl_newproduct` (`id`, `newproduct_name`, `newcost_price`, `newproduct_price`, `newproduct_qty`, `dateCreate`) VALUES
(73, 'โค้ก 1.5 ลิตร', 20.00, 42.00, 12, '2024-11-08 05:03:41'),
(74, 'น้ำแดง', 12.00, 15.00, 24, '2024-11-08 05:04:35'),
(75, 'ข้อต่อตรงเกลียวใน 1 1/2', 70.00, 100.00, 100, '2024-11-08 05:06:51'),
(76, 'ข้องอ90 เกลียวใน 1 1/2', 69.00, 100.00, 100, '2024-11-08 05:07:59'),
(77, 'pong', 1.00, 101.00, 201, '2024-11-09 07:12:39'),
(78, 'หลอดไฟ Panasonic LED', 69.00, 120.00, 10, '2024-11-09 08:03:38'),
(79, 'น้ำแดง1', 23.00, 40.00, 2, '2024-11-09 13:22:13'),
(80, 'น้ำเขียว', 30.00, 50.00, 10, '2024-11-09 13:25:54'),
(81, 'น้ำเขียว', 15.00, 25.00, 24, '2024-11-10 15:26:52'),
(85, 'sff', 20.00, 30.00, 5, '2024-11-14 07:05:57'),
(86, 'iphone', 2000.00, 4000.00, 10, '2024-11-14 08:41:14'),
(87, 'iphone', 2500.00, 4500.00, 10, '2024-11-15 09:04:13'),
(88, 'iphone', 2000.00, 4500.00, 2, '2024-11-16 07:08:43');

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
  `quantity` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_order`
--

INSERT INTO `tbl_order` (`id`, `product_id`, `product_name`, `cost_price`, `sell_price`, `date_out`, `quantity`) VALUES
(176, 67, 'โค้ก 1.5 ลิตร', 20.00, 42.00, '2024-11-08 05:04:49', 5),
(177, 68, 'น้ำแดง', 12.00, 15.00, '2024-11-08 05:05:03', 6),
(178, 34, 'ข้องอ90 เกลียวใน 1 1/2', 69.00, 100.00, '2024-11-08 05:08:19', 50),
(179, 68, 'น้ำแดง', 12.00, 15.00, '2024-11-08 20:43:11', 1),
(180, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, '2024-11-09 07:10:44', 1),
(181, 69, 'pong', 1.00, 101.00, '2024-11-09 07:12:57', 10),
(183, 69, 'pong', 1.00, 101.00, '2024-11-10 15:25:36', 3),
(184, 69, 'pong', 1.00, 101.00, '2024-11-11 12:40:32', 1),
(185, 69, 'pong', 1.00, 101.00, '2024-11-11 12:40:51', 10),
(186, 70, 'น้ำเขียว', 15.00, 25.00, '2024-11-12 05:42:38', 2),
(187, 69, 'pong', 1.00, 101.00, '2024-11-12 05:49:29', 5),
(188, 70, 'น้ำเขียว', 15.00, 25.00, '2024-11-12 08:51:49', 1),
(189, 70, 'น้ำเขียว', 15.00, 25.00, '2024-11-13 01:52:20', 3),
(190, 69, 'pong', 1.00, 101.00, '2024-11-13 14:30:58', 5),
(193, 70, 'น้ำเขียว', 15.00, 25.00, '2024-11-14 03:03:36', 12),
(194, 70, 'น้ำเขียว', 15.00, 25.00, '2024-11-14 03:03:48', 2),
(195, 45, 'หลอดไฟ Panasonic LED', 69.00, 120.00, '2024-11-14 03:04:48', 10),
(196, 69, 'pong', 1.00, 101.00, '2024-11-14 03:04:48', 1),
(200, 74, 'iphone', 2000.00, 4000.00, '2024-11-14 08:51:44', 2),
(201, 74, 'iphone', 2000.00, 4000.00, '2024-11-14 09:07:45', 3),
(202, 74, 'iphone', 2000.00, 4000.00, '2024-11-14 09:08:18', 1),
(203, 74, 'iphone', 2000.00, 4000.00, '2024-11-15 08:26:23', 2),
(204, 74, 'iphone', 2000.00, 4000.00, '2024-11-15 08:53:20', 1),
(205, 74, 'iphone', 2000.00, 4500.00, '2024-11-16 07:08:54', 2);

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
(6, 3, 'ท่อPVC 1/2*8.5', 40, 45.00, 60.00, '185429536220240818_180904.jpg', '2024-08-17 11:46:11', NULL, 10),
(9, 3, 'ท่อ PVC 1/2*13.5', 18, 49.00, 65.00, '85219110820240909_103103.jpg', '2024-09-09 01:31:03', NULL, 10),
(14, 3, 'ท่อ PVC 1*8.5', 19, 65.00, 80.00, '71642284620240909_113413.jpg', '2024-09-09 02:34:13', NULL, 10),
(15, 3, 'ท่อ PVC 1*13.5', 20, 97.00, 120.00, '167399066820240909_113452.jpg', '2024-09-09 02:34:52', NULL, 10),
(21, 3, 'ท่อPVC 1/4*5', 10, 57.00, 100.00, '212399322420240915_105117.jpg', '2024-09-15 01:51:17', NULL, 10),
(22, 3, 'ท่อPVC 1/2*5', 10, 53.00, 120.00, '198412970320240915_105139.jpg', '2024-09-15 01:51:39', NULL, 10),
(23, 3, 'ท่อ PVC 2*5', 10, 79.00, 130.00, '147745388920240915_105411.jpg', '2024-09-15 01:54:11', NULL, 10),
(24, 3, 'ท่อ PVC 3*5', 10, 105.00, 270.00, '78095768220240915_105649.jpg', '2024-09-15 01:56:49', NULL, 10),
(25, 3, 'ท่อ PVC 4*5', 15, 28.00, 38.00, '165130528620240915_105716.jpg', '2024-09-15 01:57:16', NULL, 10),
(26, 3, 'ท่อ PVC 3/4*8.5', 12, 49.00, 65.00, '192995548920240915_105821.jpg', '2024-09-15 01:58:21', NULL, 10),
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
(67, 17, 'โค้ก 1.5 ลิตร', 7, 20.00, 42.00, '147692499620241108_120258.png', '2024-11-08 05:02:58', NULL, 5),
(68, 17, 'น้ำแดง', 17, 12.00, 15.00, '30618203720241108_120309.png', '2024-11-08 05:03:09', NULL, 5),
(69, 16, 'pong', 165, 1.00, 101.00, '135159715720241109_071125.jpg', '2024-11-09 07:11:25', NULL, 20),
(70, 17, 'น้ำเขียว', 4, 15.00, 25.00, '209104978820241110_152627.png', '2024-11-10 15:26:27', NULL, 5),
(74, 18, 'iphone', 11, 2000.00, 4500.00, '194429683720241114_084058.png', '2024-11-14 08:40:58', NULL, 5);

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
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `ref_type_id` (`ref_type_id`),
  ADD KEY `newproduct_id` (`newproduct_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `tbl_order`
--
ALTER TABLE `tbl_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `tbl_product`
--
ALTER TABLE `tbl_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `tbl_type`
--
ALTER TABLE `tbl_type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_order`
--
ALTER TABLE `tbl_order`
  ADD CONSTRAINT `tbl_order_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `tbl_product` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD CONSTRAINT `tbl_product_ibfk_1` FOREIGN KEY (`ref_type_id`) REFERENCES `tbl_type` (`type_id`),
  ADD CONSTRAINT `tbl_product_ibfk_2` FOREIGN KEY (`newproduct_id`) REFERENCES `tbl_newproduct` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
