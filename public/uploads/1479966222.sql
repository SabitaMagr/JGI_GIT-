-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 09, 2016 at 02:52 PM
-- Server version: 5.7.13-0ubuntu0.16.04.2
-- PHP Version: 7.0.8-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `album`
--

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE `album` (
  `id` int(11) NOT NULL,
  `artist` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `album`
--

INSERT INTO `album` (`id`, `artist`, `title`) VALUES
(1, 'The Military Wives', 'In My Dreams'),
(2, 'Adele', '21'),
(3, 'Bruce Springsteen', 'Wrecking Ball (Deluxe)'),
(4, 'Lana Del Rey', 'Born To Die'),
(5, 'Gotye', 'Making Mirrors'),
(6, 'title', 'album'),
(7, 'testing update artist2', 'testing update title1');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `companyCode` int(11) NOT NULL,
  `companyName` varchar(50) NOT NULL,
  `inNepali` varchar(20) NOT NULL,
  `addressFirst` varchar(20) NOT NULL,
  `addressSecond` varchar(20) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `fax` varchar(20) NOT NULL,
  `web` varchar(20) NOT NULL,
  `registrationNo` varchar(20) NOT NULL,
  `vatNo` varchar(20) NOT NULL,
  `smtpHost` varchar(20) NOT NULL,
  `serverPath` varchar(20) NOT NULL,
  `fiscalStart` varchar(20) NOT NULL,
  `fiscalEnd` varchar(20) NOT NULL,
  `startTime` varchar(20) NOT NULL,
  `endTime` varchar(20) NOT NULL,
  `graceStartTime` varchar(20) NOT NULL,
  `graceEndTime` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`companyCode`, `companyName`, `inNepali`, `addressFirst`, `addressSecond`, `telephone`, `email`, `fax`, `web`, `registrationNo`, `vatNo`, `smtpHost`, `serverPath`, `fiscalStart`, `fiscalEnd`, `startTime`, `endTime`, `graceStartTime`, `graceEndTime`) VALUES
(1, 'company name', 'in nepali', 'address first', 'address second', 'telephone', 'email', 'fax', 'web', 'registration no', 'vat no', 'smtp host', 'server path', 'fiscal start', 'fiscal end', 'start time', 'end time ', 'grace start time', 'frace end time'),
(45, 'trytry1', 'tryrty2', 'rtytr3', 'rtyrty4', 'ghj5', 'ghj6', 'hghj7', 'hgjhg8', 'tryrt9', 'rtyrt10', '45613', '4514', 'tryt11', 'try12', 'yrty15', 'rtrt16', 'rty17', 'rt18');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `departmentCode` int(11) NOT NULL,
  `departmentName` varchar(50) NOT NULL,
  `hodCode` varchar(30) NOT NULL,
  `parentDepartment` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`departmentCode`, `departmentName`, `hodCode`, `parentDepartment`) VALUES
