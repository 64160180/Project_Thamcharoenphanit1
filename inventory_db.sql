-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Nov 06, 2024 at 11:32 AM
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
-- Table structure for table `tbl_event`
--

CREATE TABLE `tbl_event` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_event`
--

INSERT INTO `tbl_event` (`id`, `title`, `start`, `end`, `end_date`) VALUES
(0, 'ป้อง', '2024-11-01 00:00:00', '0000-00-00 00:00:00', NULL);

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
(1, 'admin@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'นาย', 'เกรียงไกร', 'ธนูธรรม', 'admin', '2024-09-16 15:09:35'),
(2, 'user@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'นาย', 'ปัณณทัต', 'ธนูธรรม', 'user', '2024-09-16 15:10:21'),
(8, 'admin4@gmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'นาง', 'yada', 'sss', 'admin', '2024-10-30 18:03:33');

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
(65, 'jj', 30.00, 50.00, 100, '2024-11-05 11:07:27'),
(66, 'jj', 30.00, 50.00, 1, '2024-11-06 10:18:03'),
(67, 'pong_phannahat', 50.00, 100.00, 20, '2024-11-06 11:32:19');

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
(14, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, '2024-09-21 08:35:18', 1),
(15, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, '2024-09-21 08:38:45', 1),
(16, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, '2024-09-21 08:49:27', 3),
(18, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, '2024-09-21 09:05:57', 2),
(27, 14, 'ท่อ PVC 1*8.5', 65.00, 80.00, '2024-09-23 15:40:12', 1),
(28, 21, 'ท่อPVC 1/4*5', 57.00, 100.00, '2024-09-23 15:40:12', 1),
(29, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, '2024-09-24 14:13:57', 1),
(30, 33, 'ข้อต่อตรงเกลียวใน 1 1/2', 48.00, 80.00, '2024-09-24 14:13:57', 1),
(32, 31, 'ข้อต่อตรงเกลียวใน 3/4', 40.00, 50.00, '2024-09-24 14:26:03', 5),
(35, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, '2024-09-24 14:27:49', 1),
(36, 27, 'ท่อ PVC 3/4*13.5', 60.00, 80.00, '2024-09-24 14:27:49', 1),
(37, 28, 'ข้อต่อตรงเกลียวใน ท/ล 1/2', 24.00, 40.00, '2024-09-24 14:27:49', 1),
(38, 29, 'ข้องอ 90 เกลียวใน ท/ล 1/2', 38.00, 50.00, '2024-09-24 14:27:49', 1),
(39, 31, 'ข้อต่อตรงเกลียวใน 3/4', 40.00, 50.00, '2024-09-24 14:27:49', 5),
(40, 32, 'ข้องอเกลียวใน 3/4', 43.00, 65.00, '2024-09-24 14:27:49', 5),
(41, 33, 'ข้อต่อตรงเกลียวใน 1 1/2', 48.00, 80.00, '2024-09-24 14:27:49', 3),
(43, 35, 'ข้อต่อตรง1/2', 2.00, 6.00, '2024-09-24 14:27:49', 20),
(44, 37, 'ข้อต่อตรงเกลียวนอก 1/2', 3.00, 8.00, '2024-09-24 14:27:49', 10),
(45, 38, 'ข้องอ 90 1/2', 3.00, 7.00, '2024-09-24 14:27:49', 20),
(49, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, '2024-09-25 13:58:54', 7),
(54, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, '2024-09-28 08:34:31', 1),
(55, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, '2024-09-28 08:46:33', 1),
(56, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, '2024-09-28 09:01:07', 1),
(101, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, '2024-10-04 15:20:27', 1),
(102, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, '2024-10-04 15:25:36', 1),
(106, 35, 'ข้อต่อตรง1/2', 2.00, 6.00, '2024-10-09 07:19:08', 5),
(111, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, '2024-10-17 08:56:10', 1),
(112, 31, 'ข้อต่อตรงเกลียวใน 3/4', 40.00, 50.00, '2024-10-17 08:56:10', 5),
(113, 35, 'ข้อต่อตรง1/2', 2.00, 6.00, '2024-10-17 08:56:10', 10),
(117, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, '2024-10-18 18:36:35', 1),
(121, 43, 'สามทางเกลียวใน 1/2', 10.00, 15.00, '2024-10-19 15:07:32', 1),
(127, 6, 'ท่อPVC 1/2*8.5', 39.00, 55.00, '2024-10-20 16:11:36', 1),
(128, 31, 'ข้อต่อตรงเกลียวใน 3/4', 40.00, 50.00, '2024-10-21 09:27:11', 5),
(129, 33, 'ข้อต่อตรงเกลียวใน 1 1/2', 48.00, 80.00, '2024-10-21 09:27:11', 1),
(130, 6, 'ท่อPVC 1/2*8.5', 39.00, 55.00, '2024-10-21 13:48:57', 1),
(131, 9, 'ท่อ PVC 1/2*13.5', 49.00, 65.00, '2024-10-21 13:53:26', 1),
(134, 14, 'ท่อ PVC 1*8.5', 65.00, 80.00, '2024-10-22 15:10:46', 11),
(135, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, '2024-10-22 17:16:13', 1),
(136, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, '2024-10-27 17:50:22', 1),
(137, 6, 'ท่อPVC 1/2*8.5', 39.00, 55.00, '2024-10-30 10:44:47', 1),
(138, 6, 'ท่อPVC 1/2*8.5', 39.00, 55.00, '2024-10-30 17:37:50', 1),
(140, 25, 'ท่อ PVC 4*5', 280.00, 380.00, '2024-11-02 12:27:48', 1),
(141, 6, 'ท่อPVC 1/2*8.5', 39.00, 55.00, '2024-11-02 14:48:45', 2),
(143, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, '2024-11-02 17:35:24', 1),
(153, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, '2024-11-03 16:04:16', 1),
(172, 61, 'jj', 30.00, 50.00, '2024-11-06 10:17:11', 1);

--
-- Triggers `tbl_order`
--
DELIMITER $$
CREATE TRIGGER `after_insert_tbl_order` AFTER INSERT ON `tbl_order` FOR EACH ROW BEGIN
    -- ตรวจสอบว่าข้อมูลที่เพิ่มใหม่มี quantity มากกว่า 0 หรือไม่
    IF NEW.quantity > 0 THEN
        -- เพิ่มข้อมูลใหม่ลงใน tbl_order_eoq
        INSERT INTO tbl_order_eoq (product_id, product_name, cost_price, sell_price, quantity)
        VALUES (NEW.product_id, NEW.product_name, NEW.cost_price, NEW.sell_price, NEW.quantity);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_order_eoq`
