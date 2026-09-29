-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2026 at 07:33 AM
-- Server version: 11.8.9-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u424007618_Developers`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us`
--

CREATE TABLE `about_us` (
  `id` int(11) NOT NULL,
  `page_title` varchar(255) NOT NULL DEFAULT 'About Us',
  `tagline` varchar(255) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `intro_content` text DEFAULT NULL,
  `mission` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `years_experience` int(11) DEFAULT NULL,
  `projects_completed` int(11) DEFAULT NULL,
  `cities_covered` int(11) DEFAULT NULL,
  `happy_customers` int(11) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `about_us`
--

INSERT INTO `about_us` (`id`, `page_title`, `tagline`, `banner_image`, `intro_content`, `mission`, `vision`, `years_experience`, `projects_completed`, `cities_covered`, `happy_customers`, `status`, `created_at`, `updated_at`) VALUES
(1, 'About Us', 'India\'s property search, sorted.', NULL, 'Write your company introduction here.', 'Write your mission statement here.', 'Write your vision statement here.', NULL, NULL, NULL, NULL, 'Active', '2026-08-08 09:06:27', '2026-08-08 09:06:27');

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `specialization` varchar(50) NOT NULL,
  `designation` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `locality` varchar(150) DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `listings_count` int(11) DEFAULT 0,
  `rating` decimal(2,1) DEFAULT 0.0,
  `reviews_count` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `agent_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`id`, `name`, `slug`, `photo`, `specialization`, `designation`, `phone`, `email`, `city`, `locality`, `experience_years`, `listings_count`, `rating`, `reviews_count`, `status`, `agent_order`, `created_at`) VALUES
(1, 'Rohit Malhotra', 'rohit-malhotra', NULL, 'buy', 'Senior Property Consultant', '+91 98765 43210', 'rohit.malhotra@example.com', 'Pune', 'Hinjawadi', 8, 42, 4.7, 96, 'Active', 1, '2026-08-28 07:41:42'),
(2, 'Ananya Kapoor', 'ananya-kapoor', NULL, 'buy', 'Residential Sales Expert', '+91 98111 22334', 'ananya.kapoor@example.com', 'Pune', 'Baner', 6, 35, 4.5, 71, 'Active', 2, '2026-08-28 07:41:42'),
(3, 'Vikram Sethi', 'vikram-sethi', NULL, 'rent', 'Rental Specialist', '+91 97654 11223', 'vikram.sethi@example.com', 'Bangalore', 'Whitefield', 5, 51, 4.6, 88, 'Active', 3, '2026-08-28 07:41:42'),
(4, 'Priya Nair', 'priya-nair', NULL, 'rent', 'Leasing Consultant', '+91 96543 22110', 'priya.nair@example.com', 'Bangalore', 'HSR Layout', 4, 29, 4.3, 40, 'Active', 4, '2026-08-28 07:41:42'),
(5, 'Sameer Qureshi', 'sameer-qureshi', NULL, 'vendor', 'Vendor Relations Manager', '+91 95432 33445', 'sameer.qureshi@example.com', 'Mumbai', 'Andheri East', 7, 18, 4.4, 33, 'Active', 5, '2026-08-28 07:41:42'),
(6, 'Neha Bhatnagar', 'neha-bhatnagar', NULL, 'interior', 'Interior Design Consultant', '+91 94321 44556', 'neha.bhatnagar@example.com', 'Pune', 'Kothrud', 9, 24, 4.8, 62, 'Active', 6, '2026-08-28 07:41:42'),
(7, 'Arjun Verma', 'arjun-verma', NULL, 'interior', 'Interior Project Lead', '+91 93210 55667', 'arjun.verma@example.com', 'Delhi', 'Dwarka', 5, 15, 4.2, 21, 'Active', 7, '2026-08-28 07:41:42'),
(8, 'Kavita Reddy', 'kavita-reddy', NULL, 'local_services', 'Local Services Coordinator', '+91 92109 66778', 'kavita.reddy@example.com', 'Hyderabad', 'Gachibowli', 3, 12, 4.1, 18, 'Active', 8, '2026-08-28 07:41:42'),
(9, 'Farhan Ali', 'farhan-ali', NULL, 'hotels_homestays', 'Hospitality Partnerships Manager', '+91 91098 77889', 'farhan.ali@example.com', 'Goa', 'Calangute', 6, 20, 4.6, 55, 'Active', 9, '2026-08-28 07:41:42'),
(10, 'Meera Iyer', 'meera-iyer', NULL, 'hotels_homestays', 'Homestay Onboarding Lead', '+91 90987 88990', 'meera.iyer@example.com', 'Goa', 'Baga', 4, 16, 4.5, 39, 'Active', 10, '2026-08-28 07:41:42'),
(11, 'Deepak Chauhan', 'deepak-chauhan', NULL, 'pg_hostels', 'PG & Hostel Advisor', '+91 89876 99001', 'deepak.chauhan@example.com', 'Pune', 'Viman Nagar', 4, 27, 4.3, 44, 'Active', 11, '2026-08-28 07:41:42'),
(12, 'Simran Kaur', 'simran-kaur', NULL, 'pg_hostels', 'Student Housing Specialist', '+91 88765 00112', 'simran.kaur@example.com', 'Delhi', 'Laxmi Nagar', 3, 19, 4.4, 30, 'Active', 12, '2026-08-28 07:41:42');

-- --------------------------------------------------------

--
-- Table structure for table `api_tokens`
--

CREATE TABLE `api_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_tokens`
--

INSERT INTO `api_tokens` (`id`, `user_id`, `token`, `created_at`, `expires_at`) VALUES
(1, 1, 'e559695415832a8999f5a32f55423c9e56b9a6338cc15baebc5e84aa2d033e71', '2026-07-21 13:14:11', '2026-08-20 13:14:11'),
(2, 1, '8c03cdc889fbf71ac82f05f9d125d4039c6124d855d6951cc8076538c47e3fa3', '2026-07-21 13:22:33', '2026-08-20 13:22:33'),
(5, 1, '86536d63e7feaaae8b079a82cd23275d05398838ad7323029551d41cf0187a0c', '2026-07-21 15:15:28', '2026-08-20 15:15:28'),
(6, 1, 'c1f4d2c259db14d2edeaae8bce28084de63a6c483e3e352be29371de55b2268b', '2026-07-21 15:22:40', '2026-08-20 15:22:40'),
(7, 1, '04acd9658c59cd3e248b95c0629962c884a791b500cc1ab8da1fa6f3340a7072', '2026-07-21 15:28:14', '2026-08-20 15:28:14'),
(8, 1, 'da62d1161afed4a99618f0c26d6cbba8ae12f23e061c0f4adce7e0987943f67f', '2026-07-21 15:32:56', '2026-08-20 15:32:56'),
(9, 1, 'd07e045929f397dc51248b1ea8a81ec225883d86b04893ae74df758c0fdbe105', '2026-07-21 15:36:39', '2026-08-20 15:36:39'),
(16, 3, '180e85a1f0ad022599218ecc2fa6a1b27a68aeee6c0873ee657faddcd3e2c788', '2026-07-22 12:28:50', '2026-08-21 12:28:50'),
(17, 7, '556adcd8a3c573e2498cff68bae2e9076ad33ec01f7f10613e6284d8fe380d67', '2026-07-23 04:16:50', '2026-08-22 04:16:50'),
(18, 1, '7fae6623b84914a939ad5ee144827e33dd0bf480ed8ca345496452add20f7b30', '2026-07-23 04:18:55', '2026-08-22 04:18:55'),
(19, 8, '189384d0e0393f066f4d376a8564aee4d597e7be39cfd566a1aae77907085231', '2026-07-23 04:36:25', '2026-08-22 04:36:25'),
(20, 1, 'f4210e30f78249a7b4f1f6cb4fad4870cefbe67fb71395c66a84e21a2bb18c31', '2026-07-23 05:11:06', '2026-08-22 05:11:06'),
(21, 1, '94c4e58f9e93518f1b967a59d474a8937a7e361db964d8e40c67384da9ade273', '2026-07-23 05:32:27', '2026-08-22 05:32:27'),
(22, 1, '9b155ce6c62b38abb63a83b019ad295aa9871b907dcaef73f04de95671ce03f3', '2026-07-23 06:03:03', '2026-08-22 06:03:03'),
(23, 9, 'b10ab09925f5b66611034ceab72b7492dc26e61add9979afddcdb71abe212ef4', '2026-07-23 06:04:28', '2026-08-22 06:04:28'),
(25, 10, '87ec0a21c55d2a26bda0f64152c977fac7b10c5ee5c0b864847b57e8bc5ccd8d', '2026-07-23 06:30:51', '2026-08-22 06:30:51'),
(29, 3, '5d597a4fb3d779226034591db350dc2a7a37c9d84f53e5e1ee8af07b4a115243', '2026-07-24 04:26:12', '2026-08-23 04:26:12'),
(32, 3, 'df49659e784a46dcfd01544574a3525458478161d1119f9715b57bd6d98e06ac', '2026-07-27 08:56:36', '2026-08-26 08:56:36'),
(33, 10, 'b5976e6f43ba0cca00556c401ed8bd2402efa8c5b41e9a270dae81be9102f756', '2026-07-28 08:16:02', '2026-08-27 08:16:02'),
(34, 10, '041b5c92912884e9028fbd9eb9501edd2343ec82fbbc7cc74c439775be42e635', '2026-07-28 08:31:07', '2026-08-27 08:31:07'),
(35, 3, 'ee60df15559c8cce03315f981765cc53a0770e7f04fdd405d681240097cf9518', '2026-07-30 10:29:33', '2026-08-29 10:29:33'),
(37, 3, '2c2a49de1b17f7aeadfbd8bddf81941ac363bbd2d9efefd55435f8ebf9e19fa4', '2026-08-04 04:47:42', '2026-09-03 04:47:42');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `banner_order` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `subtitle`, `image`, `banner_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'India\'s Fastest-Growing Real Estate Platform', 'From search to keys, we\'ve got you covered', 'banner_6a5ded829c90f1.02624626.jpg', 1, 'Active', '2026-07-20 09:15:31', '2026-07-20 09:43:14'),
(2, 'India\'s Fastest-Growing Real Estate Platform11', 'From search to keys, we\'ve got you covered11', 'banner_6a7064266d0b15.91428911.jpg', 2, 'Active', '2026-07-29 12:09:38', '2026-08-03 09:49:26');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `excerpt` varchar(500) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `blog_order` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `slug`, `category`, `author`, `image`, `excerpt`, `content`, `status`, `blog_order`, `created_at`, `updated_at`) VALUES
(1, 'Real Estate Outlook 2026: Which Indian Cities Are Set to Outperform?', 'real-estate-outlook-2026-which-indian-cities-are-set-to-outperform', 'Market Trends', '', 'blog_6a75be1f177af8.58025909.jpeg', 'From Ahmedabad\'s steady appreciation to Bangalore\'s tech-led demand, we break down the price trends, upcoming infrastructure, and locality-level data shaping the year ahead.', 'From Ahmedabad\'s steady appreciation to Bangalore\'s tech-led demand, we break down the price trends, upcoming infrastructure, and locality-level data shaping the year ahead.From Ahmedabad\'s steady appreciation to Bangalore\'s tech-led demand, we break down the price trends, upcoming infrastructure, and locality-level data shaping the year ahead.From Ahmedabad\'s steady appreciation to Bangalore\'s tech-led demand, we break down the price trends, upcoming infrastructure, and locality-level data shaping the year ahead.From Ahmedabad\'s steady appreciation to Bangalore\'s tech-led demand, we break down the price trends, upcoming infrastructure, and locality-level data shaping the year ahead.From Ahmedabad\'s steady appreciation to Bangalore\'s tech-led demand, we break down the price trends, upcoming infrastructure, and locality-level data shaping the year ahead.From Ahmedabad\'s steady appreciation to Bangalore\'s tech-led demand, we break down the price trends, upcoming infrastructure, and locality-level data shaping the year ahead.From Ahmedabad\'s steady appreciation to Bangalore\'s tech-led demand, we break down the price trends, upcoming infrastructure, and locality-level data shaping the year ahead.From Ahmedabad\'s steady appreciation to Bangalore\'s tech-led demand, we break down the price trends, upcoming infrastructure, and locality-level data shaping the year ahead.', 'Active', 1, '2026-08-07 11:14:39', '2026-08-07 11:14:39'),
(2, 'First-Time Homebuyer? Here\'s Your Complete Checklist', 'first-time-homebuyer-here-s-your-complete-checklist', 'Buying Guide', '', 'blog_6a75c1a56f8c71.33970937.jpeg', 'From verifying RERA registration to reading a sale agreement, here\'s everything to check before you sign.', 'From verifying RERA registration to reading a sale agreement, here\'s everything to check before you sign.From verifying RERA registration to reading a sale agreement, here\'s everything to check before you sign.From verifying RERA registration to reading a sale agreement, here\'s everything to check before you sign.', 'Active', 1, '2026-08-07 11:29:41', '2026-08-07 11:29:41'),
(3, 'Home Loan Interest Rates: How to Get the Best Deal in 2026', 'home-loan-interest-rates-how-to-get-the-best-deal-in-2026', 'Home Loans', '', 'blog_6a75c26fc27fa2.04077525.jpeg', 'A practical comparison of fixed vs floating rates and how your credit score affects what banks offer you.', 'A practical comparison of fixed vs floating rates and how your credit score affects what banks offer you.A practical comparison of fixed vs floating rates and how your credit score affects what banks offer you.A practical comparison of fixed vs floating rates and how your credit score affects what banks offer you.', 'Active', 1, '2026-08-07 11:33:03', '2026-08-07 11:33:03'),
(4, 'Small-Space Living: 6 Layout Ideas for Compact 1 BHK Apartments', 'small-space-living-6-layout-ideas-for-compact-1-bhk-apartments', 'Lifestyle', '', 'blog_6a75c2b3d8f1a3.18140242.jpeg', 'Smart storage, multi-use furniture, and light tricks that make compact homes feel twice the size.', 'Smart storage, multi-use furniture, and light tricks that make compact homes feel twice the size.Smart storage, multi-use furniture, and light tricks that make compact homes feel twice the size.Smart storage, multi-use furniture, and light tricks that make compact homes feel twice the size.', 'Active', 1, '2026-08-07 11:34:11', '2026-08-07 11:34:11'),
(5, 'Why Kolkata\'s Eastern Suburbs Are Attracting First-Time Buyers', 'why-kolkata-s-eastern-suburbs-are-attracting-first-time-buyers', 'Market Trends', '', 'blog_6a75c2e2eb0e40.40433549.jpeg', 'New metro connectivity and IT park development are quietly reshaping demand along the EM Bypass corridor.', 'New metro connectivity and IT park development are quietly reshaping demand along the EM Bypass corridor.New metro connectivity and IT park development are quietly reshaping demand along the EM Bypass corridor.', 'Active', 1, '2026-08-07 11:34:58', '2026-08-07 11:34:58'),
(6, '7 Legal Documents You Must Verify Before Buying a Resale Flat', '7-legal-documents-you-must-verify-before-buying-a-resale-flat', 'Legal & Vastu', '', 'blog_6a75c31b950dc6.53413457.jpeg', 'Title deeds, encumbrance certificates, and occupancy certificates explained in plain language.', 'Title deeds, encumbrance certificates, and occupancy certificates explained in plain language.Title deeds, encumbrance certificates, and occupancy certificates explained in plain language.Title deeds, encumbrance certificates, and occupancy certificates explained in plain language.', 'Active', 1, '2026-08-07 11:35:55', '2026-08-07 11:35:55');

-- --------------------------------------------------------

--
-- Table structure for table `builder_contact_enquiries`
--