(1, 'corporate', '00102', 'null'),
(5, 'ert', 're', 'H'),
(6, 'department name6', 'hod code6', 'H'),
(65, 'department name', 'hod code', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `HRIS_positions`
--

CREATE TABLE `HRIS_positions` (
  `POSITION_ID` int(11) NOT NULL,
  `POSITION_CODE` varchar(20) NOT NULL,
  `POSITION_NAME` varchar(50) NOT NULL,
  `REMARKS` text NOT NULL,
  `STATUS` varchar(10) NOT NULL,
  `CREATED_DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `MODIFIED_DT` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `HRIS_positions`
--

INSERT INTO `HRIS_positions` (`POSITION_ID`, `POSITION_CODE`, `POSITION_NAME`, `REMARKS`, `STATUS`, `CREATED_DT`, `MODIFIED_DT`) VALUES
(1, '015', 'hellow', 'hi', 'e', '2016-08-09 06:23:10', NULL),
(2, '015', 'hellow', 'hi', 'e', '2016-08-09 06:29:29', NULL),
(3, '456ghgfh', 'trybnv', 'try', 'D', '2016-08-09 06:36:11', NULL),
(4, 'yrtvb4564', 'yuiyui', 'fgbnv', 'D', '2016-08-09 06:36:49', NULL),
(5, 'rty54', 'trytr', 'fghtrrt', 'D', '2016-08-09 07:36:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `HRIS_service_types`
--

CREATE TABLE `HRIS_service_types` (
  `SERVICE_TYPE_ID` int(11) NOT NULL,
  `SERVICE_TYPE_CODE` varchar(50) NOT NULL,
  `SERVICE_TYPE_NAME` varchar(50) NOT NULL,
  `REMARKS` text NOT NULL,
  `STATUS` varchar(50) NOT NULL,
  `CREATED_DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `MODIFIED_DT` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `HRIS_service_types`
--

INSERT INTO `HRIS_service_types` (`SERVICE_TYPE_ID`, `SERVICE_TYPE_CODE`, `SERVICE_TYPE_NAME`, `REMARKS`, `STATUS`, `CREATED_DT`, `MODIFIED_DT`) VALUES
(1, 'erter23', 'fgytutyu45', 'TYR', 'D', '2016-08-05 09:50:42', '0000-00-00'),
(47, 'RETRE45', 'RTERFB', 'ERTREGFB', 'E', '2016-08-05 10:15:55', '2016-08-05'),
(48, 'STC002', 'service type name 2', 'remarks 3', 'D', '2016-08-05 10:27:45', '2016-08-05');

-- --------------------------------------------------------

--
-- Table structure for table `HRIS_shifts`
--

CREATE TABLE `HRIS_shifts` (
  `SHIFT_ID` int(11) NOT NULL,
  `SHIFT_CODE` varchar(20) NOT NULL,
  `SHIFT_NAME` varchar(50) NOT NULL,
  `START_TIME` varchar(50) NOT NULL,
  `END_TIME` varchar(50) NOT NULL,
  `REMARKS` text NOT NULL,
  `STATUS` varchar(20) NOT NULL,
  `CREATED_DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `MODIFIED_DT` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `HRIS_shifts`
--

INSERT INTO `HRIS_shifts` (`SHIFT_ID`, `SHIFT_CODE`, `SHIFT_NAME`, `START_TIME`, `END_TIME`, `REMARKS`, `STATUS`, `CREATED_DT`, `MODIFIED_DT`) VALUES
(1, 'SHT001', 'morning', '10', '5', 'updated', 'E', '2016-08-05 11:43:48', '2016-08-05');

-- --------------------------------------------------------

--
-- Table structure for table `HRIS_USERS`
--

CREATE TABLE `HRIS_USERS` (
  `USER_NAME` varchar(20) NOT NULL,
  `PASSWORD` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `HRIS_USERS`
--

INSERT INTO `HRIS_USERS` (`USER_NAME`, `PASSWORD`) VALUES
('admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `leaveType`
--

CREATE TABLE `leaveType` (
  `leaveCode` int(11) NOT NULL,
  `leaveName` varchar(50) NOT NULL,
  `totalLeave` int(11) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `leaveType`
--

INSERT INTO `leaveType` (`leaveCode`, `leaveName`, `totalLeave`, `remarks`) VALUES
(1, 'erter', 546, 'tryrty'),
(2, 'ret', 54, 'gfhgfhbnnbv');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`companyCode`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`departmentCode`);

--
-- Indexes for table `HRIS_positions`
--
ALTER TABLE `HRIS_positions`
  ADD PRIMARY KEY (`POSITION_ID`);

--
-- Indexes for table `HRIS_service_types`
--
ALTER TABLE `HRIS_service_types`
  ADD PRIMARY KEY (`SERVICE_TYPE_ID`);

--
-- Indexes for table `HRIS_shifts`
--
ALTER TABLE `HRIS_shifts`
  ADD PRIMARY KEY (`SHIFT_ID`);

--
-- Indexes for table `HRIS_USERS`
--
ALTER TABLE `HRIS_USERS`
  ADD PRIMARY KEY (`USER_NAME`);

--
-- Indexes for table `leaveType`
--
ALTER TABLE `leaveType`
  ADD PRIMARY KEY (`leaveCode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `album`
--
ALTER TABLE `album`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `departmentCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
--
-- AUTO_INCREMENT for table `HRIS_positions`
--
ALTER TABLE `HRIS_positions`
  MODIFY `POSITION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `HRIS_service_types`
--
ALTER TABLE `HRIS_service_types`
  MODIFY `SERVICE_TYPE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT for table `HRIS_shifts`
--
ALTER TABLE `HRIS_shifts`
  MODIFY `SHIFT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
