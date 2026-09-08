-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Aug 27, 2026 at 11:35 PM
-- Server version: 8.0.44
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `employee_payroll`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `fullname`, `username`, `password`, `created_at`) VALUES
(5, 'System Administrator', 'admin', '$2y$10$UTQRgiC7LjsIHy2P5G10quYFXy/q/lYl9S.uTiTXHSgu1JOKkyYl.', '2026-07-22 22:56:22');

-- --------------------------------------------------------

--
-- Table structure for table `allowances`
--

CREATE TABLE `allowances` (
  `allowance_id` int NOT NULL,
  `allowance_name` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deductions`
--

CREATE TABLE `deductions` (
  `deduction_id` int NOT NULL,
  `deduction_name` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int NOT NULL,
  `department_name` varchar(100) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `description`, `created_at`) VALUES
(2, 'Nursing Science', 'For all students who are studying Nursing Science', '2026-08-06 16:06:30'),
(3, 'Software Engineering', 'The department for Software Engineering students', '2026-08-06 23:15:06'),
(4, 'Computer Engineering', 'For computer engineering students', '2026-08-20 22:36:27'),
(5, 'Electrical Engineering', 'For electrical engineering students', '2026-08-20 22:37:10'),
(6, 'Mechanical Engineering', 'For mechanical engineering students', '2026-08-24 01:19:53'),
(7, 'Medical Laboratory Science', 'For Medical Laboratory Science', '2026-08-24 01:20:44');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int NOT NULL,
  `employee_number` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `department_id` int NOT NULL,
  `position_id` int NOT NULL,
  `employment_type` enum('Full-Time','Part-Time','Contract','Intern') DEFAULT 'Full-Time',
  `employment_date` date DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `employee_number`, `first_name`, `last_name`, `gender`, `date_of_birth`, `email`, `phone`, `address`, `department_id`, `position_id`, `employment_type`, `employment_date`, `username`, `password`, `photo`, `status`, `created_at`) VALUES
(3, 'EMP0001', 'Stephanie', 'Edeigba', 'Female', '2004-02-12', 'stephanieedeigba33@gmail.com', '07049675060', '123 kenler road', 2, 1, 'Full-Time', '2026-08-28', 'stephyy', '$2y$10$HEbPZN422B1K3Gb3tG2p3.h4Xn87rjo5SWdO1kUOLUX/5BCjLFzZa', '1787529272_christopher-campbell-rDEOVtE7vOs-unsplash.jpg', 'Active', '2026-08-07 17:14:38'),
(4, 'EMP0002', 'Omome', 'Eddy', 'Female', '2009-03-12', 'omo@gmail.com', '09052582727', '123 erly road', 3, 1, 'Full-Time', '2012-03-12', 'omo', '$2y$10$aCURVskwTse4wi1U5X2jcOQbJ.ol/gDBfTVE6kDyY6Htaf7p7s63e', '1787529413_IMG_5407.jpg', 'Active', '2026-08-07 17:16:20'),
(5, 'EMP0003', 'Johnny', 'Tim', 'Male', '2001-09-23', 'johnny@gmail.com', '09021342134', 'kampa road, off Iyanapaja', 5, 1, 'Part-Time', '2018-08-12', 'John', '$2y$10$Hl91VCgmWn.r4VzlVFuj.enjR6HNdS4rFK4qVW97DWnmrvV4P7Y4K', '1787529013_albert-dera-ILip77SbmOE-unsplash.jpg', 'Active', '2026-08-19 14:00:37');

-- --------------------------------------------------------

--
-- Table structure for table `employee_allowances`
--

CREATE TABLE `employee_allowances` (
  `id` int NOT NULL,
  `employee_id` int DEFAULT NULL,
  `allowance_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_deductions`
--

CREATE TABLE `employee_deductions` (
  `id` int NOT NULL,
  `employee_id` int DEFAULT NULL,
  `deduction_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `leave_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `leave_type_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int NOT NULL,
  `reason` text,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `approved_by` int DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `rejected_reason` text,
  `processed_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`leave_id`, `employee_id`, `leave_type_id`, `start_date`, `end_date`, `total_days`, `reason`, `status`, `approved_by`, `approval_date`, `rejected_reason`, `processed_by`, `created_at`) VALUES
(1, 4, 1, '2026-03-03', '2026-04-03', 32, 'I need this leave in order to spend time with family', 'Approved', NULL, '2026-08-19 10:32:10', NULL, 5, '2026-08-10 21:14:00'),
(2, 4, 2, '2026-03-15', '2026-04-15', 32, 'I have fever and I need some rest', 'Rejected', NULL, '2026-08-19 11:54:33', 'No concrete reason', 5, '2026-08-19 15:52:48'),
(3, 5, 1, '2026-05-12', '2026-06-12', 32, 'I need a break from work, I feel tired', 'Approved', NULL, '2026-08-19 12:09:28', NULL, 5, '2026-08-19 15:59:01'),
(4, 4, 2, '2026-08-03', '2026-09-04', 33, 'I\'m not feeling too well', 'Approved', NULL, '2026-08-19 12:08:33', NULL, 5, '2026-08-19 16:07:26'),
(5, 5, 1, '2026-09-01', '2026-10-01', 31, 'Omoooooo', 'Rejected', NULL, '2026-08-19 12:11:40', 'No good reason', 5, '2026-08-19 16:11:02');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `leave_type_id` int NOT NULL,
  `leave_name` varchar(100) NOT NULL,
  `max_days` int NOT NULL,
  `description` text,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`leave_type_id`, `leave_name`, `max_days`, `description`, `status`) VALUES
(1, 'Annual Leave', 30, 'Leave due for just one month', 'Active'),
(2, 'Sick Leave', 14, 'Sick leave is for 14 days and above depending on complications', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `payroll_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `payroll_month` varchar(20) NOT NULL,
  `payroll_year` year NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `allowances` decimal(10,2) DEFAULT '0.00',
  `deductions` decimal(10,2) DEFAULT '0.00',
  `tax` decimal(10,2) DEFAULT '0.00',
  `pension` decimal(10,2) DEFAULT '0.00',
  `net_salary` decimal(10,2) NOT NULL,
  `payment_status` enum('Pending','Paid') DEFAULT 'Pending',
  `generated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`payroll_id`, `employee_id`, `payroll_month`, `payroll_year`, `basic_salary`, `allowances`, `deductions`, `tax`, `pension`, `net_salary`, `payment_status`, `generated_at`) VALUES
(9, 3, 'January', '2026', 500000.00, 0.00, 0.00, 25000.00, 40000.00, 435000.00, 'Pending', '2026-08-07 17:16:49'),
(10, 4, 'February', '2026', 500000.00, 0.00, 0.00, 25000.00, 40000.00, 435000.00, 'Pending', '2026-08-07 17:17:06');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_settings`
--

CREATE TABLE `payroll_settings` (
  `setting_id` int NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT '5.00',
  `currency` varchar(10) NOT NULL DEFAULT '₦',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payroll_settings`
--

INSERT INTO `payroll_settings` (`setting_id`, `company_name`, `tax_percentage`, `currency`, `updated_at`) VALUES
(1, 'ABC COMPANY LTD', 5.00, '₦', '2026-08-06 14:44:20');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `position_id` int NOT NULL,
  `position_name` varchar(100) DEFAULT NULL,
  `department_id` int NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`position_id`, `position_name`, `department_id`, `basic_salary`, `description`, `created_at`) VALUES
(1, 'HOD', 2, 700000.00, 'The head of department of nursing science', '2026-08-06 23:23:46'),
(3, 'Professor', 4, 1000000.00, 'Professor of computer engineering', '2026-08-24 18:59:41'),
(4, 'Assistant Professor', 4, 800000.00, 'Assistant computer engineering professor', '2026-08-24 19:00:39'),
(5, 'Senior Lecturer', 2, 400000.00, 'Senior lecturer for nursing science', '2026-08-24 19:02:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `allowances`
--
ALTER TABLE `allowances`
  ADD PRIMARY KEY (`allowance_id`);

--
-- Indexes for table `deductions`
--
ALTER TABLE `deductions`
  ADD PRIMARY KEY (`deduction_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `employee_number` (`employee_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_employee_department` (`department_id`),
  ADD KEY `fk_employee_position` (`position_id`);

--
-- Indexes for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `allowance_id` (`allowance_id`);

--
-- Indexes for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `deduction_id` (`deduction_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `leave_type_id` (`leave_type_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`leave_type_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`payroll_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `payroll_settings`
--
ALTER TABLE `payroll_settings`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`position_id`),
  ADD KEY `fk_department` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `allowances`
--
ALTER TABLE `allowances`
  MODIFY `allowance_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deductions`
--
ALTER TABLE `deductions`
  MODIFY `deduction_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `leave_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `leave_type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `payroll_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payroll_settings`
--
ALTER TABLE `payroll_settings`
  MODIFY `setting_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employee_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_employee_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD CONSTRAINT `employee_allowances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `employee_allowances_ibfk_2` FOREIGN KEY (`allowance_id`) REFERENCES `allowances` (`allowance_id`);

--
-- Constraints for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD CONSTRAINT `employee_deductions_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `employee_deductions_ibfk_2` FOREIGN KEY (`deduction_id`) REFERENCES `deductions` (`deduction_id`);

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`leave_type_id`) ON DELETE RESTRICT;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `fk_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