--

CREATE TABLE `tbl_order_eoq` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `sell_price` decimal(10,2) DEFAULT NULL,
  `quantity` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_order_eoq`
--

INSERT INTO `tbl_order_eoq` (`id`, `product_id`, `product_name`, `cost_price`, `sell_price`, `quantity`) VALUES
(2, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, 1),
(3, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, 1),
(4, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, 3),
(5, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, 2),
(6, 14, 'ท่อ PVC 1*8.5', 65.00, 80.00, 1),
(7, 21, 'ท่อPVC 1/4*5', 57.00, 100.00, 1),
(8, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, 1),
(9, 33, 'ข้อต่อตรงเกลียวใน 1 1/2', 48.00, 80.00, 1),
(10, 31, 'ข้อต่อตรงเกลียวใน 3/4', 40.00, 50.00, 5),
(11, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, 1),
(12, 27, 'ท่อ PVC 3/4*13.5', 60.00, 80.00, 1),
(13, 28, 'ข้อต่อตรงเกลียวใน ท/ล 1/2', 24.00, 40.00, 1),
(14, 29, 'ข้องอ 90 เกลียวใน ท/ล 1/2', 38.00, 50.00, 1),
(15, 31, 'ข้อต่อตรงเกลียวใน 3/4', 40.00, 50.00, 5),
(16, 32, 'ข้องอเกลียวใน 3/4', 43.00, 65.00, 5),
(17, 33, 'ข้อต่อตรงเกลียวใน 1 1/2', 48.00, 80.00, 3),
(18, 35, 'ข้อต่อตรง1/2', 2.00, 6.00, 20),
(19, 37, 'ข้อต่อตรงเกลียวนอก 1/2', 3.00, 8.00, 10),
(20, 38, 'ข้องอ 90 1/2', 3.00, 7.00, 20),
(21, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, 7),
(22, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, 1),
(23, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, 1),
(24, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, 1),
(25, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, 1),
(26, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, 1),
(27, 35, 'ข้อต่อตรง1/2', 2.00, 6.00, 5),
(28, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, 1),
(29, 31, 'ข้อต่อตรงเกลียวใน 3/4', 40.00, 50.00, 5),
(30, 35, 'ข้อต่อตรง1/2', 2.00, 6.00, 10),
(31, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, 1),
(32, 43, 'สามทางเกลียวใน 1/2', 10.00, 15.00, 1),
(33, 6, 'ท่อPVC 1/2*8.5', 39.00, 55.00, 1),
(34, 31, 'ข้อต่อตรงเกลียวใน 3/4', 40.00, 50.00, 5),
(35, 33, 'ข้อต่อตรงเกลียวใน 1 1/2', 48.00, 80.00, 1),
(36, 6, 'ท่อPVC 1/2*8.5', 39.00, 55.00, 1),
(37, 9, 'ท่อ PVC 1/2*13.5', 49.00, 65.00, 1),
(38, 14, 'ท่อ PVC 1*8.5', 65.00, 80.00, 11),
(39, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, 1),
(40, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, 1),
(41, 6, 'ท่อPVC 1/2*8.5', 39.00, 55.00, 1),
(42, 6, 'ท่อPVC 1/2*8.5', 39.00, 55.00, 1),
(43, 25, 'ท่อ PVC 4*5', 280.00, 380.00, 1),
(44, 6, 'ท่อPVC 1/2*8.5', 39.00, 55.00, 2),
(45, 45, 'หลอดไฟ Panasonic LED', 60.00, 120.00, 1),
(46, 26, 'ท่อ PVC 3/4*8.5', 49.00, 65.00, 1),
(73, 61, 'jj', 30.00, 50.00, 1);

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
  `dateCrate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_product`
--

INSERT INTO `tbl_product` (`id`, `ref_type_id`, `product_name`, `product_qty`, `cost_price`, `product_price`, `product_image`, `dateCrate`) VALUES
(6, 3, 'ท่อPVC 1/2*8.5', 40, 45.00, 60.00, '185429536220240818_180904.jpg', '2024-08-17 18:46:11'),
(9, 3, 'ท่อ PVC 1/2*13.5', 18, 49.00, 65.00, '85219110820240909_103103.jpg', '2024-09-09 08:31:03'),
(14, 3, 'ท่อ PVC 1*8.5', 19, 65.00, 80.00, '71642284620240909_113413.jpg', '2024-09-09 09:34:13'),
(15, 3, 'ท่อ PVC 1*13.5', 20, 97.00, 120.00, '167399066820240909_113452.jpg', '2024-09-09 09:34:52'),
(21, 3, 'ท่อPVC 1/4*5', 10, 57.00, 100.00, '212399322420240915_105117.jpg', '2024-09-15 08:51:17'),
(22, 3, 'ท่อPVC 1/2*5', 10, 53.00, 120.00, '198412970320240915_105139.jpg', '2024-09-15 08:51:39'),
(23, 3, 'ท่อ PVC 2*5', 10, 79.00, 130.00, '147745388920240915_105411.jpg', '2024-09-15 08:54:11'),
(24, 3, 'ท่อ PVC 3*5', 10, 105.00, 270.00, '78095768220240915_105649.jpg', '2024-09-15 08:56:49'),
(25, 3, 'ท่อ PVC 4*5', 15, 28.00, 38.00, '165130528620240915_105716.jpg', '2024-09-15 08:57:16'),
(26, 3, 'ท่อ PVC 3/4*8.5', 12, 49.00, 65.00, '192995548920240915_105821.jpg', '2024-09-15 08:58:21'),
(27, 3, 'ท่อ PVC 3/4*13.5', 24, 60.00, 80.00, '156858420120240915_105851.jpg', '2024-09-15 08:58:51'),
(28, 1, 'ข้อต่อตรงเกลียวใน ท/ล 1/2', 69, 24.00, 40.00, '7998771720240915_113550.jpg', '2024-09-15 09:02:16'),
(29, 1, 'ข้องอ 90 เกลียวใน ท/ล 1/2', 79, 38.00, 50.00, '24860394420240915_113638.jpg', '2024-09-15 09:03:37'),
(30, 1, 'สามทางเกลียวใน ท/ล 1/2', 51, 45.00, 60.00, '61317957620240915_113737.jpg', '2024-09-15 09:05:11'),
(31, 1, 'ข้อต่อตรงเกลียวใน 3/4', 70, 40.00, 50.00, '63756312220240915_110626.jpg', '2024-09-15 09:06:26'),
(32, 1, 'ข้องอเกลียวใน 3/4', 55, 43.00, 65.00, '166174911520240915_110810.jpg', '2024-09-15 09:08:10'),
(33, 1, 'ข้อต่อตรงเกลียวใน 1 1/2', 45, 48.00, 80.00, '15698737920240915_112611.jpg', '2024-09-15 09:26:11'),
(34, 1, 'ข้องอ90 เกลียวใน 1 1/2', 28, 69.00, 100.00, '204229060420240915_112759.jpg', '2024-09-15 09:27:59'),
(35, 1, 'ข้อต่อตรง1/2', 185, 2.00, 6.00, '121039633620240915_112952.png', '2024-09-15 09:29:52'),
(36, 1, 'ข้อต่อตรงเกลียวใน 1/2', 100, 3.00, 8.00, '100588976320240915_113840.png', '2024-09-15 09:38:40'),
(37, 1, 'ข้อต่อตรงเกลียวนอก 1/2', 190, 3.00, 8.00, '130428159420240915_113943.jpg', '2024-09-15 09:39:43'),
(38, 1, 'ข้องอ 90 1/2', 160, 3.00, 7.00, '181297205620240915_114052.png', '2024-09-15 09:40:52'),
(39, 1, 'ข้องอ 45 1/2', 180, 4.00, 8.00, '197253724020240915_114142.jpg', '2024-09-15 09:41:42'),
(40, 1, 'ข้องอเกลียวใน 1/2', 100, 6.00, 10.00, '211838488020240915_114304.png', '2024-09-15 09:43:04'),
(41, 1, 'ข้องอเกลียวนอก 1/2', 180, 7.00, 12.00, '16265035520240915_114356.jpg', '2024-09-15 09:43:56'),
(42, 1, 'สามทาง 1/2', 100, 5.00, 10.00, '179778774220240915_114458.jpg', '2024-09-15 09:44:58'),
(43, 1, 'สามทางเกลียวใน 1/2', 54, 10.00, 15.00, '46252919220240915_114543.jpg', '2024-09-15 09:45:43'),
(45, 4, 'หลอดไฟ Panasonic LED', 10, 60.00, 120.00, '139936493620240918_182919.jpg', '2024-09-18 16:21:10'),
(61, 14, 'jj', 100, 30.00, 50.00, '156230341020241106_104927.jpg', '2024-11-04 11:29:57'),
(64, 16, 'pong_phannahat', 20, 50.00, 100.00, '165539830020241106_113202.jpg', '2024-11-06 11:31:47');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_type`
--