CREATE TABLE `builder_contact_enquiries` (
  `id` int(11) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `is_dealer` varchar(10) DEFAULT NULL,
  `buy_timeline` varchar(50) DEFAULT NULL,
  `interested_home_loan` varchar(10) DEFAULT NULL,
  `interested_site_visits` varchar(10) DEFAULT NULL,
  `terms_agree` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `builder_contact_enquiries`
--

INSERT INTO `builder_contact_enquiries` (`id`, `page_id`, `page_title`, `name`, `contact`, `is_dealer`, `buy_timeline`, `interested_home_loan`, `interested_site_visits`, `terms_agree`, `created_at`) VALUES
(1, 57, 'HomeLane Modular Kitchen Studio', 'Shahbaz Khan', '8149462320', 'no', 'more_than_6', 'no', 'yes', 'yes', '2026-08-31 10:35:13'),
(2, 55, 'Casa Grande', 'Shahbaz Khan', '8149462320', 'no', 'Immediately', 'yes', 'yes', 'yes', '2026-09-01 05:28:20'),
(3, 12, '3BHK Apartment in MVP Colony', 'dsfdsf', '9897989798', 'no', 'more_than_6', 'no', 'no', 'yes', '2026-09-01 05:44:48'),
(4, 98, 'SafeStay Girls PG', 'Dnyaneshwar patil', '8329928379', 'no', 'more_than_6', 'no', 'yes', 'yes', '2026-09-05 09:11:44'),
(5, 98, 'SafeStay Girls PG', 'Umakant Nehete', '8329928379', 'no', 'more_than_6', 'no', 'yes', 'yes', '2026-09-05 09:13:00'),
(6, 56, 'Ananda Vihara', 'Richa', '9247559154', 'no', 'Immediately', 'yes', 'yes', 'yes', '2026-09-10 10:25:31'),
(7, 71, 'Lakeside Villa Stay - Udaipur', 'Yukkteshwar Sood', '9806684799', 'no', '3_months', 'yes', 'no', 'yes', '2026-09-20 08:38:24');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `city_image` varchar(255) DEFAULT NULL,
  `city_order` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `city_name`, `city_image`, `city_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mumbai', '', 1, 'Active', '2026-07-20 09:35:16', '2026-07-20 09:35:16'),
(2, 'Bangalore', '', 1, 'Active', '2026-07-20 09:35:30', '2026-07-20 09:35:30'),
(3, 'Gurgaon', '', 1, 'Active', '2026-07-20 09:35:36', '2026-07-20 09:35:36'),
(4, 'Pune', '', 1, 'Active', '2026-07-20 09:35:42', '2026-07-20 09:35:42'),
(5, 'Noida', '', 1, 'Active', '2026-07-20 09:35:49', '2026-07-20 09:35:49'),
(6, 'Hyderabad', '', 1, 'Active', '2026-07-20 09:35:55', '2026-07-20 09:35:55'),
(7, 'Delhi', '', 1, 'Active', '2026-07-20 09:36:04', '2026-07-20 09:36:04'),
(8, 'Ahmedabad', '', 1, 'Active', '2026-07-20 09:36:09', '2026-07-20 09:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `looking_for` varchar(50) DEFAULT NULL,
  `project` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('New','Contacted','Closed') NOT NULL DEFAULT 'New',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `contact_submissions`
--

INSERT INTO `contact_submissions` (`id`, `first_name`, `last_name`, `email`, `phone`, `address`, `looking_for`, `project`, `message`, `status`, `created_at`) VALUES
(1, 'Shahbaz', 'Khan', 'shahbaz1990kh@gmail.com', '8149462320', 'Maharashtra , Mumbai , Powai', 'farm-lands', 'nbvgvnvnbnb', 'xtfdhgfchfhgfghfghfghfh', 'New', '2026-09-06 08:11:17'),
(2, 'Norine', 'Cuthbertson', 'info@freeb2bdata.org', '9045847656', '1004 Boundary Street', 'open-plots', 'Norine Cuthbertson', 'Hello,\r\n\r\nI am Norine Cuthbertson from FreeB2BData\r\n\r\nIt is with sad regret to inform you that we are shutting down\r\n\r\nWe have over 252 countries and over 33 million companies available in our database with phone numbers, industries, emails, etc.\r\n\r\nPlease come and download your data in the next 24 hours. \r\n\r\nhttps://Norine.freeb2bdata.org\r\n\r\nRegards,\r\nNorine Cuthbertson', 'New', '2026-09-24 22:48:42');

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `mobile_number` varchar(30) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `property_type` varchar(100) DEFAULT NULL,
  `bhk_type` varchar(50) DEFAULT NULL,
  `budget_range` varchar(100) DEFAULT NULL,
  `preferred_locality` varchar(150) DEFAULT NULL,
  `move_timeline` varchar(100) DEFAULT NULL,
  `additional_info` varchar(500) DEFAULT NULL,
  `consent` tinyint(1) NOT NULL DEFAULT 0,
  `enquiry_type` enum('Enquiry','Callback') NOT NULL DEFAULT 'Enquiry',
  `source_page` varchar(255) DEFAULT NULL,
  `status` enum('New','Contacted','Closed') NOT NULL DEFAULT 'New',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `enquiries`
--

INSERT INTO `enquiries` (`id`, `full_name`, `mobile_number`, `email`, `property_type`, `bhk_type`, `budget_range`, `preferred_locality`, `move_timeline`, `additional_info`, `consent`, `enquiry_type`, `source_page`, `status`, `created_at`) VALUES
(1, 'Shahbaz Khan', '8149462320', 'shahbaz1990kh@gmail.com', 'Villa', '2 BHK', '₹25L - ₹50L', 'Mumbai', 'Within 3 Months', '', 1, 'Enquiry', 'https://test.tatitodevelopersandpromoters.com/front/index.php', 'New', '2026-07-27 12:46:55'),
(2, 'John Doe', '9876543210', 'john@gmail.com', 'Apartment', '2 BHK', '50L-75L', 'Hyderabad', 'Within 3 Months', 'Need near metro station.', 1, 'Enquiry', 'https://test.tatitodevelopersandpromoters.com/', 'New', '2026-07-27 12:56:10'),
(3, 'John Doe', '9876543210', 'john@gmail.com', 'Apartment', '2 BHK', '50L-75L', 'Hyderabad', 'Within 3 Months', 'Need near metro station.', 1, 'Enquiry', 'https://test.tatitodevelopersandpromoters.com/', 'New', '2026-07-28 08:57:33'),
(4, 'saravana', '9685758494', 'saro@example.com', 'Apartment', '2 BHK', 'Below 25L', 'Kondapur', 'Within 1 Month', 'i want a apartment', 1, 'Enquiry', 'https://test.tatitodevelopersandpromoters.com/', 'New', '2026-07-28 10:30:26'),
(5, 'saravana', '9685758494', 'saro@example.com', 'Apartment', '2 BHK', 'Below 25L', 'Kondapur', 'Within 1 Month', '', 1, 'Callback', 'https://test.tatitodevelopersandpromoters.com/', 'New', '2026-07-28 10:47:13');

-- --------------------------------------------------------

--
-- Table structure for table `floor_plans`
--

CREATE TABLE `floor_plans` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL COMMENT 'References trending_projects.id',
  `bhk_type` varchar(50) NOT NULL COMMENT 'e.g., 2 BHK, 3 BHK, 4 BHK',
  `carpet_area_sqft` varchar(50) DEFAULT NULL COMMENT 'e.g., 840 - 855',
  `carpet_area_sqm` varchar(50) DEFAULT NULL COMMENT 'e.g., 78.04 - 79.43',
  `price` varchar(100) DEFAULT NULL COMMENT 'e.g., ₹96 L',
  `price_range` varchar(100) DEFAULT NULL COMMENT 'e.g., ₹91 L - ₹1.28 Cr',
  `status` varchar(50) DEFAULT 'Under Construction',
  `possession_date` varchar(50) DEFAULT NULL COMMENT 'e.g., Dec ''26',
  `floor_plan_image` varchar(255) DEFAULT NULL COMMENT 'Image of floor plan',
  `display_order` int(11) DEFAULT 0,
  `status_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `floor_plans`
--

INSERT INTO `floor_plans` (`id`, `project_id`, `bhk_type`, `carpet_area_sqft`, `carpet_area_sqm`, `price`, `price_range`, `status`, `possession_date`, `floor_plan_image`, `display_order`, `status_active`, `created_at`, `updated_at`) VALUES
(9, 1, '2 BHK Apartment', '840 - 855', '78.04 - 79.43', '', '₹91 L - ₹1.28 Cr', 'Under Construction', 'Dec \'26', NULL, 0, 1, '2026-08-20 10:29:01', NULL),
(10, 1, '3 BHK Apartment', '1065 - 1120', '98.94 - 104.05', '', '₹1.2 Cr - ₹1.8 Cr', 'Under Construction', 'Dec \'26', NULL, 1, 1, '2026-08-20 10:29:01', NULL),
(11, 1, '2 BHK Apartment', '837', '77.76', '₹96 L', '', 'Under Construction', 'Dec \'26', NULL, 2, 1, '2026-08-20 10:29:01', NULL),
(12, 1, '3 BHK Apartment', '1100', '102.19', '₹1.5 Cr', '', 'Under Construction', 'Dec \'26', NULL, 3, 1, '2026-08-20 10:29:01', NULL),
(13, 1, '2 BHK Apartment', '880', '81.75', 'Price on request', '', 'Under Construction', 'Dec \'26', NULL, 4, 1, '2026-08-20 10:29:01', NULL),
(14, 1, '3 BHK Apartment', '1150', '106.84', 'Price on request', '', 'Under Construction', 'Dec \'26', NULL, 5, 1, '2026-08-20 10:29:01', NULL),
(15, 1, '2 BHK Apartment', '900 - 920', '83.61 - 85.47', 'Price on request', '', 'Under Construction', 'Dec \'26', NULL, 6, 1, '2026-08-20 10:29:01', NULL),
(16, 1, '2 BHK Apartment', '955', '88.72', 'Price on request', '', 'Under Construction', 'Dec \'26', NULL, 7, 1, '2026-08-20 10:29:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `footer_columns`
--

CREATE TABLE `footer_columns` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `column_order` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `footer_columns`
--

INSERT INTO `footer_columns` (`id`, `title`, `column_order`, `status`) VALUES
(1, 'For Buyers', 1, 'Active'),
(2, 'For Tenants', 2, 'Active'),
(3, 'For Owners', 3, 'Active'),
(4, 'Company', 4, 'Active'),
(5, 'Legal', 5, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `footer_legal_links`
--

CREATE TABLE `footer_legal_links` (
  `id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '#',
  `link_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `footer_legal_links`
--

INSERT INTO `footer_legal_links` (`id`, `label`, `url`, `link_order`) VALUES
(1, 'Privacy Policy', '#', 1),
(2, 'Terms of Use', '#', 2),
(3, 'Sitemap', '#', 3);

-- --------------------------------------------------------

--
-- Table structure for table `footer_links`
--

CREATE TABLE `footer_links` (
  `id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `label` varchar(150) NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '#',
  `link_order` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `footer_links`
--

INSERT INTO `footer_links` (`id`, `column_id`, `label`, `url`, `link_order`, `status`) VALUES
(1, 1, 'Properties for Sale', 'https://tatitodevelopersandpromoters.com/front/properties.php?listing_type=for-sale&category=buy-residential-properties', 1, 'Active'),
(2, 1, 'Home Loan', 'https://tatitodevelopersandpromoters.com/front/finance.php', 2, 'Active'),
(3, 1, 'Property Valuation', 'https://tatitodevelopersandpromoters.com/front/properties.php?listing_type=for-rent-lease&category=rent-residential-rentals', 3, 'Active'),
(4, 1, 'Vastu Calculator', 'https://tatitodevelopersandpromoters.com/front/finance.php', 4, 'Active'),
(5, 1, 'Legal Assistance', 'https://tatitodevelopersandpromoters.com/front/finance.php', 5, 'Active'),
(6, 2, 'Properties for Rent', 'https://tatitodevelopersandpromoters.com/front/properties.php?listing_type=for-rent-lease&category=rent-residential-rentals', 1, 'Active'),
(7, 2, 'Online Rent Agreement', 'https://tatitodevelopersandpromoters.com/front/finance.php', 2, 'Active'),
(8, 2, 'Rent Receipts', '#', 3, 'Active'),
(9, 2, 'PG & Co-living', 'https://tatitodevelopersandpromoters.com/front/properties.php?category=pgh-boys-pg', 4, 'Active'),
(10, 3, 'Post Property Free', 'sell-property.php', 1, 'Active'),
(11, 3, 'Property Management', '#', 2, 'Active'),
(12, 3, 'Co-broking Network', '#', 3, 'Active'),
(13, 3, 'Advertise With Us', '#', 4, 'Active'),
(14, 4, 'About Us', 'about.php', 1, 'Active'),
(15, 4, 'Careers', '#', 2, 'Active'),
(16, 4, 'Press & Media', '#', 3, 'Active'),
(17, 4, 'Contact Us', 'contact.php', 4, 'Active'),
(18, 5, 'Privacy Policy', 'privacy-policy.php', 1, 'Active'),
(19, 5, 'Cookie Policy', 'cookie-policy.php', 1, 'Active'),
(20, 5, 'Terms & Conditions', 'terms-conditions.php', 3, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `footer_settings`
--

CREATE TABLE `footer_settings` (
  `id` int(11) NOT NULL,
  `bg_color` varchar(20) NOT NULL DEFAULT '#0f1f38',
  `text_color` varchar(20) NOT NULL DEFAULT '#c9d3e0',
  `heading_color` varchar(20) NOT NULL DEFAULT '#ffffff',
  `accent_color` varchar(20) NOT NULL DEFAULT '#f5a623',
  `newsletter_title` varchar(150) NOT NULL DEFAULT 'GET WEEKLY MARKET UPDATES',
  `newsletter_subtitle` varchar(255) NOT NULL DEFAULT 'New launches, price trends, and locality reports — every Friday.',
  `newsletter_button_text` varchar(50) NOT NULL DEFAULT 'Subscribe',
  `brand_name` varchar(100) NOT NULL DEFAULT 'EstateHub',
  `brand_tagline` varchar(255) NOT NULL DEFAULT 'India''s property search, sorted. Search, shortlist, and close — backed by verified listings across India''s top cities.',
  `social_facebook` varchar(255) DEFAULT '',
  `social_instagram` varchar(255) DEFAULT '',
  `social_twitter` varchar(255) DEFAULT '',
  `social_linkedin` varchar(255) DEFAULT '',
  `copyright_text` varchar(255) NOT NULL DEFAULT '© 2026 EstateHub. All rights reserved.',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `approved_by_badges` text DEFAULT 'RERA|CREDAI',
  `partnership_badges` text DEFAULT 'NAR India|Builders Association',
  `collaboration_badges` text DEFAULT 'Architects Forum|PropTech Alliance',
  `association_badges` text DEFAULT 'REALTORS®|FIABCI',
  `contact_email_1` varchar(255) DEFAULT 'info.realestatehub@gmail.com',
  `contact_email_2` varchar(255) DEFAULT 'support@realestatehub.com',
  `contact_phone` varchar(50) DEFAULT '+91 98765 43210',
  `app_store_link` varchar(255) DEFAULT '#',
  `google_play_link` varchar(255) DEFAULT '#'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `footer_settings`
--

INSERT INTO `footer_settings` (`id`, `bg_color`, `text_color`, `heading_color`, `accent_color`, `newsletter_title`, `newsletter_subtitle`, `newsletter_button_text`, `brand_name`, `brand_tagline`, `social_facebook`, `social_instagram`, `social_twitter`, `social_linkedin`, `copyright_text`, `updated_at`, `approved_by_badges`, `partnership_badges`, `collaboration_badges`, `association_badges`, `contact_email_1`, `contact_email_2`, `contact_phone`, `app_store_link`, `google_play_link`) VALUES
(1, '#030b16', '#c9d3e0', '#ffffff', '#f5a623', 'GET WEEKLY MARKET UPDATES', 'New launches, price trends, and locality reports — every Friday.', 'Subscribe', 'Tatito Developers And Promoters', 'India\'s property search, sorted. Search, shortlist, and close — backed by verified listings across India\'s top cities.', '#', '#', '#', '#', '© 2026 Tatito Developers And Promoters. All Right Reserved.', '2026-09-06 08:39:49', 'RERA|CREDAI', 'NAR India|Builders Association', 'Architects Forum|PropTech Alliance', 'REALTORS®|FIABCI', 'info.tatitodevelopersandpromoters@gmail.com', 'support@tatitodevelopersandpromoters.com', '+91 98765 43210', '#', '#');

-- --------------------------------------------------------

--
-- Table structure for table `header_settings`
--

CREATE TABLE `header_settings` (
  `id` int(11) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `site_title` varchar(150) NOT NULL DEFAULT 'Tatito Developers',
  `header_bg_color` varchar(20) NOT NULL DEFAULT '#000000',
  `nav_link_color` varchar(20) NOT NULL DEFAULT '#ffffff',
  `login_btn_text` varchar(50) NOT NULL DEFAULT 'Login',
  `login_btn_text_color` varchar(20) NOT NULL DEFAULT '#ffffff',
  `login_btn_bg_color` varchar(20) NOT NULL DEFAULT '#1f6feb',
  `register_btn_text` varchar(50) NOT NULL DEFAULT 'Register',
  `register_btn_text_color` varchar(20) NOT NULL DEFAULT '#12213a',
  `register_btn_bg_color` varchar(20) NOT NULL DEFAULT '#f0b429',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `list_property_btn_text` varchar(50) DEFAULT 'List Property',
  `list_property_btn_bg_color` varchar(20) DEFAULT '#f0b429'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `header_settings`
--

INSERT INTO `header_settings` (`id`, `logo`, `site_title`, `header_bg_color`, `nav_link_color`, `login_btn_text`, `login_btn_text_color`, `login_btn_bg_color`, `register_btn_text`, `register_btn_text_color`, `register_btn_bg_color`, `updated_at`, `list_property_btn_text`, `list_property_btn_bg_color`) VALUES
(1, 'logo_f77a21f05180.png', 'Real Estates', '#030411', '#ffffff', 'Login', '#ffffff', '#030308', 'Register', '#030811', '#ba860d', '2026-08-30 08:46:38', 'List Propertys', '#f0b429');

-- --------------------------------------------------------

--
-- Table structure for table `interiors`
--

CREATE TABLE `interiors` (
  `id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `category` varchar(80) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `price` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interiors`
--

INSERT INTO `interiors` (`id`, `product_name`, `slug`, `category`, `brand`, `price`, `image`, `description`, `status`, `created_at`) VALUES
(1, 'Teak Wood Panel Door', 'teak-wood-panel-door', 'int-teak-wood-door', 'WoodCraft', '₹18,500', '', 'Solid teak wood panel door, made to size.', 'Active', '2026-08-23 08:59:08'),
(2, 'Modular L-Shaped Kitchen', 'modular-l-shaped-kitchen', 'int-l-shaped-kitchen', 'HomeLane', '₹1,45,000 onwards', '', 'Full modular kitchen with soft-close fittings.', 'Active', '2026-08-23 08:59:08'),
(3, 'Vitrified Floor Tiles 2x2', 'vitrified-floor-tiles-2x2', 'int-vitrified-tiles', 'Kajaria', '₹65/sqft', '', 'Glossy finish vitrified tiles.', 'Active', '2026-08-23 08:59:08');

-- --------------------------------------------------------

--
-- Table structure for table `listing_packages`
--

CREATE TABLE `listing_packages` (
  `id` int(10) UNSIGNED NOT NULL,
  `package_key` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration_days` int(11) NOT NULL DEFAULT 30,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listing_packages`
--

INSERT INTO `listing_packages` (`id`, `package_key`, `name`, `price`, `duration_days`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'free', 'Basic', 0.00, 30, 1, '2026-08-17 10:55:24', '2026-08-17 10:55:24'),
(2, 'standard', 'Standard', 499.00, 30, 1, '2026-08-17 10:55:24', '2026-08-17 10:55:24'),
(3, 'premium', 'Premium', 999.00, 45, 1, '2026-08-17 10:55:24', '2026-08-17 10:55:24');

-- --------------------------------------------------------

--
-- Table structure for table `listing_types`
--

CREATE TABLE `listing_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `listing_order` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `listing_types`
--

INSERT INTO `listing_types` (`id`, `name`, `slug`, `listing_order`, `status`, `created_at`) VALUES
(1, 'For Sale', 'for-sale', 1, 'Active', '2026-08-19 08:46:01'),
(2, 'For Rent/Lease', 'for-rent-lease', 2, 'Active', '2026-08-19 08:46:01'),
(3, 'For PG/Hostel', 'for-pg-hostel', 3, 'Active', '2026-08-19 08:46:01'),
(4, 'New Launch/Pre-launch', 'new-launch-pre-launch', 4, 'Active', '2026-08-19 08:46:01'),
(5, 'Resale', 'resale', 5, 'Active', '2026-08-19 08:46:01'),
(6, 'Under Construction', 'under-construction', 6, 'Active', '2026-08-19 08:46:01'),
(7, 'Ready to Move', 'ready-to-move', 7, 'Active', '2026-08-19 08:46:01');

-- --------------------------------------------------------

--
-- Table structure for table `local_services`
--

CREATE TABLE `local_services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `category` varchar(80) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `price` varchar(100) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `local_services`
--

INSERT INTO `local_services` (`id`, `service_name`, `slug`, `category`, `city`, `address`, `phone`, `price`, `rating`, `image`, `description`, `status`, `created_at`) VALUES
(1, 'QuickClean House Cleaning', 'quickclean-house-cleaning', 'lsv-house-cleaning', 'Pune', 'Aundh, Pune, Maharashtra', '9800000011', '₹800/service', 4.5, '', 'Professional home cleaning, same-day booking.', 'Active', '2026-08-23 08:59:08'),
(2, 'FixIt Electrician Service', 'fixit-electrician-service', 'lsv-electrician-service', 'Bangalore', 'Koramangala, Bangalore, Karnataka', '9800000012', '₹300/visit', 4.4, '', '24x7 electrician callouts.', 'Active', '2026-08-23 08:59:08'),
(3, 'GreenThumb Gardening', 'greenthumb-gardening', 'lsv-gardening-service', 'Mumbai', 'Bhandup West, Mumbai, Maharashtra', '9800000013', '₹1,200/month', 4.2, '', 'Weekly garden maintenance.', 'Active', '2026-08-23 08:59:08'),
(4, 'HandyFix Plumbing Service', 'handyfix-plumbing-service', 'lsv-plumber-service', 'Hyderabad', 'Kondapur, Hyderabad, Telangana', '9800000014', '₹400/visit', 4.3, '', 'Same-day plumbing repairs and installations.', 'Active', '2026-08-31 09:00:00'),
(5, 'SafeGuard Pest Control', 'safeguard-pest-control', 'lsv-pest-control', 'Chennai', 'T Nagar, Chennai, Tamil Nadu', '9800000015', '₹1,000/service', 4.5, '', 'General and termite pest control for homes.', 'Active', '2026-08-31 09:00:00'),
(6, 'SparkleMaid Cleaning Co.', 'sparklemaid-cleaning-co', 'lsv-deep-cleaning', 'Delhi', 'Rohini, Delhi', '9800000016', '₹1,500/service', 4.4, '', 'Deep cleaning for apartments and offices.', 'Active', '2026-08-31 09:00:00'),
(7, 'QuickShift Packers & Movers', 'quickshift-packers-movers', 'lsv-packers-movers-local', 'Bangalore', 'Jayanagar, Bangalore, Karnataka', '9800000017', '₹3,500/shift', 4.1, '', 'Affordable local shifting with insured handling.', 'Active', '2026-08-31 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `menu_name` varchar(100) NOT NULL,
  `menu_slug` varchar(191) DEFAULT NULL,
  `menu_order` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `menu_name`, `menu_slug`, `menu_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Buy', 'buy', 1, 'Active', '2026-07-19 09:41:32', '2026-08-15 04:59:34'),
(2, 'Rent', 'rent', 2, 'Active', '2026-07-19 09:48:03', '2026-08-15 04:59:30'),
(6, 'Finance', 'finance', 4, 'Active', '2026-08-03 13:17:30', '2026-08-14 11:49:34'),
(9, 'Blogs', 'blog.php', 5, 'Active', '2026-08-03 13:19:54', '2026-08-14 11:49:38'),
(10, 'Interiors', 'interiors.php', 7, 'Active', '2026-08-11 08:50:40', '2026-08-18 13:53:16'),
(11, 'Sell', 'sell', 3, 'Active', '2026-08-14 11:48:31', '2026-08-14 11:48:31'),
(12, 'Local Services', 'local-services', 6, 'Active', '2026-08-14 11:51:17', '2026-08-15 04:59:53'),
(13, 'Agents', 'agents', 5, 'Active', '2026-08-14 11:51:31', '2026-08-19 09:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_reports`
--

CREATE TABLE `project_reports` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `report_reason` varchar(100) NOT NULL,
  `report_details` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` enum('pending','reviewed','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_reviews`
--

CREATE TABLE `project_reviews` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `category` varchar(50) NOT NULL COMMENT 'Homeowner, Investor, Dealer, etc.',
  `rating` tinyint(1) NOT NULL DEFAULT 5 COMMENT '1-5 stars',
  `review_text` text NOT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_reviews`
--

INSERT INTO `project_reviews` (`id`, `project_id`, `user_name`, `user_email`, `category`, `rating`, `review_text`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, 1, 's khan', 's@gmail.com', 'Agent', 3, 'adasdasdasdas', 0, '2026-08-20 11:27:28', NULL),
(2, 1, 'rohit', 'rohit@gmail.com', 'Investor', 5, '\"Great investment opportunity. Property value has appreciated significantly.\"', 0, '2026-08-28 11:08:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_testimonials`
--

CREATE TABLE `project_testimonials` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `initials` varchar(10) NOT NULL,
  `role` varchar(100) NOT NULL,
  `testimonial_text` text NOT NULL,
  `rating` tinyint(1) DEFAULT 5,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `property_categories`
--

CREATE TABLE `property_categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `category_type` enum('residential','commercial','land','hotel','homestay','pg','hostel','home_services','vendor','interior','other','buy','rent','local_services','hotels_homestays','pg_hostels') DEFAULT 'residential',
  `menu_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `category_order` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `property_categories`
--

INSERT INTO `property_categories` (`id`, `parent_id`, `category_type`, `menu_id`, `name`, `slug`, `icon`, `category_order`, `status`, `created_at`) VALUES
(1, 9, 'residential', NULL, 'Apartments/Flats', 'apartments-flats', NULL, 1, 'Inactive', '2026-07-28 11:44:25'),
(2, 33, 'residential', NULL, 'Villa', 'villa', NULL, 2, 'Inactive', '2026-07-28 11:44:25'),
(3, 37, 'residential', NULL, 'Plot', 'plot', NULL, 3, 'Inactive', '2026-07-28 11:44:25'),
(4, 33, 'residential', NULL, 'Independent House', 'independent-house', NULL, 4, 'Inactive', '2026-07-28 11:44:25'),
(5, 1, 'residential', NULL, 'Penthouse', 'penthouse', NULL, 6, 'Inactive', '2026-07-28 11:44:25'),
(6, 1, 'residential', NULL, 'Studio Apartment', 'studio-apartment', NULL, 5, 'Inactive', '2026-07-28 11:44:25'),
(7, 33, 'residential', NULL, 'Farmhouse', 'farmhouse', NULL, 7, 'Inactive', '2026-07-28 11:44:25'),
(8, 24, 'commercial', NULL, 'Commercial Space', 'commercial-space', NULL, 8, 'Inactive', '2026-07-28 11:44:25'),
(9, 0, 'residential', NULL, 'Residential', 'residential', 'bi-house-door-fill', 1, 'Inactive', '2026-08-18 10:32:43'),
(17, 25, 'hostel', NULL, 'Boys Hostel', 'boys-hostel', 'bi-hotel-fill', 1, 'Inactive', '2026-08-18 10:41:23'),
(18, 25, 'hostel', NULL, 'Girls Hostel', 'girls-hostel', 'bi-bed-fill', 2, 'Inactive', '2026-08-18 10:41:58'),
(19, 25, 'hostel', NULL, 'Co-ed Hostel', 'co-ed-hostel', 'bi-bed-fill', 3, 'Inactive', '2026-08-18 10:42:28'),
(20, 25, 'hostel', NULL, 'Backpacker Hostel (Tourist)', 'backpacker-hostel-tourist', 'bi-bed-fill', 4, 'Inactive', '2026-08-18 10:42:41'),
(21, 25, 'hostel', NULL, 'Working Women\'s Hostel', 'working-women-s-hostel', 'bi-bed-fill', 5, 'Inactive', '2026-08-18 10:42:58'),
(22, 25, 'hostel', NULL, 'Working Men\'s Hostel', 'working-men-s-hostel', 'bi-bed-fill', 6, 'Inactive', '2026-08-18 10:43:10'),
(23, 24, 'commercial', NULL, 'Office Space (Bare Shell / Furnished)', 'office-space-bare-shell-furnished', 'bi-building-fill', 1, 'Inactive', '2026-08-18 10:54:14'),
(24, 0, 'commercial', NULL, 'Commercial', 'commercial', 'bi-building', 2, 'Inactive', '2026-08-19 08:19:44'),
(25, 0, 'hostel', NULL, 'Hostel', 'hostel', 'bi-bed-fill', 2, 'Inactive', '2026-08-19 08:19:44'),
(27, 1, 'residential', NULL, '1 BHK Apartment', '1-bhk-apartment', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(28, 1, 'residential', NULL, '2 BHK Apartment', '2-bhk-apartment', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(29, 1, 'residential', NULL, '3 BHK Apartment', '3-bhk-apartment', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(30, 1, 'residential', NULL, '4 BHK+ Apartment', '4-bhk-apartment-plus', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(31, 1, 'residential', NULL, 'Duplex Apartment', 'duplex-apartment', NULL, 7, 'Inactive', '2026-08-19 08:44:55'),
(32, 1, 'residential', NULL, 'Service Apartment', 'service-apartment', NULL, 8, 'Inactive', '2026-08-19 08:44:55'),
(33, 9, 'residential', NULL, 'Villas & Independent Houses', 'villas-independent-houses', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(34, 33, 'residential', NULL, 'Independent Villa', 'independent-villa', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(35, 33, 'residential', NULL, 'Row House / Twin Villa', 'row-house-twin-villa', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(36, 33, 'residential', NULL, 'Gated Community Villa', 'gated-community-villa', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(37, 9, 'residential', NULL, 'Plots/Land (Residential)', 'plots-land-residential', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(38, 37, 'residential', NULL, 'Residential Open Plot', 'residential-open-plot', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(39, 37, 'residential', NULL, 'Gated Community Plot', 'gated-community-plot', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(40, 37, 'residential', NULL, 'HMDA/DTCP/VUDA Approved Plot', 'hmda-dtcp-vuda-approved-plot', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(41, 37, 'residential', NULL, 'Corner Plot', 'corner-plot', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(42, 9, 'residential', NULL, 'Other Residential', 'other-residential', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(43, 42, 'residential', NULL, 'Builder Floor', 'builder-floor', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(44, 42, 'residential', NULL, 'Co-living Spaces', 'co-living-spaces', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(45, 42, 'residential', NULL, 'Senior Citizen Homes/Retirement Homes', 'senior-citizen-retirement-homes', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(46, 24, 'commercial', NULL, 'Shop/Retail Space', 'shop-retail-space', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(47, 24, 'commercial', NULL, 'Showroom', 'showroom', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(48, 24, 'commercial', NULL, 'Commercial Complex/Building', 'commercial-complex-building', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(49, 24, 'commercial', NULL, 'Co-working Space', 'co-working-space', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(50, 24, 'commercial', NULL, 'Warehouse/Godown', 'warehouse-godown', NULL, 7, 'Inactive', '2026-08-19 08:44:55'),
(51, 24, 'commercial', NULL, 'Industrial Shed/Factory', 'industrial-shed-factory', NULL, 8, 'Inactive', '2026-08-19 08:44:55'),
(52, 24, 'commercial', NULL, 'Commercial Plot/Land', 'commercial-plot-land', NULL, 9, 'Inactive', '2026-08-19 08:44:55'),
(53, 24, 'commercial', NULL, 'Business Center', 'business-center', NULL, 10, 'Inactive', '2026-08-19 08:44:55'),
(54, 24, 'commercial', NULL, 'Restaurant/Hotel Space', 'restaurant-hotel-space', NULL, 11, 'Inactive', '2026-08-19 08:44:55'),
(55, 24, 'commercial', NULL, 'Bank/ATM Space', 'bank-atm-space', NULL, 12, 'Inactive', '2026-08-19 08:44:55'),
(56, 0, 'land', NULL, 'Land', 'land', 'bi-map', 3, 'Inactive', '2026-08-19 08:44:55'),
(57, 56, 'land', NULL, 'Agricultural Land', 'agricultural-land', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(58, 56, 'land', NULL, 'Farm Land', 'farm-land', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(59, 56, 'land', NULL, 'Industrial Land', 'industrial-land', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(60, 56, 'land', NULL, 'Mixed-use Land', 'mixed-use-land', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(61, 0, 'hotel', NULL, 'Hotel', 'hotel', 'bi-building-fill', 1, 'Inactive', '2026-08-19 08:44:55'),
(62, 61, 'hotel', NULL, 'By Star Rating', 'hotel-star-rating', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(63, 62, 'hotel', NULL, '1 Star / Budget Hotel', '1-star-budget-hotel', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(64, 62, 'hotel', NULL, '2 Star Hotel', '2-star-hotel', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(65, 62, 'hotel', NULL, '3 Star Hotel', '3-star-hotel', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(66, 62, 'hotel', NULL, '4 Star Hotel', '4-star-hotel', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(67, 62, 'hotel', NULL, '5 Star Hotel', '5-star-hotel', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(68, 62, 'hotel', NULL, '7 Star / Ultra Luxury Hotel', '7-star-ultra-luxury-hotel', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(69, 61, 'hotel', NULL, 'By Type', 'hotel-by-type', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(70, 69, 'hotel', NULL, 'Business Hotel', 'business-hotel', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(71, 69, 'hotel', NULL, 'Boutique Hotel', 'boutique-hotel', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(72, 69, 'hotel', NULL, 'Resort Hotel', 'resort-hotel', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(73, 69, 'hotel', NULL, 'Heritage Hotel', 'heritage-hotel', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(74, 69, 'hotel', NULL, 'Airport Hotel', 'airport-hotel', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(75, 69, 'hotel', NULL, 'Transit Hotel', 'transit-hotel', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(76, 69, 'hotel', NULL, 'Capsule Hotel', 'capsule-hotel', NULL, 7, 'Inactive', '2026-08-19 08:44:55'),
(77, 69, 'hotel', NULL, 'Suite Hotel/All-Suite Hotel', 'suite-hotel-all-suite-hotel', NULL, 8, 'Inactive', '2026-08-19 08:44:55'),
(78, 69, 'hotel', NULL, 'Motel', 'motel', NULL, 9, 'Inactive', '2026-08-19 08:44:55'),
(79, 0, 'homestay', NULL, 'Homestay', 'homestay', 'bi-house-heart-fill', 2, 'Inactive', '2026-08-19 08:44:55'),
(80, 79, 'homestay', NULL, 'Entire Home/Villa', 'entire-home-villa', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(81, 79, 'homestay', NULL, 'Private Room in Home', 'private-room-in-home', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(82, 79, 'homestay', NULL, 'Shared Room/Dormitory', 'shared-room-dormitory-homestay', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(83, 79, 'homestay', NULL, 'Farm Stay', 'farm-stay', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(84, 79, 'homestay', NULL, 'Cottage/Cabin', 'cottage-cabin', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(85, 79, 'homestay', NULL, 'Tree House', 'tree-house', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(86, 79, 'homestay', NULL, 'Houseboat', 'houseboat', NULL, 7, 'Inactive', '2026-08-19 08:44:55'),
(87, 79, 'homestay', NULL, 'Beach House', 'beach-house', NULL, 8, 'Inactive', '2026-08-19 08:44:55'),
(88, 79, 'homestay', NULL, 'Hill Station Stay', 'hill-station-stay', NULL, 9, 'Inactive', '2026-08-19 08:44:55'),
(89, 79, 'homestay', NULL, 'Heritage Homestay', 'heritage-homestay', NULL, 10, 'Inactive', '2026-08-19 08:44:55'),
(90, 79, 'homestay', NULL, 'Bungalow Stay', 'bungalow-stay', NULL, 11, 'Inactive', '2026-08-19 08:44:55'),
(91, 79, 'homestay', NULL, 'Serviced Apartment/Service Villa', 'serviced-apartment-service-villa', NULL, 12, 'Inactive', '2026-08-19 08:44:55'),
(92, 79, 'homestay', NULL, 'Camping/Glamping (Tent Stay)', 'camping-glamping-tent-stay', NULL, 13, 'Inactive', '2026-08-19 08:44:55'),
(93, 61, 'hotel', NULL, 'Other Accommodation', 'other-accommodation', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(94, 93, 'hotel', NULL, 'Guest House', 'guest-house', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(95, 93, 'hotel', NULL, 'Bed & Breakfast (B&B)', 'bed-and-breakfast-bb', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(96, 93, 'hotel', NULL, 'Lodge', 'lodge', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(97, 93, 'hotel', NULL, 'Resort/Cottage Resort', 'resort-cottage-resort', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(98, 93, 'hotel', NULL, 'Dharamshala/Ashram Stay', 'dharamshala-ashram-stay', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(99, 0, 'pg', NULL, 'PG', 'pg', 'bi-house-fill', 1, 'Inactive', '2026-08-19 08:44:55'),
(100, 99, 'pg', NULL, 'By Gender', 'pg-by-gender', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(101, 100, 'pg', NULL, 'Boys PG / Gents PG', 'boys-gents-pg', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(102, 100, 'pg', NULL, 'Girls PG / Ladies PG', 'girls-ladies-pg', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(103, 100, 'pg', NULL, 'Co-living PG (Unisex/Mixed)', 'co-living-pg-unisex-mixed', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(104, 100, 'pg', NULL, 'Family PG', 'family-pg', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(105, 99, 'pg', NULL, 'By Room Sharing', 'pg-by-room-sharing', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(106, 105, 'pg', NULL, 'Single Occupancy/Private Room', 'pg-single-occupancy-private-room', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(107, 105, 'pg', NULL, 'Double Sharing', 'pg-double-sharing', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(108, 105, 'pg', NULL, 'Triple Sharing', 'pg-triple-sharing', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(109, 105, 'pg', NULL, '4-Sharing/Dormitory Style', 'pg-4-sharing-dormitory-style', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(110, 99, 'pg', NULL, 'By Meal Plan', 'pg-by-meal-plan', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(111, 110, 'pg', NULL, 'PG with Food (Full Meal Plan)', 'pg-with-food-full-meal-plan', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(112, 110, 'pg', NULL, 'PG without Food', 'pg-without-food', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(113, 110, 'pg', NULL, 'PG with Breakfast Only', 'pg-with-breakfast-only', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(114, 110, 'pg', NULL, 'PG with 2 Meals (Breakfast + Dinner)', 'pg-with-2-meals-breakfast-dinner', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(115, 99, 'pg', NULL, 'By Target Audience', 'pg-by-target-audience', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(116, 115, 'pg', NULL, 'Student PG', 'student-pg', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(117, 115, 'pg', NULL, 'Working Professional PG', 'working-professional-pg', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(118, 115, 'pg', NULL, 'Corporate PG/Managed Co-living', 'corporate-pg-managed-co-living', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(119, 25, 'hostel', NULL, 'Institution-linked', 'hostel-institution-linked', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(120, 119, 'hostel', NULL, 'College Hostel (own campus)', 'college-hostel-own-campus', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(121, 119, 'hostel', NULL, 'University Hostel', 'university-hostel', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(122, 119, 'hostel', NULL, 'Private Hostel (near college, independent)', 'private-hostel-near-college', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(123, 119, 'hostel', NULL, 'Coaching Center Hostel', 'coaching-center-hostel', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(124, 25, 'hostel', NULL, 'By Sharing Type', 'hostel-by-sharing-type', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(125, 124, 'hostel', NULL, 'Single Room (Hostel)', 'hostel-single-room', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(126, 124, 'hostel', NULL, '2-Sharing (Hostel)', 'hostel-2-sharing', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(127, 124, 'hostel', NULL, '3-Sharing (Hostel)', 'hostel-3-sharing', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(128, 124, 'hostel', NULL, '4-Sharing (Hostel)', 'hostel-4-sharing', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(129, 124, 'hostel', NULL, 'Dormitory (6+ beds)', 'hostel-dormitory-6-plus-beds', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(130, 0, 'home_services', NULL, 'Home Services', 'home-services', 'bi-tools', 0, 'Inactive', '2026-08-19 08:44:55'),
(131, 130, 'home_services', NULL, 'Electrical', 'electrical', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(132, 131, 'home_services', NULL, 'Wiring/Rewiring', 'wiring-rewiring', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(133, 131, 'home_services', NULL, 'Switch & Socket Repair', 'switch-socket-repair', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(134, 131, 'home_services', NULL, 'MCB/Fuse Repair', 'mcb-fuse-repair', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(135, 131, 'home_services', NULL, 'Fan Installation', 'fan-installation', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(136, 131, 'home_services', NULL, 'Light Fixture Installation', 'light-fixture-installation', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(137, 131, 'home_services', NULL, 'Inverter Installation', 'inverter-installation', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(138, 131, 'home_services', NULL, 'Stabilizer Installation', 'stabilizer-installation', NULL, 7, 'Inactive', '2026-08-19 08:44:55'),
(139, 131, 'home_services', NULL, 'Electrical Safety Audit', 'electrical-safety-audit', NULL, 8, 'Inactive', '2026-08-19 08:44:55'),
(140, 130, 'home_services', NULL, 'Plumbing', 'plumbing', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(141, 140, 'home_services', NULL, 'Tap Repair', 'tap-repair', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(142, 140, 'home_services', NULL, 'Pipe Leak Fix', 'pipe-leak-fix', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(143, 140, 'home_services', NULL, 'Bathroom Fitting Installation', 'bathroom-fitting-installation', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(144, 140, 'home_services', NULL, 'Toilet Repair', 'toilet-repair', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(145, 140, 'home_services', NULL, 'Water Tank Cleaning', 'water-tank-cleaning', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(146, 140, 'home_services', NULL, 'Drainage Clearing', 'drainage-clearing', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(147, 140, 'home_services', NULL, 'Water Heater/Geyser Installation', 'water-heater-geyser-installation', NULL, 7, 'Inactive', '2026-08-19 08:44:55'),
(148, 140, 'home_services', NULL, 'RO Installation', 'ro-installation', NULL, 8, 'Inactive', '2026-08-19 08:44:55'),
(149, 130, 'home_services', NULL, 'Carpentry', 'carpentry', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(150, 149, 'home_services', NULL, 'Furniture Repair', 'furniture-repair', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(151, 149, 'home_services', NULL, 'Door/Window Repair', 'door-window-repair', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(152, 149, 'home_services', NULL, 'Wardrobe Installation', 'wardrobe-installation', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(153, 149, 'home_services', NULL, 'Lock Installation/Repair', 'lock-installation-repair', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(154, 149, 'home_services', NULL, 'Curtain Rod Installation', 'curtain-rod-installation', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(155, 149, 'home_services', NULL, 'Furniture Assembly', 'furniture-assembly', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(156, 149, 'home_services', NULL, 'Wood Polishing', 'wood-polishing', NULL, 7, 'Inactive', '2026-08-19 08:44:55'),
(157, 149, 'home_services', NULL, 'Hinge Repair', 'hinge-repair', NULL, 8, 'Inactive', '2026-08-19 08:44:55'),
(158, 130, 'home_services', NULL, 'Painting', 'painting', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(159, 158, 'home_services', NULL, 'Interior Wall Painting', 'interior-wall-painting', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(160, 158, 'home_services', NULL, 'Exterior Painting', 'exterior-painting', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(161, 158, 'home_services', NULL, 'Waterproofing', 'waterproofing', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(162, 158, 'home_services', NULL, 'Texture Painting', 'texture-painting', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(163, 158, 'home_services', NULL, 'Wood Painting', 'wood-painting', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(164, 158, 'home_services', NULL, 'Metal Grill Painting', 'metal-grill-painting', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(165, 130, 'home_services', NULL, 'AC & Appliance Repair', 'ac-appliance-repair', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(166, 165, 'home_services', NULL, 'AC Installation', 'ac-installation', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(167, 165, 'home_services', NULL, 'AC Service', 'ac-service', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(168, 165, 'home_services', NULL, 'AC Gas Refill', 'ac-gas-refill', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(169, 165, 'home_services', NULL, 'AC Repair', 'ac-repair', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(170, 165, 'home_services', NULL, 'Washing Machine Repair', 'washing-machine-repair', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(171, 165, 'home_services', NULL, 'Refrigerator Repair', 'refrigerator-repair', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(172, 165, 'home_services', NULL, 'Microwave Repair', 'microwave-repair', NULL, 7, 'Inactive', '2026-08-19 08:44:55'),
(173, 165, 'home_services', NULL, 'Geyser Repair', 'geyser-repair', NULL, 8, 'Inactive', '2026-08-19 08:44:55'),
(174, 130, 'home_services', NULL, 'Home Cleaning', 'home-cleaning', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(175, 174, 'home_services', NULL, 'Full Home Cleaning', 'full-home-cleaning', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(176, 174, 'home_services', NULL, 'Kitchen Deep Clean', 'kitchen-deep-clean', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(177, 174, 'home_services', NULL, 'Bathroom Deep Clean', 'bathroom-deep-clean', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(178, 174, 'home_services', NULL, 'Sofa Cleaning', 'sofa-cleaning', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(179, 174, 'home_services', NULL, 'Carpet Cleaning', 'carpet-cleaning', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(180, 174, 'home_services', NULL, 'Water Tank Cleaning (Cleaning Service)', 'water-tank-cleaning-cleaning-service', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(181, 174, 'home_services', NULL, 'Car Cleaning', 'car-cleaning', NULL, 7, 'Inactive', '2026-08-19 08:44:55'),
(182, 174, 'home_services', NULL, 'Office Cleaning', 'office-cleaning', NULL, 8, 'Inactive', '2026-08-19 08:44:55'),
(183, 174, 'home_services', NULL, 'Move-in/Move-out Cleaning', 'move-in-move-out-cleaning', NULL, 9, 'Inactive', '2026-08-19 08:44:55'),
(184, 174, 'home_services', NULL, 'Mattress Cleaning', 'mattress-cleaning', NULL, 10, 'Inactive', '2026-08-19 08:44:55'),
(185, 130, 'home_services', NULL, 'Pest Control', 'pest-control', NULL, 7, 'Inactive', '2026-08-19 08:44:55'),
(186, 185, 'home_services', NULL, 'General Pest Control', 'general-pest-control', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(187, 185, 'home_services', NULL, 'Termite Control', 'termite-control', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(188, 185, 'home_services', NULL, 'Cockroach Control', 'cockroach-control', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(189, 185, 'home_services', NULL, 'Mosquito Control', 'mosquito-control', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(190, 185, 'home_services', NULL, 'Rodent Control', 'rodent-control', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(191, 185, 'home_services', NULL, 'Bed Bug Treatment', 'bed-bug-treatment', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(192, 130, 'home_services', NULL, 'Home Security', 'home-security', NULL, 8, 'Inactive', '2026-08-19 08:44:55'),
(193, 192, 'home_services', NULL, 'CCTV Installation', 'cctv-installation', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(194, 192, 'home_services', NULL, 'Video Door Phone', 'video-door-phone', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(195, 192, 'home_services', NULL, 'Smart Lock Installation', 'smart-lock-installation', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(196, 192, 'home_services', NULL, 'Alarm System', 'alarm-system', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(197, 192, 'home_services', NULL, 'Intercom Installation', 'intercom-installation', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(198, 192, 'home_services', NULL, 'Safe/Locker Installation', 'safe-locker-installation', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(199, 130, 'home_services', NULL, 'Packers & Movers', 'packers-movers', NULL, 9, 'Inactive', '2026-08-19 08:44:55'),
(200, 199, 'home_services', NULL, 'Local Shifting', 'local-shifting', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(201, 199, 'home_services', NULL, 'Intercity Shifting', 'intercity-shifting', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(202, 199, 'home_services', NULL, 'Office Relocation', 'office-relocation', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(203, 199, 'home_services', NULL, 'Vehicle Transport', 'vehicle-transport', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(204, 199, 'home_services', NULL, 'Warehouse Storage', 'warehouse-storage', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(205, 130, 'home_services', NULL, 'Gardening & Landscaping', 'gardening-landscaping', NULL, 10, 'Inactive', '2026-08-19 08:44:55'),
(206, 205, 'home_services', NULL, 'Garden Maintenance', 'garden-maintenance', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(207, 205, 'home_services', NULL, 'Lawn Mowing', 'lawn-mowing', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(208, 205, 'home_services', NULL, 'Tree Trimming', 'tree-trimming', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(209, 205, 'home_services', NULL, 'Landscape Design', 'landscape-design', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(210, 205, 'home_services', NULL, 'Plant Nursery/Potting', 'plant-nursery-potting', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(211, 130, 'home_services', NULL, 'Small Renovation Jobs', 'small-renovation-jobs', NULL, 11, 'Inactive', '2026-08-19 08:44:55'),
(212, 211, 'home_services', NULL, 'False Ceiling', 'false-ceiling', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(213, 211, 'home_services', NULL, 'Wall Panelling', 'wall-panelling', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(214, 211, 'home_services', NULL, 'Wallpaper Installation', 'wallpaper-installation', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(215, 211, 'home_services', NULL, 'Modular Kitchen Fix', 'modular-kitchen-fix', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(216, 211, 'home_services', NULL, 'Tile/Marble Work', 'tile-marble-work', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(217, 211, 'home_services', NULL, 'Flooring Repair', 'flooring-repair', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(218, 130, 'home_services', NULL, 'Beauty & Wellness at Home', 'beauty-wellness-at-home', NULL, 12, 'Inactive', '2026-08-19 08:44:55'),
(219, 218, 'home_services', NULL, 'Salon at Home (Women)', 'salon-at-home-women', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(220, 218, 'home_services', NULL, 'Salon at Home (Men)', 'salon-at-home-men', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(221, 218, 'home_services', NULL, 'Spa/Massage at Home', 'spa-massage-at-home', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(222, 218, 'home_services', NULL, 'Makeup Artist', 'makeup-artist', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(223, 218, 'home_services', NULL, 'Mehendi Artist', 'mehendi-artist', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(224, 218, 'home_services', NULL, 'Yoga Trainer', 'yoga-trainer', NULL, 6, 'Inactive', '2026-08-19 08:44:55'),
(225, 218, 'home_services', NULL, 'Physiotherapy at Home', 'physiotherapy-at-home', NULL, 7, 'Inactive', '2026-08-19 08:44:55'),
(226, 218, 'home_services', NULL, 'Nursing Care at Home', 'nursing-care-at-home', NULL, 8, 'Inactive', '2026-08-19 08:44:55'),
(227, 130, 'home_services', NULL, 'Fitness', 'fitness', NULL, 13, 'Inactive', '2026-08-19 08:44:55'),
(228, 227, 'home_services', NULL, 'Personal Trainer', 'personal-trainer', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(229, 227, 'home_services', NULL, 'Home Gym Setup', 'home-gym-setup', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(230, 227, 'home_services', NULL, 'Yoga Instructor', 'yoga-instructor', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(231, 130, 'home_services', NULL, 'Vehicle Services', 'vehicle-services', NULL, 14, 'Inactive', '2026-08-19 08:44:55'),
(232, 231, 'home_services', NULL, 'Car Wash at Home', 'car-wash-at-home', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(233, 231, 'home_services', NULL, 'Bike Servicing', 'bike-servicing', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(234, 231, 'home_services', NULL, 'Car Servicing', 'car-servicing', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(235, 231, 'home_services', NULL, 'Battery/Jump Start', 'battery-jump-start', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(236, 231, 'home_services', NULL, 'Car AC Service', 'car-ac-service', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(237, 130, 'home_services', NULL, 'Event & Catering', 'event-catering', NULL, 15, 'Inactive', '2026-08-19 08:44:55'),
(238, 237, 'home_services', NULL, 'Home Catering', 'home-catering', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(239, 237, 'home_services', NULL, 'Event Decoration', 'event-decoration', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(240, 237, 'home_services', NULL, 'Photography', 'photography', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(241, 237, 'home_services', NULL, 'DJ/Sound Setup', 'dj-sound-setup', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(242, 237, 'home_services', NULL, 'Tent/Chair Setup', 'tent-chair-setup', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(243, 130, 'home_services', NULL, 'Home Tutors', 'home-tutors', NULL, 16, 'Inactive', '2026-08-19 08:44:55'),
(244, 243, 'home_services', NULL, 'School Tuition', 'school-tuition', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(245, 243, 'home_services', NULL, 'Spoken English', 'spoken-english', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(246, 243, 'home_services', NULL, 'Music Class', 'music-class', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(247, 243, 'home_services', NULL, 'Dance Class', 'dance-class', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(248, 130, 'home_services', NULL, 'IT/Gadget Repair', 'it-gadget-repair', NULL, 17, 'Inactive', '2026-08-19 08:44:55'),
(249, 248, 'home_services', NULL, 'Laptop Repair', 'laptop-repair', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(250, 248, 'home_services', NULL, 'Mobile Repair', 'mobile-repair', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(251, 248, 'home_services', NULL, 'WiFi/Router Setup', 'wifi-router-setup', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(252, 248, 'home_services', NULL, 'Smart TV Installation', 'smart-tv-installation', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(253, 248, 'home_services', NULL, 'Computer AMC', 'computer-amc', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(254, 130, 'home_services', NULL, 'General Handyman', 'general-handyman', NULL, 18, 'Inactive', '2026-08-19 08:44:55'),
(255, 254, 'home_services', NULL, 'Furniture Assembly (Handyman)', 'furniture-assembly-handyman', NULL, 1, 'Inactive', '2026-08-19 08:44:55'),
(256, 254, 'home_services', NULL, 'Curtain Installation', 'curtain-installation', NULL, 2, 'Inactive', '2026-08-19 08:44:55'),
(257, 254, 'home_services', NULL, 'Wall Mounting (TV/Shelf)', 'wall-mounting-tv-shelf', NULL, 3, 'Inactive', '2026-08-19 08:44:55'),
(258, 254, 'home_services', NULL, 'General Repairs', 'general-repairs', NULL, 4, 'Inactive', '2026-08-19 08:44:55'),
(259, 254, 'home_services', NULL, 'Small Item Shifting', 'small-item-shifting', NULL, 5, 'Inactive', '2026-08-19 08:44:55'),
(260, 0, 'vendor', NULL, 'Vendors', 'vendors', 'bi-briefcase-fill', 0, 'Inactive', '2026-08-22 11:32:34'),
(261, 260, 'vendor', NULL, 'Architects', 'architects', NULL, 1, 'Inactive', '2026-08-22 11:32:34'),
(262, 260, 'vendor', NULL, 'Civil Contractors', 'civil-contractors', NULL, 2, 'Inactive', '2026-08-22 11:32:34'),
(263, 260, 'vendor', NULL, 'Interior Designers', 'interior-designers', NULL, 3, 'Inactive', '2026-08-22 11:32:34'),
(264, 260, 'vendor', NULL, 'Real Estate Agents/Brokers', 'real-estate-agents-brokers', NULL, 4, 'Inactive', '2026-08-22 11:32:34'),
(265, 260, 'vendor', NULL, 'Legal/Documentation Consultants', 'legal-documentation-consultants', NULL, 5, 'Inactive', '2026-08-22 11:32:34'),
(266, 260, 'vendor', NULL, 'Loan/Finance Advisors', 'loan-finance-advisors', NULL, 6, 'Inactive', '2026-08-22 11:32:34'),
(267, 260, 'vendor', NULL, 'Vastu Consultants', 'vastu-consultants', NULL, 7, 'Inactive', '2026-08-22 11:32:34'),
(268, 260, 'vendor', NULL, 'Structural Engineers', 'structural-engineers', NULL, 8, 'Inactive', '2026-08-22 11:32:34'),
(269, 0, 'interior', NULL, 'Interiors', 'interiors', 'bi-palette-fill', 0, 'Active', '2026-08-22 11:32:34'),
(270, 269, 'interior', NULL, 'Flooring (Tiles, Marble, Wood, Vinyl)', 'flooring', NULL, 1, 'Active', '2026-08-22 11:32:34'),
(271, 269, 'interior', NULL, 'Modular Kitchen', 'modular-kitchen', NULL, 2, 'Active', '2026-08-22 11:32:34'),
(272, 269, 'interior', NULL, 'Wardrobes', 'wardrobes', NULL, 3, 'Active', '2026-08-22 11:32:34'),
(273, 269, 'interior', NULL, 'False Ceiling', 'false-ceiling-interior', NULL, 4, 'Active', '2026-08-22 11:32:34'),
(274, 269, 'interior', NULL, 'Paints & Wall Finishes', 'paints-wall-finishes', NULL, 5, 'Active', '2026-08-22 11:32:34'),
(275, 269, 'interior', NULL, 'Sanitaryware & Bath Fittings', 'sanitaryware-bath-fittings', NULL, 6, 'Active', '2026-08-22 11:32:34'),
(276, 269, 'interior', NULL, 'Lighting', 'lighting', NULL, 7, 'Active', '2026-08-22 11:32:34'),
(277, 269, 'interior', NULL, 'Furniture', 'furniture', NULL, 8, 'Active', '2026-08-22 11:32:34'),
(278, 269, 'interior', NULL, 'Home Decor', 'home-decor', NULL, 9, 'Active', '2026-08-22 11:32:34'),
(279, 269, 'interior', NULL, 'Doors', 'doors', NULL, 1, 'Active', '2026-08-22 12:30:05'),
(280, 279, 'interior', NULL, 'Panel Door', 'panel-door', NULL, 1, 'Active', '2026-08-22 12:30:05'),
(281, 279, 'interior', NULL, 'Flush Door', 'flush-door', NULL, 2, 'Active', '2026-08-22 12:30:05'),
(282, 279, 'interior', NULL, 'Laminated Door', 'laminated-door', NULL, 3, 'Active', '2026-08-22 12:30:05'),
(283, 279, 'interior', NULL, 'Veneer Door', 'veneer-door', NULL, 4, 'Active', '2026-08-22 12:30:05'),
(284, 279, 'interior', NULL, 'Membrane Door', 'membrane-door', NULL, 5, 'Active', '2026-08-22 12:30:05'),
(285, 279, 'interior', NULL, 'PVC Door', 'pvc-door', NULL, 6, 'Active', '2026-08-22 12:30:05'),
(286, 279, 'interior', NULL, 'UPVC Door', 'upvc-door', NULL, 7, 'Active', '2026-08-22 12:30:05'),
(287, 279, 'interior', NULL, 'WPC Door', 'wpc-door', NULL, 8, 'Active', '2026-08-22 12:30:05'),
(288, 279, 'interior', NULL, 'Fiber / FRP Door', 'fiber-frp-door', NULL, 9, 'Active', '2026-08-22 12:30:05'),
(289, 279, 'interior', NULL, 'Plain Glass Door', 'plain-glass-door', NULL, 10, 'Active', '2026-08-22 12:30:05'),
(290, 279, 'interior', NULL, 'Frosted Glass Door', 'frosted-glass-door', NULL, 11, 'Active', '2026-08-22 12:30:05'),
(291, 279, 'interior', NULL, 'Toughened Glass Door', 'toughened-glass-door', NULL, 12, 'Active', '2026-08-22 12:30:05'),
(292, 279, 'interior', NULL, 'Sliding Door', 'sliding-door', NULL, 13, 'Active', '2026-08-22 12:30:05'),
(293, 279, 'interior', NULL, 'Folding Door', 'folding-door', NULL, 14, 'Active', '2026-08-22 12:30:05'),
(294, 279, 'interior', NULL, 'Bi-fold Door', 'bi-fold-door', NULL, 15, 'Active', '2026-08-22 12:30:05'),
(295, 279, 'interior', NULL, 'French Door', 'french-door', NULL, 16, 'Active', '2026-08-22 12:30:05'),
(296, 279, 'interior', NULL, 'Dutch Door', 'dutch-door', NULL, 17, 'Active', '2026-08-22 12:30:05'),
(297, 279, 'interior', NULL, 'Louvered Door', 'louvered-door', NULL, 18, 'Active', '2026-08-22 12:30:05'),
(298, 279, 'interior', NULL, 'Barn Door', 'barn-door', NULL, 19, 'Active', '2026-08-22 12:30:05'),
(299, 279, 'interior', NULL, 'Pocket Door', 'pocket-door', NULL, 20, 'Active', '2026-08-22 12:30:05'),
(300, 279, 'interior', NULL, 'Revolving Door', 'revolving-door', NULL, 21, 'Active', '2026-08-22 12:30:05'),
(301, 279, 'interior', NULL, 'Teak Wood Door', 'teak-wood-door', NULL, 22, 'Active', '2026-08-22 12:30:05'),
(302, 279, 'interior', NULL, 'Sal Wood Door', 'sal-wood-door', NULL, 23, 'Active', '2026-08-22 12:30:05'),
(303, 279, 'interior', NULL, 'Sheesham Wood Door', 'sheesham-wood-door', NULL, 24, 'Active', '2026-08-22 12:30:05'),
(304, 279, 'interior', NULL, 'Metal Door', 'metal-door', NULL, 25, 'Active', '2026-08-22 12:30:05'),
(305, 279, 'interior', NULL, 'Steel Door', 'steel-door', NULL, 26, 'Active', '2026-08-22 12:30:05'),
(306, 279, 'interior', NULL, 'Aluminium Door', 'aluminium-door', NULL, 27, 'Active', '2026-08-22 12:30:05'),
(307, 279, 'interior', NULL, 'Security / Safety Door', 'security-safety-door', NULL, 28, 'Active', '2026-08-22 12:30:05'),
(308, 279, 'interior', NULL, 'Fire Rated Door', 'fire-rated-door', NULL, 29, 'Active', '2026-08-22 12:30:05'),
(309, 279, 'interior', NULL, 'Soundproof Door', 'soundproof-door', NULL, 30, 'Active', '2026-08-22 12:30:05'),
(310, 279, 'interior', NULL, 'Waterproof Bathroom Door', 'waterproof-bathroom-door', NULL, 31, 'Active', '2026-08-22 12:30:05'),
(311, 279, 'interior', NULL, 'Main / Entrance Door', 'main-entrance-door', NULL, 32, 'Active', '2026-08-22 12:30:05'),
(312, 279, 'interior', NULL, 'Pooja Room Door', 'pooja-room-door', NULL, 33, 'Active', '2026-08-22 12:30:05'),
(313, 279, 'interior', NULL, 'Bedroom Door', 'bedroom-door', NULL, 34, 'Active', '2026-08-22 12:30:05'),
(314, 279, 'interior', NULL, 'Kitchen Door', 'kitchen-door', NULL, 35, 'Active', '2026-08-22 12:30:05'),
(315, 279, 'interior', NULL, 'Balcony Door', 'balcony-door', NULL, 36, 'Active', '2026-08-22 12:30:05'),
(316, 279, 'interior', NULL, 'Garage Door', 'garage-door', NULL, 37, 'Active', '2026-08-22 12:30:05'),
(317, 279, 'interior', NULL, 'Rolling Shutter Door', 'rolling-shutter-door', NULL, 38, 'Active', '2026-08-22 12:30:05'),
(318, 279, 'interior', NULL, 'Collapsible Gate', 'collapsible-gate', NULL, 39, 'Active', '2026-08-22 12:30:05'),
(319, 269, 'interior', NULL, 'Windows', 'windows', NULL, 2, 'Inactive', '2026-08-22 12:30:05'),
(320, 319, 'interior', NULL, 'Sliding Window', 'sliding-window', NULL, 1, 'Inactive', '2026-08-22 12:30:05'),
(321, 319, 'interior', NULL, 'Casement Window', 'casement-window', NULL, 2, 'Inactive', '2026-08-22 12:30:05'),
(322, 319, 'interior', NULL, 'Fixed Window', 'fixed-window', NULL, 3, 'Inactive', '2026-08-22 12:30:05'),
(323, 319, 'interior', NULL, 'Awning Window', 'awning-window', NULL, 4, 'Inactive', '2026-08-22 12:30:05'),
(324, 319, 'interior', NULL, 'Bay Window', 'bay-window', NULL, 5, 'Inactive', '2026-08-22 12:30:05'),
(325, 319, 'interior', NULL, 'Bow Window', 'bow-window', NULL, 6, 'Inactive', '2026-08-22 12:30:05'),
(326, 319, 'interior', NULL, 'Louvered Window', 'louvered-window', NULL, 7, 'Inactive', '2026-08-22 12:30:05'),
(327, 319, 'interior', NULL, 'Georgian Window', 'georgian-window', NULL, 8, 'Inactive', '2026-08-22 12:30:05'),
(328, 319, 'interior', NULL, 'Skylight Window', 'skylight-window', NULL, 9, 'Inactive', '2026-08-22 12:30:05'),
(329, 319, 'interior', NULL, 'Tilt & Turn Window', 'tilt-and-turn-window', NULL, 10, 'Inactive', '2026-08-22 12:30:05'),
(330, 319, 'interior', NULL, 'Double Hung Window', 'double-hung-window', NULL, 11, 'Inactive', '2026-08-22 12:30:05'),
(331, 319, 'interior', NULL, 'Single Hung Window', 'single-hung-window', NULL, 12, 'Inactive', '2026-08-22 12:30:05'),
(332, 319, 'interior', NULL, 'French Window', 'french-window', NULL, 13, 'Inactive', '2026-08-22 12:30:05'),
(333, 319, 'interior', NULL, 'UPVC Window', 'upvc-window', NULL, 14, 'Inactive', '2026-08-22 12:30:05'),
(334, 319, 'interior', NULL, 'Aluminium Window', 'aluminium-window', NULL, 15, 'Inactive', '2026-08-22 12:30:05'),
(335, 319, 'interior', NULL, 'Wooden Window', 'wooden-window', NULL, 16, 'Inactive', '2026-08-22 12:30:05'),
(336, 319, 'interior', NULL, 'Steel Window', 'steel-window', NULL, 17, 'Inactive', '2026-08-22 12:30:05'),
(337, 319, 'interior', NULL, 'Toughened Glass Window', 'toughened-glass-window', NULL, 18, 'Inactive', '2026-08-22 12:30:05'),
(338, 319, 'interior', NULL, 'Combination Window', 'combination-window', NULL, 19, 'Inactive', '2026-08-22 12:30:05'),
(339, 319, 'interior', NULL, 'Corner Window', 'corner-window', NULL, 20, 'Inactive', '2026-08-22 12:30:05'),
(340, 319, 'interior', NULL, 'Ventilator Window', 'ventilator-window', NULL, 21, 'Inactive', '2026-08-22 12:30:05'),
(341, 319, 'interior', NULL, 'Arch / Round Window', 'arch-round-window', NULL, 22, 'Inactive', '2026-08-22 12:30:05'),
(342, 319, 'interior', NULL, 'Frosted Bathroom Window', 'frosted-bathroom-window', NULL, 23, 'Inactive', '2026-08-22 12:30:05'),
(343, 319, 'interior', NULL, 'Mosquito Mesh Window', 'mosquito-mesh-window', NULL, 24, 'Inactive', '2026-08-22 12:30:05'),
(344, 270, 'interior', NULL, 'Vitrified Tiles', 'vitrified-tiles', NULL, 1, 'Active', '2026-08-22 12:30:05'),
(345, 270, 'interior', NULL, 'Ceramic Tiles', 'ceramic-tiles', NULL, 2, 'Active', '2026-08-22 12:30:05'),
(346, 270, 'interior', NULL, 'Porcelain Tiles', 'porcelain-tiles', NULL, 3, 'Active', '2026-08-22 12:30:05'),
(347, 270, 'interior', NULL, 'Marble Flooring', 'marble-flooring', NULL, 4, 'Active', '2026-08-22 12:30:05'),
(348, 270, 'interior', NULL, 'Granite Flooring', 'granite-flooring', NULL, 5, 'Active', '2026-08-22 12:30:05'),
(349, 270, 'interior', NULL, 'Italian Marble', 'italian-marble', NULL, 6, 'Active', '2026-08-22 12:30:05'),
(350, 270, 'interior', NULL, 'Kota Stone', 'kota-stone', NULL, 7, 'Active', '2026-08-22 12:30:05'),
(351, 270, 'interior', NULL, 'Cuddapah Stone', 'cuddapah-stone', NULL, 8, 'Active', '2026-08-22 12:30:05'),
(352, 270, 'interior', NULL, 'Wooden Flooring', 'wooden-flooring', NULL, 9, 'Active', '2026-08-22 12:30:05'),
(353, 270, 'interior', NULL, 'Laminate Flooring', 'laminate-flooring', NULL, 10, 'Active', '2026-08-22 12:30:05'),
(354, 270, 'interior', NULL, 'Engineered Wood Flooring', 'engineered-wood-flooring', NULL, 11, 'Active', '2026-08-22 12:30:05'),
(355, 270, 'interior', NULL, 'Vinyl Flooring', 'vinyl-flooring', NULL, 12, 'Active', '2026-08-22 12:30:05'),
(356, 270, 'interior', NULL, 'Epoxy Flooring', 'epoxy-flooring', NULL, 13, 'Active', '2026-08-22 12:30:05'),
(357, 270, 'interior', NULL, 'PVC Flooring', 'pvc-flooring', NULL, 14, 'Active', '2026-08-22 12:30:05'),
(358, 270, 'interior', NULL, 'Rubber Flooring', 'rubber-flooring', NULL, 15, 'Active', '2026-08-22 12:30:05'),
(359, 270, 'interior', NULL, 'Carpet Flooring', 'carpet-flooring', NULL, 16, 'Active', '2026-08-22 12:30:05'),
(360, 270, 'interior', NULL, 'Terrazzo Flooring', 'terrazzo-flooring', NULL, 17, 'Active', '2026-08-22 12:30:05'),
(361, 270, 'interior', NULL, 'Mosaic Flooring', 'mosaic-flooring', NULL, 18, 'Active', '2026-08-22 12:30:05'),
(362, 270, 'interior', NULL, 'IPS Flooring', 'ips-flooring', NULL, 19, 'Active', '2026-08-22 12:30:05'),
(363, 270, 'interior', NULL, 'Cement Flooring', 'cement-flooring', NULL, 20, 'Active', '2026-08-22 12:30:05'),
(364, 270, 'interior', NULL, 'Anti-skid Tiles', 'anti-skid-tiles', NULL, 21, 'Active', '2026-08-22 12:30:05'),
(365, 270, 'interior', NULL, 'Wooden Deck Flooring', 'wooden-deck-flooring', NULL, 22, 'Active', '2026-08-22 12:30:05'),
(366, 270, 'interior', NULL, 'Stone Cladding Flooring', 'stone-cladding-flooring', NULL, 23, 'Active', '2026-08-22 12:30:05'),
(367, 270, 'interior', NULL, 'Athangudi Tiles', 'athangudi-tiles', NULL, 24, 'Active', '2026-08-22 12:30:05'),
(368, 270, 'interior', NULL, 'Handmade Tiles', 'handmade-tiles', NULL, 25, 'Active', '2026-08-22 12:30:05'),
(369, 270, 'interior', NULL, 'Kitchen / Bathroom Tiles', 'kitchen-bathroom-tiles', NULL, 26, 'Active', '2026-08-22 12:30:05'),
(370, 274, 'interior', NULL, 'Emulsion Paint', 'emulsion-paint', NULL, 1, 'Active', '2026-08-22 12:30:05'),
(371, 274, 'interior', NULL, 'Distemper Paint', 'distemper-paint', NULL, 2, 'Active', '2026-08-22 12:30:05'),
(372, 274, 'interior', NULL, 'Texture Paint', 'texture-paint', NULL, 3, 'Active', '2026-08-22 12:30:05'),
(373, 274, 'interior', NULL, 'POP Finish', 'pop-finish', NULL, 4, 'Active', '2026-08-22 12:30:05'),
(374, 274, 'interior', NULL, 'Wallpaper', 'wallpaper', NULL, 5, 'Active', '2026-08-22 12:30:05'),
(375, 274, 'interior', NULL, 'Wood Wall Panelling', 'wood-wall-panelling', NULL, 6, 'Active', '2026-08-22 12:30:05'),
(376, 274, 'interior', NULL, 'PVC Wall Panel', 'pvc-wall-panel', NULL, 7, 'Active', '2026-08-22 12:30:05'),
(377, 274, 'interior', NULL, 'WPC Wall Panel', 'wpc-wall-panel', NULL, 8, 'Active', '2026-08-22 12:30:05'),
(378, 274, 'interior', NULL, 'Stone Cladding', 'stone-cladding', NULL, 9, 'Active', '2026-08-22 12:30:05'),
(379, 274, 'interior', NULL, 'Brick Cladding', 'brick-cladding', NULL, 10, 'Active', '2026-08-22 12:30:05'),
(380, 274, 'interior', NULL, 'Exposed Brick Wall', 'exposed-brick-wall', NULL, 11, 'Active', '2026-08-22 12:30:05'),
(381, 274, 'interior', NULL, '3D Wall Panel', '3d-wall-panel', NULL, 12, 'Active', '2026-08-22 12:30:05'),
(382, 274, 'interior', NULL, 'Fabric Wall Panel', 'fabric-wall-panel', NULL, 13, 'Active', '2026-08-22 12:30:05'),
(383, 274, 'interior', NULL, 'Mirror Wall Panel', 'mirror-wall-panel', NULL, 14, 'Active', '2026-08-22 12:30:05'),
(384, 274, 'interior', NULL, 'Glass Wall Panel', 'glass-wall-panel', NULL, 15, 'Active', '2026-08-22 12:30:05'),
(385, 274, 'interior', NULL, 'Wall Putty', 'wall-putty', NULL, 16, 'Active', '2026-08-22 12:30:05'),
(386, 274, 'interior', NULL, 'Waterproof Wall Coating', 'waterproof-wall-coating', NULL, 17, 'Active', '2026-08-22 12:30:05'),
(387, 274, 'interior', NULL, 'Metallic Paint Finish', 'metallic-paint-finish', NULL, 18, 'Active', '2026-08-22 12:30:05'),
(388, 274, 'interior', NULL, 'Textured Wall Art', 'textured-wall-art', NULL, 19, 'Active', '2026-08-22 12:30:05'),
(389, 273, 'interior', NULL, 'POP False Ceiling', 'pop-false-ceiling', NULL, 1, 'Active', '2026-08-22 12:30:05'),
(390, 273, 'interior', NULL, 'Gypsum False Ceiling', 'gypsum-false-ceiling', NULL, 2, 'Active', '2026-08-22 12:30:05'),
(391, 273, 'interior', NULL, 'PVC Ceiling', 'pvc-ceiling', NULL, 3, 'Active', '2026-08-22 12:30:05'),
(392, 273, 'interior', NULL, 'Wooden Ceiling', 'wooden-ceiling', NULL, 4, 'Active', '2026-08-22 12:30:05'),
(393, 273, 'interior', NULL, 'Metal Ceiling', 'metal-ceiling', NULL, 5, 'Active', '2026-08-22 12:30:05'),
(394, 273, 'interior', NULL, 'Grid Ceiling', 'grid-ceiling', NULL, 6, 'Active', '2026-08-22 12:30:05'),
(395, 273, 'interior', NULL, 'Stretch Ceiling', 'stretch-ceiling', NULL, 7, 'Active', '2026-08-22 12:30:05'),
(396, 273, 'interior', NULL, 'Coffered Ceiling', 'coffered-ceiling', NULL, 8, 'Active', '2026-08-22 12:30:05'),
(397, 273, 'interior', NULL, 'Cove Ceiling', 'cove-ceiling', NULL, 9, 'Active', '2026-08-22 12:30:05'),
(398, 273, 'interior', NULL, 'Tray Ceiling', 'tray-ceiling', NULL, 10, 'Active', '2026-08-22 12:30:05'),
(399, 273, 'interior', NULL, 'Wooden Beam Ceiling', 'wooden-beam-ceiling', NULL, 11, 'Active', '2026-08-22 12:30:05'),
(400, 273, 'interior', NULL, 'Acoustic Ceiling', 'acoustic-ceiling', NULL, 12, 'Active', '2026-08-22 12:30:05'),
(401, 271, 'interior', NULL, 'L-Shaped Kitchen', 'l-shaped-kitchen', NULL, 1, 'Active', '2026-08-22 12:30:05'),
(402, 271, 'interior', NULL, 'U-Shaped Kitchen', 'u-shaped-kitchen', NULL, 2, 'Active', '2026-08-22 12:30:05'),
(403, 271, 'interior', NULL, 'Straight Kitchen', 'straight-kitchen', NULL, 3, 'Active', '2026-08-22 12:30:05'),
(404, 271, 'interior', NULL, 'Parallel Kitchen', 'parallel-kitchen', NULL, 4, 'Active', '2026-08-22 12:30:05'),
(405, 271, 'interior', NULL, 'Island Kitchen', 'island-kitchen', NULL, 5, 'Active', '2026-08-22 12:30:05'),
(406, 271, 'interior', NULL, 'Open Kitchen', 'open-kitchen', NULL, 6, 'Active', '2026-08-22 12:30:05'),
(407, 271, 'interior', NULL, 'Closed Kitchen', 'closed-kitchen', NULL, 7, 'Active', '2026-08-22 12:30:05'),
(408, 271, 'interior', NULL, 'Laminate Kitchen Cabinets', 'laminate-kitchen-cabinets', NULL, 8, 'Active', '2026-08-22 12:30:05'),
(409, 271, 'interior', NULL, 'Acrylic Kitchen Cabinets', 'acrylic-kitchen-cabinets', NULL, 9, 'Active', '2026-08-22 12:30:05'),
(410, 271, 'interior', NULL, 'PU Finish Cabinets', 'pu-finish-cabinets', NULL, 10, 'Active', '2026-08-22 12:30:05'),
(411, 271, 'interior', NULL, 'Membrane Kitchen Cabinets', 'membrane-kitchen-cabinets', NULL, 11, 'Active', '2026-08-22 12:30:05'),
(412, 271, 'interior', NULL, 'Veneer Kitchen Cabinets', 'veneer-kitchen-cabinets', NULL, 12, 'Active', '2026-08-22 12:30:05'),
(413, 271, 'interior', NULL, 'Granite Countertop', 'granite-countertop', NULL, 13, 'Active', '2026-08-22 12:30:05'),
(414, 271, 'interior', NULL, 'Quartz Countertop', 'quartz-countertop', NULL, 14, 'Active', '2026-08-22 12:30:05'),
(415, 271, 'interior', NULL, 'Marble Countertop', 'marble-countertop', NULL, 15, 'Active', '2026-08-22 12:30:05'),
(416, 271, 'interior', NULL, 'Corian Countertop', 'corian-countertop', NULL, 16, 'Active', '2026-08-22 12:30:05'),
(417, 271, 'interior', NULL, 'Stainless Steel Countertop', 'stainless-steel-countertop', NULL, 17, 'Active', '2026-08-22 12:30:05'),
(418, 271, 'interior', NULL, 'Stainless Steel Sink', 'stainless-steel-sink', NULL, 18, 'Active', '2026-08-22 12:30:05'),
(419, 271, 'interior', NULL, 'Granite Sink', 'granite-sink', NULL, 19, 'Active', '2026-08-22 12:30:05'),
(420, 271, 'interior', NULL, 'Ceramic Sink', 'ceramic-sink', NULL, 20, 'Active', '2026-08-22 12:30:05'),
(421, 271, 'interior', NULL, 'Kitchen Chimney', 'kitchen-chimney', NULL, 21, 'Active', '2026-08-22 12:30:05'),
(422, 272, 'interior', NULL, 'Sliding Wardrobe', 'sliding-wardrobe', NULL, 1, 'Active', '2026-08-22 12:30:05'),
(423, 272, 'interior', NULL, 'Hinged Wardrobe', 'hinged-wardrobe', NULL, 2, 'Active', '2026-08-22 12:30:05'),
(424, 272, 'interior', NULL, 'Walk-in Wardrobe', 'walk-in-wardrobe', NULL, 3, 'Active', '2026-08-22 12:30:05'),
(425, 272, 'interior', NULL, 'Modular Wardrobe', 'modular-wardrobe', NULL, 4, 'Active', '2026-08-22 12:30:05'),
(426, 272, 'interior', NULL, 'Loft Wardrobe', 'loft-wardrobe', NULL, 5, 'Active', '2026-08-22 12:30:05'),
(427, 272, 'interior', NULL, 'Mirror Wardrobe', 'mirror-wardrobe', NULL, 6, 'Active', '2026-08-22 12:30:05'),
(428, 272, 'interior', NULL, 'Laminate Wardrobe', 'laminate-wardrobe', NULL, 7, 'Active', '2026-08-22 12:30:05'),
(429, 272, 'interior', NULL, 'Veneer Wardrobe', 'veneer-wardrobe', NULL, 8, 'Active', '2026-08-22 12:30:05'),
(430, 272, 'interior', NULL, 'Acrylic Wardrobe', 'acrylic-wardrobe', NULL, 9, 'Active', '2026-08-22 12:30:05'),
(431, 272, 'interior', NULL, 'PU Finish Wardrobe', 'pu-finish-wardrobe', NULL, 10, 'Active', '2026-08-22 12:30:05'),
(432, 272, 'interior', NULL, 'Shoe Rack Units', 'shoe-rack-units', NULL, 11, 'Active', '2026-08-22 12:30:05'),
(433, 272, 'interior', NULL, 'Crockery Units', 'crockery-units', NULL, 12, 'Active', '2026-08-22 12:30:05'),
(434, 275, 'interior', NULL, 'Wall-mounted WC', 'wall-mounted-wc', NULL, 1, 'Active', '2026-08-22 12:30:05'),
(435, 275, 'interior', NULL, 'Floor-mounted WC', 'floor-mounted-wc', NULL, 2, 'Active', '2026-08-22 12:30:05'),
(436, 275, 'interior', NULL, 'One-Piece WC', 'one-piece-wc', NULL, 3, 'Active', '2026-08-22 12:30:05'),
(437, 275, 'interior', NULL, 'Wash Basin', 'wash-basin', NULL, 4, 'Active', '2026-08-22 12:30:05'),
(438, 275, 'interior', NULL, 'Counter Basin', 'counter-basin', NULL, 5, 'Active', '2026-08-22 12:30:05'),
(439, 275, 'interior', NULL, 'Pedestal Basin', 'pedestal-basin', NULL, 6, 'Active', '2026-08-22 12:30:05'),
(440, 275, 'interior', NULL, 'Bathtub', 'bathtub', NULL, 7, 'Active', '2026-08-22 12:30:05'),
(441, 275, 'interior', NULL, 'Shower Enclosure', 'shower-enclosure', NULL, 8, 'Active', '2026-08-22 12:30:05'),
(442, 275, 'interior', NULL, 'Shower Panel', 'shower-panel', NULL, 9, 'Active', '2026-08-22 12:30:05'),
(443, 275, 'interior', NULL, 'Health Faucet', 'health-faucet', NULL, 10, 'Active', '2026-08-22 12:30:05'),
(444, 275, 'interior', NULL, 'Diverter / Mixer Tap', 'diverter-mixer-tap', NULL, 11, 'Active', '2026-08-22 12:30:05'),
(445, 275, 'interior', NULL, 'Rain Shower', 'rain-shower', NULL, 12, 'Active', '2026-08-22 12:30:05'),
(446, 275, 'interior', NULL, 'Geyser / Water Heater', 'geyser-water-heater', NULL, 13, 'Active', '2026-08-22 12:30:05'),
(447, 275, 'interior', NULL, 'Bathroom Vanity Unit', 'bathroom-vanity-unit', NULL, 14, 'Active', '2026-08-22 12:30:05'),
(448, 276, 'interior', NULL, 'Chandeliers', 'chandeliers', NULL, 1, 'Active', '2026-08-22 12:30:05'),
(449, 276, 'interior', NULL, 'Pendant Lights', 'pendant-lights', NULL, 2, 'Active', '2026-08-22 12:30:05'),
(450, 276, 'interior', NULL, 'LED Panel Lights', 'led-panel-lights', NULL, 3, 'Active', '2026-08-22 12:30:05');
INSERT INTO `property_categories` (`id`, `parent_id`, `category_type`, `menu_id`, `name`, `slug`, `icon`, `category_order`, `status`, `created_at`) VALUES
(451, 276, 'interior', NULL, 'Cove Lighting', 'cove-lighting', NULL, 4, 'Active', '2026-08-22 12:30:05'),
(452, 276, 'interior', NULL, 'Spotlights', 'spotlights', NULL, 5, 'Active', '2026-08-22 12:30:05'),
(453, 276, 'interior', NULL, 'Track Lighting', 'track-lighting', NULL, 6, 'Active', '2026-08-22 12:30:05'),
(454, 276, 'interior', NULL, 'Wall Sconces', 'wall-sconces', NULL, 7, 'Active', '2026-08-22 12:30:05'),
(455, 276, 'interior', NULL, 'Recessed Lighting', 'recessed-lighting', NULL, 8, 'Active', '2026-08-22 12:30:05'),
(456, 276, 'interior', NULL, 'Decorative Table Lamps', 'decorative-table-lamps', NULL, 9, 'Active', '2026-08-22 12:30:05'),
(457, 276, 'interior', NULL, 'Outdoor / Garden Lighting', 'outdoor-garden-lighting', NULL, 10, 'Active', '2026-08-22 12:30:05'),
(458, 276, 'interior', NULL, 'Smart Lighting', 'smart-lighting', NULL, 11, 'Active', '2026-08-22 12:30:05'),
(459, 277, 'interior', NULL, 'Sofa Sets', 'sofa-sets', NULL, 1, 'Active', '2026-08-22 12:30:05'),
(460, 277, 'interior', NULL, 'Dining Tables', 'dining-tables', NULL, 2, 'Active', '2026-08-22 12:30:05'),
(461, 277, 'interior', NULL, 'Beds', 'beds', NULL, 3, 'Active', '2026-08-22 12:30:05'),
(462, 277, 'interior', NULL, 'TV Units', 'tv-units', NULL, 4, 'Active', '2026-08-22 12:30:05'),
(463, 277, 'interior', NULL, 'Study Tables', 'study-tables', NULL, 5, 'Active', '2026-08-22 12:30:05'),
(464, 277, 'interior', NULL, 'Bookshelves', 'bookshelves', NULL, 6, 'Active', '2026-08-22 12:30:05'),
(465, 277, 'interior', NULL, 'Recliners', 'recliners', NULL, 7, 'Active', '2026-08-22 12:30:05'),
(466, 277, 'interior', NULL, 'Ottomans', 'ottomans', NULL, 8, 'Active', '2026-08-22 12:30:05'),
(467, 277, 'interior', NULL, 'Coffee Tables', 'coffee-tables', NULL, 9, 'Active', '2026-08-22 12:30:05'),
(468, 277, 'interior', NULL, 'Console Tables', 'console-tables', NULL, 10, 'Active', '2026-08-22 12:30:05'),
(469, 269, 'interior', NULL, 'Curtains, Blinds & Soft Furnishing', 'curtains-blinds-and-soft-furnishing', NULL, 11, 'Inactive', '2026-08-22 12:30:05'),
(470, 469, 'interior', NULL, 'Roller Blinds', 'roller-blinds', NULL, 1, 'Inactive', '2026-08-22 12:30:05'),
(471, 469, 'interior', NULL, 'Roman Blinds', 'roman-blinds', NULL, 2, 'Inactive', '2026-08-22 12:30:05'),
(472, 469, 'interior', NULL, 'Vertical Blinds', 'vertical-blinds', NULL, 3, 'Inactive', '2026-08-22 12:30:05'),
(473, 469, 'interior', NULL, 'Wooden Blinds', 'wooden-blinds', NULL, 4, 'Inactive', '2026-08-22 12:30:05'),
(474, 469, 'interior', NULL, 'Sheer Curtains', 'sheer-curtains', NULL, 5, 'Inactive', '2026-08-22 12:30:05'),
(475, 469, 'interior', NULL, 'Blackout Curtains', 'blackout-curtains', NULL, 6, 'Inactive', '2026-08-22 12:30:05'),
(476, 469, 'interior', NULL, 'Motorised Curtains', 'motorised-curtains', NULL, 7, 'Inactive', '2026-08-22 12:30:05'),
(477, 469, 'interior', NULL, 'Cushion Covers', 'cushion-covers', NULL, 8, 'Inactive', '2026-08-22 12:30:05'),
(478, 469, 'interior', NULL, 'Bed Linen', 'bed-linen', NULL, 9, 'Inactive', '2026-08-22 12:30:05'),
(479, 269, 'interior', NULL, 'Hardware & Fittings', 'hardware-and-fittings', NULL, 12, 'Inactive', '2026-08-22 12:30:05'),
(480, 479, 'interior', NULL, 'Door Handles & Locks', 'door-handles-and-locks', NULL, 1, 'Inactive', '2026-08-22 12:30:05'),
(481, 479, 'interior', NULL, 'Hinges', 'hinges', NULL, 2, 'Inactive', '2026-08-22 12:30:05'),
(482, 479, 'interior', NULL, 'Cabinet Handles', 'cabinet-handles', NULL, 3, 'Inactive', '2026-08-22 12:30:05'),
(483, 479, 'interior', NULL, 'Drawer Channels', 'drawer-channels', NULL, 4, 'Inactive', '2026-08-22 12:30:05'),
(484, 479, 'interior', NULL, 'Soft-close Fittings', 'soft-close-fittings', NULL, 5, 'Inactive', '2026-08-22 12:30:05'),
(485, 479, 'interior', NULL, 'Door Closers', 'door-closers', NULL, 6, 'Inactive', '2026-08-22 12:30:05'),
(486, 479, 'interior', NULL, 'Magic Corner Fittings', 'magic-corner-fittings', NULL, 7, 'Inactive', '2026-08-22 12:30:05'),
(487, 479, 'interior', NULL, 'Wardrobe Accessories', 'wardrobe-accessories', NULL, 8, 'Inactive', '2026-08-22 12:30:05'),
(488, 260, 'vendor', NULL, 'Design & Consultancy', 'design-and-consultancy', NULL, 9, 'Inactive', '2026-08-22 12:30:05'),
(489, 488, 'vendor', NULL, 'Landscape Designer', 'landscape-designer', NULL, 1, 'Inactive', '2026-08-22 12:30:05'),
(490, 488, 'vendor', NULL, 'Site Engineer', 'site-engineer', NULL, 2, 'Inactive', '2026-08-22 12:30:05'),
(491, 488, 'vendor', NULL, 'Land Surveyor', 'land-surveyor', NULL, 3, 'Inactive', '2026-08-22 12:30:05'),
(492, 488, 'vendor', NULL, 'Soil Testing Engineer', 'soil-testing-engineer', NULL, 4, 'Inactive', '2026-08-22 12:30:05'),
(493, 488, 'vendor', NULL, 'Chartered Engineer', 'chartered-engineer', NULL, 5, 'Inactive', '2026-08-22 12:30:05'),
(494, 488, 'vendor', NULL, 'Property Valuer', 'property-valuer', NULL, 6, 'Inactive', '2026-08-22 12:30:05'),
(495, 488, 'vendor', NULL, 'RERA Consultant', 'rera-consultant', NULL, 7, 'Inactive', '2026-08-22 12:30:05'),
(496, 488, 'vendor', NULL, '3D Rendering / Walkthrough Vendor', '3d-rendering-walkthrough-vendor', NULL, 8, 'Inactive', '2026-08-22 12:30:05'),
(497, 488, 'vendor', NULL, 'Drone Survey Vendor', 'drone-survey-vendor', NULL, 9, 'Inactive', '2026-08-22 12:30:05'),
(498, 488, 'vendor', NULL, 'GPS Land Survey Vendor', 'gps-land-survey-vendor', NULL, 10, 'Inactive', '2026-08-22 12:30:05'),
(499, 260, 'vendor', NULL, 'Civil & Structural Contractors', 'civil-and-structural-contractors', NULL, 10, 'Inactive', '2026-08-22 12:30:05'),
(500, 499, 'vendor', NULL, 'Turnkey Contractor', 'turnkey-contractor', NULL, 1, 'Inactive', '2026-08-22 12:30:05'),
(501, 499, 'vendor', NULL, 'Excavation Contractor', 'excavation-contractor', NULL, 2, 'Inactive', '2026-08-22 12:30:05'),
(502, 499, 'vendor', NULL, 'Earthwork Contractor', 'earthwork-contractor', NULL, 3, 'Inactive', '2026-08-22 12:30:05'),
(503, 499, 'vendor', NULL, 'Piling Contractor', 'piling-contractor', NULL, 4, 'Inactive', '2026-08-22 12:30:05'),
(504, 499, 'vendor', NULL, 'Foundation Contractor', 'foundation-contractor', NULL, 5, 'Inactive', '2026-08-22 12:30:05'),
(505, 499, 'vendor', NULL, 'Masonry Contractor', 'masonry-contractor', NULL, 6, 'Inactive', '2026-08-22 12:30:05'),
(506, 499, 'vendor', NULL, 'Brickwork Contractor', 'brickwork-contractor', NULL, 7, 'Inactive', '2026-08-22 12:30:05'),
(507, 499, 'vendor', NULL, 'Plastering Contractor', 'plastering-contractor', NULL, 8, 'Inactive', '2026-08-22 12:30:05'),
(508, 499, 'vendor', NULL, 'Demolition Contractor', 'demolition-contractor', NULL, 9, 'Inactive', '2026-08-22 12:30:05'),
(509, 499, 'vendor', NULL, 'Scaffolding Rental', 'scaffolding-rental', NULL, 10, 'Inactive', '2026-08-22 12:30:05'),
(510, 499, 'vendor', NULL, 'Crane Rental', 'crane-rental', NULL, 11, 'Inactive', '2026-08-22 12:30:05'),
(511, 499, 'vendor', NULL, 'JCB / Excavator Rental', 'jcb-excavator-rental', NULL, 12, 'Inactive', '2026-08-22 12:30:05'),
(512, 499, 'vendor', NULL, 'Concrete Mixer Rental', 'concrete-mixer-rental', NULL, 13, 'Inactive', '2026-08-22 12:30:05'),
(513, 499, 'vendor', NULL, 'Labour Contractor', 'labour-contractor', NULL, 14, 'Inactive', '2026-08-22 12:30:05'),
(514, 499, 'vendor', NULL, 'RCC Contractor', 'rcc-contractor', NULL, 15, 'Inactive', '2026-08-22 12:30:05'),
(515, 260, 'vendor', NULL, 'MEP (Mechanical, Electrical, Plumbing)', 'mep-mechanical-electrical-plumbing', NULL, 11, 'Inactive', '2026-08-22 12:30:05'),
(516, 515, 'vendor', NULL, 'Plumbing Contractor', 'plumbing-contractor', NULL, 1, 'Inactive', '2026-08-22 12:30:05'),
(517, 515, 'vendor', NULL, 'Electrical Contractor', 'electrical-contractor', NULL, 2, 'Inactive', '2026-08-22 12:30:05'),
(518, 515, 'vendor', NULL, 'HVAC Contractor', 'hvac-contractor', NULL, 3, 'Inactive', '2026-08-22 12:30:05'),
(519, 515, 'vendor', NULL, 'Fire Fighting Contractor', 'fire-fighting-contractor', NULL, 4, 'Inactive', '2026-08-22 12:30:05'),
(520, 515, 'vendor', NULL, 'Lift / Elevator Vendor', 'lift-elevator-vendor', NULL, 5, 'Inactive', '2026-08-22 12:30:05'),
(521, 515, 'vendor', NULL, 'Solar Panel Installer', 'solar-panel-installer', NULL, 6, 'Inactive', '2026-08-22 12:30:05'),
(522, 515, 'vendor', NULL, 'Borewell Drilling Contractor', 'borewell-drilling-contractor', NULL, 7, 'Inactive', '2026-08-22 12:30:05'),
(523, 515, 'vendor', NULL, 'Water Tank Supplier', 'water-tank-supplier', NULL, 8, 'Inactive', '2026-08-22 12:30:05'),
(524, 515, 'vendor', NULL, 'STP / Sewage Treatment Vendor', 'stp-sewage-treatment-vendor', NULL, 9, 'Inactive', '2026-08-22 12:30:05'),
(525, 515, 'vendor', NULL, 'CCTV / Security System Vendor', 'cctv-security-system-vendor', NULL, 10, 'Inactive', '2026-08-22 12:30:05'),
(526, 515, 'vendor', NULL, 'Home Automation Vendor', 'home-automation-vendor', NULL, 11, 'Inactive', '2026-08-22 12:30:05'),
(527, 260, 'vendor', NULL, 'Finishing Contractors', 'finishing-contractors', NULL, 12, 'Inactive', '2026-08-22 12:30:05'),
(528, 527, 'vendor', NULL, 'Painting Contractor', 'painting-contractor', NULL, 1, 'Inactive', '2026-08-22 12:30:05'),
(529, 527, 'vendor', NULL, 'Waterproofing Contractor', 'waterproofing-contractor', NULL, 2, 'Inactive', '2026-08-22 12:30:05'),
(530, 527, 'vendor', NULL, 'Tile Contractor', 'tile-contractor', NULL, 3, 'Inactive', '2026-08-22 12:30:05'),
(531, 527, 'vendor', NULL, 'Flooring Contractor', 'flooring-contractor', NULL, 4, 'Inactive', '2026-08-22 12:30:05'),
(532, 527, 'vendor', NULL, 'False Ceiling Contractor', 'false-ceiling-contractor', NULL, 5, 'Inactive', '2026-08-22 12:30:05'),
(533, 527, 'vendor', NULL, 'Carpentry Contractor', 'carpentry-contractor', NULL, 6, 'Inactive', '2026-08-22 12:30:05'),
(534, 527, 'vendor', NULL, 'Modular Kitchen Vendor', 'modular-kitchen-vendor', NULL, 7, 'Inactive', '2026-08-22 12:30:05'),
(535, 527, 'vendor', NULL, 'Wardrobe Vendor', 'wardrobe-vendor', NULL, 8, 'Inactive', '2026-08-22 12:30:05'),
(536, 527, 'vendor', NULL, 'Glass & Glazing Vendor', 'glass-and-glazing-vendor', NULL, 9, 'Inactive', '2026-08-22 12:30:05'),
(537, 527, 'vendor', NULL, 'Aluminium Fabricator', 'aluminium-fabricator', NULL, 10, 'Inactive', '2026-08-22 12:30:05'),
(538, 527, 'vendor', NULL, 'UPVC Window Vendor', 'upvc-window-vendor', NULL, 11, 'Inactive', '2026-08-22 12:30:05'),
(539, 527, 'vendor', NULL, 'Door Vendor', 'door-vendor', NULL, 12, 'Inactive', '2026-08-22 12:30:05'),
(540, 527, 'vendor', NULL, 'Grill / Fabrication Vendor', 'grill-fabrication-vendor', NULL, 13, 'Inactive', '2026-08-22 12:30:05'),
(541, 527, 'vendor', NULL, 'Welding Contractor', 'welding-contractor', NULL, 14, 'Inactive', '2026-08-22 12:30:05'),
(542, 527, 'vendor', NULL, 'Roofing Contractor', 'roofing-contractor', NULL, 15, 'Inactive', '2026-08-22 12:30:05'),
(543, 527, 'vendor', NULL, 'Terrace Waterproofing Vendor', 'terrace-waterproofing-vendor', NULL, 16, 'Inactive', '2026-08-22 12:30:05'),
(544, 527, 'vendor', NULL, 'Paver Block Contractor', 'paver-block-contractor', NULL, 17, 'Inactive', '2026-08-22 12:30:05'),
(545, 527, 'vendor', NULL, 'Compound Wall Contractor', 'compound-wall-contractor', NULL, 18, 'Inactive', '2026-08-22 12:30:05'),
(546, 527, 'vendor', NULL, 'Swimming Pool Contractor', 'swimming-pool-contractor', NULL, 19, 'Inactive', '2026-08-22 12:30:05'),
(547, 527, 'vendor', NULL, 'Landscaping Contractor', 'landscaping-contractor', NULL, 20, 'Inactive', '2026-08-22 12:30:05'),
(548, 260, 'vendor', NULL, 'Material Suppliers', 'material-suppliers', NULL, 13, 'Inactive', '2026-08-22 12:30:05'),
(549, 548, 'vendor', NULL, 'Concrete (RMC) Supplier', 'concrete-rmc-supplier', NULL, 1, 'Inactive', '2026-08-22 12:30:05'),
(550, 548, 'vendor', NULL, 'Steel (TMT) Supplier', 'steel-tmt-supplier', NULL, 2, 'Inactive', '2026-08-22 12:30:05'),
(551, 548, 'vendor', NULL, 'Cement Dealer', 'cement-dealer', NULL, 3, 'Inactive', '2026-08-22 12:30:05'),
(552, 548, 'vendor', NULL, 'Sand Supplier', 'sand-supplier', NULL, 4, 'Inactive', '2026-08-22 12:30:05'),
(553, 548, 'vendor', NULL, 'Aggregate Supplier', 'aggregate-supplier', NULL, 5, 'Inactive', '2026-08-22 12:30:05'),
(554, 548, 'vendor', NULL, 'Bricks Supplier', 'bricks-supplier', NULL, 6, 'Inactive', '2026-08-22 12:30:05'),
(555, 548, 'vendor', NULL, 'AAC / Fly Ash Blocks Supplier', 'aac-fly-ash-blocks-supplier', NULL, 7, 'Inactive', '2026-08-22 12:30:05'),
(556, 548, 'vendor', NULL, 'Granite / Marble Supplier', 'granite-marble-supplier', NULL, 8, 'Inactive', '2026-08-22 12:30:05'),
(557, 548, 'vendor', NULL, 'Hardware Store', 'hardware-store', NULL, 9, 'Inactive', '2026-08-22 12:30:05'),
(558, 548, 'vendor', NULL, 'Sanitary Ware Supplier', 'sanitary-ware-supplier', NULL, 10, 'Inactive', '2026-08-22 12:30:05'),
(559, 548, 'vendor', NULL, 'CP Fittings Supplier', 'cp-fittings-supplier', NULL, 11, 'Inactive', '2026-08-22 12:30:05'),
(560, 548, 'vendor', NULL, 'Electrical Fittings Supplier', 'electrical-fittings-supplier', NULL, 12, 'Inactive', '2026-08-22 12:30:05'),
(561, 548, 'vendor', NULL, 'Switch & Wire Supplier', 'switch-and-wire-supplier', NULL, 13, 'Inactive', '2026-08-22 12:30:05'),
(562, 548, 'vendor', NULL, 'Cable Supplier', 'cable-supplier', NULL, 14, 'Inactive', '2026-08-22 12:30:05'),
(563, 548, 'vendor', NULL, 'Paint Brands Dealer', 'paint-brands-dealer', NULL, 15, 'Inactive', '2026-08-22 12:30:05'),
(564, 548, 'vendor', NULL, 'Waterproofing Chemical Supplier', 'waterproofing-chemical-supplier', NULL, 16, 'Inactive', '2026-08-22 12:30:05'),
(565, 260, 'vendor', NULL, 'Allied Services & Agents', 'allied-services-and-agents', NULL, 14, 'Inactive', '2026-08-22 12:30:05'),
(566, 565, 'vendor', NULL, 'Termite Control Vendor', 'termite-control-vendor', NULL, 1, 'Inactive', '2026-08-22 12:30:05'),
(567, 565, 'vendor', NULL, 'Pest Control Vendor', 'pest-control-vendor', NULL, 2, 'Inactive', '2026-08-22 12:30:05'),
(568, 565, 'vendor', NULL, 'Property Legal Documentation Vendor', 'property-legal-documentation-vendor', NULL, 3, 'Inactive', '2026-08-22 12:30:05'),
(569, 565, 'vendor', NULL, 'Home Loan / DSA Agent', 'home-loan-dsa-agent', NULL, 4, 'Inactive', '2026-08-22 12:30:05'),
(570, 565, 'vendor', NULL, 'Property Insurance Agent', 'property-insurance-agent', NULL, 5, 'Inactive', '2026-08-22 12:30:05'),
(571, 565, 'vendor', NULL, 'Packers & Movers', 'packers-and-movers', NULL, 6, 'Inactive', '2026-08-22 12:30:05'),
(572, 565, 'vendor', NULL, 'Property Photographer', 'property-photographer', NULL, 7, 'Inactive', '2026-08-22 12:30:05'),
(573, 565, 'vendor', NULL, 'Liaisoning Agent (Govt. Approvals)', 'liaisoning-agent-govt-approvals', NULL, 8, 'Inactive', '2026-08-22 12:30:05'),
(10000, 0, 'buy', NULL, 'Buy', 'buy', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10001, 10000, 'buy', NULL, 'Residential Properties', 'buy-residential-properties', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10002, 10001, 'buy', NULL, 'Apartment / Flat', 'buy-apartment-flat', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10003, 10001, 'buy', NULL, 'Villa', 'buy-villa', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10004, 10001, 'buy', NULL, 'Independent House', 'buy-independent-house', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10005, 10001, 'buy', NULL, 'Row House', 'buy-row-house', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10006, 10001, 'buy', NULL, 'Duplex House', 'buy-duplex-house', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10007, 10001, 'buy', NULL, 'Triplex House', 'buy-triplex-house', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10008, 10001, 'buy', NULL, 'Penthouse', 'buy-penthouse', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10009, 10001, 'buy', NULL, 'Studio Apartment', 'buy-studio-apartment', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10010, 10001, 'buy', NULL, 'Service Apartment', 'buy-service-apartment', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10011, 10001, 'buy', NULL, 'Builder Floor', 'buy-builder-floor', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10012, 10001, 'buy', NULL, 'Farmhouse', 'buy-farmhouse', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10013, 10001, 'buy', NULL, 'Bungalow', 'buy-bungalow', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10014, 10001, 'buy', NULL, 'Cottage', 'buy-cottage', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10015, 10001, 'buy', NULL, 'Gated Community Villa', 'buy-gated-community-villa', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10016, 10001, 'buy', NULL, 'Township Apartment', 'buy-township-apartment', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10017, 10001, 'buy', NULL, 'Senior Living Apartment', 'buy-senior-living-apartment', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10018, 10001, 'buy', NULL, 'Co-living Apartment', 'buy-co-living-apartment', NULL, 17, 'Active', '2026-08-23 06:56:46'),
(10019, 10001, 'buy', NULL, '1 RK', 'buy-1-rk', NULL, 18, 'Active', '2026-08-23 06:56:46'),
(10020, 10001, 'buy', NULL, '1 BHK', 'buy-1-bhk', NULL, 19, 'Active', '2026-08-23 06:56:46'),
(10021, 10001, 'buy', NULL, '2 BHK', 'buy-2-bhk', NULL, 20, 'Active', '2026-08-23 06:56:46'),
(10022, 10001, 'buy', NULL, '3 BHK', 'buy-3-bhk', NULL, 21, 'Active', '2026-08-23 06:56:46'),
(10023, 10001, 'buy', NULL, '4 BHK', 'buy-4-bhk', NULL, 22, 'Active', '2026-08-23 06:56:46'),
(10024, 10001, 'buy', NULL, '4+ BHK / Luxury Apartment', 'buy-4-bhk-luxury-apartment', NULL, 23, 'Active', '2026-08-23 06:56:46'),
(10025, 10001, 'buy', NULL, 'Duplex Villa', 'buy-duplex-villa', NULL, 24, 'Active', '2026-08-23 06:56:46'),
(10026, 10001, 'buy', NULL, 'Twin Villa', 'buy-twin-villa', NULL, 25, 'Active', '2026-08-23 06:56:46'),
(10027, 10001, 'buy', NULL, 'Semi-Independent House', 'buy-semi-independent-house', NULL, 26, 'Active', '2026-08-23 06:56:46'),
(10028, 10001, 'buy', NULL, 'Row Villa', 'buy-row-villa', NULL, 27, 'Active', '2026-08-23 06:56:46'),
(10029, 10000, 'buy', NULL, 'Plots / Open Land (Residential)', 'buy-plots-open-land-residential', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10030, 10029, 'buy', NULL, 'Residential Open Plot', 'buy-residential-open-plot', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10031, 10029, 'buy', NULL, 'Corner Plot', 'buy-corner-plot', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10032, 10029, 'buy', NULL, 'Gated Community Plot', 'buy-gated-community-plot', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10033, 10029, 'buy', NULL, 'HMDA/DTCP Approved Plot', 'buy-hmda-dtcp-approved-plot', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10034, 10029, 'buy', NULL, 'RERA Approved Plot', 'buy-rera-approved-plot', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10035, 10029, 'buy', NULL, 'Panchayat Approved Plot', 'buy-panchayat-approved-plot', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10036, 10029, 'buy', NULL, 'East Facing Plot', 'buy-east-facing-plot', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10037, 10029, 'buy', NULL, 'West Facing Plot', 'buy-west-facing-plot', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10038, 10029, 'buy', NULL, 'North Facing Plot', 'buy-north-facing-plot', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10039, 10029, 'buy', NULL, 'South Facing Plot', 'buy-south-facing-plot', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10040, 10029, 'buy', NULL, 'Vaastu Compliant Plot', 'buy-vaastu-compliant-plot', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10041, 10029, 'buy', NULL, 'Farm Plot', 'buy-farm-plot', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10042, 10029, 'buy', NULL, 'Layout Plot', 'buy-layout-plot', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10043, 10029, 'buy', NULL, 'Corner Layout Plot', 'buy-corner-layout-plot', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10044, 10029, 'buy', NULL, 'Park Facing Plot', 'buy-park-facing-plot', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10045, 10029, 'buy', NULL, 'Road Facing Plot', 'buy-road-facing-plot', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10046, 10000, 'buy', NULL, 'Commercial Properties', 'buy-commercial-properties', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10047, 10046, 'buy', NULL, 'Office Space', 'buy-office-space', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10048, 10046, 'buy', NULL, 'IT Office Space', 'buy-it-office-space', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10049, 10046, 'buy', NULL, 'Co-working Space', 'buy-co-working-space', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10050, 10046, 'buy', NULL, 'Shop', 'buy-shop', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10051, 10046, 'buy', NULL, 'Showroom', 'buy-showroom', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10052, 10046, 'buy', NULL, 'Retail Space', 'buy-retail-space', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10053, 10046, 'buy', NULL, 'Mall Space', 'buy-mall-space', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10054, 10046, 'buy', NULL, 'Shopping Complex Unit', 'buy-shopping-complex-unit', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10055, 10046, 'buy', NULL, 'Commercial Plot', 'buy-commercial-plot', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10056, 10046, 'buy', NULL, 'Commercial Building', 'buy-commercial-building', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10057, 10046, 'buy', NULL, 'Warehouse', 'buy-warehouse', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10058, 10046, 'buy', NULL, 'Godown', 'buy-godown', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10059, 10046, 'buy', NULL, 'Cold Storage', 'buy-cold-storage', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10060, 10046, 'buy', NULL, 'Industrial Shed', 'buy-industrial-shed', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10061, 10046, 'buy', NULL, 'Industrial Plot', 'buy-industrial-plot', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10062, 10046, 'buy', NULL, 'Industrial Building', 'buy-industrial-building', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10063, 10046, 'buy', NULL, 'Factory', 'buy-factory', NULL, 17, 'Active', '2026-08-23 06:56:46'),
(10064, 10046, 'buy', NULL, 'Manufacturing Unit', 'buy-manufacturing-unit', NULL, 18, 'Active', '2026-08-23 06:56:46'),
(10065, 10046, 'buy', NULL, 'Restaurant Space', 'buy-restaurant-space', NULL, 19, 'Active', '2026-08-23 06:56:46'),
(10066, 10046, 'buy', NULL, 'Hotel Property', 'buy-hotel-property', NULL, 20, 'Active', '2026-08-23 06:56:46'),
(10067, 10046, 'buy', NULL, 'Resort Property', 'buy-resort-property', NULL, 21, 'Active', '2026-08-23 06:56:46'),
(10068, 10046, 'buy', NULL, 'Banquet Hall', 'buy-banquet-hall', NULL, 22, 'Active', '2026-08-23 06:56:46'),
(10069, 10046, 'buy', NULL, 'Function Hall', 'buy-function-hall', NULL, 23, 'Active', '2026-08-23 06:56:46'),
(10070, 10046, 'buy', NULL, 'Cinema / Theatre Property', 'buy-cinema-theatre-property', NULL, 24, 'Active', '2026-08-23 06:56:46'),
(10071, 10046, 'buy', NULL, 'Hospital Building', 'buy-hospital-building', NULL, 25, 'Active', '2026-08-23 06:56:46'),
(10072, 10046, 'buy', NULL, 'Clinic Space', 'buy-clinic-space', NULL, 26, 'Active', '2026-08-23 06:56:46'),
(10073, 10046, 'buy', NULL, 'School Building', 'buy-school-building', NULL, 27, 'Active', '2026-08-23 06:56:46'),
(10074, 10046, 'buy', NULL, 'College Building', 'buy-college-building', NULL, 28, 'Active', '2026-08-23 06:56:46'),
(10075, 10046, 'buy', NULL, 'Petrol Bunk Land', 'buy-petrol-bunk-land', NULL, 29, 'Active', '2026-08-23 06:56:46'),
(10076, 10046, 'buy', NULL, 'Highway Commercial Land', 'buy-highway-commercial-land', NULL, 30, 'Active', '2026-08-23 06:56:46'),
(10077, 10046, 'buy', NULL, 'Data Center Space', 'buy-data-center-space', NULL, 31, 'Active', '2026-08-23 06:56:46'),
(10078, 10046, 'buy', NULL, 'Bank / ATM Space', 'buy-bank-atm-space', NULL, 32, 'Active', '2026-08-23 06:56:46'),
(10079, 10046, 'buy', NULL, 'Bare Shell Office', 'buy-bare-shell-office', NULL, 33, 'Active', '2026-08-23 06:56:46'),
(10080, 10046, 'buy', NULL, 'Fitted-out Office', 'buy-fitted-out-office', NULL, 34, 'Active', '2026-08-23 06:56:46'),
(10081, 10000, 'buy', NULL, 'Agricultural & Farm Land', 'buy-agricultural-farm-land', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10082, 10081, 'buy', NULL, 'Agricultural Land', 'buy-agricultural-land', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10083, 10081, 'buy', NULL, 'Farm Land', 'buy-farm-land', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10084, 10081, 'buy', NULL, 'Orchard Land', 'buy-orchard-land', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10085, 10081, 'buy', NULL, 'Plantation Land', 'buy-plantation-land', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10086, 10081, 'buy', NULL, 'Mango Grove', 'buy-mango-grove', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10087, 10081, 'buy', NULL, 'Coconut Grove', 'buy-coconut-grove', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10088, 10081, 'buy', NULL, 'Poultry Farm Land', 'buy-poultry-farm-land', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10089, 10081, 'buy', NULL, 'Dairy Farm Land', 'buy-dairy-farm-land', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10090, 10081, 'buy', NULL, 'Fishery Land', 'buy-fishery-land', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10091, 10081, 'buy', NULL, 'Government Assigned Land', 'buy-government-assigned-land', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10092, 10081, 'buy', NULL, 'Patta Land', 'buy-patta-land', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10093, 10081, 'buy', NULL, 'Non-Agricultural (NA) Converted Land', 'buy-non-agricultural-na-converted-land', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10094, 10081, 'buy', NULL, 'Irrigated Land', 'buy-irrigated-land', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10095, 10081, 'buy', NULL, 'Dry Land', 'buy-dry-land', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10096, 10000, 'buy', NULL, 'Special / Other Categories', 'buy-special-other-categories', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10097, 10096, 'buy', NULL, 'New Launch Property', 'buy-new-launch-property', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10098, 10096, 'buy', NULL, 'Under Construction Property', 'buy-under-construction-property', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10099, 10096, 'buy', NULL, 'Ready to Move Property', 'buy-ready-to-move-property', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10100, 10096, 'buy', NULL, 'Pre-Launch Property', 'buy-pre-launch-property', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10101, 10096, 'buy', NULL, 'Resale Property', 'buy-resale-property', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10102, 10096, 'buy', NULL, 'Redevelopment Property', 'buy-redevelopment-property', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10103, 10096, 'buy', NULL, 'Auction Property', 'buy-auction-property', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10104, 10096, 'buy', NULL, 'Bank Auction / SARFAESI Property', 'buy-bank-auction-sarfaesi-property', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10105, 10096, 'buy', NULL, 'Distress Sale Property', 'buy-distress-sale-property', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10106, 10096, 'buy', NULL, 'NRI Property', 'buy-nri-property', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10107, 10096, 'buy', NULL, 'Joint Venture Property', 'buy-joint-venture-property', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10108, 10096, 'buy', NULL, 'Land for Joint Development', 'buy-land-for-joint-development', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10109, 10096, 'buy', NULL, 'Builder Direct Property', 'buy-builder-direct-property', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10110, 0, 'rent', NULL, 'Rent', 'rent', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10111, 10110, 'rent', NULL, 'Residential Rentals', 'rent-residential-rentals', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10112, 10111, 'rent', NULL, '1 RK for Rent', 'rent-1-rk-for-rent', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10113, 10111, 'rent', NULL, '1 BHK for Rent', 'rent-1-bhk-for-rent', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10114, 10111, 'rent', NULL, '2 BHK for Rent', 'rent-2-bhk-for-rent', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10115, 10111, 'rent', NULL, '3 BHK for Rent', 'rent-3-bhk-for-rent', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10116, 10111, 'rent', NULL, '4 BHK for Rent', 'rent-4-bhk-for-rent', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10117, 10111, 'rent', NULL, '4+ BHK / Luxury for Rent', 'rent-4-bhk-luxury-for-rent', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10118, 10111, 'rent', NULL, 'Independent House for Rent', 'rent-independent-house-for-rent', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10119, 10111, 'rent', NULL, 'Villa for Rent', 'rent-villa-for-rent', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10120, 10111, 'rent', NULL, 'Studio Apartment for Rent', 'rent-studio-apartment-for-rent', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10121, 10111, 'rent', NULL, 'Service Apartment for Rent', 'rent-service-apartment-for-rent', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10122, 10111, 'rent', NULL, 'Builder Floor for Rent', 'rent-builder-floor-for-rent', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10123, 10111, 'rent', NULL, 'Furnished House', 'rent-furnished-house', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10124, 10111, 'rent', NULL, 'Semi-Furnished House', 'rent-semi-furnished-house', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10125, 10111, 'rent', NULL, 'Unfurnished House', 'rent-unfurnished-house', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10126, 10111, 'rent', NULL, 'Bachelor Accommodation', 'rent-bachelor-accommodation', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10127, 10111, 'rent', NULL, 'Family Accommodation', 'rent-family-accommodation', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10128, 10111, 'rent', NULL, 'Corporate Lease Accommodation', 'rent-corporate-lease-accommodation', NULL, 17, 'Active', '2026-08-23 06:56:46'),
(10129, 10111, 'rent', NULL, 'Senior Citizen Rental', 'rent-senior-citizen-rental', NULL, 18, 'Active', '2026-08-23 06:56:46'),
(10130, 10110, 'rent', NULL, 'Commercial Rentals', 'rent-commercial-rentals', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10131, 10130, 'rent', NULL, 'Office Space for Rent', 'rent-office-space-for-rent', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10132, 10130, 'rent', NULL, 'Co-working Seat / Desk', 'rent-co-working-seat-desk', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10133, 10130, 'rent', NULL, 'Virtual Office', 'rent-virtual-office', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10134, 10130, 'rent', NULL, 'Shop for Rent', 'rent-shop-for-rent', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10135, 10130, 'rent', NULL, 'Showroom for Rent', 'rent-showroom-for-rent', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10136, 10130, 'rent', NULL, 'Warehouse for Rent', 'rent-warehouse-for-rent', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10137, 10130, 'rent', NULL, 'Godown for Rent', 'rent-godown-for-rent', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10138, 10130, 'rent', NULL, 'Industrial Shed for Rent', 'rent-industrial-shed-for-rent', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10139, 10130, 'rent', NULL, 'Retail Space for Rent', 'rent-retail-space-for-rent', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10140, 10130, 'rent', NULL, 'Restaurant Space for Rent', 'rent-restaurant-space-for-rent', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10141, 10130, 'rent', NULL, 'Banquet Hall on Rent', 'rent-banquet-hall-on-rent', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10142, 10130, 'rent', NULL, 'Commercial Building for Rent', 'rent-commercial-building-for-rent', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10143, 10130, 'rent', NULL, 'Bare Shell Office Rent', 'rent-bare-shell-office-rent', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10144, 10130, 'rent', NULL, 'Furnished Office Rent', 'rent-furnished-office-rent', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10145, 10110, 'rent', NULL, 'Short Term / Vacation Rentals', 'rent-short-term-vacation-rentals', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10146, 10145, 'rent', NULL, 'Daily Rental Home', 'rent-daily-rental-home', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10147, 10145, 'rent', NULL, 'Weekly Rental Home', 'rent-weekly-rental-home', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10148, 10145, 'rent', NULL, 'Monthly Rental Home', 'rent-monthly-rental-home', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10149, 10145, 'rent', NULL, 'Vacation Rental Villa', 'rent-vacation-rental-villa', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10150, 10145, 'rent', NULL, 'Furnished Short Stay Apartment', 'rent-furnished-short-stay-apartment', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10151, 10145, 'rent', NULL, 'Airbnb-style Listing', 'rent-airbnb-style-listing', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10152, 10145, 'rent', NULL, 'Event Rental Property', 'rent-event-rental-property', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10153, 10110, 'rent', NULL, 'Land / Plot Rentals', 'rent-land-plot-rentals', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10154, 10153, 'rent', NULL, 'Open Land for Rent / Lease', 'rent-open-land-for-rent-lease', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10155, 10153, 'rent', NULL, 'Agricultural Land Lease', 'rent-agricultural-land-lease', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10156, 10153, 'rent', NULL, 'Parking Yard Lease', 'rent-parking-yard-lease', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10157, 10153, 'rent', NULL, 'Godown Land Lease', 'rent-godown-land-lease', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10158, 10153, 'rent', NULL, 'Billboard / Hoarding Land Lease', 'rent-billboard-hoarding-land-lease', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10159, 0, 'vendor', NULL, 'Vendors', 'vnd', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10160, 10159, 'vendor', NULL, 'Design & Consultancy', 'vnd-design-consultancy', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10161, 10160, 'vendor', NULL, 'Architect', 'vnd-architect', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10162, 10160, 'vendor', NULL, 'Structural Engineer', 'vnd-structural-engineer', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10163, 10160, 'vendor', NULL, 'Interior Designer', 'vnd-interior-designer', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10164, 10160, 'vendor', NULL, 'Landscape Designer', 'vnd-landscape-designer', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10165, 10160, 'vendor', NULL, 'Vastu Consultant', 'vnd-vastu-consultant', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10166, 10160, 'vendor', NULL, 'Site Engineer', 'vnd-site-engineer', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10167, 10160, 'vendor', NULL, 'Land Surveyor', 'vnd-land-surveyor', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10168, 10160, 'vendor', NULL, 'Soil Testing Engineer', 'vnd-soil-testing-engineer', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10169, 10160, 'vendor', NULL, 'Chartered Engineer', 'vnd-chartered-engineer', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10170, 10160, 'vendor', NULL, 'Property Valuer', 'vnd-property-valuer', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10171, 10160, 'vendor', NULL, 'RERA Consultant', 'vnd-rera-consultant', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10172, 10160, 'vendor', NULL, '3D Rendering / Walkthrough Vendor', 'vnd-3d-rendering-walkthrough-vendor', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10173, 10160, 'vendor', NULL, 'Drone Survey Vendor', 'vnd-drone-survey-vendor', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10174, 10160, 'vendor', NULL, 'GPS Land Survey Vendor', 'vnd-gps-land-survey-vendor', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10175, 10159, 'vendor', NULL, 'Civil & Structural Contractors', 'vnd-civil-structural-contractors', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10176, 10175, 'vendor', NULL, 'Civil Contractor', 'vnd-civil-contractor', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10177, 10175, 'vendor', NULL, 'RCC Contractor', 'vnd-rcc-contractor', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10178, 10175, 'vendor', NULL, 'Turnkey Contractor', 'vnd-turnkey-contractor', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10179, 10175, 'vendor', NULL, 'Excavation Contractor', 'vnd-excavation-contractor', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10180, 10175, 'vendor', NULL, 'Earthwork Contractor', 'vnd-earthwork-contractor', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10181, 10175, 'vendor', NULL, 'Piling Contractor', 'vnd-piling-contractor', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10182, 10175, 'vendor', NULL, 'Foundation Contractor', 'vnd-foundation-contractor', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10183, 10175, 'vendor', NULL, 'Masonry Contractor', 'vnd-masonry-contractor', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10184, 10175, 'vendor', NULL, 'Brickwork Contractor', 'vnd-brickwork-contractor', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10185, 10175, 'vendor', NULL, 'Plastering Contractor', 'vnd-plastering-contractor', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10186, 10175, 'vendor', NULL, 'Demolition Contractor', 'vnd-demolition-contractor', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10187, 10175, 'vendor', NULL, 'Scaffolding Rental', 'vnd-scaffolding-rental', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10188, 10175, 'vendor', NULL, 'Crane Rental', 'vnd-crane-rental', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10189, 10175, 'vendor', NULL, 'JCB / Excavator Rental', 'vnd-jcb-excavator-rental', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10190, 10175, 'vendor', NULL, 'Concrete Mixer Rental', 'vnd-concrete-mixer-rental', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10191, 10175, 'vendor', NULL, 'Labour Contractor', 'vnd-labour-contractor', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10192, 10159, 'vendor', NULL, 'MEP (Mechanical, Electrical, Plumbing)', 'vnd-mep-mechanical-electrical-plumbing', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10193, 10192, 'vendor', NULL, 'Plumbing Contractor', 'vnd-plumbing-contractor', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10194, 10192, 'vendor', NULL, 'Electrical Contractor', 'vnd-electrical-contractor', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10195, 10192, 'vendor', NULL, 'HVAC Contractor', 'vnd-hvac-contractor', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10196, 10192, 'vendor', NULL, 'Fire Fighting Contractor', 'vnd-fire-fighting-contractor', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10197, 10192, 'vendor', NULL, 'Lift / Elevator Vendor', 'vnd-lift-elevator-vendor', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10198, 10192, 'vendor', NULL, 'Solar Panel Installer', 'vnd-solar-panel-installer', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10199, 10192, 'vendor', NULL, 'Borewell Drilling Contractor', 'vnd-borewell-drilling-contractor', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10200, 10192, 'vendor', NULL, 'Water Tank Supplier', 'vnd-water-tank-supplier', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10201, 10192, 'vendor', NULL, 'STP / Sewage Treatment Vendor', 'vnd-stp-sewage-treatment-vendor', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10202, 10192, 'vendor', NULL, 'CCTV / Security System Vendor', 'vnd-cctv-security-system-vendor', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10203, 10192, 'vendor', NULL, 'Home Automation Vendor', 'vnd-home-automation-vendor', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10204, 10159, 'vendor', NULL, 'Finishing Contractors', 'vnd-finishing-contractors', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10205, 10204, 'vendor', NULL, 'Painting Contractor', 'vnd-painting-contractor', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10206, 10204, 'vendor', NULL, 'Waterproofing Contractor', 'vnd-waterproofing-contractor', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10207, 10204, 'vendor', NULL, 'Tile Contractor', 'vnd-tile-contractor', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10208, 10204, 'vendor', NULL, 'Flooring Contractor', 'vnd-flooring-contractor', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10209, 10204, 'vendor', NULL, 'False Ceiling Contractor', 'vnd-false-ceiling-contractor', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10210, 10204, 'vendor', NULL, 'Carpentry Contractor', 'vnd-carpentry-contractor', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10211, 10204, 'vendor', NULL, 'Modular Kitchen Vendor', 'vnd-modular-kitchen-vendor', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10212, 10204, 'vendor', NULL, 'Wardrobe Vendor', 'vnd-wardrobe-vendor', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10213, 10204, 'vendor', NULL, 'Glass & Glazing Vendor', 'vnd-glass-glazing-vendor', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10214, 10204, 'vendor', NULL, 'Aluminium Fabricator', 'vnd-aluminium-fabricator', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10215, 10204, 'vendor', NULL, 'UPVC Window Vendor', 'vnd-upvc-window-vendor', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10216, 10204, 'vendor', NULL, 'Door Vendor', 'vnd-door-vendor', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10217, 10204, 'vendor', NULL, 'Grill / Fabrication Vendor', 'vnd-grill-fabrication-vendor', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10218, 10204, 'vendor', NULL, 'Welding Contractor', 'vnd-welding-contractor', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10219, 10204, 'vendor', NULL, 'Roofing Contractor', 'vnd-roofing-contractor', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10220, 10204, 'vendor', NULL, 'Terrace Waterproofing Vendor', 'vnd-terrace-waterproofing-vendor', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10221, 10204, 'vendor', NULL, 'Paver Block Contractor', 'vnd-paver-block-contractor', NULL, 17, 'Active', '2026-08-23 06:56:46'),
(10222, 10204, 'vendor', NULL, 'Compound Wall Contractor', 'vnd-compound-wall-contractor', NULL, 18, 'Active', '2026-08-23 06:56:46'),
(10223, 10204, 'vendor', NULL, 'Swimming Pool Contractor', 'vnd-swimming-pool-contractor', NULL, 19, 'Active', '2026-08-23 06:56:46'),
(10224, 10204, 'vendor', NULL, 'Landscaping Contractor', 'vnd-landscaping-contractor', NULL, 20, 'Active', '2026-08-23 06:56:46'),
(10225, 10159, 'vendor', NULL, 'Material Suppliers', 'vnd-material-suppliers', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10226, 10225, 'vendor', NULL, 'Concrete (RMC) Supplier', 'vnd-concrete-rmc-supplier', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10227, 10225, 'vendor', NULL, 'Steel (TMT) Supplier', 'vnd-steel-tmt-supplier', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10228, 10225, 'vendor', NULL, 'Cement Dealer', 'vnd-cement-dealer', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10229, 10225, 'vendor', NULL, 'Sand Supplier', 'vnd-sand-supplier', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10230, 10225, 'vendor', NULL, 'Aggregate Supplier', 'vnd-aggregate-supplier', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10231, 10225, 'vendor', NULL, 'Bricks Supplier', 'vnd-bricks-supplier', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10232, 10225, 'vendor', NULL, 'AAC / Fly Ash Blocks Supplier', 'vnd-aac-fly-ash-blocks-supplier', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10233, 10225, 'vendor', NULL, 'Granite / Marble Supplier', 'vnd-granite-marble-supplier', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10234, 10225, 'vendor', NULL, 'Hardware Store', 'vnd-hardware-store', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10235, 10225, 'vendor', NULL, 'Sanitary Ware Supplier', 'vnd-sanitary-ware-supplier', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10236, 10225, 'vendor', NULL, 'CP Fittings Supplier', 'vnd-cp-fittings-supplier', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10237, 10225, 'vendor', NULL, 'Electrical Fittings Supplier', 'vnd-electrical-fittings-supplier', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10238, 10225, 'vendor', NULL, 'Switch & Wire Supplier', 'vnd-switch-wire-supplier', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10239, 10225, 'vendor', NULL, 'Cable Supplier', 'vnd-cable-supplier', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10240, 10225, 'vendor', NULL, 'Paint Brands Dealer', 'vnd-paint-brands-dealer', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10241, 10225, 'vendor', NULL, 'Waterproofing Chemical Supplier', 'vnd-waterproofing-chemical-supplier', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10242, 10159, 'vendor', NULL, 'Allied Services & Agents', 'vnd-allied-services-agents', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10243, 10242, 'vendor', NULL, 'Termite Control Vendor', 'vnd-termite-control-vendor', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10244, 10242, 'vendor', NULL, 'Pest Control Vendor', 'vnd-pest-control-vendor', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10245, 10242, 'vendor', NULL, 'Property Legal Documentation Vendor', 'vnd-property-legal-documentation-vendor', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10246, 10242, 'vendor', NULL, 'Home Loan / DSA Agent', 'vnd-home-loan-dsa-agent', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10247, 10242, 'vendor', NULL, 'Property Insurance Agent', 'vnd-property-insurance-agent', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10248, 10242, 'vendor', NULL, 'Packers & Movers', 'vnd-packers-movers', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10249, 10242, 'vendor', NULL, 'Property Photographer', 'vnd-property-photographer', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10250, 10242, 'vendor', NULL, 'Liaisoning Agent (Govt. Approvals)', 'vnd-liaisoning-agent-govt-approvals', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10251, 0, 'local_services', NULL, 'Local Services', 'lsv', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10252, 10251, 'local_services', NULL, 'Cleaning & Pest Control', 'lsv-cleaning-pest-control', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10253, 10252, 'local_services', NULL, 'House Cleaning', 'lsv-house-cleaning', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10254, 10252, 'local_services', NULL, 'Deep Cleaning', 'lsv-deep-cleaning', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10255, 10252, 'local_services', NULL, 'Sofa Cleaning', 'lsv-sofa-cleaning', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10256, 10252, 'local_services', NULL, 'Carpet Cleaning', 'lsv-carpet-cleaning', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10257, 10252, 'local_services', NULL, 'Water Tank Cleaning', 'lsv-water-tank-cleaning', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10258, 10252, 'local_services', NULL, 'Chimney Cleaning', 'lsv-chimney-cleaning', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10259, 10252, 'local_services', NULL, 'Pest Control', 'lsv-pest-control', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10260, 10252, 'local_services', NULL, 'Termite Control', 'lsv-termite-control', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10261, 10252, 'local_services', NULL, 'Cockroach Control', 'lsv-cockroach-control', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10262, 10252, 'local_services', NULL, 'Rodent Control', 'lsv-rodent-control', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10263, 10252, 'local_services', NULL, 'Mosquito Fogging', 'lsv-mosquito-fogging', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10264, 10252, 'local_services', NULL, 'Bathroom Deep Cleaning', 'lsv-bathroom-deep-cleaning', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10265, 10251, 'local_services', NULL, 'Repairs & Maintenance', 'lsv-repairs-maintenance', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10266, 10265, 'local_services', NULL, 'AC Repair & Service', 'lsv-ac-repair-service', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10267, 10265, 'local_services', NULL, 'AC Installation', 'lsv-ac-installation', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10268, 10265, 'local_services', NULL, 'Refrigerator Repair', 'lsv-refrigerator-repair', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10269, 10265, 'local_services', NULL, 'Washing Machine Repair', 'lsv-washing-machine-repair', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10270, 10265, 'local_services', NULL, 'RO Water Purifier Service', 'lsv-ro-water-purifier-service', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10271, 10265, 'local_services', NULL, 'Electrician Service', 'lsv-electrician-service', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10272, 10265, 'local_services', NULL, 'Plumber Service', 'lsv-plumber-service', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10273, 10265, 'local_services', NULL, 'Carpenter Service', 'lsv-carpenter-service', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10274, 10265, 'local_services', NULL, 'Locksmith Service', 'lsv-locksmith-service', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10275, 10265, 'local_services', NULL, 'Key Duplication', 'lsv-key-duplication', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10276, 10265, 'local_services', NULL, 'Glass Repair / Replacement', 'lsv-glass-repair-replacement', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10277, 10265, 'local_services', NULL, 'Furniture Repair', 'lsv-furniture-repair', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10278, 10265, 'local_services', NULL, 'Motor Repair (Water Motor)', 'lsv-motor-repair-water-motor', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10279, 10265, 'local_services', NULL, 'Solar Water Heater Service', 'lsv-solar-water-heater-service', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10280, 10265, 'local_services', NULL, 'Inverter / Battery Service', 'lsv-inverter-battery-service', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10281, 10265, 'local_services', NULL, 'Generator Rental', 'lsv-generator-rental', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10282, 10251, 'local_services', NULL, 'Painting, Décor & Installation', 'lsv-painting-d-cor-installation', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10283, 10282, 'local_services', NULL, 'Home Painting', 'lsv-home-painting', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10284, 10282, 'local_services', NULL, 'Wall Putty Service', 'lsv-wall-putty-service', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10285, 10282, 'local_services', NULL, 'Waterproofing Service', 'lsv-waterproofing-service', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10286, 10282, 'local_services', NULL, 'POP / False Ceiling Service', 'lsv-pop-false-ceiling-service', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10287, 10282, 'local_services', NULL, 'CCTV Installation', 'lsv-cctv-installation', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10288, 10282, 'local_services', NULL, 'Home Automation Setup', 'lsv-home-automation-setup', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10289, 10282, 'local_services', NULL, 'Solar Installation', 'lsv-solar-installation', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10290, 10282, 'local_services', NULL, 'Curtain & Blinds Installation', 'lsv-curtain-blinds-installation', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10291, 10282, 'local_services', NULL, 'Wallpaper Installation', 'lsv-wallpaper-installation', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10292, 10282, 'local_services', NULL, 'Upholstery Service', 'lsv-upholstery-service', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10293, 10282, 'local_services', NULL, 'Photo Framing Service', 'lsv-photo-framing-service', NULL, 11, 'Active', '2026-08-23 06:56:46');
INSERT INTO `property_categories` (`id`, `parent_id`, `category_type`, `menu_id`, `name`, `slug`, `icon`, `category_order`, `status`, `created_at`) VALUES
(10294, 10251, 'local_services', NULL, 'Gardening & Outdoor', 'lsv-gardening-outdoor', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10295, 10294, 'local_services', NULL, 'Gardening Service', 'lsv-gardening-service', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10296, 10294, 'local_services', NULL, 'Landscaping Service', 'lsv-landscaping-service', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10297, 10294, 'local_services', NULL, 'Lawn Mowing', 'lsv-lawn-mowing', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10298, 10294, 'local_services', NULL, 'Borewell Service', 'lsv-borewell-service', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10299, 10294, 'local_services', NULL, 'Water Tanker Supply', 'lsv-water-tanker-supply', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10300, 10251, 'local_services', NULL, 'Household Staffing & Personal Care', 'lsv-household-staffing-personal-care', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10301, 10300, 'local_services', NULL, 'Security Guard Service', 'lsv-security-guard-service', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10302, 10300, 'local_services', NULL, 'Housekeeping Staff', 'lsv-housekeeping-staff', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10303, 10300, 'local_services', NULL, 'Maid Service', 'lsv-maid-service', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10304, 10300, 'local_services', NULL, 'Cook Service', 'lsv-cook-service', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10305, 10300, 'local_services', NULL, 'Babysitting / Nanny Service', 'lsv-babysitting-nanny-service', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10306, 10300, 'local_services', NULL, 'Elderly Care Service', 'lsv-elderly-care-service', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10307, 10300, 'local_services', NULL, 'Nursing Care at Home', 'lsv-nursing-care-at-home', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10308, 10300, 'local_services', NULL, 'Physiotherapy at Home', 'lsv-physiotherapy-at-home', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10309, 10300, 'local_services', NULL, 'Salon at Home', 'lsv-salon-at-home', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10310, 10300, 'local_services', NULL, 'Spa at Home', 'lsv-spa-at-home', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10311, 10251, 'local_services', NULL, 'Moving, Vehicles & Events', 'lsv-moving-vehicles-events', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10312, 10311, 'local_services', NULL, 'Packers & Movers (Local)', 'lsv-packers-movers-local', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10313, 10311, 'local_services', NULL, 'Packers & Movers (Interstate)', 'lsv-packers-movers-interstate', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10314, 10311, 'local_services', NULL, 'Bike / Car Transportation', 'lsv-bike-car-transportation', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10315, 10311, 'local_services', NULL, 'Storage / Warehouse Service', 'lsv-storage-warehouse-service', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10316, 10311, 'local_services', NULL, 'Vehicle Cleaning', 'lsv-vehicle-cleaning', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10317, 10311, 'local_services', NULL, 'Car Wash', 'lsv-car-wash', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10318, 10311, 'local_services', NULL, 'Bike Servicing', 'lsv-bike-servicing', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10319, 10311, 'local_services', NULL, 'Event Decoration', 'lsv-event-decoration', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10320, 10311, 'local_services', NULL, 'Catering Service', 'lsv-catering-service', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10321, 10311, 'local_services', NULL, 'Tent House Service', 'lsv-tent-house-service', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10322, 10251, 'local_services', NULL, 'Property & Society Management', 'lsv-property-society-management', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10323, 10322, 'local_services', NULL, 'Home Inspection Service', 'lsv-home-inspection-service', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10324, 10322, 'local_services', NULL, 'Vastu Consultation Service', 'lsv-vastu-consultation-service', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10325, 10322, 'local_services', NULL, 'Legal Documentation Service', 'lsv-legal-documentation-service', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10326, 10322, 'local_services', NULL, 'Society / Apartment Management Service', 'lsv-society-apartment-management-service', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10327, 10322, 'local_services', NULL, 'Facility Management Service', 'lsv-facility-management-service', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10328, 0, 'interior', NULL, 'Interiors', 'int', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10329, 10328, 'interior', NULL, 'Doors', 'int-doors', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10330, 10329, 'interior', NULL, 'Panel Door', 'int-panel-door', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10331, 10329, 'interior', NULL, 'Flush Door', 'int-flush-door', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10332, 10329, 'interior', NULL, 'Laminated Door', 'int-laminated-door', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10333, 10329, 'interior', NULL, 'Veneer Door', 'int-veneer-door', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10334, 10329, 'interior', NULL, 'Membrane Door', 'int-membrane-door', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10335, 10329, 'interior', NULL, 'PVC Door', 'int-pvc-door', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10336, 10329, 'interior', NULL, 'UPVC Door', 'int-upvc-door', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10337, 10329, 'interior', NULL, 'WPC Door', 'int-wpc-door', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10338, 10329, 'interior', NULL, 'Fiber / FRP Door', 'int-fiber-frp-door', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10339, 10329, 'interior', NULL, 'Plain Glass Door', 'int-plain-glass-door', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10340, 10329, 'interior', NULL, 'Frosted Glass Door', 'int-frosted-glass-door', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10341, 10329, 'interior', NULL, 'Toughened Glass Door', 'int-toughened-glass-door', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10342, 10329, 'interior', NULL, 'Sliding Door', 'int-sliding-door', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10343, 10329, 'interior', NULL, 'Folding Door', 'int-folding-door', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10344, 10329, 'interior', NULL, 'Bi-fold Door', 'int-bi-fold-door', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10345, 10329, 'interior', NULL, 'French Door', 'int-french-door', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10346, 10329, 'interior', NULL, 'Dutch Door', 'int-dutch-door', NULL, 17, 'Active', '2026-08-23 06:56:46'),
(10347, 10329, 'interior', NULL, 'Louvered Door', 'int-louvered-door', NULL, 18, 'Active', '2026-08-23 06:56:46'),
(10348, 10329, 'interior', NULL, 'Barn Door', 'int-barn-door', NULL, 19, 'Active', '2026-08-23 06:56:46'),
(10349, 10329, 'interior', NULL, 'Pocket Door', 'int-pocket-door', NULL, 20, 'Active', '2026-08-23 06:56:46'),
(10350, 10329, 'interior', NULL, 'Revolving Door', 'int-revolving-door', NULL, 21, 'Active', '2026-08-23 06:56:46'),
(10351, 10329, 'interior', NULL, 'Teak Wood Door', 'int-teak-wood-door', NULL, 22, 'Active', '2026-08-23 06:56:46'),
(10352, 10329, 'interior', NULL, 'Sal Wood Door', 'int-sal-wood-door', NULL, 23, 'Active', '2026-08-23 06:56:46'),
(10353, 10329, 'interior', NULL, 'Sheesham Wood Door', 'int-sheesham-wood-door', NULL, 24, 'Active', '2026-08-23 06:56:46'),
(10354, 10329, 'interior', NULL, 'Metal Door', 'int-metal-door', NULL, 25, 'Active', '2026-08-23 06:56:46'),
(10355, 10329, 'interior', NULL, 'Steel Door', 'int-steel-door', NULL, 26, 'Active', '2026-08-23 06:56:46'),
(10356, 10329, 'interior', NULL, 'Aluminium Door', 'int-aluminium-door', NULL, 27, 'Active', '2026-08-23 06:56:46'),
(10357, 10329, 'interior', NULL, 'Security / Safety Door', 'int-security-safety-door', NULL, 28, 'Active', '2026-08-23 06:56:46'),
(10358, 10329, 'interior', NULL, 'Fire Rated Door', 'int-fire-rated-door', NULL, 29, 'Active', '2026-08-23 06:56:46'),
(10359, 10329, 'interior', NULL, 'Soundproof Door', 'int-soundproof-door', NULL, 30, 'Active', '2026-08-23 06:56:46'),
(10360, 10329, 'interior', NULL, 'Waterproof Bathroom Door', 'int-waterproof-bathroom-door', NULL, 31, 'Active', '2026-08-23 06:56:46'),
(10361, 10329, 'interior', NULL, 'Main / Entrance Door', 'int-main-entrance-door', NULL, 32, 'Active', '2026-08-23 06:56:46'),
(10362, 10329, 'interior', NULL, 'Pooja Room Door', 'int-pooja-room-door', NULL, 33, 'Active', '2026-08-23 06:56:46'),
(10363, 10329, 'interior', NULL, 'Bedroom Door', 'int-bedroom-door', NULL, 34, 'Active', '2026-08-23 06:56:46'),
(10364, 10329, 'interior', NULL, 'Kitchen Door', 'int-kitchen-door', NULL, 35, 'Active', '2026-08-23 06:56:46'),
(10365, 10329, 'interior', NULL, 'Balcony Door', 'int-balcony-door', NULL, 36, 'Active', '2026-08-23 06:56:46'),
(10366, 10329, 'interior', NULL, 'Garage Door', 'int-garage-door', NULL, 37, 'Active', '2026-08-23 06:56:46'),
(10367, 10329, 'interior', NULL, 'Rolling Shutter Door', 'int-rolling-shutter-door', NULL, 38, 'Active', '2026-08-23 06:56:46'),
(10368, 10329, 'interior', NULL, 'Collapsible Gate', 'int-collapsible-gate', NULL, 39, 'Active', '2026-08-23 06:56:46'),
(10369, 10328, 'interior', NULL, 'Windows', 'int-windows', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10370, 10369, 'interior', NULL, 'Sliding Window', 'int-sliding-window', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10371, 10369, 'interior', NULL, 'Casement Window', 'int-casement-window', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10372, 10369, 'interior', NULL, 'Fixed Window', 'int-fixed-window', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10373, 10369, 'interior', NULL, 'Awning Window', 'int-awning-window', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10374, 10369, 'interior', NULL, 'Bay Window', 'int-bay-window', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10375, 10369, 'interior', NULL, 'Bow Window', 'int-bow-window', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10376, 10369, 'interior', NULL, 'Louvered Window', 'int-louvered-window', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10377, 10369, 'interior', NULL, 'Georgian Window', 'int-georgian-window', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10378, 10369, 'interior', NULL, 'Skylight Window', 'int-skylight-window', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10379, 10369, 'interior', NULL, 'Tilt & Turn Window', 'int-tilt-turn-window', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10380, 10369, 'interior', NULL, 'Double Hung Window', 'int-double-hung-window', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10381, 10369, 'interior', NULL, 'Single Hung Window', 'int-single-hung-window', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10382, 10369, 'interior', NULL, 'French Window', 'int-french-window', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10383, 10369, 'interior', NULL, 'UPVC Window', 'int-upvc-window', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10384, 10369, 'interior', NULL, 'Aluminium Window', 'int-aluminium-window', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10385, 10369, 'interior', NULL, 'Wooden Window', 'int-wooden-window', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10386, 10369, 'interior', NULL, 'Steel Window', 'int-steel-window', NULL, 17, 'Active', '2026-08-23 06:56:46'),
(10387, 10369, 'interior', NULL, 'Toughened Glass Window', 'int-toughened-glass-window', NULL, 18, 'Active', '2026-08-23 06:56:46'),
(10388, 10369, 'interior', NULL, 'Combination Window', 'int-combination-window', NULL, 19, 'Active', '2026-08-23 06:56:46'),
(10389, 10369, 'interior', NULL, 'Corner Window', 'int-corner-window', NULL, 20, 'Active', '2026-08-23 06:56:46'),
(10390, 10369, 'interior', NULL, 'Ventilator Window', 'int-ventilator-window', NULL, 21, 'Active', '2026-08-23 06:56:46'),
(10391, 10369, 'interior', NULL, 'Arch / Round Window', 'int-arch-round-window', NULL, 22, 'Active', '2026-08-23 06:56:46'),
(10392, 10369, 'interior', NULL, 'Frosted Bathroom Window', 'int-frosted-bathroom-window', NULL, 23, 'Active', '2026-08-23 06:56:46'),
(10393, 10369, 'interior', NULL, 'Mosquito Mesh Window', 'int-mosquito-mesh-window', NULL, 24, 'Active', '2026-08-23 06:56:46'),
(10394, 10328, 'interior', NULL, 'Flooring', 'int-flooring', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10395, 10394, 'interior', NULL, 'Vitrified Tiles', 'int-vitrified-tiles', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10396, 10394, 'interior', NULL, 'Ceramic Tiles', 'int-ceramic-tiles', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10397, 10394, 'interior', NULL, 'Porcelain Tiles', 'int-porcelain-tiles', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10398, 10394, 'interior', NULL, 'Marble Flooring', 'int-marble-flooring', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10399, 10394, 'interior', NULL, 'Granite Flooring', 'int-granite-flooring', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10400, 10394, 'interior', NULL, 'Italian Marble', 'int-italian-marble', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10401, 10394, 'interior', NULL, 'Kota Stone', 'int-kota-stone', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10402, 10394, 'interior', NULL, 'Cuddapah Stone', 'int-cuddapah-stone', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10403, 10394, 'interior', NULL, 'Wooden Flooring', 'int-wooden-flooring', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10404, 10394, 'interior', NULL, 'Laminate Flooring', 'int-laminate-flooring', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10405, 10394, 'interior', NULL, 'Engineered Wood Flooring', 'int-engineered-wood-flooring', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10406, 10394, 'interior', NULL, 'Vinyl Flooring', 'int-vinyl-flooring', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10407, 10394, 'interior', NULL, 'Epoxy Flooring', 'int-epoxy-flooring', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10408, 10394, 'interior', NULL, 'PVC Flooring', 'int-pvc-flooring', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10409, 10394, 'interior', NULL, 'Rubber Flooring', 'int-rubber-flooring', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10410, 10394, 'interior', NULL, 'Carpet Flooring', 'int-carpet-flooring', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10411, 10394, 'interior', NULL, 'Terrazzo Flooring', 'int-terrazzo-flooring', NULL, 17, 'Active', '2026-08-23 06:56:46'),
(10412, 10394, 'interior', NULL, 'Mosaic Flooring', 'int-mosaic-flooring', NULL, 18, 'Active', '2026-08-23 06:56:46'),
(10413, 10394, 'interior', NULL, 'IPS Flooring', 'int-ips-flooring', NULL, 19, 'Active', '2026-08-23 06:56:46'),
(10414, 10394, 'interior', NULL, 'Cement Flooring', 'int-cement-flooring', NULL, 20, 'Active', '2026-08-23 06:56:46'),
(10415, 10394, 'interior', NULL, 'Anti-skid Tiles', 'int-anti-skid-tiles', NULL, 21, 'Active', '2026-08-23 06:56:46'),
(10416, 10394, 'interior', NULL, 'Wooden Deck Flooring', 'int-wooden-deck-flooring', NULL, 22, 'Active', '2026-08-23 06:56:46'),
(10417, 10394, 'interior', NULL, 'Stone Cladding Flooring', 'int-stone-cladding-flooring', NULL, 23, 'Active', '2026-08-23 06:56:46'),
(10418, 10394, 'interior', NULL, 'Athangudi Tiles', 'int-athangudi-tiles', NULL, 24, 'Active', '2026-08-23 06:56:46'),
(10419, 10394, 'interior', NULL, 'Handmade Tiles', 'int-handmade-tiles', NULL, 25, 'Active', '2026-08-23 06:56:46'),
(10420, 10394, 'interior', NULL, 'Kitchen / Bathroom Tiles', 'int-kitchen-bathroom-tiles', NULL, 26, 'Active', '2026-08-23 06:56:46'),
(10421, 10328, 'interior', NULL, 'Wall Finishes & Paints', 'int-wall-finishes-paints', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10422, 10421, 'interior', NULL, 'Emulsion Paint', 'int-emulsion-paint', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10423, 10421, 'interior', NULL, 'Distemper Paint', 'int-distemper-paint', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10424, 10421, 'interior', NULL, 'Texture Paint', 'int-texture-paint', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10425, 10421, 'interior', NULL, 'POP Finish', 'int-pop-finish', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10426, 10421, 'interior', NULL, 'Wallpaper', 'int-wallpaper', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10427, 10421, 'interior', NULL, 'Wood Wall Panelling', 'int-wood-wall-panelling', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10428, 10421, 'interior', NULL, 'PVC Wall Panel', 'int-pvc-wall-panel', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10429, 10421, 'interior', NULL, 'WPC Wall Panel', 'int-wpc-wall-panel', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10430, 10421, 'interior', NULL, 'Stone Cladding', 'int-stone-cladding', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10431, 10421, 'interior', NULL, 'Brick Cladding', 'int-brick-cladding', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10432, 10421, 'interior', NULL, 'Exposed Brick Wall', 'int-exposed-brick-wall', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10433, 10421, 'interior', NULL, '3D Wall Panel', 'int-3d-wall-panel', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10434, 10421, 'interior', NULL, 'Fabric Wall Panel', 'int-fabric-wall-panel', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10435, 10421, 'interior', NULL, 'Mirror Wall Panel', 'int-mirror-wall-panel', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10436, 10421, 'interior', NULL, 'Glass Wall Panel', 'int-glass-wall-panel', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10437, 10421, 'interior', NULL, 'Wall Putty', 'int-wall-putty', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10438, 10421, 'interior', NULL, 'Waterproof Wall Coating', 'int-waterproof-wall-coating', NULL, 17, 'Active', '2026-08-23 06:56:46'),
(10439, 10421, 'interior', NULL, 'Metallic Paint Finish', 'int-metallic-paint-finish', NULL, 18, 'Active', '2026-08-23 06:56:46'),
(10440, 10421, 'interior', NULL, 'Textured Wall Art', 'int-textured-wall-art', NULL, 19, 'Active', '2026-08-23 06:56:46'),
(10441, 10328, 'interior', NULL, 'Ceiling', 'int-ceiling', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10442, 10441, 'interior', NULL, 'POP False Ceiling', 'int-pop-false-ceiling', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10443, 10441, 'interior', NULL, 'Gypsum False Ceiling', 'int-gypsum-false-ceiling', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10444, 10441, 'interior', NULL, 'PVC Ceiling', 'int-pvc-ceiling', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10445, 10441, 'interior', NULL, 'Wooden Ceiling', 'int-wooden-ceiling', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10446, 10441, 'interior', NULL, 'Metal Ceiling', 'int-metal-ceiling', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10447, 10441, 'interior', NULL, 'Grid Ceiling', 'int-grid-ceiling', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10448, 10441, 'interior', NULL, 'Stretch Ceiling', 'int-stretch-ceiling', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10449, 10441, 'interior', NULL, 'Coffered Ceiling', 'int-coffered-ceiling', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10450, 10441, 'interior', NULL, 'Cove Ceiling', 'int-cove-ceiling', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10451, 10441, 'interior', NULL, 'Tray Ceiling', 'int-tray-ceiling', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10452, 10441, 'interior', NULL, 'Wooden Beam Ceiling', 'int-wooden-beam-ceiling', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10453, 10441, 'interior', NULL, 'Acoustic Ceiling', 'int-acoustic-ceiling', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10454, 10328, 'interior', NULL, 'Kitchen', 'int-kitchen', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10455, 10454, 'interior', NULL, 'Modular Kitchen', 'int-modular-kitchen', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10456, 10454, 'interior', NULL, 'L-Shaped Kitchen', 'int-l-shaped-kitchen', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10457, 10454, 'interior', NULL, 'U-Shaped Kitchen', 'int-u-shaped-kitchen', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10458, 10454, 'interior', NULL, 'Straight Kitchen', 'int-straight-kitchen', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10459, 10454, 'interior', NULL, 'Parallel Kitchen', 'int-parallel-kitchen', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10460, 10454, 'interior', NULL, 'Island Kitchen', 'int-island-kitchen', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10461, 10454, 'interior', NULL, 'Open Kitchen', 'int-open-kitchen', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10462, 10454, 'interior', NULL, 'Closed Kitchen', 'int-closed-kitchen', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10463, 10454, 'interior', NULL, 'Laminate Kitchen Cabinets', 'int-laminate-kitchen-cabinets', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10464, 10454, 'interior', NULL, 'Acrylic Kitchen Cabinets', 'int-acrylic-kitchen-cabinets', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10465, 10454, 'interior', NULL, 'PU Finish Cabinets', 'int-pu-finish-cabinets', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10466, 10454, 'interior', NULL, 'Membrane Kitchen Cabinets', 'int-membrane-kitchen-cabinets', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10467, 10454, 'interior', NULL, 'Veneer Kitchen Cabinets', 'int-veneer-kitchen-cabinets', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10468, 10454, 'interior', NULL, 'Granite Countertop', 'int-granite-countertop', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10469, 10454, 'interior', NULL, 'Quartz Countertop', 'int-quartz-countertop', NULL, 15, 'Active', '2026-08-23 06:56:46'),
(10470, 10454, 'interior', NULL, 'Marble Countertop', 'int-marble-countertop', NULL, 16, 'Active', '2026-08-23 06:56:46'),
(10471, 10454, 'interior', NULL, 'Corian Countertop', 'int-corian-countertop', NULL, 17, 'Active', '2026-08-23 06:56:46'),
(10472, 10454, 'interior', NULL, 'Stainless Steel Countertop', 'int-stainless-steel-countertop', NULL, 18, 'Active', '2026-08-23 06:56:46'),
(10473, 10454, 'interior', NULL, 'Stainless Steel Sink', 'int-stainless-steel-sink', NULL, 19, 'Active', '2026-08-23 06:56:46'),
(10474, 10454, 'interior', NULL, 'Granite Sink', 'int-granite-sink', NULL, 20, 'Active', '2026-08-23 06:56:46'),
(10475, 10454, 'interior', NULL, 'Ceramic Sink', 'int-ceramic-sink', NULL, 21, 'Active', '2026-08-23 06:56:46'),
(10476, 10454, 'interior', NULL, 'Kitchen Chimney', 'int-kitchen-chimney', NULL, 22, 'Active', '2026-08-23 06:56:46'),
(10477, 10328, 'interior', NULL, 'Wardrobe & Storage', 'int-wardrobe-storage', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10478, 10477, 'interior', NULL, 'Sliding Wardrobe', 'int-sliding-wardrobe', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10479, 10477, 'interior', NULL, 'Hinged Wardrobe', 'int-hinged-wardrobe', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10480, 10477, 'interior', NULL, 'Walk-in Wardrobe', 'int-walk-in-wardrobe', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10481, 10477, 'interior', NULL, 'Modular Wardrobe', 'int-modular-wardrobe', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10482, 10477, 'interior', NULL, 'Loft Wardrobe', 'int-loft-wardrobe', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10483, 10477, 'interior', NULL, 'Mirror Wardrobe', 'int-mirror-wardrobe', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10484, 10477, 'interior', NULL, 'Laminate Wardrobe', 'int-laminate-wardrobe', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10485, 10477, 'interior', NULL, 'Veneer Wardrobe', 'int-veneer-wardrobe', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10486, 10477, 'interior', NULL, 'Acrylic Wardrobe', 'int-acrylic-wardrobe', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10487, 10477, 'interior', NULL, 'PU Finish Wardrobe', 'int-pu-finish-wardrobe', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10488, 10477, 'interior', NULL, 'Shoe Rack Units', 'int-shoe-rack-units', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10489, 10477, 'interior', NULL, 'Crockery Units', 'int-crockery-units', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10490, 10328, 'interior', NULL, 'Bathroom & Sanitaryware', 'int-bathroom-sanitaryware', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10491, 10490, 'interior', NULL, 'Wall-mounted WC', 'int-wall-mounted-wc', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10492, 10490, 'interior', NULL, 'Floor-mounted WC', 'int-floor-mounted-wc', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10493, 10490, 'interior', NULL, 'One-Piece WC', 'int-one-piece-wc', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10494, 10490, 'interior', NULL, 'Wash Basin', 'int-wash-basin', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10495, 10490, 'interior', NULL, 'Counter Basin', 'int-counter-basin', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10496, 10490, 'interior', NULL, 'Pedestal Basin', 'int-pedestal-basin', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10497, 10490, 'interior', NULL, 'Bathtub', 'int-bathtub', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10498, 10490, 'interior', NULL, 'Shower Enclosure', 'int-shower-enclosure', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10499, 10490, 'interior', NULL, 'Shower Panel', 'int-shower-panel', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10500, 10490, 'interior', NULL, 'Health Faucet', 'int-health-faucet', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10501, 10490, 'interior', NULL, 'Diverter / Mixer Tap', 'int-diverter-mixer-tap', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10502, 10490, 'interior', NULL, 'Rain Shower', 'int-rain-shower', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10503, 10490, 'interior', NULL, 'Geyser / Water Heater', 'int-geyser-water-heater', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10504, 10490, 'interior', NULL, 'Bathroom Vanity Unit', 'int-bathroom-vanity-unit', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10505, 10328, 'interior', NULL, 'Lighting', 'int-lighting', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10506, 10505, 'interior', NULL, 'Chandeliers', 'int-chandeliers', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10507, 10505, 'interior', NULL, 'Pendant Lights', 'int-pendant-lights', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10508, 10505, 'interior', NULL, 'LED Panel Lights', 'int-led-panel-lights', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10509, 10505, 'interior', NULL, 'Cove Lighting', 'int-cove-lighting', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10510, 10505, 'interior', NULL, 'Spotlights', 'int-spotlights', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10511, 10505, 'interior', NULL, 'Track Lighting', 'int-track-lighting', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10512, 10505, 'interior', NULL, 'Wall Sconces', 'int-wall-sconces', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10513, 10505, 'interior', NULL, 'Recessed Lighting', 'int-recessed-lighting', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10514, 10505, 'interior', NULL, 'Decorative Table Lamps', 'int-decorative-table-lamps', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10515, 10505, 'interior', NULL, 'Outdoor / Garden Lighting', 'int-outdoor-garden-lighting', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10516, 10505, 'interior', NULL, 'Smart Lighting', 'int-smart-lighting', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10517, 10328, 'interior', NULL, 'Furniture', 'int-furniture', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10518, 10517, 'interior', NULL, 'Sofa Sets', 'int-sofa-sets', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10519, 10517, 'interior', NULL, 'Dining Tables', 'int-dining-tables', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10520, 10517, 'interior', NULL, 'Beds', 'int-beds', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10521, 10517, 'interior', NULL, 'TV Units', 'int-tv-units', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10522, 10517, 'interior', NULL, 'Study Tables', 'int-study-tables', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10523, 10517, 'interior', NULL, 'Bookshelves', 'int-bookshelves', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10524, 10517, 'interior', NULL, 'Recliners', 'int-recliners', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10525, 10517, 'interior', NULL, 'Ottomans', 'int-ottomans', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10526, 10517, 'interior', NULL, 'Coffee Tables', 'int-coffee-tables', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10527, 10517, 'interior', NULL, 'Console Tables', 'int-console-tables', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10528, 10328, 'interior', NULL, 'Curtains, Blinds & Soft Furnishing', 'int-curtains-blinds-soft-furnishing', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10529, 10528, 'interior', NULL, 'Roller Blinds', 'int-roller-blinds', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10530, 10528, 'interior', NULL, 'Roman Blinds', 'int-roman-blinds', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10531, 10528, 'interior', NULL, 'Vertical Blinds', 'int-vertical-blinds', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10532, 10528, 'interior', NULL, 'Wooden Blinds', 'int-wooden-blinds', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10533, 10528, 'interior', NULL, 'Sheer Curtains', 'int-sheer-curtains', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10534, 10528, 'interior', NULL, 'Blackout Curtains', 'int-blackout-curtains', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10535, 10528, 'interior', NULL, 'Motorised Curtains', 'int-motorised-curtains', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10536, 10528, 'interior', NULL, 'Cushion Covers', 'int-cushion-covers', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10537, 10528, 'interior', NULL, 'Bed Linen', 'int-bed-linen', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10538, 10328, 'interior', NULL, 'Hardware & Fittings', 'int-hardware-fittings', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10539, 10538, 'interior', NULL, 'Door Handles & Locks', 'int-door-handles-locks', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10540, 10538, 'interior', NULL, 'Hinges', 'int-hinges', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10541, 10538, 'interior', NULL, 'Cabinet Handles', 'int-cabinet-handles', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10542, 10538, 'interior', NULL, 'Drawer Channels', 'int-drawer-channels', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10543, 10538, 'interior', NULL, 'Soft-close Fittings', 'int-soft-close-fittings', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10544, 10538, 'interior', NULL, 'Door Closers', 'int-door-closers', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10545, 10538, 'interior', NULL, 'Magic Corner Fittings', 'int-magic-corner-fittings', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10546, 10538, 'interior', NULL, 'Wardrobe Accessories', 'int-wardrobe-accessories', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10547, 0, 'hotels_homestays', NULL, 'Hotels & Homestays', 'hhs', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10548, 10547, 'hotels_homestays', NULL, 'Hotels', 'hhs-hotels', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10549, 10548, 'hotels_homestays', NULL, 'Budget Hotel', 'hhs-budget-hotel', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10550, 10548, 'hotels_homestays', NULL, '2 Star Hotel', 'hhs-2-star-hotel', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10551, 10548, 'hotels_homestays', NULL, '3 Star Hotel', 'hhs-3-star-hotel', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10552, 10548, 'hotels_homestays', NULL, '4 Star Hotel', 'hhs-4-star-hotel', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10553, 10548, 'hotels_homestays', NULL, '5 Star Hotel', 'hhs-5-star-hotel', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10554, 10548, 'hotels_homestays', NULL, 'Luxury Hotel', 'hhs-luxury-hotel', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10555, 10548, 'hotels_homestays', NULL, 'Boutique Hotel', 'hhs-boutique-hotel', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10556, 10548, 'hotels_homestays', NULL, 'Business Hotel', 'hhs-business-hotel', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10557, 10548, 'hotels_homestays', NULL, 'Heritage Hotel', 'hhs-heritage-hotel', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10558, 10548, 'hotels_homestays', NULL, 'Transit Hotel', 'hhs-transit-hotel', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10559, 10548, 'hotels_homestays', NULL, 'Airport Hotel', 'hhs-airport-hotel', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10560, 10548, 'hotels_homestays', NULL, 'Highway Hotel / Motel', 'hhs-highway-hotel-motel', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10561, 10548, 'hotels_homestays', NULL, 'Capsule Hotel', 'hhs-capsule-hotel', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10562, 10547, 'hotels_homestays', NULL, 'Resorts & Retreats', 'hhs-resorts-retreats', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10563, 10562, 'hotels_homestays', NULL, 'Luxury Resort', 'hhs-luxury-resort', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10564, 10562, 'hotels_homestays', NULL, 'Hill Station Resort', 'hhs-hill-station-resort', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10565, 10562, 'hotels_homestays', NULL, 'Beach Resort', 'hhs-beach-resort', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10566, 10562, 'hotels_homestays', NULL, 'Wildlife Resort', 'hhs-wildlife-resort', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10567, 10562, 'hotels_homestays', NULL, 'Ayurveda Resort', 'hhs-ayurveda-resort', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10568, 10562, 'hotels_homestays', NULL, 'Wellness Retreat', 'hhs-wellness-retreat', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10569, 10562, 'hotels_homestays', NULL, 'Eco Resort', 'hhs-eco-resort', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10570, 10562, 'hotels_homestays', NULL, 'Lake View Resort', 'hhs-lake-view-resort', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10571, 10562, 'hotels_homestays', NULL, 'Golf Resort', 'hhs-golf-resort', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10572, 10562, 'hotels_homestays', NULL, 'Adventure Resort', 'hhs-adventure-resort', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10573, 10547, 'hotels_homestays', NULL, 'Homestays & Alternate Stays', 'hhs-homestays-alternate-stays', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10574, 10573, 'hotels_homestays', NULL, 'Homestay', 'hhs-homestay', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10575, 10573, 'hotels_homestays', NULL, 'Farmhouse Stay', 'hhs-farmhouse-stay', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10576, 10573, 'hotels_homestays', NULL, 'Villa Stay', 'hhs-villa-stay', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10577, 10573, 'hotels_homestays', NULL, 'Cottage Stay', 'hhs-cottage-stay', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10578, 10573, 'hotels_homestays', NULL, 'Treehouse Stay', 'hhs-treehouse-stay', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10579, 10573, 'hotels_homestays', NULL, 'Houseboat', 'hhs-houseboat', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10580, 10573, 'hotels_homestays', NULL, 'Service Apartment Stay', 'hhs-service-apartment-stay', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10581, 10573, 'hotels_homestays', NULL, 'Bed & Breakfast', 'hhs-bed-breakfast', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10582, 10573, 'hotels_homestays', NULL, 'Camping / Glamping', 'hhs-camping-glamping', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10583, 10573, 'hotels_homestays', NULL, 'Heritage Homestay', 'hhs-heritage-homestay', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10584, 10547, 'hotels_homestays', NULL, 'Religious & Budget Stays', 'hhs-religious-budget-stays', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10585, 10584, 'hotels_homestays', NULL, 'Pilgrim Lodge', 'hhs-pilgrim-lodge', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10586, 10584, 'hotels_homestays', NULL, 'Dharamshala', 'hhs-dharamshala', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10587, 10584, 'hotels_homestays', NULL, 'Guest House', 'hhs-guest-house', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10588, 10584, 'hotels_homestays', NULL, 'Yatri Niwas', 'hhs-yatri-niwas', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10589, 10584, 'hotels_homestays', NULL, 'Ashram Stay', 'hhs-ashram-stay', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10590, 0, 'pg_hostels', NULL, 'PG & Hostels', 'pgh', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10591, 10590, 'pg_hostels', NULL, 'PG Accommodation', 'pgh-pg-accommodation', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10592, 10591, 'pg_hostels', NULL, 'Boys PG', 'pgh-boys-pg', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10593, 10591, 'pg_hostels', NULL, 'Girls PG', 'pgh-girls-pg', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10594, 10591, 'pg_hostels', NULL, 'Co-ed PG', 'pgh-co-ed-pg', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10595, 10591, 'pg_hostels', NULL, 'Working Professionals PG', 'pgh-working-professionals-pg', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10596, 10591, 'pg_hostels', NULL, 'Student PG', 'pgh-student-pg', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10597, 10591, 'pg_hostels', NULL, 'Single Sharing PG', 'pgh-single-sharing-pg', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10598, 10591, 'pg_hostels', NULL, 'Double Sharing PG', 'pgh-double-sharing-pg', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10599, 10591, 'pg_hostels', NULL, 'Triple Sharing PG', 'pgh-triple-sharing-pg', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10600, 10591, 'pg_hostels', NULL, 'AC PG', 'pgh-ac-pg', NULL, 9, 'Active', '2026-08-23 06:56:46'),
(10601, 10591, 'pg_hostels', NULL, 'Non-AC PG', 'pgh-non-ac-pg', NULL, 10, 'Active', '2026-08-23 06:56:46'),
(10602, 10591, 'pg_hostels', NULL, 'PG with Food', 'pgh-pg-with-food', NULL, 11, 'Active', '2026-08-23 06:56:46'),
(10603, 10591, 'pg_hostels', NULL, 'PG without Food', 'pgh-pg-without-food', NULL, 12, 'Active', '2026-08-23 06:56:46'),
(10604, 10591, 'pg_hostels', NULL, 'Premium PG', 'pgh-premium-pg', NULL, 13, 'Active', '2026-08-23 06:56:46'),
(10605, 10591, 'pg_hostels', NULL, 'Budget PG', 'pgh-budget-pg', NULL, 14, 'Active', '2026-08-23 06:56:46'),
(10606, 10590, 'pg_hostels', NULL, 'Hostels & Dormitories', 'pgh-hostels-dormitories', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10607, 10606, 'pg_hostels', NULL, 'Boys Hostel', 'pgh-boys-hostel', NULL, 1, 'Active', '2026-08-23 06:56:46'),
(10608, 10606, 'pg_hostels', NULL, 'Girls Hostel', 'pgh-girls-hostel', NULL, 2, 'Active', '2026-08-23 06:56:46'),
(10609, 10606, 'pg_hostels', NULL, 'Co-ed Hostel', 'pgh-co-ed-hostel', NULL, 3, 'Active', '2026-08-23 06:56:46'),
(10610, 10606, 'pg_hostels', NULL, 'College Hostel', 'pgh-college-hostel', NULL, 4, 'Active', '2026-08-23 06:56:46'),
(10611, 10606, 'pg_hostels', NULL, 'Corporate Hostel', 'pgh-corporate-hostel', NULL, 5, 'Active', '2026-08-23 06:56:46'),
(10612, 10606, 'pg_hostels', NULL, 'Backpacker Hostel', 'pgh-backpacker-hostel', NULL, 6, 'Active', '2026-08-23 06:56:46'),
(10613, 10606, 'pg_hostels', NULL, 'Budget Hostel', 'pgh-budget-hostel', NULL, 7, 'Active', '2026-08-23 06:56:46'),
(10614, 10606, 'pg_hostels', NULL, 'Dormitory', 'pgh-dormitory', NULL, 8, 'Active', '2026-08-23 06:56:46'),
(10615, 10606, 'pg_hostels', NULL, 'Working Women\'s Hostel', 'pgh-working-women-s-hostel', NULL, 9, 'Active', '2026-08-23 06:56:46');

-- --------------------------------------------------------

--
-- Table structure for table `property_contact_enquiries`
--

CREATE TABLE `property_contact_enquiries` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `appointment_date` date DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `property_contact_enquiries`
--

INSERT INTO `property_contact_enquiries` (`id`, `page_id`, `page_title`, `appointment_date`, `name`, `email`, `contact`, `description`, `created_at`) VALUES
(1, 1, 'Kalpataru Vian', '2026-08-22', 'Shahbaz Khan', 'shahbaz1990kh@gmail.com', '8149462320', NULL, '2026-08-05 13:22:55'),
(2, 1, 'Kalpataru Vian', '2026-08-22', 'Shahbaz Khan', 'shahbaz1990kh@gmail.com', '8149462320', NULL, '2026-08-05 13:47:01'),
(3, 2, 'Mahindra Rainforest', '2026-08-14', 'Shahbaz Khan', 'shahbaz1990kh@gmail.com', '8149462320', NULL, '2026-08-05 13:47:53'),
(4, 1, 'Kalpataru Vian', '2026-08-22', 'Shahbaz Khan', 'shahbaz1990kh@gmail.com', '8149462320', NULL, '2026-08-07 09:26:24'),
(5, 1, 'Kalpataru Vian', '2026-08-28', 'Shahbaz Khan', 'sss@gmail.com', '8149462320', NULL, '2026-08-17 14:57:38'),
(6, 1, 'Kalpataru Vian', NULL, 'Shahbaz Khan', 'shahbaz1990kh@gmail.com', '8149462320', 'rgssfdvsfsdfsfsafa', '2026-08-18 12:03:54'),
(7, 57, 'HomeLane Modular Kitchen Studio', '2026-09-02', 'Shahbaz Khan', 's@gmail.com', '8149462322', 'awdasdsadsadsadsadsadsa', '2026-08-31 10:46:02'),
(8, 35, 'Heritage Hotel - Jaipur', '2026-09-20', 'Sikandar', '9928251443bhati@gmail.com', '09928251443', 'Hello Sir/Madam,\r\n\r\nMy name is Sikandar from Vaibhav Industries, Jaipur. We manufacture sofas, chairs, beds, dining tables and hotel furniture.\r\n\r\nI am contacting you regarding furniture supply for the Heritage Hotel Jaipur project. Please share the contact details of the purchase, interior or project team handling furniture procurement.\r\n\r\nI can share our catalogue, samples and quotation.\r\n\r\nThank you\r\nSikandar\r\nVaibhav Industries\r\nJaipur', '2026-09-18 06:55:34');

-- --------------------------------------------------------

--
-- Table structure for table `property_extra_details`
--

CREATE TABLE `property_extra_details` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `property_type` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `country` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `pincode` varchar(20) NOT NULL,
  `locality` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_extra_details`
--

INSERT INTO `property_extra_details` (`id`, `project_id`, `property_type`, `description`, `country`, `state`, `pincode`, `locality`, `created_at`, `updated_at`) VALUES
(1, 12, 'Apartment', 'Spacious 3BHK with modern amenities, close to schools and shopping.', 'India', 'Maharashtra', '411001', 'Available for sale amit bloomfield society 3bhk flat amit bloomfield society ambegoan bk', '2026-08-17 10:56:09', '2026-08-17 10:56:09'),
(2, 13, 'Flat/Apartment', 'Spacious 3BHK with modern amenities, close to schools and shopping.', 'India', 'Jharkhand', '827001', 'ost Office BOKARO STEEL CITY, BOKARO', '2026-08-17 11:12:11', '2026-08-17 11:12:11'),
(3, 14, 'Serviced Apartment', 'Spacious 3BHK with modern amenities, close to schools and shopping.', 'India', 'Haryana', '411057', 'FARIDABAD NIT, OFFICE District: FARIDABAD', '2026-08-17 11:28:00', '2026-08-17 11:28:00'),
(4, 55, 'Apartment / Flat', 'Opulent and spacious ultra luxurious apartments collection\r\n50% open wide space\r\nStrict visitor protocols\r\nIn-house medical consultation\r\nCheck out Nakshatra Casa Grande in Akkarampalle, one of the upcoming under-construction housing societies in Tirupati. There are apartments for sale in Nakshatra Casa Grande Akkarampalle, Tirupati. This society will have all basic facilities and amen.', 'India', 'Andhra Pradesh', '517501', 'Akkarampalle, Tirupati', '2026-08-27 07:45:28', '2026-08-27 07:45:28'),
(5, 56, 'Apartment / Flat', 'Spacious 3BHK with modern amenities, close to schools and shopping.', 'India', 'Andhra Pradesh', '517503', 'Thandlam, Tirupati', '2026-08-27 07:55:36', '2026-08-27 07:55:36'),
(6, 175, 'Apartment / Flat', 'Spacious 3BHK with modern amenities, close to schools and shopping.', 'India', 'Telangana', '500029', 'hyderabad', '2026-09-01 06:54:03', '2026-09-01 06:54:03'),
(7, 176, 'Residential Open Plot', 'Spacious 3BHK with modern amenities, close to schools and shopping.', 'India', 'Telangana', '500029', 'hyderabad', '2026-09-01 06:59:18', '2026-09-01 06:59:18');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `reviewer_name` varchar(150) NOT NULL,
  `project_name` varchar(150) DEFAULT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5,
  `review_text` text NOT NULL,
  `review_order` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `reviewer_name`, `project_name`, `rating`, `review_text`, `review_order`, `status`, `created_at`) VALUES
(1, 'Ananya Rao', 'Bought at Kalpataru Vian', 5, 'The entire process from site visit to booking was smooth and transparent. Highly recommend the team.', 1, 'Active', '2026-08-07 10:07:13'),
(2, 'Rohan Mehta', 'Bought at DLF Camellias', 5, 'Great location and excellent build quality. Our family is very happy with the new home.', 2, 'Active', '2026-08-07 10:07:13'),
(3, 'Priya Nair', 'Bought at Puravankara Elm', 4, 'Good experience overall, though the handover took a little longer than expected.', 3, 'Active', '2026-08-07 10:07:13'),
(4, 'Ashti Jain', 'Bought at Puravankara Elm3213123', 5, 'Good experience overall, though the handover took a little longer than expected.wqeweqwe', 4, 'Active', '2026-08-07 15:40:45');

-- --------------------------------------------------------

--
-- Table structure for table `site_visit_requests`
--

CREATE TABLE `site_visit_requests` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `budget` varchar(100) NOT NULL,
  `square_feet` varchar(100) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `site_visit_requests`
--

INSERT INTO `site_visit_requests` (`id`, `company_name`, `project_name`, `location`, `budget`, `square_feet`, `project_id`, `status`, `created_at`) VALUES
(1, 'wqerwrwe', 'Kalpataru Vian', 'Andheri West, Mumbai', '₹1.76 Cr', '2365', 1, 'pending', '2026-08-17 15:00:59');

-- --------------------------------------------------------

--
-- Table structure for table `submenus`
--

CREATE TABLE `submenus` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `submenu_name` varchar(100) NOT NULL,
  `submenu_link` varchar(255) DEFAULT NULL,
  `submenu_order` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submenus`
--

INSERT INTO `submenus` (`id`, `menu_id`, `submenu_name`, `submenu_link`, `submenu_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Apartments', '', 1, 'Active', '2026-07-19 09:54:43', '2026-07-19 09:54:43'),
(2, 1, 'Villas', '', 1, 'Active', '2026-07-19 09:54:48', '2026-07-19 09:56:26'),
(3, 5, 'villas 1', '', 1, 'Active', '2026-07-31 10:26:13', '2026-07-31 10:26:13');

-- --------------------------------------------------------

--
-- Table structure for table `trending_projects`
--

CREATE TABLE `trending_projects` (
  `id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `listing_type_id` int(11) DEFAULT NULL,
  `category_type` varchar(50) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `rera_number` varchar(50) DEFAULT NULL,
  `approvals` varchar(255) DEFAULT NULL,
  `min_sft` varchar(50) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `price` varchar(100) NOT NULL,
  `bedrooms` int(11) DEFAULT NULL,
  `bathrooms` int(11) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `year_built` varchar(10) DEFAULT NULL,
  `furnished` varchar(30) DEFAULT NULL,
  `plot_area` varchar(50) DEFAULT NULL,
  `towers` varchar(50) DEFAULT NULL,
  `floors` varchar(50) DEFAULT NULL,
  `units` varchar(50) DEFAULT NULL,
  `total_project_area` varchar(100) DEFAULT NULL,
  `open_area` varchar(50) DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `about_project` text DEFAULT NULL,
  `phases_json` text DEFAULT NULL,
  `spec_flooring` text DEFAULT NULL,
  `spec_bathrooms` text DEFAULT NULL,
  `spec_kitchen` text DEFAULT NULL,
  `spec_doors_windows` text DEFAULT NULL,
  `owner_email` varchar(150) DEFAULT NULL,
  `owner_phone` varchar(30) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `brochure` varchar(255) DEFAULT NULL COMMENT 'PDF filename',
  `project_order` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `package_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trending_projects`
--

INSERT INTO `trending_projects` (`id`, `project_name`, `category_id`, `listing_type_id`, `category_type`, `slug`, `rera_number`, `approvals`, `min_sft`, `address`, `city`, `price`, `bedrooms`, `bathrooms`, `area`, `year_built`, `furnished`, `plot_area`, `towers`, `floors`, `units`, `total_project_area`, `open_area`, `amenities`, `about_project`, `phases_json`, `spec_flooring`, `spec_bathrooms`, `spec_kitchen`, `spec_doors_windows`, `owner_email`, `owner_phone`, `company_name`, `appointment_date`, `image`, `brochure`, `project_order`, `status`, `package_id`, `created_at`, `updated_at`) VALUES
(1, 'Kalpataru Vian', 10001, 7, NULL, 'kalpataru-vian', 'P02400053321', 'HMDA | RERA', '840 - 855', 'Lokhandwala, Andheri West, Mumbai', 'Mumbai', '₹1.78 Cr', 6, 6, '4,200 sqft', 'Aug, 2026', 'Semi-furnished', '4729', 'N/A', '1', 'N/A', '4721', 'N/A', 'Parking.Lift,24x7 Security,Power Backup,Gym,Swimming Pool', 'Check out Kalpataru Vian in Andheri West, western suburb, Mumbai, one of the upcoming under-construction housing societies. There are apartments for sale in this society. This society will have all basic facilities and amenities to suit homebuyer\'s needs and requirements.\n\nBeing a RERA-registered society, the project details and other important information is also available on the state RERA portal.\n\nHere\'s everything you need to know about the must-know features of this housing society along with photos, floor plans, payment plans, brochure download procedure and other exciting facts about your future home:', NULL, 'Wooden flooring in the master bedroom\n\n800x800mm double charged vitrified tiled flooring in all rooms\n\n600x600mm rustic tiles in terraces and balconies\n\n300x300mm matte finished tiled flooring in all toilets', '600x300mm digital wall tiles in all toilets up to lintel level\nProvision for exhaust fans\nHot and cold mixer unit in bathrooms\nJAQUAR/GROHE/CERA or equivalent CP fittings\nHINDWARE/RAK/CERA or equivalent sanitary ware\nConcealed plumbing', 'Granite kitchen platform with stainless steel sink\n600x300mm digital wall tiles up to ceiling\nProvision for exhaust fan\nR.O. water purifier', 'Decorative main door with both side laminated flush door with laminated / polished wooden door frames in living and bed rooms\nGranite door frames in toilets and terraces with both side sliding/french doors for terraces attached to living rooms\nHigh density powder coated aluminium sliding windows with mosquito net & MS grills', 'contact@test.tatitodevelopersandpromoters.com', '+9190000011', 'tatitodevelopersandpromoters', '2026-08-22', 'project_6a5df40319bad3.10083575.jpg', 'brochure_1_1787998860.pdf', 1, 'Active', NULL, '2026-07-20 10:10:11', '2026-09-06 08:17:10'),
(2, 'Mahindra Rainforest', 10003, 7, NULL, 'mahindra-rainforest', NULL, NULL, NULL, 'Bhandup West, Mumbai', 'Mumbai', '₹1.76 Cr', 2, 2, '2,800 sqft', '2012 G.C', 'Furnished', '3360', NULL, NULL, NULL, NULL, NULL, 'Parking.Lift,24x7 Security,Power Backup,Gym,Swimming Pool', NULL, NULL, NULL, NULL, NULL, NULL, 'tatitodevelopersandpromoters@gmail.com', '+919000000000', NULL, '2026-08-31', 'project_6a74689098dda4.82535386.jpg', NULL, 2, 'Active', NULL, '2026-07-20 10:11:17', '2026-08-23 08:29:25'),
(3, 'Godrej Vanantara', 10029, 7, NULL, 'godrej-vanantara', NULL, NULL, NULL, 'Bannerghatta, Bangalore', 'Bangalore', '₹1.76 Cr', 1, 1, '4,100 sqft', '2018 G.C', 'Furnished', '4920', NULL, NULL, NULL, NULL, NULL, 'Attached Market,24 x 7 Security,ATM\'s', NULL, NULL, NULL, NULL, NULL, NULL, 'tatitodevelopersandpromoters@gmail.com', '+919000000000', NULL, '2026-08-23', 'project_6a7468c061ed16.13478731.jpg', NULL, 3, 'Active', NULL, '2026-07-20 10:12:22', '2026-08-23 08:29:25'),
(4, '1 Bedroom 450 Sq.Ft. Apartment in Powai Mumbai', 10002, 7, NULL, '1-bhk-apartment-in-powai', NULL, NULL, NULL, 'Maharashtra , Mumbai , Powai', 'Powai', '₹1,25,00,000', 1, 1, '4,100 sqft', '2018 G.C', 'Furnished', '4920', NULL, NULL, NULL, NULL, NULL, 'Attached Market,24 x 7 Security,ATM\'s', NULL, NULL, NULL, NULL, NULL, NULL, 'srwerwerwe@gmail.com', '+919000000000', NULL, '2026-08-30', 'project_6a7333d94dc2b3.19634800.jpg', NULL, 4, 'Active', NULL, '2026-07-24 10:52:56', '2026-08-23 08:29:25'),
(5, '4 BHK Villa in Kolkata', 10004, 7, NULL, '4-bhk-villa-in-kolkata', NULL, NULL, NULL, 'West Bengal , Kolkata', 'Kolkata', '₹4,90,00,000', 1, 3, '2,096 sqft', '2012 G.C', 'Furnished', '4728', NULL, NULL, NULL, NULL, NULL, 'Parking.Lift,24x7 Security,Power Backup,Gym,Swimming Pool,Clubhouse,Garden,CCTV', NULL, NULL, NULL, NULL, NULL, NULL, 'contact@godrej-properties.com', '+919001012341', NULL, '2026-07-30', 'project_6a7468dfc4adf4.55399021.jpg', NULL, 5, 'Active', NULL, '2026-07-25 08:32:19', '2026-08-23 08:29:25'),
(6, '5 Bedroom 7350 Sq.Ft. Penthouse in Prahlad Nagar Ahmedabad', 10008, 7, NULL, '5-bhk-penthouse-in-satellite', NULL, NULL, NULL, 'Gujarat , Ahmedabad , Satellite', 'Ahmedabad', '₹4,50,00,000 Cr.', 5, 4, '7350 Sq.Ft.', '2018 G.C', 'Furnished', '4920', NULL, NULL, NULL, NULL, NULL, 'Parking.Lift,24x7 Security,Power Backup,Gym,Swimming Pool', NULL, NULL, NULL, NULL, NULL, NULL, 'ravi@raviRealty.com', '+91 9876543210', NULL, '2026-07-31', 'project_6a76bfdfb86d42.12915140.jpeg', NULL, 6, 'Active', NULL, '2026-07-25 08:37:37', '2026-08-23 08:29:25'),
(7, 'Independent Villa in Whitefield', 10003, 7, NULL, 'independent-villa-in-whitefield', NULL, NULL, NULL, 'Karnataka , Bangalore , Whitefield', 'Bangalore', '₹3,20,00,000', 4, 4, '2,800 sqft', '2021 G.C', 'Furnished', '3360', NULL, NULL, NULL, NULL, NULL, 'Parking.Lift,24x7 Security,Power Backup,Gym,Swimming Pool,Clubhouse,Garden,CCTV', NULL, NULL, NULL, NULL, NULL, NULL, 'contact@lodha-group.com', '+91 9003012343', NULL, '2026-08-04', 'project_6a76bedf1bc4c6.73425435.jpeg', NULL, 7, 'Active', NULL, '2026-07-27 11:44:38', '2026-08-23 08:29:25'),
(8, 'Godrej Brooklyn Avenue', 10002, 7, NULL, 'godrej-brooklyn-avenue', NULL, NULL, NULL, 'Kukatpally, Hyderabad', 'Hyderabad', '1.98 Cr - 4.07 Cr', 1, 2, '3261 sqft', '2018 G.C', NULL, '7.75', NULL, NULL, NULL, NULL, NULL, 'Parking.Lift,24x7 Security,Power Backup,Gym,Swimming Pool', NULL, NULL, NULL, NULL, NULL, NULL, 'tatitodevelopersandpromoters@gmail.com', '+919000000000', NULL, '2026-08-15', 'project_6a76bcec8c1cf3.26161153.jpeg', NULL, 6, 'Active', NULL, '2026-08-08 05:21:48', '2026-08-23 08:29:25'),
(9, 'Brigade Manor', 10002, 7, NULL, 'brigade-manor', NULL, NULL, NULL, 'Moti Nagar, Hyderabad', 'Hyderabad', '2.09 Cr - 3.20 Cr', 1, 2, '4.79 Acres', '2018 G.C', 'Furnished', '2067 to 3164 Sq. Ft', NULL, NULL, NULL, NULL, NULL, 'Parking.Lift,24x7 Security,Power Backup,Gym,Swimming Pool', NULL, NULL, NULL, NULL, NULL, NULL, 'tatitodevelopersandpromoters@gmail.com', '+919000000000', NULL, '2026-08-10', 'project_6a76bde6889aa7.69499125.jpeg', NULL, 9, 'Active', NULL, '2026-08-08 05:25:58', '2026-08-23 08:29:25'),
(10, 'Puravankara Northern Lights', 10002, 7, NULL, 'puravankara-northern-lights', NULL, NULL, NULL, 'Bagalur, Bangalore', 'Bangalore', '₹1.21 Cr - 4.55 Cr', 1, 2, '24.59 Acres', '2018 G.C', '', '1097 to 4131 Sq. Ft', NULL, NULL, NULL, NULL, NULL, 'Parking.Lift,24x7 Security,Power Backup,Gym,Swimming Pool', NULL, NULL, NULL, NULL, NULL, NULL, 'tatitodevelopersandpromoters@gmail.com', '+919000000000', NULL, '2026-08-20', 'project_6a76be953d3e94.34562794.jpeg', NULL, 10, 'Active', NULL, '2026-08-08 05:28:53', '2026-08-23 08:29:25'),
(11, 'The Prestige City Hyderabad', 10002, 7, NULL, 'the-prestige-city-hyderabad', NULL, NULL, NULL, 'Rajendra Nagar, Hyderabad', 'Hyderabad', '₹1.19 Cr - 3.06 Cr', 2, 3, '31.1 Acres', '2018 G.C', 'Furnished', '1179 to 3431 Sq. Ft', NULL, NULL, NULL, NULL, NULL, 'Parking.Lift,24x7 Security,Power Backup,Gym,Swimming Pool', NULL, NULL, NULL, NULL, NULL, NULL, 'tatitodevelopersandpromoters@gmail.com', '+919000000000', NULL, NULL, 'project_6a76c97fc76e05.16820444.jpeg', NULL, 11, 'Active', NULL, '2026-08-08 06:15:27', '2026-08-23 08:29:25'),
(12, '3BHK Apartment in MVP Colony', 10114, 7, 'rent', '3bhk-apartment-in-mvp-colony', 'P02400053321', 'HMDA | RERA', '840 - 855', 'Rajiv Gandhi Infotech Park Hinjawadi, Maharashtra', 'Pune', '₹4500000', 2, 2, '1200', 'Aug, 2026', 'Semi-Furnished', '', NULL, NULL, NULL, NULL, NULL, 'Gym, Pool, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'tatitodevelopersandpromoters@gmail.com', '+919000000000', 'tatitodevelopersandpromoters', '0000-00-00', 'main_12_1788077972.jpg', NULL, 0, 'Active', 1, '2026-08-17 10:56:09', '2026-09-06 08:17:10'),
(13, '3BHK Apartment in MVP Colony', 10114, 7, 'rent', '3bhk-apartment-in-mvp-colony', NULL, NULL, NULL, 'ost Office BOKARO STEEL CITY, BOKARO', 'Bokaro', '4500000', 2, 2, '1200', '2020', 'Semi-Furnished', '1500', NULL, NULL, NULL, NULL, NULL, 'Gym, Pool, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'tatitodevelopersandpromoters@gmail.com', '+919000000000', NULL, '0000-00-00', '', NULL, 0, 'Active', 1, '2026-08-17 11:12:11', '2026-09-03 11:30:10'),
(14, '3BHK Apartment in MVP Colony', 10114, 7, 'rent', '3bhk-apartment-in-mvp-colony', NULL, NULL, NULL, 'FARIDABAD NIT, OFFICE', 'Faridabad', '4500000', 2, 2, '1200', '2020', 'Semi-Furnished', '', NULL, NULL, NULL, NULL, NULL, 'Gym, Pool, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'tatitodevelopersandpromoters@gmail.com', '', NULL, '0000-00-00', 'uploads/properties/prop_6a82f040cd8857.83920481.jpg', NULL, 0, 'Active', 1, '2026-08-17 11:28:00', '2026-09-03 11:30:10'),
(15, 'Zolo Stays - HSR Layout', 99, NULL, 'pg', 'zolo-stays-hsr-layout', NULL, NULL, NULL, 'HSR Layout, Bangalore', 'Bangalore', '₹12,000/month', 1, 1, '180 sqft', '2022', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, AC, Meals, Laundry, Gym, 24/7 Security, CCTV', NULL, NULL, NULL, NULL, NULL, NULL, 'contact@zolostays.com', '+91 9876543210', NULL, NULL, 'pg_zolo_hsr.jpg', NULL, 1, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(16, 'Stanza Living - Marathahalli', 101, NULL, 'pg', 'stanza-living-marathahalli', NULL, NULL, NULL, 'Marathahalli, Bangalore', 'Bangalore', '₹15,000/month', 1, 1, '200 sqft', '2021', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, AC, Meals, Housekeeping, Gym, Study Room, 24/7 Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@stanzaliving.com', '+91 8765432109', NULL, NULL, 'pg_stanza_marathahalli.jpg', NULL, 2, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(17, 'Colive - Kalyani Nagar', 102, NULL, 'pg', 'colive-kalyani-nagar', NULL, NULL, NULL, 'Kalyani Nagar, Pune', 'Pune', '₹10,000/month', 1, 1, '150 sqft', '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Housekeeping, Parking, Gym, CCTV', NULL, NULL, NULL, NULL, NULL, NULL, 'hello@colive.com', '+91 7654321098', NULL, NULL, 'pg_colive_pune.jpg', NULL, 3, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(18, 'Housr - Kondapur', 103, NULL, 'pg', 'housr-kondapur', NULL, NULL, NULL, 'Kondapur, Hyderabad', 'Hyderabad', '₹11,000/month', 1, 1, '160 sqft', '2022', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, AC, Housekeeping, Gym, 24/7 Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@housr.com', '+91 6543210987', NULL, NULL, 'pg_housr_hyderabad.jpg', NULL, 4, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(19, 'YourSpace - Whitefield', 106, NULL, 'pg', 'yourspace-whitefield', NULL, NULL, NULL, 'Whitefield, Bangalore', 'Bangalore', '₹14,000/month', 1, 1, '190 sqft', '2021', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, AC, Meals, Laundry, Gym, Swimming Pool, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'contact@yourspace.in', '+91 5432109876', NULL, NULL, 'pg_yourspace_whitefield.jpg', NULL, 5, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(20, 'CoHo - Baner', 107, NULL, 'pg', 'coho-baner', NULL, NULL, NULL, 'Baner, Pune', 'Pune', '₹9,500/month', 1, 1, '140 sqft', '2020', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Housekeeping, Parking, CCTV', NULL, NULL, NULL, NULL, NULL, NULL, 'hello@coho.co.in', '+91 4321098765', NULL, NULL, 'pg_coho_pune.jpg', NULL, 6, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(21, 'Living Space - Andheri', 110, NULL, 'pg', 'living-space-andheri', NULL, NULL, NULL, 'Andheri West, Mumbai', 'Mumbai', '₹18,000/month', 1, 1, '220 sqft', '2022', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, AC, Meals, Laundry, Gym, 24/7 Security, Power Backup', NULL, NULL, NULL, NULL, NULL, NULL, 'info@livingspace.com', '+91 3210987654', NULL, NULL, 'pg_living_space_mumbai.jpg', NULL, 7, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(22, 'Grexter - Rajajinagar', 111, NULL, 'pg', 'grexter-rajajinagar', NULL, NULL, NULL, 'Rajajinagar, Bangalore', 'Bangalore', '₹13,000/month', 1, 1, '175 sqft', '2021', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, AC, Housekeeping, Gym, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@grexter.in', '+91 2109876543', NULL, NULL, 'pg_grexter_bangalore.jpg', NULL, 8, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(23, 'YMCA Hostel - Delhi', 17, NULL, 'hostel', 'ymca-hostel-delhi', NULL, NULL, NULL, 'Connaught Place, New Delhi', 'Delhi', '₹8,000/month', 1, 1, '120 sqft', '2018', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Laundry, Library, Sports Facilities, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@ymcadelhi.com', '+91 9988776655', NULL, NULL, 'hostel_ymca_delhi.jpg', NULL, 9, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(24, 'Vagabond Hostel - Koramangala', 20, NULL, 'hostel', 'vagabond-hostel-koramangala', NULL, NULL, NULL, 'Koramangala, Bangalore', 'Bangalore', '₹6,500/month', 1, 1, '100 sqft', '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Bunk Beds, Common Kitchen, Lounge, Games Area, Laundry', NULL, NULL, NULL, NULL, NULL, NULL, 'hello@vagabondhostel.com', '+91 8877665544', NULL, NULL, 'hostel_vagabond_bangalore.jpg', NULL, 10, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(25, 'Zostel - Fort Kochi', 20, NULL, 'hostel', 'zostel-fort-kochi', NULL, NULL, NULL, 'Fort Kochi, Kochi', 'Kochi', '₹5,500/month', 1, 1, '90 sqft', '2019', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Dorm Beds, Common Area, Kitchen, Laundry, Tours', NULL, NULL, NULL, NULL, NULL, NULL, 'info@zostel.com', '+91 7766554433', NULL, NULL, 'hostel_zostel_kochi.jpg', NULL, 11, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(26, 'GoStops - Pushkar', 20, NULL, 'hostel', 'gostops-pushkar', NULL, NULL, NULL, 'Pushkar, Rajasthan', 'Pushkar', '₹4,500/month', 1, 1, '80 sqft', '2018', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Dorm Beds, Rooftop, Common Kitchen, Yoga Area', NULL, NULL, NULL, NULL, NULL, NULL, 'hello@gostops.in', '+91 6655443322', NULL, NULL, 'hostel_gostops_pushkar.jpg', NULL, 12, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(27, 'Backpacker Hostel - McLeod Ganj', 20, NULL, 'hostel', 'backpacker-hostel-mcleod-ganj', NULL, NULL, NULL, 'McLeod Ganj, Dharamshala', 'Dharamshala', '₹5,000/month', 1, 1, '85 sqft', '2019', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Dorm Beds, Common Area, Kitchen, Mountain View', NULL, NULL, NULL, NULL, NULL, NULL, 'info@backpackerhostel.com', '+91 5544332211', NULL, NULL, 'hostel_backpacker_dharamshala.jpg', NULL, 13, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(28, 'Girls Hostel - Aundh', 18, NULL, 'hostel', 'girls-hostel-aundh', NULL, NULL, NULL, 'Aundh, Pune', 'Pune', '₹9,000/month', 1, 1, '150 sqft', '2021', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, AC, CCTV, 24/7 Security, Study Room, Gym', NULL, NULL, NULL, NULL, NULL, NULL, 'info@girlshostelpune.com', '+91 4433221100', NULL, NULL, 'hostel_girls_pune.jpg', NULL, 14, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(29, 'Working Women\'s Hostel - Indiranagar', 21, NULL, 'hostel', 'working-womens-hostel-indiranagar', NULL, NULL, NULL, 'Indiranagar, Bangalore', 'Bangalore', '₹11,000/month', 1, 1, '165 sqft', '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, AC, Meals, Housekeeping, Gym, 24/7 Security, Parking', NULL, NULL, NULL, NULL, NULL, NULL, 'info@womenshostel.in', '+91 3322110099', NULL, NULL, 'hostel_women_bangalore.jpg', NULL, 15, 'Active', NULL, '2026-08-19 09:55:16', '2026-08-19 09:55:16'),
(30, 'Taj Palace - New Delhi', 62, NULL, 'hotel', 'taj-palace-new-delhi', NULL, NULL, NULL, 'Sardar Patel Marg, New Delhi', 'Delhi', '₹25,000/night', 0, 0, NULL, '1980', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Swimming Pool, Spa, Gym, Fine Dining, Conference Hall, 24/7 Service, WiFi', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@tajhotels.com', '+91 1126112233', NULL, NULL, 'hotel_taj_delhi.jpg', NULL, 16, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(31, 'The Oberoi - Mumbai', 65, NULL, 'hotel', 'the-oberoi-mumbai', NULL, NULL, NULL, 'Nariman Point, Mumbai', 'Mumbai', '₹30,000/night', 0, 0, NULL, '1986', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Sea View, Swimming Pool, Spa, Gym, Restaurants, Lounge, WiFi', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@oberoihotels.com', '+91 2224323232', NULL, NULL, 'hotel_oberoi_mumbai.jpg', NULL, 17, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(32, 'ITC Grand Chola - Chennai', 65, NULL, 'hotel', 'itc-grand-chola-chennai', NULL, NULL, NULL, 'Guindy, Chennai', 'Chennai', '₹22,000/night', 0, 0, NULL, '2012', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Swimming Pool, Spa, Gym, Fine Dining, WiFi, Business Center', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@itchotels.in', '+91 4422222222', NULL, NULL, 'hotel_itc_chennai.jpg', NULL, 18, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(33, 'The Leela Palace - Bangalore', 65, NULL, 'hotel', 'the-leela-palace-bangalore', NULL, NULL, NULL, 'Old Airport Road, Bangalore', 'Bangalore', '₹20,000/night', 0, 0, NULL, '2001', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Swimming Pool, Spa, Gym, Restaurants, Conference Hall, WiFi', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@theleela.com', '+91 8032112345', NULL, NULL, 'hotel_leela_bangalore.jpg', NULL, 19, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(34, 'Boutique Hotel - Panjim', 71, NULL, 'hotel', 'boutique-hotel-panjim', NULL, NULL, NULL, 'Panjim, Goa', 'Goa', '₹8,000/night', 0, 0, NULL, '2018', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Pool, Restaurant, Lounge, WiFi, Garden, Parking', NULL, NULL, NULL, NULL, NULL, NULL, 'info@boutiquehotel.in', '+91 8321111111', NULL, NULL, 'hotel_boutique_goa.jpg', NULL, 20, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(35, 'Heritage Hotel - Jaipur', 73, NULL, 'hotel', 'heritage-hotel-jaipur', NULL, NULL, NULL, 'Jaipur City, Jaipur', 'Jaipur', '₹10,000/night', 0, 0, NULL, '1875', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Heritage Decor, Pool, Restaurant, WiFi, Garden, Parking', NULL, NULL, NULL, NULL, NULL, NULL, 'info@heritagehotel.in', '+91 1411111111', NULL, NULL, 'hotel_heritage_jaipur.jpg', NULL, 21, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(36, 'Resort Hotel - Alibaug', 72, NULL, 'hotel', 'resort-hotel-alibaug', NULL, NULL, NULL, 'Alibaug, Maharashtra', 'Alibaug', '₹12,000/night', 0, 0, NULL, '2015', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Beach View, Pool, Spa, Restaurant, WiFi, Parking, Gardens', NULL, NULL, NULL, NULL, NULL, NULL, 'info@resortalibaug.in', '+91 2141111111', NULL, NULL, 'hotel_resort_alibaug.jpg', NULL, 22, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(37, 'Entire Villa - Candolim', 80, NULL, 'homestay', 'entire-villa-candolim', NULL, NULL, NULL, 'Candolim, Goa', 'Goa', '₹15,000/night', 4, 4, '2500 sqft', '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Private Pool, Garden, Kitchen, WiFi, 4 Bedrooms, Parking, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@villagoa.com', '+91 8322222222', NULL, NULL, 'homestay_villa_goa.jpg', NULL, 23, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(38, 'Farm Stay - Coorg', 83, NULL, 'homestay', 'farm-stay-coorg', NULL, NULL, NULL, 'Madikeri, Coorg', 'Coorg', '₹8,000/night', 2, 2, '1500 sqft', '2018', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Organic Farm, Coffee Plantation, Trekking, WiFi, Organic Meals', NULL, NULL, NULL, NULL, NULL, NULL, 'info@farmstaycoorg.com', '+91 8272222222', NULL, NULL, 'homestay_farm_coorg.jpg', NULL, 24, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(39, 'Private Room - Munnar', 81, NULL, 'homestay', 'private-room-munnar', NULL, NULL, NULL, 'Munnar, Kerala', 'Munnar', '₹5,000/night', 1, 1, '400 sqft', '2019', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Mountain View, Organic Food, WiFi, Trekking, Gardens', NULL, NULL, NULL, NULL, NULL, NULL, 'info@munnarhomestay.com', '+91 4862222222', NULL, NULL, 'homestay_munnar.jpg', NULL, 25, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(40, 'Tree House - Wayanad', 85, NULL, 'homestay', 'tree-house-wayanad', NULL, NULL, NULL, 'Wayanad, Kerala', 'Wayanad', '₹7,500/night', 1, 1, '350 sqft', '2021', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Tree House, Jungle View, Organic Meals, WiFi, Hiking', NULL, NULL, NULL, NULL, NULL, NULL, 'info@treehousewayanad.com', '+91 4932222222', NULL, NULL, 'homestay_treehouse_wayanad.jpg', NULL, 26, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(41, 'Beach House - Varkala', 87, NULL, 'homestay', 'beach-house-varkala', NULL, NULL, NULL, 'Varkala, Kerala', 'Varkala', '₹6,000/night', 2, 2, '800 sqft', '2020', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Beach View, Private Access, Kitchen, WiFi, Gardens', NULL, NULL, NULL, NULL, NULL, NULL, 'info@beachhousevarkala.com', '+91 4702222222', NULL, NULL, 'homestay_beach_varkala.jpg', NULL, 27, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(42, 'Hill Station Stay - Manali', 88, NULL, 'homestay', 'hill-station-stay-manali', NULL, NULL, NULL, 'Manali, Himachal Pradesh', 'Manali', '₹4,500/night', 2, 2, '600 sqft', '2017', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Mountain View, Fireplace, Organic Meals, WiFi, Trekking', NULL, NULL, NULL, NULL, NULL, NULL, 'info@manalihomestay.com', '+91 1902222222', NULL, NULL, 'homestay_manali.jpg', NULL, 28, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(43, 'Heritage Homestay - Jaipur', 89, NULL, 'homestay', 'heritage-homestay-jaipur', NULL, NULL, NULL, 'Old City, Jaipur', 'Jaipur', '₹5,500/night', 2, 2, '700 sqft', '1920', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Heritage Decor, Traditional Meals, WiFi, City Tours', NULL, NULL, NULL, NULL, NULL, NULL, 'info@heritagehomestay.com', '+91 1413333333', NULL, NULL, 'homestay_heritage_jaipur.jpg', NULL, 29, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(44, 'Serviced Apartment - Gurgaon', 91, NULL, 'homestay', 'serviced-apartment-gurgaon', NULL, NULL, NULL, 'Cyber City, Gurgaon', 'Gurgaon', '₹8,500/night', 2, 2, '1100 sqft', '2021', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Kitchen, WiFi, AC, Gym, Parking, 24/7 Security, Housekeeping', NULL, NULL, NULL, NULL, NULL, NULL, 'info@servicedapt.in', '+91 1242222222', NULL, NULL, 'homestay_serviced_gurgaon.jpg', NULL, 30, 'Active', NULL, '2026-08-19 09:56:14', '2026-08-19 09:56:14'),
(45, 'Urban Company - Home Services', 130, NULL, 'home_services', 'urban-company-home-services', NULL, NULL, NULL, 'Pan India', 'Mumbai', '₹500/service', 0, 0, NULL, '2014', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AC Service, Plumbing, Electrician, Cleaning, Painting, Carpentry', NULL, NULL, NULL, NULL, NULL, NULL, 'info@urbancompany.com', '+91 18002002000', NULL, NULL, 'vendor_urbancompany.jpg', NULL, 31, 'Active', NULL, '2026-08-19 09:56:22', '2026-08-19 09:56:22'),
(46, 'Housejoy - AC Repair', 166, NULL, 'home_services', 'housejoy-ac-repair', NULL, NULL, NULL, 'Bangalore, Mumbai, Delhi', 'Bangalore', '₹600/service', 0, 0, NULL, '2015', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AC Installation, AC Service, AC Gas Refill, AC Repair', NULL, NULL, NULL, NULL, NULL, NULL, 'info@housejoy.in', '+91 18001234567', NULL, NULL, 'vendor_housejoy_ac.jpg', NULL, 32, 'Active', NULL, '2026-08-19 09:56:22', '2026-08-19 09:56:22'),
(47, 'Mr. Right - Plumbing Services', 140, NULL, 'home_services', 'mr-right-plumbing-services', NULL, NULL, NULL, 'All Major Cities', 'Delhi', '₹400/service', 0, 0, NULL, '2016', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tap Repair, Pipe Fix, Bathroom Fitting, Toilet Repair, Drainage', NULL, NULL, NULL, NULL, NULL, NULL, 'info@mrright.in', '+91 18009876543', NULL, NULL, 'vendor_mrright_plumbing.jpg', NULL, 33, 'Active', NULL, '2026-08-19 09:56:22', '2026-08-19 09:56:22'),
(48, 'Clean India - Home Cleaning', 174, NULL, 'home_services', 'clean-india-home-cleaning', NULL, NULL, NULL, 'Mumbai, Pune, Bangalore', 'Mumbai', '₹800/service', 0, 0, NULL, '2018', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Deep Cleaning, Sofa Cleaning, Carpet Cleaning, Bathroom Cleaning', NULL, NULL, NULL, NULL, NULL, NULL, 'info@cleanindia.in', '+91 18004567890', NULL, NULL, 'vendor_cleanindia.jpg', NULL, 34, 'Active', NULL, '2026-08-19 09:56:22', '2026-08-19 09:56:22'),
(49, 'PaintWala - Interior Painting', 158, NULL, 'home_services', 'paintwala-interior-painting', NULL, NULL, NULL, 'All Cities', 'Bangalore', '₹2,500/room', 0, 0, NULL, '2017', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Wall Painting, Texture, Waterproofing, Wood Painting', NULL, NULL, NULL, NULL, NULL, NULL, 'info@paintwala.com', '+91 18005678901', NULL, NULL, 'vendor_paintwala.jpg', NULL, 35, 'Active', NULL, '2026-08-19 09:56:22', '2026-08-19 09:56:22'),
(50, 'Pest Control Solutions', 185, NULL, 'home_services', 'pest-control-solutions', NULL, NULL, NULL, 'Pan India', 'Delhi', '₹1,200/service', 0, 0, NULL, '2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'General Pest, Termite, Cockroach, Mosquito, Rodent Control', NULL, NULL, NULL, NULL, NULL, NULL, 'info@pestcontrol.in', '+91 18006789012', NULL, NULL, 'vendor_pestcontrol.jpg', NULL, 36, 'Active', NULL, '2026-08-19 09:56:22', '2026-08-19 09:56:22'),
(51, 'SecureHome - CCTV Installation', 192, NULL, 'home_services', 'securehome-cctv-installation', NULL, NULL, NULL, 'All Major Cities', 'Mumbai', '₹3,500/installation', 0, 0, NULL, '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CCTV, Video Door Phone, Smart Locks, Alarm Systems, Intercom', NULL, NULL, NULL, NULL, NULL, NULL, 'info@securehome.in', '+91 18007890123', NULL, NULL, 'vendor_securehome.jpg', NULL, 37, 'Active', NULL, '2026-08-19 09:56:22', '2026-08-19 09:56:22'),
(52, 'Packers & Movers - Agarwal', 199, NULL, 'home_services', 'packers-movers-agarwal', NULL, NULL, NULL, 'All Cities', 'Delhi', '₹5,000/shift', 0, 0, NULL, '2010', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Local Shifting, Intercity Shifting, Office Relocation, Vehicle Transport', NULL, NULL, NULL, NULL, NULL, NULL, 'info@agrawalpackers.com', '+91 18008901234', NULL, NULL, 'vendor_packers_movers.jpg', NULL, 38, 'Active', NULL, '2026-08-19 09:56:22', '2026-08-19 09:56:22'),
(53, 'Green Thumb - Landscaping', 205, NULL, 'home_services', 'green-thumb-landscaping', NULL, NULL, NULL, 'Bangalore, Hyderabad', 'Bangalore', '₹2,000/service', 0, 0, NULL, '2018', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Garden Maintenance, Lawn Mowing, Tree Trimming, Landscape Design', NULL, NULL, NULL, NULL, NULL, NULL, 'info@greenthumb.in', '+91 18009012345', NULL, NULL, 'vendor_greenthumb.jpg', NULL, 39, 'Active', NULL, '2026-08-19 09:56:22', '2026-08-19 09:56:22'),
(54, 'Home Salon - Blossom', 218, NULL, 'home_services', 'home-salon-blossom', NULL, NULL, NULL, 'All Major Cities', 'Delhi', '₹800/service', 0, 0, NULL, '2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Salon at Home, Spa, Makeup Artist, Mehendi, Beauty Services', NULL, NULL, NULL, NULL, NULL, NULL, 'info@homesalon.in', '+91 18001234568', NULL, NULL, 'vendor_homesalon.jpg', NULL, 40, 'Active', NULL, '2026-08-19 09:56:22', '2026-08-19 09:56:22'),
(55, 'Casa Grande', 10002, NULL, 'buy', 'nakshatra-casa-grande', 'P024000533211', 'HMDA | RERA', '840 - 855', 'Balaji Colony, Akkarampalle, Tirupati', 'Tirupati', '₹1500', 2, 2, '1200', 'Aug, 2026', 'Semi-Furnished', '1500', NULL, NULL, NULL, NULL, NULL, 'Gym, Pool, Securitym,Lift(s),CCTV Surveillance,Gymnasium,Visitor Parking', NULL, NULL, NULL, NULL, NULL, NULL, 'tatitodevelopersandpromoters@gmail.com', '+919000000000', 'Estate Hubss', '0000-00-00', 'main_55_1787999658.jpg', NULL, 0, 'Active', 2, '2026-08-27 07:45:28', '2026-08-30 07:04:47'),
(56, 'Ananda Vihara', 10002, NULL, 'buy', 'ananda-vihara', NULL, NULL, NULL, 'Thandlam Tirupati', 'Tirupati', '4500000', 2, 2, '1200', '2024', 'Semi-Furnished', '1700', NULL, NULL, NULL, NULL, NULL, 'Gym, Pool, Securitym,Lift(s),CCTV Surveillance,Gymnasium,Visitor Parking', NULL, NULL, NULL, NULL, NULL, NULL, 'tatitodevelopersandpromoters@gmail.com', '+919000000000', NULL, '0000-00-00', '', NULL, 0, 'Active', 2, '2026-08-27 07:55:36', '2026-08-27 07:55:36'),
(57, 'HomeLane Modular Kitchen Studio', 271, NULL, 'interior', 'homelane-modular-kitchen-studio', NULL, NULL, NULL, 'Kothrud, Pune, Maharashtra', 'Pune', '₹1,45,000 onwards', 2, 2, '1000', '2025', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No additional details available for this category.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'main_57_1788166179.jpg', NULL, 1, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 12:58:37'),
(58, 'Livspace Island Kitchen Makeover', 271, NULL, 'interior', 'livspace-island-kitchen-makeover', NULL, NULL, NULL, 'Whitefield, Bangalore, Karnataka', 'Bangalore', '₹3,20,000 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 2, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 08:32:15'),
(59, 'Kajaria Premium Flooring Service', 270, NULL, 'interior', 'kajaria-premium-flooring-service', NULL, NULL, NULL, 'Dwarka, Delhi', 'Delhi', 'Starting ₹65/sqft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 08:29:16'),
(60, 'Italian Marble Flooring Experts', 270, NULL, 'interior', 'italian-marble-flooring-experts', NULL, NULL, NULL, 'Banjara Hills, Hyderabad, Telangana', 'Hyderabad', 'Starting ₹280/sqft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 2, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 08:32:27'),
(61, 'Godrej Interio Wardrobe Design', 272, NULL, 'interior', 'godrej-interio-wardrobe-design', NULL, NULL, NULL, 'Andheri West, Mumbai, Maharashtra', 'Mumbai', '₹68,000 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 08:30:14'),
(62, 'Gyproc False Ceiling Contractors', 273, NULL, 'interior', 'gyproc-false-ceiling-contractors', NULL, NULL, NULL, 'Koramangala, Bangalore, Karnataka', 'Bangalore', 'Starting ₹65/sqft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 08:30:36'),
(63, 'Asian Paints Interior Finishing', 274, NULL, 'interior', 'asian-paints-interior-finishing', NULL, NULL, NULL, 'Salt Lake, Kolkata, West Bengal', 'Kolkata', 'Starting ₹320/litre', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 08:31:08'),
(64, 'Jaquar Bathroom Fitting Studio', 275, NULL, 'interior', 'jaquar-bathroom-fitting-studio', NULL, NULL, NULL, 'Vastrapur, Ahmedabad, Gujarat', 'Ahmedabad', '₹18,000 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 08:31:19'),
(65, 'Philips Home Lighting Solutions', 276, NULL, 'interior', 'philips-home-lighting-solutions', NULL, NULL, NULL, 'Indiranagar, Bangalore, Karnataka', 'Bangalore', '₹850 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 08:31:32'),
(66, 'Urban Ladder Furniture Consultants', 277, NULL, 'interior', 'urban-ladder-furniture-consultants', NULL, NULL, NULL, 'Powai, Mumbai, Maharashtra', 'Mumbai', '₹34,999 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 08:31:42'),
(67, 'Pepperfry Home Styling Service', 278, NULL, 'interior', 'pepperfry-home-styling-service', NULL, NULL, NULL, 'MVP Colony, Visakhapatnam, Andhra Pradesh', 'Visakhapatnam', '₹3,200 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 08:31:52'),
(68, 'WoodCraft Custom Door Studio', 279, NULL, 'interior', 'woodcraft-custom-door-studio', NULL, NULL, NULL, 'Akkarampalle, Tirupati, Andhra Pradesh', 'Tirupati', '₹19,500 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 'Active', NULL, '2026-08-31 08:25:42', '2026-08-31 08:32:02'),
(69, 'Radisson Blu - Pune', 10553, NULL, 'hotel', 'radisson-blu-pune', NULL, NULL, NULL, 'Kharadi, Pune', 'Pune', '₹9,500/night', 0, 0, NULL, '2015', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Swimming Pool, Gym, Spa, Restaurant, WiFi, Business Center', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@radissonpune.com', '+91 2066112233', NULL, NULL, 'hotel_radisson_pune.jpg', NULL, 41, 'Active', NULL, '2026-08-31 09:00:00', '2026-08-31 09:00:00'),
(70, 'Fern Residency - Ahmedabad', 10549, NULL, 'hotel', 'fern-residency-ahmedabad', NULL, NULL, NULL, 'Navrangpura, Ahmedabad', 'Ahmedabad', '₹3,200/night', 0, 0, NULL, '2017', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'AC, Restaurant, WiFi, Parking, 24/7 Front Desk', NULL, NULL, NULL, NULL, NULL, NULL, 'info@fernresidency.com', '+91 7912345678', NULL, NULL, 'hotel_fern_ahmedabad.jpg', NULL, 42, 'Active', NULL, '2026-08-31 09:00:00', '2026-08-31 09:00:00'),
(71, 'Lakeside Villa Stay - Udaipur', 10576, NULL, 'homestay', 'lakeside-villa-stay-udaipur', NULL, NULL, NULL, 'Fatehsagar Lake, Udaipur', 'Udaipur', '₹11,000/night', 3, 3, '2200 sqft', '2019', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Lake View, Private Pool, Kitchen, WiFi, Garden', NULL, NULL, NULL, NULL, NULL, NULL, 'info@lakesidevilla.com', '+91 2942222333', NULL, NULL, 'homestay_lakeside_udaipur.jpg', NULL, 43, 'Active', NULL, '2026-08-31 09:00:00', '2026-08-31 09:00:00'),
(72, 'Backwater Houseboat - Alleppey', 10579, NULL, 'homestay', 'backwater-houseboat-alleppey', NULL, NULL, NULL, 'Alleppey, Kerala', 'Alleppey', '₹9,000/night', 2, 2, NULL, '2016', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Houseboat, Meals Included, Sundeck, WiFi', NULL, NULL, NULL, NULL, NULL, NULL, 'info@keralahouseboats.com', '+91 4772223344', NULL, NULL, 'homestay_houseboat_alleppey.jpg', NULL, 44, 'Active', NULL, '2026-08-31 09:00:00', '2026-08-31 09:00:00'),
(73, 'Hillside Cottage - Shimla', 10577, NULL, 'homestay', 'hillside-cottage-shimla', NULL, NULL, NULL, 'Mall Road, Shimla', 'Shimla', '₹6,500/night', 2, 1, '900 sqft', '2018', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Mountain View, Fireplace, WiFi, Home-cooked Meals', NULL, NULL, NULL, NULL, NULL, NULL, 'info@shimlacottage.com', '+91 1772223344', NULL, NULL, 'homestay_cottage_shimla.jpg', NULL, 45, 'Active', NULL, '2026-08-31 09:00:00', '2026-08-31 09:00:00'),
(74, 'Nestaway PG - Electronic City', 10592, NULL, 'pg', 'nestaway-pg-electronic-city', NULL, NULL, NULL, 'Electronic City, Bangalore', 'Bangalore', '₹9,800/month', 1, 1, '160 sqft', '2021', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, AC, Housekeeping, 24/7 Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@nestaway.com', '+91 9876500011', NULL, NULL, 'pg_nestaway_bangalore.jpg', NULL, 46, 'Active', NULL, '2026-08-31 09:00:00', '2026-08-31 09:00:00'),
(75, 'SafeStay Girls PG - Kothrud', 10593, NULL, 'pg', 'safestay-girls-pg-kothrud', NULL, NULL, NULL, 'Kothrud, Pune', 'Pune', '₹10,500/month', 1, 1, '170 sqft', '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, CCTV, Warden on Premises, Laundry', NULL, NULL, NULL, NULL, NULL, NULL, 'info@safestaypg.com', '+91 9876500012', NULL, NULL, 'pg_safestay_pune.jpg', NULL, 47, 'Active', NULL, '2026-08-31 09:00:00', '2026-08-31 09:00:00'),
(76, 'Corporate Co-ed PG - Gachibowli', 10594, NULL, 'pg', 'corporate-co-ed-pg-gachibowli', NULL, NULL, NULL, 'Gachibowli, Hyderabad', 'Hyderabad', '₹13,500/month', 1, 1, '185 sqft', '2022', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, AC, Meals, Gym, Co-working Space', NULL, NULL, NULL, NULL, NULL, NULL, 'info@corporatepg.com', '+91 9876500013', NULL, NULL, 'pg_corporate_hyderabad.jpg', NULL, 48, 'Active', NULL, '2026-08-31 09:00:00', '2026-08-31 09:00:00'),
(77, 'Sunrise Boys Hostel - Anna Nagar', 10607, NULL, 'hostel', 'sunrise-boys-hostel-anna-nagar', NULL, NULL, NULL, 'Anna Nagar, Chennai', 'Chennai', '₹7,000/month', 1, 1, '110 sqft', '2019', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Study Room, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sunrisehostel.com', '+91 9876500014', NULL, NULL, 'hostel_sunrise_chennai.jpg', NULL, 49, 'Active', NULL, '2026-08-31 09:00:00', '2026-08-31 09:00:00'),
(78, 'Campus Co-ed Hostel - Vidyaranyapura', 10609, NULL, 'hostel', 'campus-co-ed-hostel-vidyaranyapura', NULL, NULL, NULL, 'Vidyaranyapura, Bangalore', 'Bangalore', '₹8,200/month', 1, 1, '130 sqft', '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Study Room, Common Lounge, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@campushostel.com', '+91 9876500015', NULL, NULL, 'hostel_campus_bangalore.jpg', NULL, 50, 'Active', NULL, '2026-08-31 09:00:00', '2026-08-31 09:00:00'),
(79, 'Studio Verte Architects', 10161, NULL, 'vendor', 'studio-verte-architects-vnd', NULL, NULL, NULL, 'Baner, Pune, Maharashtra', 'Pune', '₹25,000 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Site Visits, 3D Design, Vastu Compliant Plans', NULL, NULL, NULL, NULL, NULL, NULL, 'contact@studioverte.com', '+91 9800000001', NULL, NULL, 'main_79_1788178579.jpg', NULL, 51, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 12:16:19'),
(80, 'BuildRight Civil Contractors', 10176, NULL, 'vendor', 'buildright-civil-contractors-vnd', NULL, NULL, NULL, 'Whitefield, Bangalore, Karnataka', 'Bangalore', '₹180/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Turnkey Construction, RCC Work, Labour Included', NULL, NULL, NULL, NULL, NULL, NULL, 'info@buildright.com', '+91 9800000002', NULL, NULL, 'vendor_civil_bangalore.jpg', NULL, 52, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(81, 'Elegant Spaces Interior Designers', 10163, NULL, 'vendor', 'elegant-spaces-interior-designers', NULL, NULL, NULL, 'Andheri West, Mumbai, Maharashtra', 'Mumbai', '₹1,200/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Home Interiors, Modular Kitchen, 3D Walkthrough', NULL, NULL, NULL, NULL, NULL, NULL, 'info@elegantspaces.com', '+91 9800000008', NULL, NULL, 'vendor_interior_mumbai.jpg', NULL, 53, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(82, 'Spark Plumbing Contractors', 10193, NULL, 'vendor', 'spark-plumbing-contractors', NULL, NULL, NULL, 'Kondapur, Hyderabad, Telangana', 'Hyderabad', '₹500 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pipe Fitting, Bathroom Plumbing, Leak Repairs', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sparkplumbing.com', '+91 9800000009', NULL, NULL, 'vendor_plumbing_hyderabad.jpg', NULL, 54, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(83, 'ColorTech Painting Contractors', 10205, NULL, 'vendor', 'colortech-painting-contractors', NULL, NULL, NULL, 'Salt Lake, Kolkata, West Bengal', 'Kolkata', '₹18/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Interior & Exterior Painting, Waterproofing', NULL, NULL, NULL, NULL, NULL, NULL, 'info@colortech.com', '+91 9800000010', NULL, NULL, 'vendor_painting_kolkata.jpg', NULL, 55, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(84, 'BuildMart RMC Suppliers', 10226, NULL, 'vendor', 'buildmart-rmc-suppliers', NULL, NULL, NULL, 'Ambattur, Chennai, Tamil Nadu', 'Chennai', '₹4,200/cu.m onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ready Mix Concrete, Bulk Supply, Fast Delivery', NULL, NULL, NULL, NULL, NULL, NULL, 'info@buildmart.com', '+91 9800000011', NULL, NULL, 'vendor_rmc_chennai.jpg', NULL, 56, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(85, 'QuickClean House Cleaning', 10253, NULL, 'local_services', 'quickclean-house-cleaning-lsv', NULL, NULL, NULL, 'Aundh, Pune, Maharashtra', 'Pune', '₹800/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Same-day Booking, Trained Staff, Eco Products', NULL, NULL, NULL, NULL, NULL, NULL, 'info@quickclean.com', '+91 9800000012', NULL, NULL, 'lsv_cleaning_pune.jpg', NULL, 57, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(86, 'FixIt Electrician Service', 10271, NULL, 'local_services', 'fixit-electrician-service-lsv', NULL, NULL, NULL, 'Koramangala, Bangalore, Karnataka', 'Bangalore', '₹300/visit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '24x7 Callouts, Wiring, Fan & Switch Repair', NULL, NULL, NULL, NULL, NULL, NULL, 'info@fixitelectric.com', '+91 9800000013', NULL, NULL, 'lsv_electrician_bangalore.jpg', NULL, 58, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(87, 'SafeGuard Pest Control', 10259, NULL, 'local_services', 'safeguard-pest-control-lsv', NULL, NULL, NULL, 'T Nagar, Chennai, Tamil Nadu', 'Chennai', '₹1,000/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Termite Control, Cockroach & Rodent Treatment', NULL, NULL, NULL, NULL, NULL, NULL, 'info@safeguardpest.com', '+91 9800000014', NULL, NULL, 'lsv_pest_chennai.jpg', NULL, 59, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(88, 'QuickShift Packers & Movers', 10312, NULL, 'local_services', 'quickshift-packers-movers-lsv', NULL, NULL, NULL, 'Jayanagar, Bangalore, Karnataka', 'Bangalore', '₹3,500/shift', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Local Shifting, Insured Handling, Free Quote', NULL, NULL, NULL, NULL, NULL, NULL, 'info@quickshift.com', '+91 9800000015', NULL, NULL, 'lsv_packers_bangalore.jpg', NULL, 60, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(89, 'HomeCare Maid Service', 10303, NULL, 'local_services', 'homecare-maid-service-lsv', NULL, NULL, NULL, 'Rohini, Delhi', 'Delhi', '₹6,000/month', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Verified Maids, Flexible Hours, Background Checked', NULL, NULL, NULL, NULL, NULL, NULL, 'info@homecaremaid.com', '+91 9800000016', NULL, NULL, 'lsv_maid_delhi.jpg', NULL, 61, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(90, 'GlamHome Salon at Home', 10309, NULL, 'local_services', 'glamhome-salon-at-home-lsv', NULL, NULL, NULL, 'Banjara Hills, Hyderabad, Telangana', 'Hyderabad', '₹800/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Haircut, Facial, Waxing, Certified Beauticians', NULL, NULL, NULL, NULL, NULL, NULL, 'info@glamhome.com', '+91 9800000017', NULL, NULL, 'lsv_salon_hyderabad.jpg', NULL, 62, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(91, 'Comfort Inn Budget Stay', 10549, NULL, 'hotel', 'comfort-inn-budget-stay', NULL, NULL, NULL, 'MG Road, Pune, Maharashtra', 'Pune', '₹2,200/night', 0, 0, NULL, '2016', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, AC, Restaurant, Parking, 24/7 Front Desk', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@comfortinn.com', '+91 9800000018', NULL, NULL, 'hhs_budget_pune.jpg', NULL, 63, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(92, 'Grand Regal 5 Star Hotel', 10553, NULL, 'hotel', 'grand-regal-5-star-hotel', NULL, NULL, NULL, 'Banjara Hills, Hyderabad, Telangana', 'Hyderabad', '₹18,000/night', 0, 0, NULL, '2010', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Swimming Pool, Spa, Gym, Fine Dining, WiFi, Valet', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@grandregal.com', '+91 9800000019', NULL, NULL, 'hhs_5star_hyderabad.jpg', NULL, 64, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(93, 'Emerald Hills Luxury Resort', 10563, NULL, 'hotel', 'emerald-hills-luxury-resort', NULL, NULL, NULL, 'Lonavala, Maharashtra', 'Lonavala', '₹14,500/night', 0, 0, NULL, '2017', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Valley View, Pool, Spa, Multi-cuisine Restaurant', NULL, NULL, NULL, NULL, NULL, NULL, 'info@emeraldhills.com', '+91 9800000020', NULL, NULL, 'hhs_resort_lonavala.jpg', NULL, 65, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(94, 'Sunrise Farmstay Homestay', 10574, NULL, 'homestay', 'sunrise-farmstay-homestay', NULL, NULL, NULL, 'Coorg, Karnataka', 'Coorg', '₹6,800/night', 2, 2, NULL, '2019', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Coffee Plantation, Organic Meals, WiFi, Trekking', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sunrisefarmstay.com', '+91 9800000021', NULL, NULL, 'hhs_homestay_coorg.jpg', NULL, 66, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(95, 'Backwater Bliss Houseboat', 10579, NULL, 'homestay', 'backwater-bliss-houseboat', NULL, NULL, NULL, 'Alleppey, Kerala', 'Alleppey', '₹9,500/night', 2, 2, NULL, '2015', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Houseboat, Meals Included, Sundeck, WiFi', NULL, NULL, NULL, NULL, NULL, NULL, 'info@backwaterbliss.com', '+91 9800000022', NULL, NULL, 'hhs_houseboat_alleppey.jpg', NULL, 67, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(96, 'Yatri Comfort Guest House', 10587, NULL, 'hotel', 'yatri-comfort-guest-house', NULL, NULL, NULL, 'Haridwar, Uttarakhand', 'Haridwar', '₹1,800/night', 0, 0, NULL, '2012', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'AC, Hot Water, Parking, Vegetarian Meals', NULL, NULL, NULL, NULL, NULL, NULL, 'info@yatricomfort.com', '+91 9800000023', NULL, NULL, 'hhs_guesthouse_haridwar.jpg', NULL, 68, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(97, 'Nestaway Boys PG', 10592, NULL, 'pg', 'nestaway-boys-pg-pgh', NULL, NULL, NULL, 'Electronic City, Bangalore, Karnataka', 'Bangalore', '₹9,800/month', 1, 1, NULL, '2021', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, AC, Housekeeping, 24/7 Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@nestaway.com', '+91 9800000024', NULL, NULL, 'pgh_boys_bangalore.jpg', NULL, 69, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(98, 'SafeStay Girls PG', 10593, NULL, 'pg', 'safestay-girls-pg-pgh', NULL, NULL, NULL, 'Kothrud, Pune, Maharashtra', 'Pune', '₹10,500/month', 1, 1, NULL, '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, CCTV, Warden on Premises, Laundry', NULL, NULL, NULL, NULL, NULL, NULL, 'info@safestaypg.com', '+91 9800000025', NULL, NULL, 'pgh_girls_pune.jpg', NULL, 70, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(99, 'Elite AC PG Residency', 10600, NULL, 'pg', 'elite-ac-pg-residency', NULL, NULL, NULL, 'Gachibowli, Hyderabad, Telangana', 'Hyderabad', '₹13,500/month', 1, 1, NULL, '2022', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'AC, WiFi, Meals, Gym, Co-working Space', NULL, NULL, NULL, NULL, NULL, NULL, 'info@elitepg.com', '+91 9800000026', NULL, NULL, 'pgh_ac_hyderabad.jpg', NULL, 71, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(100, 'Sunrise Boys Hostel', 10607, NULL, 'hostel', 'sunrise-boys-hostel-pgh', NULL, NULL, NULL, 'Anna Nagar, Chennai, Tamil Nadu', 'Chennai', '₹7,000/month', 1, 1, NULL, '2019', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Study Room, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sunrisehostel.com', '+91 9800000027', NULL, NULL, 'pgh_boys_chennai.jpg', NULL, 72, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(101, 'Campus Co-ed Hostel', 10609, NULL, 'hostel', 'campus-co-ed-hostel-pgh', NULL, NULL, NULL, 'Vidyaranyapura, Bangalore, Karnataka', 'Bangalore', '₹8,200/month', 1, 1, NULL, '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Study Room, Common Lounge, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@campushostel.com', '+91 9800000028', NULL, NULL, 'pgh_coed_bangalore.jpg', NULL, 73, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59');
INSERT INTO `trending_projects` (`id`, `project_name`, `category_id`, `listing_type_id`, `category_type`, `slug`, `rera_number`, `approvals`, `min_sft`, `address`, `city`, `price`, `bedrooms`, `bathrooms`, `area`, `year_built`, `furnished`, `plot_area`, `towers`, `floors`, `units`, `total_project_area`, `open_area`, `amenities`, `about_project`, `phases_json`, `spec_flooring`, `spec_bathrooms`, `spec_kitchen`, `spec_doors_windows`, `owner_email`, `owner_phone`, `company_name`, `appointment_date`, `image`, `brochure`, `project_order`, `status`, `package_id`, `created_at`, `updated_at`) VALUES
(102, 'Metro Dormitory Stay', 10614, NULL, 'hostel', 'metro-dormitory-stay-pgh', NULL, NULL, NULL, 'Rajouri Garden, Delhi', 'Delhi', '₹5,500/month', 1, 1, NULL, '2018', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Bunk Beds, Common Kitchen, Laundry', NULL, NULL, NULL, NULL, NULL, NULL, 'info@metrodorm.com', '+91 9800000029', NULL, NULL, 'pgh_dorm_delhi.jpg', NULL, 74, 'Active', NULL, '2026-08-31 09:07:59', '2026-08-31 09:07:59'),
(103, 'Studio Verte Architects', 10161, NULL, 'vendor', 'studio-verte-architects-vnd', NULL, NULL, NULL, 'Baner, Pune, Maharashtra', 'Pune', '₹25,000 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Site Visits, 3D Design, Vastu Compliant Plans', NULL, NULL, NULL, NULL, NULL, NULL, 'contact@studioverte.com', '+91 9800000001', NULL, NULL, 'vendor_architect_pune.jpg', NULL, 51, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(104, 'BuildRight Civil Contractors', 10176, NULL, 'vendor', 'buildright-civil-contractors-vnd', NULL, NULL, NULL, 'Whitefield, Bangalore, Karnataka', 'Bangalore', '₹180/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Turnkey Construction, RCC Work, Labour Included', NULL, NULL, NULL, NULL, NULL, NULL, 'info@buildright.com', '+91 9800000002', NULL, NULL, 'vendor_civil_bangalore.jpg', NULL, 52, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(105, 'Elegant Spaces Interior Designers', 10163, NULL, 'vendor', 'elegant-spaces-interior-designers', NULL, NULL, NULL, 'Andheri West, Mumbai, Maharashtra', 'Mumbai', '₹1,200/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Home Interiors, Modular Kitchen, 3D Walkthrough', NULL, NULL, NULL, NULL, NULL, NULL, 'info@elegantspaces.com', '+91 9800000008', NULL, NULL, 'vendor_interior_mumbai.jpg', NULL, 53, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(106, 'Spark Plumbing Contractors', 10193, NULL, 'vendor', 'spark-plumbing-contractors', NULL, NULL, NULL, 'Kondapur, Hyderabad, Telangana', 'Hyderabad', '₹500 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pipe Fitting, Bathroom Plumbing, Leak Repairs', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sparkplumbing.com', '+91 9800000009', NULL, NULL, 'vendor_plumbing_hyderabad.jpg', NULL, 54, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(107, 'ColorTech Painting Contractors', 10205, NULL, 'vendor', 'colortech-painting-contractors', NULL, NULL, NULL, 'Salt Lake, Kolkata, West Bengal', 'Kolkata', '₹18/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Interior & Exterior Painting, Waterproofing', NULL, NULL, NULL, NULL, NULL, NULL, 'info@colortech.com', '+91 9800000010', NULL, NULL, 'vendor_painting_kolkata.jpg', NULL, 55, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(108, 'BuildMart RMC Suppliers', 10226, NULL, 'vendor', 'buildmart-rmc-suppliers', NULL, NULL, NULL, 'Ambattur, Chennai, Tamil Nadu', 'Chennai', '₹4,200/cu.m onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ready Mix Concrete, Bulk Supply, Fast Delivery', NULL, NULL, NULL, NULL, NULL, NULL, 'info@buildmart.com', '+91 9800000011', NULL, NULL, 'vendor_rmc_chennai.jpg', NULL, 56, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(109, 'QuickClean House Cleaning', 10253, NULL, 'local_services', 'quickclean-house-cleaning-lsv', NULL, NULL, NULL, 'Aundh, Pune, Maharashtra', 'Pune', '₹800/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Same-day Booking, Trained Staff, Eco Products', NULL, NULL, NULL, NULL, NULL, NULL, 'info@quickclean.com', '+91 9800000012', NULL, NULL, 'lsv_cleaning_pune.jpg', NULL, 57, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(110, 'FixIt Electrician Service', 10271, NULL, 'local_services', 'fixit-electrician-service-lsv', NULL, NULL, NULL, 'Koramangala, Bangalore, Karnataka', 'Bangalore', '₹300/visit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '24x7 Callouts, Wiring, Fan & Switch Repair', NULL, NULL, NULL, NULL, NULL, NULL, 'info@fixitelectric.com', '+91 9800000013', NULL, NULL, 'lsv_electrician_bangalore.jpg', NULL, 58, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(111, 'SafeGuard Pest Control', 10259, NULL, 'local_services', 'safeguard-pest-control-lsv', NULL, NULL, NULL, 'T Nagar, Chennai, Tamil Nadu', 'Chennai', '₹1,000/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Termite Control, Cockroach & Rodent Treatment', NULL, NULL, NULL, NULL, NULL, NULL, 'info@safeguardpest.com', '+91 9800000014', NULL, NULL, 'lsv_pest_chennai.jpg', NULL, 59, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(112, 'QuickShift Packers & Movers', 10312, NULL, 'local_services', 'quickshift-packers-movers-lsv', NULL, NULL, NULL, 'Jayanagar, Bangalore, Karnataka', 'Bangalore', '₹3,500/shift', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Local Shifting, Insured Handling, Free Quote', NULL, NULL, NULL, NULL, NULL, NULL, 'info@quickshift.com', '+91 9800000015', NULL, NULL, 'lsv_packers_bangalore.jpg', NULL, 60, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(113, 'HomeCare Maid Service', 10303, NULL, 'local_services', 'homecare-maid-service-lsv', NULL, NULL, NULL, 'Rohini, Delhi', 'Delhi', '₹6,000/month', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Verified Maids, Flexible Hours, Background Checked', NULL, NULL, NULL, NULL, NULL, NULL, 'info@homecaremaid.com', '+91 9800000016', NULL, NULL, 'lsv_maid_delhi.jpg', NULL, 61, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(114, 'GlamHome Salon at Home', 10309, NULL, 'local_services', 'glamhome-salon-at-home-lsv', NULL, NULL, NULL, 'Banjara Hills, Hyderabad, Telangana', 'Hyderabad', '₹800/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Haircut, Facial, Waxing, Certified Beauticians', NULL, NULL, NULL, NULL, NULL, NULL, 'info@glamhome.com', '+91 9800000017', NULL, NULL, 'lsv_salon_hyderabad.jpg', NULL, 62, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(115, 'Comfort Inn Budget Stay', 10549, NULL, 'hotel', 'comfort-inn-budget-stay', NULL, NULL, NULL, 'MG Road, Pune, Maharashtra', 'Pune', '₹2,200/night', 0, 0, NULL, '2016', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, AC, Restaurant, Parking, 24/7 Front Desk', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@comfortinn.com', '+91 9800000018', NULL, NULL, 'hhs_budget_pune.jpg', NULL, 63, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(116, 'Grand Regal 5 Star Hotel', 10553, NULL, 'hotel', 'grand-regal-5-star-hotel', NULL, NULL, NULL, 'Banjara Hills, Hyderabad, Telangana', 'Hyderabad', '₹18,000/night', 0, 0, NULL, '2010', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Swimming Pool, Spa, Gym, Fine Dining, WiFi, Valet', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@grandregal.com', '+91 9800000019', NULL, NULL, 'hhs_5star_hyderabad.jpg', NULL, 64, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(117, 'Emerald Hills Luxury Resort', 10563, NULL, 'hotel', 'emerald-hills-luxury-resort', NULL, NULL, NULL, 'Lonavala, Maharashtra', 'Lonavala', '₹14,500/night', 0, 0, NULL, '2017', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Valley View, Pool, Spa, Multi-cuisine Restaurant', NULL, NULL, NULL, NULL, NULL, NULL, 'info@emeraldhills.com', '+91 9800000020', NULL, NULL, 'hhs_resort_lonavala.jpg', NULL, 65, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(118, 'Sunrise Farmstay Homestay', 10574, NULL, 'homestay', 'sunrise-farmstay-homestay', NULL, NULL, NULL, 'Coorg, Karnataka', 'Coorg', '₹6,800/night', 2, 2, NULL, '2019', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Coffee Plantation, Organic Meals, WiFi, Trekking', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sunrisefarmstay.com', '+91 9800000021', NULL, NULL, 'hhs_homestay_coorg.jpg', NULL, 66, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(119, 'Backwater Bliss Houseboat', 10579, NULL, 'homestay', 'backwater-bliss-houseboat', NULL, NULL, NULL, 'Alleppey, Kerala', 'Alleppey', '₹9,500/night', 2, 2, NULL, '2015', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Houseboat, Meals Included, Sundeck, WiFi', NULL, NULL, NULL, NULL, NULL, NULL, 'info@backwaterbliss.com', '+91 9800000022', NULL, NULL, 'hhs_houseboat_alleppey.jpg', NULL, 67, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(120, 'Yatri Comfort Guest House', 10587, NULL, 'hotel', 'yatri-comfort-guest-house', NULL, NULL, NULL, 'Haridwar, Uttarakhand', 'Haridwar', '₹1,800/night', 0, 0, NULL, '2012', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'AC, Hot Water, Parking, Vegetarian Meals', NULL, NULL, NULL, NULL, NULL, NULL, 'info@yatricomfort.com', '+91 9800000023', NULL, NULL, 'hhs_guesthouse_haridwar.jpg', NULL, 68, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(121, 'Nestaway Boys PG', 10592, NULL, 'pg', 'nestaway-boys-pg-pgh', NULL, NULL, NULL, 'Electronic City, Bangalore, Karnataka', 'Bangalore', '₹9,800/month', 1, 1, NULL, '2021', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, AC, Housekeeping, 24/7 Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@nestaway.com', '+91 9800000024', NULL, NULL, 'pgh_boys_bangalore.jpg', NULL, 69, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(122, 'SafeStay Girls PG', 10593, NULL, 'pg', 'safestay-girls-pg-pgh', NULL, NULL, NULL, 'Kothrud, Pune, Maharashtra', 'Pune', '₹10,500/month', 1, 1, NULL, '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, CCTV, Warden on Premises, Laundry', NULL, NULL, NULL, NULL, NULL, NULL, 'info@safestaypg.com', '+91 9800000025', NULL, NULL, 'pgh_girls_pune.jpg', NULL, 70, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(123, 'Elite AC PG Residency', 10600, NULL, 'pg', 'elite-ac-pg-residency', NULL, NULL, NULL, 'Gachibowli, Hyderabad, Telangana', 'Hyderabad', '₹13,500/month', 1, 1, NULL, '2022', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'AC, WiFi, Meals, Gym, Co-working Space', NULL, NULL, NULL, NULL, NULL, NULL, 'info@elitepg.com', '+91 9800000026', NULL, NULL, 'pgh_ac_hyderabad.jpg', NULL, 71, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(124, 'Sunrise Boys Hostel', 10607, NULL, 'hostel', 'sunrise-boys-hostel-pgh', NULL, NULL, NULL, 'Anna Nagar, Chennai, Tamil Nadu', 'Chennai', '₹7,000/month', 1, 1, NULL, '2019', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Study Room, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sunrisehostel.com', '+91 9800000027', NULL, NULL, 'pgh_boys_chennai.jpg', NULL, 72, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(125, 'Campus Co-ed Hostel', 10609, NULL, 'hostel', 'campus-co-ed-hostel-pgh', NULL, NULL, NULL, 'Vidyaranyapura, Bangalore, Karnataka', 'Bangalore', '₹8,200/month', 1, 1, NULL, '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Study Room, Common Lounge, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@campushostel.com', '+91 9800000028', NULL, NULL, 'pgh_coed_bangalore.jpg', NULL, 73, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(126, 'Metro Dormitory Stay', 10614, NULL, 'hostel', 'metro-dormitory-stay-pgh', NULL, NULL, NULL, 'Rajouri Garden, Delhi', 'Delhi', '₹5,500/month', 1, 1, NULL, '2018', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Bunk Beds, Common Kitchen, Laundry', NULL, NULL, NULL, NULL, NULL, NULL, 'info@metrodorm.com', '+91 9800000029', NULL, NULL, 'pgh_dorm_delhi.jpg', NULL, 74, 'Active', NULL, '2026-08-31 09:08:10', '2026-08-31 09:08:10'),
(127, 'Studio Verte Architects', 10161, NULL, 'vendor', 'studio-verte-architects-vnd', NULL, NULL, NULL, 'Baner, Pune, Maharashtra', 'Pune', '₹25,000 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Site Visits, 3D Design, Vastu Compliant Plans', NULL, NULL, NULL, NULL, NULL, NULL, 'contact@studioverte.com', '+91 9800000001', NULL, NULL, 'vendor_architect_pune.jpg', NULL, 51, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(128, 'BuildRight Civil Contractors', 10176, NULL, 'vendor', 'buildright-civil-contractors-vnd', NULL, NULL, NULL, 'Whitefield, Bangalore, Karnataka', 'Bangalore', '₹180/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Turnkey Construction, RCC Work, Labour Included', NULL, NULL, NULL, NULL, NULL, NULL, 'info@buildright.com', '+91 9800000002', NULL, NULL, 'vendor_civil_bangalore.jpg', NULL, 52, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(129, 'Elegant Spaces Interior Designers', 10163, NULL, 'vendor', 'elegant-spaces-interior-designers', NULL, NULL, NULL, 'Andheri West, Mumbai, Maharashtra', 'Mumbai', '₹1,200/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Home Interiors, Modular Kitchen, 3D Walkthrough', NULL, NULL, NULL, NULL, NULL, NULL, 'info@elegantspaces.com', '+91 9800000008', NULL, NULL, 'vendor_interior_mumbai.jpg', NULL, 53, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(130, 'Spark Plumbing Contractors', 10193, NULL, 'vendor', 'spark-plumbing-contractors', NULL, NULL, NULL, 'Kondapur, Hyderabad, Telangana', 'Hyderabad', '₹500 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pipe Fitting, Bathroom Plumbing, Leak Repairs', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sparkplumbing.com', '+91 9800000009', NULL, NULL, 'vendor_plumbing_hyderabad.jpg', NULL, 54, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(131, 'ColorTech Painting Contractors', 10205, NULL, 'vendor', 'colortech-painting-contractors', NULL, NULL, NULL, 'Salt Lake, Kolkata, West Bengal', 'Kolkata', '₹18/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Interior & Exterior Painting, Waterproofing', NULL, NULL, NULL, NULL, NULL, NULL, 'info@colortech.com', '+91 9800000010', NULL, NULL, 'vendor_painting_kolkata.jpg', NULL, 55, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(132, 'BuildMart RMC Suppliers', 10226, NULL, 'vendor', 'buildmart-rmc-suppliers', NULL, NULL, NULL, 'Ambattur, Chennai, Tamil Nadu', 'Chennai', '₹4,200/cu.m onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ready Mix Concrete, Bulk Supply, Fast Delivery', NULL, NULL, NULL, NULL, NULL, NULL, 'info@buildmart.com', '+91 9800000011', NULL, NULL, 'vendor_rmc_chennai.jpg', NULL, 56, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(133, 'QuickClean House Cleaning', 10253, NULL, 'local_services', 'quickclean-house-cleaning-lsv', NULL, NULL, NULL, 'Aundh, Pune, Maharashtra', 'Pune', '₹800/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Same-day Booking, Trained Staff, Eco Products', NULL, NULL, NULL, NULL, NULL, NULL, 'info@quickclean.com', '+91 9800000012', NULL, NULL, 'lsv_cleaning_pune.jpg', NULL, 57, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(134, 'FixIt Electrician Service', 10271, NULL, 'local_services', 'fixit-electrician-service-lsv', NULL, NULL, NULL, 'Koramangala, Bangalore, Karnataka', 'Bangalore', '₹300/visit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '24x7 Callouts, Wiring, Fan & Switch Repair', NULL, NULL, NULL, NULL, NULL, NULL, 'info@fixitelectric.com', '+91 9800000013', NULL, NULL, 'lsv_electrician_bangalore.jpg', NULL, 58, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(135, 'SafeGuard Pest Control', 10259, NULL, 'local_services', 'safeguard-pest-control-lsv', NULL, NULL, NULL, 'T Nagar, Chennai, Tamil Nadu', 'Chennai', '₹1,000/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Termite Control, Cockroach & Rodent Treatment', NULL, NULL, NULL, NULL, NULL, NULL, 'info@safeguardpest.com', '+91 9800000014', NULL, NULL, 'lsv_pest_chennai.jpg', NULL, 59, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(136, 'QuickShift Packers & Movers', 10312, NULL, 'local_services', 'quickshift-packers-movers-lsv', NULL, NULL, NULL, 'Jayanagar, Bangalore, Karnataka', 'Bangalore', '₹3,500/shift', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Local Shifting, Insured Handling, Free Quote', NULL, NULL, NULL, NULL, NULL, NULL, 'info@quickshift.com', '+91 9800000015', NULL, NULL, 'lsv_packers_bangalore.jpg', NULL, 60, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(137, 'HomeCare Maid Service', 10303, NULL, 'local_services', 'homecare-maid-service-lsv', NULL, NULL, NULL, 'Rohini, Delhi', 'Delhi', '₹6,000/month', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Verified Maids, Flexible Hours, Background Checked', NULL, NULL, NULL, NULL, NULL, NULL, 'info@homecaremaid.com', '+91 9800000016', NULL, NULL, 'lsv_maid_delhi.jpg', NULL, 61, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(138, 'GlamHome Salon at Home', 10309, NULL, 'local_services', 'glamhome-salon-at-home-lsv', NULL, NULL, NULL, 'Banjara Hills, Hyderabad, Telangana', 'Hyderabad', '₹800/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Haircut, Facial, Waxing, Certified Beauticians', NULL, NULL, NULL, NULL, NULL, NULL, 'info@glamhome.com', '+91 9800000017', NULL, NULL, 'lsv_salon_hyderabad.jpg', NULL, 62, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(139, 'Comfort Inn Budget Stay', 10549, NULL, 'hotel', 'comfort-inn-budget-stay', NULL, NULL, NULL, 'MG Road, Pune, Maharashtra', 'Pune', '₹2,200/night', 0, 0, NULL, '2016', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, AC, Restaurant, Parking, 24/7 Front Desk', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@comfortinn.com', '+91 9800000018', NULL, NULL, 'hhs_budget_pune.jpg', NULL, 63, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(140, 'Grand Regal 5 Star Hotel', 10553, NULL, 'hotel', 'grand-regal-5-star-hotel', NULL, NULL, NULL, 'Banjara Hills, Hyderabad, Telangana', 'Hyderabad', '₹18,000/night', 0, 0, NULL, '2010', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Swimming Pool, Spa, Gym, Fine Dining, WiFi, Valet', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@grandregal.com', '+91 9800000019', NULL, NULL, 'hhs_5star_hyderabad.jpg', NULL, 64, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(141, 'Emerald Hills Luxury Resort', 10563, NULL, 'hotel', 'emerald-hills-luxury-resort', NULL, NULL, NULL, 'Lonavala, Maharashtra', 'Lonavala', '₹14,500/night', 0, 0, NULL, '2017', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Valley View, Pool, Spa, Multi-cuisine Restaurant', NULL, NULL, NULL, NULL, NULL, NULL, 'info@emeraldhills.com', '+91 9800000020', NULL, NULL, 'hhs_resort_lonavala.jpg', NULL, 65, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(142, 'Sunrise Farmstay Homestay', 10574, NULL, 'homestay', 'sunrise-farmstay-homestay', NULL, NULL, NULL, 'Coorg, Karnataka', 'Coorg', '₹6,800/night', 2, 2, NULL, '2019', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Coffee Plantation, Organic Meals, WiFi, Trekking', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sunrisefarmstay.com', '+91 9800000021', NULL, NULL, 'hhs_homestay_coorg.jpg', NULL, 66, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(143, 'Backwater Bliss Houseboat', 10579, NULL, 'homestay', 'backwater-bliss-houseboat', NULL, NULL, NULL, 'Alleppey, Kerala', 'Alleppey', '₹9,500/night', 2, 2, NULL, '2015', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Houseboat, Meals Included, Sundeck, WiFi', NULL, NULL, NULL, NULL, NULL, NULL, 'info@backwaterbliss.com', '+91 9800000022', NULL, NULL, 'hhs_houseboat_alleppey.jpg', NULL, 67, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(144, 'Yatri Comfort Guest House', 10587, NULL, 'hotel', 'yatri-comfort-guest-house', NULL, NULL, NULL, 'Haridwar, Uttarakhand', 'Haridwar', '₹1,800/night', 0, 0, NULL, '2012', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'AC, Hot Water, Parking, Vegetarian Meals', NULL, NULL, NULL, NULL, NULL, NULL, 'info@yatricomfort.com', '+91 9800000023', NULL, NULL, 'hhs_guesthouse_haridwar.jpg', NULL, 68, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(145, 'Nestaway Boys PG', 10592, NULL, 'pg', 'nestaway-boys-pg-pgh', NULL, NULL, NULL, 'Electronic City, Bangalore, Karnataka', 'Bangalore', '₹9,800/month', 1, 1, NULL, '2021', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, AC, Housekeeping, 24/7 Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@nestaway.com', '+91 9800000024', NULL, NULL, 'pgh_boys_bangalore.jpg', NULL, 69, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(146, 'SafeStay Girls PG', 10593, NULL, 'pg', 'safestay-girls-pg-pgh', NULL, NULL, NULL, 'Kothrud, Pune, Maharashtra', 'Pune', '₹10,500/month', 1, 1, NULL, '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, CCTV, Warden on Premises, Laundry', NULL, NULL, NULL, NULL, NULL, NULL, 'info@safestaypg.com', '+91 9800000025', NULL, NULL, 'pgh_girls_pune.jpg', NULL, 70, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(147, 'Elite AC PG Residency', 10600, NULL, 'pg', 'elite-ac-pg-residency', NULL, NULL, NULL, 'Gachibowli, Hyderabad, Telangana', 'Hyderabad', '₹13,500/month', 1, 1, NULL, '2022', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'AC, WiFi, Meals, Gym, Co-working Space', NULL, NULL, NULL, NULL, NULL, NULL, 'info@elitepg.com', '+91 9800000026', NULL, NULL, 'pgh_ac_hyderabad.jpg', NULL, 71, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(148, 'Sunrise Boys Hostel', 10607, NULL, 'hostel', 'sunrise-boys-hostel-pgh', NULL, NULL, NULL, 'Anna Nagar, Chennai, Tamil Nadu', 'Chennai', '₹7,000/month', 1, 1, NULL, '2019', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Study Room, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sunrisehostel.com', '+91 9800000027', NULL, NULL, 'pgh_boys_chennai.jpg', NULL, 72, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(149, 'Campus Co-ed Hostel', 10609, NULL, 'hostel', 'campus-co-ed-hostel-pgh', NULL, NULL, NULL, 'Vidyaranyapura, Bangalore, Karnataka', 'Bangalore', '₹8,200/month', 1, 1, NULL, '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Study Room, Common Lounge, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@campushostel.com', '+91 9800000028', NULL, NULL, 'pgh_coed_bangalore.jpg', NULL, 73, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(150, 'Metro Dormitory Stay', 10614, NULL, 'hostel', 'metro-dormitory-stay-pgh', NULL, NULL, NULL, 'Rajouri Garden, Delhi', 'Delhi', '₹5,500/month', 1, 1, NULL, '2018', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Bunk Beds, Common Kitchen, Laundry', NULL, NULL, NULL, NULL, NULL, NULL, 'info@metrodorm.com', '+91 9800000029', NULL, NULL, 'pgh_dorm_delhi.jpg', NULL, 74, 'Active', NULL, '2026-08-31 09:08:50', '2026-08-31 09:08:50'),
(151, 'Studio Verte Architects', 10161, NULL, 'vendor', 'studio-verte-architects-vnd', NULL, NULL, NULL, 'Baner, Pune, Maharashtra', 'Pune', '₹25,000 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Site Visits, 3D Design, Vastu Compliant Plans', NULL, NULL, NULL, NULL, NULL, NULL, 'contact@studioverte.com', '+91 9800000001', NULL, NULL, 'vendor_architect_pune.jpg', NULL, 51, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(152, 'BuildRight Civil Contractors', 10176, NULL, 'vendor', 'buildright-civil-contractors-vnd', NULL, NULL, NULL, 'Whitefield, Bangalore, Karnataka', 'Bangalore', '₹180/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Turnkey Construction, RCC Work, Labour Included', NULL, NULL, NULL, NULL, NULL, NULL, 'info@buildright.com', '+91 9800000002', NULL, NULL, 'vendor_civil_bangalore.jpg', NULL, 52, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(153, 'Elegant Spaces Interior Designers', 10163, NULL, 'vendor', 'elegant-spaces-interior-designers', NULL, NULL, NULL, 'Andheri West, Mumbai, Maharashtra', 'Mumbai', '₹1,200/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Full Home Interiors, Modular Kitchen, 3D Walkthrough', NULL, NULL, NULL, NULL, NULL, NULL, 'info@elegantspaces.com', '+91 9800000008', NULL, NULL, 'vendor_interior_mumbai.jpg', NULL, 53, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(154, 'Spark Plumbing Contractors', 10193, NULL, 'vendor', 'spark-plumbing-contractors', NULL, NULL, NULL, 'Kondapur, Hyderabad, Telangana', 'Hyderabad', '₹500 onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pipe Fitting, Bathroom Plumbing, Leak Repairs', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sparkplumbing.com', '+91 9800000009', NULL, NULL, 'vendor_plumbing_hyderabad.jpg', NULL, 54, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(155, 'ColorTech Painting Contractors', 10205, NULL, 'vendor', 'colortech-painting-contractors', NULL, NULL, NULL, 'Salt Lake, Kolkata, West Bengal', 'Kolkata', '₹18/sqft onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Interior & Exterior Painting, Waterproofing', NULL, NULL, NULL, NULL, NULL, NULL, 'info@colortech.com', '+91 9800000010', NULL, NULL, 'vendor_painting_kolkata.jpg', NULL, 55, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(156, 'BuildMart RMC Suppliers', 10226, NULL, 'vendor', 'buildmart-rmc-suppliers', NULL, NULL, NULL, 'Ambattur, Chennai, Tamil Nadu', 'Chennai', '₹4,200/cu.m onwards', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ready Mix Concrete, Bulk Supply, Fast Delivery', NULL, NULL, NULL, NULL, NULL, NULL, 'info@buildmart.com', '+91 9800000011', NULL, NULL, 'vendor_rmc_chennai.jpg', NULL, 56, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(157, 'QuickClean House Cleaning', 10253, NULL, 'local_services', 'quickclean-house-cleaning-lsv', NULL, NULL, NULL, 'Aundh, Pune, Maharashtra', 'Pune', '₹800/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Same-day Booking, Trained Staff, Eco Products', NULL, NULL, NULL, NULL, NULL, NULL, 'info@quickclean.com', '+91 9800000012', NULL, NULL, 'lsv_cleaning_pune.jpg', NULL, 57, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(158, 'FixIt Electrician Service', 10271, NULL, 'local_services', 'fixit-electrician-service-lsv', NULL, NULL, NULL, 'Koramangala, Bangalore, Karnataka', 'Bangalore', '₹300/visit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '24x7 Callouts, Wiring, Fan & Switch Repair', NULL, NULL, NULL, NULL, NULL, NULL, 'info@fixitelectric.com', '+91 9800000013', NULL, NULL, 'lsv_electrician_bangalore.jpg', NULL, 58, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(159, 'SafeGuard Pest Control', 10259, NULL, 'local_services', 'safeguard-pest-control-lsv', NULL, NULL, NULL, 'T Nagar, Chennai, Tamil Nadu', 'Chennai', '₹1,000/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Termite Control, Cockroach & Rodent Treatment', NULL, NULL, NULL, NULL, NULL, NULL, 'info@safeguardpest.com', '+91 9800000014', NULL, NULL, 'lsv_pest_chennai.jpg', NULL, 59, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(160, 'QuickShift Packers & Movers', 10312, NULL, 'local_services', 'quickshift-packers-movers-lsv', NULL, NULL, NULL, 'Jayanagar, Bangalore, Karnataka', 'Bangalore', '₹3,500/shift', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Local Shifting, Insured Handling, Free Quote', NULL, NULL, NULL, NULL, NULL, NULL, 'info@quickshift.com', '+91 9800000015', NULL, NULL, 'lsv_packers_bangalore.jpg', NULL, 60, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(161, 'HomeCare Maid Service', 10303, NULL, 'local_services', 'homecare-maid-service-lsv', NULL, NULL, NULL, 'Rohini, Delhi', 'Delhi', '₹6,000/month', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Verified Maids, Flexible Hours, Background Checked', NULL, NULL, NULL, NULL, NULL, NULL, 'info@homecaremaid.com', '+91 9800000016', NULL, NULL, 'lsv_maid_delhi.jpg', NULL, 61, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(162, 'GlamHome Salon at Home', 10309, NULL, 'local_services', 'glamhome-salon-at-home-lsv', NULL, NULL, NULL, 'Banjara Hills, Hyderabad, Telangana', 'Hyderabad', '₹800/service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Haircut, Facial, Waxing, Certified Beauticians', NULL, NULL, NULL, NULL, NULL, NULL, 'info@glamhome.com', '+91 9800000017', NULL, NULL, 'lsv_salon_hyderabad.jpg', NULL, 62, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(163, 'Comfort Inn Budget Stay', 10549, NULL, 'hotel', 'comfort-inn-budget-stay', NULL, NULL, NULL, 'MG Road, Pune, Maharashtra', 'Pune', '₹2,200/night', 0, 0, NULL, '2016', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, AC, Restaurant, Parking, 24/7 Front Desk', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@comfortinn.com', '+91 9800000018', NULL, NULL, 'hhs_budget_pune.jpg', NULL, 63, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(164, 'Grand Regal 5 Star Hotel', 10553, NULL, 'hotel', 'grand-regal-5-star-hotel', NULL, NULL, NULL, 'Banjara Hills, Hyderabad, Telangana', 'Hyderabad', '₹18,000/night', 0, 0, NULL, '2010', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Swimming Pool, Spa, Gym, Fine Dining, WiFi, Valet', NULL, NULL, NULL, NULL, NULL, NULL, 'reservations@grandregal.com', '+91 9800000019', NULL, NULL, 'hhs_5star_hyderabad.jpg', NULL, 64, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(165, 'Emerald Hills Luxury Resort', 10563, NULL, 'hotel', 'emerald-hills-luxury-resort', NULL, NULL, NULL, 'Lonavala, Maharashtra', 'Lonavala', '₹14,500/night', 0, 0, NULL, '2017', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Valley View, Pool, Spa, Multi-cuisine Restaurant', NULL, NULL, NULL, NULL, NULL, NULL, 'info@emeraldhills.com', '+91 9800000020', NULL, NULL, 'hhs_resort_lonavala.jpg', NULL, 65, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(166, 'Sunrise Farmstay Homestay', 10574, NULL, 'homestay', 'sunrise-farmstay-homestay', NULL, NULL, NULL, 'Coorg, Karnataka', 'Coorg', '₹6,800/night', 2, 2, NULL, '2019', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Coffee Plantation, Organic Meals, WiFi, Trekking', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sunrisefarmstay.com', '+91 9800000021', NULL, NULL, 'hhs_homestay_coorg.jpg', NULL, 66, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(167, 'Backwater Bliss Houseboat', 10579, NULL, 'homestay', 'backwater-bliss-houseboat', NULL, NULL, NULL, 'Alleppey, Kerala', 'Alleppey', '₹9,500/night', 2, 2, NULL, '2015', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Houseboat, Meals Included, Sundeck, WiFi', NULL, NULL, NULL, NULL, NULL, NULL, 'info@backwaterbliss.com', '+91 9800000022', NULL, NULL, 'hhs_houseboat_alleppey.jpg', NULL, 67, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(168, 'Yatri Comfort Guest House', 10587, NULL, 'hotel', 'yatri-comfort-guest-house', NULL, NULL, NULL, 'Haridwar, Uttarakhand', 'Haridwar', '₹1,800/night', 0, 0, NULL, '2012', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'AC, Hot Water, Parking, Vegetarian Meals', NULL, NULL, NULL, NULL, NULL, NULL, 'info@yatricomfort.com', '+91 9800000023', NULL, NULL, 'hhs_guesthouse_haridwar.jpg', NULL, 68, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(169, 'Nestaway Boys PG', 10592, NULL, 'pg', 'nestaway-boys-pg-pgh', NULL, NULL, NULL, 'Electronic City, Bangalore, Karnataka', 'Bangalore', '₹9,800/month', 1, 1, NULL, '2021', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, AC, Housekeeping, 24/7 Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@nestaway.com', '+91 9800000024', NULL, NULL, 'pgh_boys_bangalore.jpg', NULL, 69, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(170, 'SafeStay Girls PG', 10593, NULL, 'pg', 'safestay-girls-pg-pgh', NULL, NULL, NULL, 'Kothrud, Pune, Maharashtra', 'Pune', '₹10,500/month', 1, 1, NULL, '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, CCTV, Warden on Premises, Laundry', NULL, NULL, NULL, NULL, NULL, NULL, 'info@safestaypg.com', '+91 9800000025', NULL, NULL, 'pgh_girls_pune.jpg', NULL, 70, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(171, 'Elite AC PG Residency', 10600, NULL, 'pg', 'elite-ac-pg-residency', NULL, NULL, NULL, 'Gachibowli, Hyderabad, Telangana', 'Hyderabad', '₹13,500/month', 1, 1, NULL, '2022', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'AC, WiFi, Meals, Gym, Co-working Space', NULL, NULL, NULL, NULL, NULL, NULL, 'info@elitepg.com', '+91 9800000026', NULL, NULL, 'pgh_ac_hyderabad.jpg', NULL, 71, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(172, 'Sunrise Boys Hostel', 10607, NULL, 'hostel', 'sunrise-boys-hostel-pgh', NULL, NULL, NULL, 'Anna Nagar, Chennai, Tamil Nadu', 'Chennai', '₹7,000/month', 1, 1, NULL, '2019', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Study Room, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@sunrisehostel.com', '+91 9800000027', NULL, NULL, 'pgh_boys_chennai.jpg', NULL, 72, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(173, 'Campus Co-ed Hostel', 10609, NULL, 'hostel', 'campus-co-ed-hostel-pgh', NULL, NULL, NULL, 'Vidyaranyapura, Bangalore, Karnataka', 'Bangalore', '₹8,200/month', 1, 1, NULL, '2020', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Meals, Study Room, Common Lounge, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'info@campushostel.com', '+91 9800000028', NULL, NULL, 'pgh_coed_bangalore.jpg', NULL, 73, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(174, 'Metro Dormitory Stay', 10614, NULL, 'hostel', 'metro-dormitory-stay-pgh', NULL, NULL, NULL, 'Rajouri Garden, Delhi', 'Delhi', '₹5,500/month', 1, 1, NULL, '2018', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, Bunk Beds, Common Kitchen, Laundry', NULL, NULL, NULL, NULL, NULL, NULL, 'info@metrodorm.com', '+91 9800000029', NULL, NULL, 'pgh_dorm_delhi.jpg', NULL, 74, 'Active', NULL, '2026-08-31 09:10:27', '2026-08-31 09:10:27'),
(175, '3BHK Apartment in MVP Colony', 10002, NULL, 'buy', '3bhk-apartment-in-mvp-colony', NULL, NULL, NULL, 'himayatnagar', 'Hyderabad', '4500000', 2, 2, '1200', '2020', 'Semi-Furnished', '', NULL, NULL, NULL, NULL, NULL, 'Gym, Pool, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'k.nishithkumar2025@gmail.com', '+917702532025', NULL, '0000-00-00', '', NULL, 0, 'Active', 1, '2026-09-01 06:54:03', '2026-09-01 06:54:03'),
(176, '3BHK Apartment in MVP Colony', 10030, NULL, 'buy', '3bhk-apartment-in-mvp-colony', NULL, NULL, NULL, 'himayatnagar', 'Hyderabad', '4500000', 2, 2, '1200', '2020', 'Semi-Furnished', '', NULL, NULL, NULL, NULL, NULL, 'Gym, Pool, Security', NULL, NULL, NULL, NULL, NULL, NULL, 'k.nishithkumar2025@gmail.com', '', NULL, '0000-00-00', 'project_6a9677c6293e41.92609205.webp', NULL, 0, 'Active', 1, '2026-09-01 06:59:18', '2026-09-01 06:59:18'),
(177, 'Sunshine Residency 2BHK', 10114, 2, 'rent', 'sunshine-residency-2bhk', NULL, NULL, NULL, 'Wakad, Pune, Maharashtra', 'Pune', '₹22,000/month', 2, 2, '950', '2020', 'Semi-Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Parking, Lift, 24x7 Security, Power Backup', NULL, NULL, NULL, NULL, NULL, NULL, 'owner1@example.com', '+919800011122', 'tatitodevelopersandpromoters', NULL, '', NULL, 1, 'Active', NULL, '2026-09-03 11:30:40', '2026-09-06 08:17:10'),
(178, 'Green Meadows 3BHK', 10115, 2, 'rent', 'green-meadows-3bhk', NULL, NULL, NULL, 'Baner, Pune, Maharashtra', 'Pune', '₹35,000/month', 3, 3, '1350', '2019', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'Gym, Swimming Pool, Clubhouse, CCTV', NULL, NULL, NULL, NULL, NULL, NULL, 'owner2@example.com', '+919800011123', 'tatitodevelopersandpromoters', NULL, '', NULL, 2, 'Active', NULL, '2026-09-03 11:30:40', '2026-09-06 08:17:10'),
(179, 'Lakeview Villa for Rent', 10119, 2, 'rent', 'lakeview-villa-for-rent', NULL, NULL, NULL, 'Whitefield, Bangalore, Karnataka', 'Bangalore', '₹75,000/month', 4, 4, '2800', '2018', 'Fully Furnished', '3200', NULL, NULL, NULL, NULL, NULL, 'Private Garden, Parking, Security, Power Backup', NULL, NULL, NULL, NULL, NULL, NULL, 'owner3@example.com', '+919800011124', 'tatitodevelopersandpromoters', NULL, '', NULL, 3, 'Active', NULL, '2026-09-03 11:30:40', '2026-09-06 08:17:10'),
(180, 'Cyber Towers Office Space', 10131, 2, 'rent', 'cyber-towers-office-space', NULL, NULL, NULL, 'Hitech City, Hyderabad, Telangana', 'Hyderabad', '₹65/sqft/month', NULL, NULL, '2200', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Lift, Power Backup, Parking, Reception', NULL, NULL, NULL, NULL, NULL, NULL, 'owner4@example.com', '+919800011125', 'tatitodevelopersandpromoters', NULL, '', NULL, 4, 'Active', NULL, '2026-09-03 11:30:40', '2026-09-06 08:17:10'),
(181, 'Furnished Short Stay - Kothrud', 10150, 2, 'rent', 'furnished-short-stay-kothrud', NULL, NULL, NULL, 'Kothrud, Pune, Maharashtra', 'Pune', '₹1,800/night', 1, 1, '450', '2022', 'Fully Furnished', NULL, NULL, NULL, NULL, NULL, NULL, 'WiFi, AC, Kitchenette, Housekeeping', NULL, NULL, NULL, NULL, NULL, NULL, 'owner5@example.com', '+919800011126', 'tatitodevelopersandpromoters', NULL, '', NULL, 5, 'Active', NULL, '2026-09-03 11:30:40', '2026-09-06 08:17:10'),
(182, 'Open Plot for Lease - Shamshabad', 10154, 2, 'rent', 'open-plot-for-lease-shamshabad', NULL, NULL, NULL, 'Shamshabad, Hyderabad, Telangana', 'Hyderabad', '₹8/sqft/month', NULL, NULL, '5000', NULL, NULL, '5000', NULL, NULL, NULL, NULL, NULL, 'Boundary Wall, Water Connection', NULL, NULL, NULL, NULL, NULL, NULL, 'owner6@example.com', '+919800011127', 'tatitodevelopersandpromoters', NULL, '', NULL, 6, 'Active', NULL, '2026-09-03 11:30:40', '2026-09-06 08:17:10');

-- --------------------------------------------------------

--
-- Table structure for table `trending_projects_section`
--

CREATE TABLE `trending_projects_section` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trending_projects_section`
--

INSERT INTO `trending_projects_section` (`id`, `title`, `subtitle`, `updated_at`) VALUES
(2, 'Trending Projects', 'Hand-picked', '2026-07-20 10:09:04');

-- --------------------------------------------------------

--
-- Table structure for table `trending_project_images`
--

CREATE TABLE `trending_project_images` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `image_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trending_project_images`
--

INSERT INTO `trending_project_images` (`id`, `project_id`, `image`, `image_order`, `created_at`) VALUES
(1, 7, 'gallery_6a6744a610e966.65891817.jpg', 0, '2026-07-27 11:44:38'),
(3, 6, 'gallery_6a6747fdd26140.49626259.jpg', 0, '2026-07-27 11:58:53'),
(4, 1, 'gallery_6a73307e6637d3.49783755.jpeg', 0, '2026-08-05 12:45:50'),
(5, 1, 'gallery_6a7330d954da79.35157640.jpeg', 1, '2026-08-05 12:47:21'),
(6, 1, 'gallery_6a7330d954fce6.05491926.jpeg', 2, '2026-08-05 12:47:21'),
(7, 4, 'gallery_6a7333d94e03f8.79708489.jpg', 0, '2026-08-05 13:00:09'),
(8, 4, 'gallery_6a7333d94e2d96.04281187.jpg', 1, '2026-08-05 13:00:09'),
(9, 2, 'gallery_6a746890993798.38768782.jpeg', 0, '2026-08-06 10:57:20'),
(10, 3, 'gallery_6a7468c0623008.50035831.jpeg', 0, '2026-08-06 10:58:08'),
(11, 5, 'gallery_6a7468dfc4e761.33123651.jpeg', 0, '2026-08-06 10:58:39'),
(12, 9, 'gallery_6a76bde688b957.67931388.jpeg', 0, '2026-08-08 05:25:58'),
(13, 10, 'gallery_6a76be953d6249.20983834.jpeg', 0, '2026-08-08 05:28:53'),
(14, 10, 'gallery_6a76be953d7e04.73065265.jpeg', 1, '2026-08-08 05:28:53'),
(15, 10, 'gallery_6a76be953d9307.09473293.jpeg', 2, '2026-08-08 05:28:53'),
(16, 6, 'gallery_6a76bfdfb8a133.96655736.jpeg', 1, '2026-08-08 05:34:23'),
(17, 11, 'gallery_6a76c97fc78d13.74750200.jpeg', 0, '2026-08-08 06:15:27'),
(25, 1, 'gallery_1_1787999575_0.jpg', 4, '2026-08-29 10:32:55'),
(26, 55, 'gallery_55_1787999612_0.jpg', 1, '2026-08-29 10:33:32'),
(27, 55, 'gallery_55_1788000716_0.jpg', 2, '2026-08-29 10:51:56'),
(28, 176, 'project_6a9677c6293e41.92609205.webp', 0, '2026-09-01 06:59:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `mobile_number` varchar(10) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `account_type` enum('buyer','seller','builder','agent','developer') NOT NULL DEFAULT 'buyer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `mobile_number`, `profile_image`, `email_verified`, `otp_code`, `otp_expires`, `last_login`, `password`, `role`, `account_type`, `created_at`, `reset_token`, `reset_expires`) VALUES
(1, 'shahbaz khan', 'shahbazuddinkha@gmail.com', '9999999999', 'uploads/profile/user_1_1785760360.png', 1, '565043', '2026-08-11 12:52:41', '2026-09-04 04:42:22', '$2y$10$jDc5by2aSnp2mi1EWQD91eU6i32bmCgA/zPAbed.BY1Ksklz93tKa', 'admin', 'seller', '2026-07-19 08:41:15', NULL, NULL),
(2, 's k', 'shahbaz1990kh@gmail.com', '8149462320', NULL, 0, NULL, NULL, NULL, '$2y$10$IqbLDrMEgZF/kaiZCu4NDOHmXxrxbMn/qMnWFTg/I.dfrlky.ztcC', 'user', 'buyer', '2026-07-22 10:46:13', NULL, NULL),
(3, 'Dwaipayan', 'db171852934@gmail.com', '7908937137', NULL, 0, NULL, NULL, NULL, '$2y$10$ctgECWaRetgy45azBjq66u2JE4U.wRy7hOt/dd5IWyOD09lvmg3Mi', 'admin', 'buyer', '2026-07-22 12:03:54', NULL, NULL),
(5, 'Dbss', 'dpnb05@gmail.com', '9876543210', NULL, 0, NULL, NULL, NULL, '$2y$10$Gy0ta8gCCcYshhe3T/Cza.XdV6SAocGFA0UVa91BPLXWKpBHXZEKe', 'user', 'buyer', '2026-07-22 12:14:51', NULL, NULL),
(6, 's k11', 'shahbaz1990kh11@gmail.com', '7894561230', NULL, 0, NULL, NULL, NULL, '$2y$10$cOFaiA9u2fvdBZsnawyFYOMdCsUb05bVUcjzUdIaeF646oSV/pL1a', 'user', 'buyer', '2026-07-22 12:22:03', NULL, NULL),
(7, 'saravana', 'saravanakumarmurugesan3@gmail.com', '9566571253', NULL, 0, NULL, NULL, NULL, '$2y$10$E5yElN9waKjyDBDh1ZzZoOHgVKay.hq99WcaX9Gq.e2vpsQQ4T6I2', 'user', 'buyer', '2026-07-23 04:16:50', NULL, NULL),
(8, 'saravana', 'saravanakumarm313@gmail.com', '9566571253', NULL, 0, NULL, NULL, NULL, '$2y$10$eLE6BCNMRowiuAFzl5EkkezTNXcQt8I8u9/CbPdxM7khHzStDi7e2', 'user', 'buyer', '2026-07-23 04:36:25', NULL, NULL),
(9, 'saro', 'saro@gmail.com', '9566571253', NULL, 0, NULL, NULL, NULL, '$2y$10$rhNLmU.h9QuB81bBrQVhvu48X32DFT8BK7XI/MxoY4i82fl7jWSkC', 'user', 'buyer', '2026-07-23 06:04:28', NULL, NULL),
(10, 'saro', 'saro@example.com', '9566571253', NULL, 0, NULL, NULL, NULL, '$2y$10$0RmyK3LPRkrg28hg2jgSg.qWPpPBlzS5hYVc3MFf05nbOEUAQzlJG', 'user', 'buyer', '2026-07-23 06:30:51', NULL, NULL),
(11, 'hhh', 'hhh', '8888888888', NULL, 0, NULL, NULL, NULL, '$2y$10$wmh5mcD48Qrq873NBuVB0OZVflLWm3DJegyjDeviXJgOZRu37KMLi', 'user', 'buyer', '2026-07-24 08:29:32', NULL, NULL),
(12, 'Trupti Sunil Chavan', 'truptichavan049@gmail.com', '7483018998', NULL, 0, NULL, NULL, NULL, '$2y$10$wltxhswTrR.Jsw8IG7IKCutx6jPN69im9Rvfosm4ZS9PW86FGHmsC', 'admin', 'buyer', '2026-08-03 06:54:57', NULL, NULL),
(16, 'm khan', 's44283293@gmail.com', '8149462320', NULL, 1, '577416', '2026-08-12 15:02:18', '2026-08-28 13:00:05', '$2y$10$cYpfhIYyxJL7jqcWLswFg.LJUJnCaiV2EAb/G95s3cBOMBHS6aR9S', 'user', 'buyer', '2026-08-12 14:52:18', NULL, NULL),
(17, 'Tatito Projects', 'tatitoprojects@gmail.com', '7995928999', NULL, 1, '909428', '2026-08-14 10:12:37', '2026-08-14 10:03:36', '$2y$10$exHjj0WNHU.0hligLafFlOfel6sW4c2rS.c5NuHcQdwkv/lX1heR.', 'user', 'buyer', '2026-08-14 10:02:37', NULL, NULL),
(18, 'Uday', 'Uday123', '6301784748', NULL, 0, NULL, NULL, NULL, '$2y$10$udzbTq2vvgXAn5YkAyg2yOk5sxSmc6oAG3mNlS.LKxDgmhAZXtcSG', 'user', 'buyer', '2026-08-18 13:21:01', NULL, NULL),
(19, 'abcd', 'abcd@gmail.com', '9898989898', NULL, 0, '180087', '2026-08-24 07:42:07', NULL, '$2y$10$ETMjVrozZt.mDl6fl.MOYeVrKkR1G38rQPHXLFSv22bOv6p1dsmQK', 'user', 'buyer', '2026-08-24 07:32:08', NULL, NULL),
(20, 'kruthika', 'kruthikapantham@gmail.com', '9898989898', NULL, 1, '211783', '2026-08-24 08:59:41', '2026-09-06 14:59:35', '$2y$10$ATRkcuyX/o0.RlQNonE1w.YvKW5pYQJg5e74S9V9dAQCYfqcJkknC', 'user', 'buyer', '2026-08-24 08:49:41', NULL, NULL),
(21, 'Kruthika seller', 'kruthikap321@gmail.com', '9121212121', NULL, 1, '694135', '2026-08-24 11:00:23', '2026-08-24 10:50:53', '$2y$10$8vbySJ6H7o42HYRyQ6AdG.7WYdfAC0l9i4SRZSy5F1KIW05pH0z2C', 'user', 'buyer', '2026-08-24 10:50:23', NULL, NULL),
(22, 'kruthika seller2', 'kruthikap789@gmail.com', '8989898989', NULL, 1, '577336', '2026-08-24 11:02:23', '2026-08-25 04:53:19', '$2y$10$EF8JWxBkmgU3UVrXHw2LfO/Pd4sC7rKk3WEZuYjbSri29zPjeu5hO', 'user', 'buyer', '2026-08-24 10:52:23', NULL, NULL),
(23, 'vamshi', 'vamshi@gmail.com', '9014243909', NULL, 0, NULL, NULL, NULL, '$2y$10$K30/WrH7EGBMjJMUoOYJkuFrdHh8or1pvI6uFeV/bRZWJ8priIssW', 'user', 'buyer', '2026-08-25 04:29:22', NULL, NULL),
(24, 'vamshi', 'vamshinaikramavath@gmail.com', '9014243909', NULL, 0, NULL, NULL, NULL, '$2y$10$cskhwHxdgCIo4QPlBx3cdup6PG8JASDCBVWaQKsNjkICaFuyOYORq', 'user', 'buyer', '2026-08-25 04:30:07', NULL, NULL),
(28, 'Seller', 'Seller@gmail.com', '9876543219', NULL, 1, NULL, NULL, '2026-09-07 06:36:42', '$2y$10$KSUteI2MweWr9YO68EUaOebvb29o4KGZuhgFBCYPv5mPc/mATmeVa', 'user', 'seller', '2026-08-25 05:50:46', NULL, NULL),
(32, 'Buyer', 'Buyer@gmail.com', '9876543234', NULL, 1, NULL, NULL, '2026-09-04 10:54:24', '$2y$10$zbsG1UW2LLx73HRQZwmzVu1flR./2v29A5LS2N68qxxtdBWRhDere', 'user', 'buyer', '2026-08-25 06:13:47', NULL, NULL),
(34, 'Admin', 'Admin@gmail.com', '9876543456', NULL, 1, NULL, NULL, '2026-09-04 04:43:11', '$2y$10$IYFsoXb6AtS8bNnEy9McDuo1oTiv1N8mRZVTpn5ghRkr9isJ3uBNm', 'admin', 'buyer', '2026-08-25 06:18:53', NULL, NULL),
(35, 'Nishith Kumar', 'k.nishithkumar2025@gmail.com', '7702532025', NULL, 1, '287428', '2026-09-01 05:59:55', '2026-09-01 05:56:31', '$2y$10$AF2.oeOgeFpJQTVTzIy6lu9ZxUUAQXEwnSTlapHoO/FiJz13ayite', 'user', 'buyer', '2026-09-01 05:49:56', '448367', '2026-09-08 10:22:06'),
(36, 'nishith', 'nishith', '7702532025', NULL, 0, NULL, NULL, NULL, '$2y$10$ahHWc/KBmxDWpMOh4s/SZe6bYdPzzsau6y.f3asldtuJmjnXWR72K', 'user', 'buyer', '2026-09-01 07:01:42', NULL, NULL),
(37, 'nishith', 'nishith2025', '7702532025', NULL, 0, NULL, NULL, NULL, '$2y$10$vw9JIEGGJL4QD1pCtCj5V.isRT1USNk8kNF9KpooXB9mcEaXiefrW', 'user', 'buyer', '2026-09-09 14:22:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `category` varchar(80) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `rating` decimal(2,1) DEFAULT 0.0,
  `starting_price` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `slug`, `category`, `city`, `address`, `phone`, `experience_years`, `rating`, `starting_price`, `image`, `description`, `status`, `created_at`) VALUES
(1, 'Studio Verte Architects', 'studio-verte-architects', 'vnd-architect', 'Pune', 'Baner, Pune, Maharashtra', '9800000001', 12, 4.6, '₹25,000 onwards', '', 'Residential and commercial architecture studio.', 'Active', '2026-08-23 08:59:08'),
(2, 'BuildRight Civil Contractors', 'buildright-civil-contractors', 'vnd-civil-contractor', 'Bangalore', 'Whitefield, Bangalore, Karnataka', '9800000002', 9, 4.3, '₹180/sqft onwards', '', 'Turnkey civil construction contractor.', 'Active', '2026-08-23 08:59:08'),
(3, 'Spark Electrical Works', 'spark-electrical-works', 'vnd-electrical-contractor', 'Mumbai', 'Andheri West, Mumbai, Maharashtra', '9800000003', 7, 4.1, '₹500 onwards', '', 'Residential and commercial electrical contracting.', 'Active', '2026-08-23 08:59:08'),
(4, 'Vastu Shastra Consultants', 'vastu-shastra-consultants', 'vnd-vastu-consultant', 'Hyderabad', 'Banjara Hills, Hyderabad, Telangana', '9800000004', 15, 4.7, '₹5,000 onwards', '', 'Traditional Vastu consultation for homes and offices.', 'Active', '2026-08-31 09:00:00'),
(5, 'GreenBuild Structural Engineers', 'greenbuild-structural-engineers', 'vnd-structural-engineer', 'Chennai', 'Anna Nagar, Chennai, Tamil Nadu', '9800000005', 11, 4.5, '₹15,000 onwards', '', 'Structural design and safety audits for residential projects.', 'Active', '2026-08-31 09:00:00'),
(6, 'Reliable Home Loan Advisors', 'reliable-home-loan-advisors', 'vnd-home-loan-dsa-agent', 'Pune', 'Shivaji Nagar, Pune, Maharashtra', '9800000006', 8, 4.2, 'Free consultation', '', 'DSA agents helping with home loan approvals across major banks.', 'Active', '2026-08-31 09:00:00'),
(7, 'Metro Packers & Movers', 'metro-packers-movers', 'vnd-packers-movers', 'Delhi', 'Karol Bagh, Delhi', '9800000007', 10, 4.0, '₹4,500 onwards', '', 'Local and interstate relocation services.', 'Active', '2026-08-31 09:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_us`
--
ALTER TABLE `about_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_slug` (`slug`);

--
-- Indexes for table `builder_contact_enquiries`
--
ALTER TABLE `builder_contact_enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_city_name` (`city_name`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `floor_plans`
--
ALTER TABLE `floor_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `footer_columns`
--
ALTER TABLE `footer_columns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `footer_legal_links`
--
ALTER TABLE `footer_legal_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `footer_links`
--
ALTER TABLE `footer_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `column_id` (`column_id`);

--
-- Indexes for table `footer_settings`
--
ALTER TABLE `footer_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `header_settings`
--
ALTER TABLE `header_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interiors`
--
ALTER TABLE `interiors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category` (`category`),
  ADD KEY `brand` (`brand`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `listing_packages`
--
ALTER TABLE `listing_packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_key` (`package_key`);

--
-- Indexes for table `listing_types`
--
ALTER TABLE `listing_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `local_services`
--
ALTER TABLE `local_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category` (`category`),
  ADD KEY `city` (`city`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_menu_name` (`menu_name`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `project_reports`
--
ALTER TABLE `project_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_reviews`
--
ALTER TABLE `project_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `is_approved` (`is_approved`);

--
-- Indexes for table `project_testimonials`
--
ALTER TABLE `project_testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `property_categories`
--
ALTER TABLE `property_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `menu_id` (`menu_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `category_type` (`category_type`),
  ADD KEY `idx_parent_type` (`parent_id`,`category_type`),
  ADD KEY `wdbi_status_category_order_id_1f1cde7c97ea` (`status`,`category_order`,`id`);

--
-- Indexes for table `property_contact_enquiries`
--
ALTER TABLE `property_contact_enquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexes for table `property_extra_details`
--
ALTER TABLE `property_extra_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project_id` (`project_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_visit_requests`
--
ALTER TABLE `site_visit_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trending_projects`
--
ALTER TABLE `trending_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_trending_projects_category` (`category_id`),
  ADD KEY `fk_trending_projects_package` (`package_id`),
  ADD KEY `fk_trending_projects_listing_type` (`listing_type_id`);

--
-- Indexes for table `trending_projects_section`
--
ALTER TABLE `trending_projects_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trending_project_images`
--
ALTER TABLE `trending_project_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email_verified` (`email_verified`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category` (`category`),
  ADD KEY `city` (`city`),
  ADD KEY `status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_us`
--
ALTER TABLE `about_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `api_tokens`
--
ALTER TABLE `api_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `builder_contact_enquiries`
--
ALTER TABLE `builder_contact_enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `floor_plans`
--
ALTER TABLE `floor_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `footer_columns`
--
ALTER TABLE `footer_columns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `footer_legal_links`
--
ALTER TABLE `footer_legal_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `footer_links`
--
ALTER TABLE `footer_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `footer_settings`
--
ALTER TABLE `footer_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `header_settings`
--
ALTER TABLE `header_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `interiors`
--
ALTER TABLE `interiors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `listing_packages`
--
ALTER TABLE `listing_packages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `listing_types`
--
ALTER TABLE `listing_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `local_services`
--
ALTER TABLE `local_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_reports`
--
ALTER TABLE `project_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_reviews`
--
ALTER TABLE `project_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_testimonials`
--
ALTER TABLE `project_testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_categories`
--
ALTER TABLE `property_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10616;

--
-- AUTO_INCREMENT for table `property_contact_enquiries`
--
ALTER TABLE `property_contact_enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `property_extra_details`
--
ALTER TABLE `property_extra_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `site_visit_requests`
--
ALTER TABLE `site_visit_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trending_projects`
--
ALTER TABLE `trending_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `trending_projects_section`
--
ALTER TABLE `trending_projects_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `trending_project_images`
--
ALTER TABLE `trending_project_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD CONSTRAINT `api_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `floor_plans`
--
ALTER TABLE `floor_plans`
  ADD CONSTRAINT `fk_floor_plans_project` FOREIGN KEY (`project_id`) REFERENCES `trending_projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `footer_links`
--
ALTER TABLE `footer_links`
  ADD CONSTRAINT `fk_footer_link_column` FOREIGN KEY (`column_id`) REFERENCES `footer_columns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_categories`
--
ALTER TABLE `property_categories`
  ADD CONSTRAINT `fk_property_categories_menu` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `property_extra_details`
--
ALTER TABLE `property_extra_details`
  ADD CONSTRAINT `fk_property_extra_details_project` FOREIGN KEY (`project_id`) REFERENCES `trending_projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `trending_projects`
--
ALTER TABLE `trending_projects`
  ADD CONSTRAINT `fk_project_category` FOREIGN KEY (`category_id`) REFERENCES `property_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_trending_projects_category` FOREIGN KEY (`category_id`) REFERENCES `property_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_trending_projects_listing_type` FOREIGN KEY (`listing_type_id`) REFERENCES `listing_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_trending_projects_package` FOREIGN KEY (`package_id`) REFERENCES `listing_packages` (`id`);

--
-- Constraints for table `trending_project_images`
--
ALTER TABLE `trending_project_images`
  ADD CONSTRAINT `trending_project_images_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `trending_projects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