CREATE TABLE `tbl_type` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(150) NOT NULL,
  `type_minimum` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_type`
--

INSERT INTO `tbl_type` (`type_id`, `type_name`, `type_minimum`) VALUES
(1, 'อุปกรณ์ต่อปะปา', 30),
(2, 'สีน้ำมัน', 15),
(3, 'ท่อ', 10),
(4, 'หลอดไฟ', 10),
(9, 'น้ำมันเครื่อง', 5),
(11, 'ป้อง', 10),
(14, 'yada', 12),
(16, 'pong', 10);

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
-- Indexes for table `tbl_order_eoq`
--
ALTER TABLE `tbl_order_eoq`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `tbl_order`
--
ALTER TABLE `tbl_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT for table `tbl_order_eoq`
--
ALTER TABLE `tbl_order_eoq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `tbl_product`
--
ALTER TABLE `tbl_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `tbl_type`
--
ALTER TABLE `tbl_type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_order`
--
ALTER TABLE `tbl_order`
  ADD CONSTRAINT `tbl_order_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `tbl_product` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_order_eoq`
--
ALTER TABLE `tbl_order_eoq`
  ADD CONSTRAINT `tbl_order_eoq_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tbl_order` (`product_id`);

--
-- Constraints for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD CONSTRAINT `tbl_product_ibfk_1` FOREIGN KEY (`ref_type_id`) REFERENCES `tbl_type` (`type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
