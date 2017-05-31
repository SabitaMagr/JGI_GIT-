-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2017 at 02:32 PM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `fncci`
--

-- --------------------------------------------------------

--
-- Table structure for table `chapter`
--

CREATE TABLE IF NOT EXISTS `chapter` (
`chapter_id` int(4) NOT NULL,
  `type_id` int(11) NOT NULL,
  `pre_type_id` int(2) DEFAULT NULL,
  `chapter_edesc` varchar(200) NOT NULL,
  `chapter_ndesc` varchar(255) DEFAULT NULL,
  `zone_id` char(2) DEFAULT NULL,
  `district_id` char(2) DEFAULT NULL,
  `address` text,
  `estd_bs_date` varchar(10) DEFAULT NULL,
  `phone1` varchar(20) DEFAULT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `nearast_airport` varchar(255) DEFAULT NULL,
  `total_member` int(4) DEFAULT NULL,
  `status` enum('D','E') NOT NULL DEFAULT 'E'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chapter_executive`
--

CREATE TABLE IF NOT EXISTS `chapter_executive` (
  `chapter_id` int(4) NOT NULL,
  `exe_id` int(3) NOT NULL,
  `name` varchar(200) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `voting_right` enum('Y','N') NOT NULL DEFAULT 'Y',
  `status` enum('E','D') NOT NULL DEFAULT 'E'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `chapter_type`
--

CREATE TABLE IF NOT EXISTS `chapter_type` (
`type_id` int(11) NOT NULL,
  `type_edesc` varchar(150) NOT NULL,
  `type_ndesc` varchar(255) NOT NULL,
  `status` enum('D','E') NOT NULL DEFAULT 'E'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `chapter_type`
--

INSERT INTO `chapter_type` (`type_id`, `type_edesc`, `type_ndesc`, `status`) VALUES
(1, 'District/Municipality', '', 'E'),
(2, 'Commodity', '', 'E'),
(3, 'Associate', '', 'E');

-- --------------------------------------------------------

--
-- Table structure for table `executive_setup`
--

CREATE TABLE IF NOT EXISTS `executive_setup` (
`exe_id` int(3) NOT NULL,
  `type_id` int(11) NOT NULL,
  `exe_edesc` varchar(150) NOT NULL,
  `exe_ndesc` varchar(255) DEFAULT NULL,
  `level` int(2) NOT NULL,
  `status` enum('D','E') NOT NULL DEFAULT 'E'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `executive_setup`
--

INSERT INTO `executive_setup` (`exe_id`, `type_id`, `exe_edesc`, `exe_ndesc`, `level`, `status`) VALUES
(6, 1, 'Vice President', '', 1, 'E'),
(7, 1, 'President', '', 3, 'E'),
(8, 1, 'general sectratry', '', 4, 'E'),
(9, 1, 'asdasd', '', 3, 'E'),
(10, 1, 'sadakshya', '', 4, 'E'),
(11, 1, 'aaaa', '', 3, 'E'),
(12, 2, 'asdasdasd', '', 6, 'E');

-- --------------------------------------------------------

--
-- Table structure for table `pre_chapter_type`
--

CREATE TABLE IF NOT EXISTS `pre_chapter_type` (
`pre_type_id` int(2) NOT NULL,
  `type_id` int(11) NOT NULL,
  `pre_type_edesc` varchar(150) NOT NULL,
  `pre_type_ndesc` varchar(255) NOT NULL,
  `status` enum('D','E') NOT NULL DEFAULT 'E'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `pre_chapter_type`
--

INSERT INTO `pre_chapter_type` (`pre_type_id`, `type_id`, `pre_type_edesc`, `pre_type_ndesc`, `status`) VALUES
(8, 1, 'airlines', '', 'E'),
(10, 3, 'sadfdasf', '', 'E'),
(11, 2, 'test 1', '', 'E'),
(12, 1, 'aaa', '', 'E');

-- --------------------------------------------------------

--
-- Table structure for table `zone_district`
--

CREATE TABLE IF NOT EXISTS `zone_district` (
  `zone_id` char(2) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `zone_name` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `district_id` char(2) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `district_name` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `zone_district`
--

INSERT INTO `zone_district` (`zone_id`, `zone_name`, `district_id`, `district_name`) VALUES
('1', 'Bagmati', '10', 'Bhaktapur'),
('1', 'Bagmati', '17', 'Dhading'),
('1', 'Bagmati', '35', 'Kathmandu'),
('1', 'Bagmati', '38', 'Lalitpur'),
('1', 'Bagmati', '48', 'Nuwakot'),
('1', 'Bagmati', '66', 'Sindhupalchok'),
('1', 'Bagmati', '56', 'Rasuwa'),
('1', 'Bagmati', '36', 'Kavrepalanchok'),
('10', 'Seti', '1', 'Achham'),
('10', 'Seti', '30', 'Kailali'),
('10', 'Seti', '22', 'Doti'),
('10', 'Seti', '5', 'Bajhang'),
('10', 'Seti', '6', 'Bajura'),
('11', 'Rapti', '15', 'Dang deokhuri'),
('11', 'Rapti', '54', 'Pyuthan'),
('11', 'Rapti', '61', 'Salyan'),
('11', 'Rapti', '59', 'Rukum'),
('11', 'Rapti', '58', 'Rolpa'),
('12', 'Lumbini', '2', 'Arghakhanchi'),
('12', 'Lumbini', '33', 'Kapilvastu'),
('12', 'Lumbini', '60', 'Rupandehi'),
('12', 'Lumbini', '47', 'Nawalparasi'),
('12', 'Lumbini', '24', 'Gulmi'),
('13', 'Karnali', '31', 'Kalikot'),
('13', 'Karnali', '25', 'Humla'),
('13', 'Karnali', '44', 'Mugu'),
('13', 'Karnali', '21', 'Dolpa'),
('13', 'Karnali', '29', 'Jumla'),
('14', 'Mahakali', '4', 'Baitadi'),
('14', 'Mahakali', '16', 'Darchula'),
('14', 'Mahakali', '32', 'Kanchanpur'),
('14', 'Mahakali', '13', 'Dadeldhura'),
('2', 'Narayani', '41', 'Makwanpur'),
('2', 'Narayani', '11', 'Bhojpur'),
('2', 'Narayani', '8', 'Bara'),
('2', 'Narayani', '57', 'Rautahat'),
('2', 'Narayani', '53', 'Parsa'),
('2', 'Narayani', '50', 'Palpa'),
('2', 'Narayani', '12', 'Chitwan'),
('3', 'Janakpur', '19', 'Dhanusa'),
('3', 'Janakpur', '20', 'Dolakha'),
('3', 'Janakpur', '40', 'Mahottari'),
('3', 'Janakpur', '65', 'Sindhuli'),
('3', 'Janakpur', '64', 'Sarlahi'),
('3', 'Janakpur', '55', 'Ramechhap'),
('4', 'Koshi', '69', 'Sunsari'),
('4', 'Koshi', '62', 'Sankhuwasabha'),
('4', 'Koshi', '43', 'Morang'),
('4', 'Koshi', '18', 'Dhankuta'),
('4', 'Koshi', '74', 'Terhathum'),
('5', 'Sagarmatha', '67', 'Siraha'),
('5', 'Sagarmatha', '49', 'Okhaldhunga'),
('5', 'Sagarmatha', '37', 'Khotang'),
('5', 'Sagarmatha', '75', 'Udayapur'),
('5', 'Sagarmatha', '63', 'Saptari'),
('5', 'Sagarmatha', '68', 'Solukhumbu'),
('6', 'Dhaulagiri', '3', 'Baglung'),
('6', 'Dhaulagiri', '52', 'Parbat'),
('6', 'Dhaulagiri', '46', 'Myagdi'),
('6', 'Dhaulagiri', '45', 'Mustang'),
('7', 'Mechi', '26', 'Ilam'),
('7', 'Mechi', '73', 'Taplejung'),
('7', 'Mechi', '51', 'Panchthar'),
('7', 'Mechi', '28', 'Jhapa'),
('8', 'Bheri', '7', 'Banke'),
('8', 'Bheri', '70', 'Surkhet'),
('8', 'Bheri', '14', 'Dailekh'),
('8', 'Bheri', '27', 'Jajarkot'),
('8', 'Bheri', '9', 'Bardiya'),
('9', 'Gandaki', '23', 'Gorkha'),
('9', 'Gandaki', '72', 'Tanahu'),
('9', 'Gandaki', '39', 'Lamjung'),
('9', 'Gandaki', '42', 'Manang'),
('9', 'Gandaki', '71', 'Syangja'),
('9', 'Gandaki', '34', 'Kaski');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chapter`
--
ALTER TABLE `chapter`
 ADD PRIMARY KEY (`chapter_id`);

--
-- Indexes for table `chapter_executive`
--
ALTER TABLE `chapter_executive`
 ADD KEY `fk_chapter_executive` (`chapter_id`), ADD KEY `fk_chapter_executive1` (`exe_id`);

--
-- Indexes for table `chapter_type`
--
ALTER TABLE `chapter_type`
 ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `executive_setup`
--
ALTER TABLE `executive_setup`
 ADD PRIMARY KEY (`exe_id`);

--
-- Indexes for table `pre_chapter_type`
--
ALTER TABLE `pre_chapter_type`
 ADD PRIMARY KEY (`pre_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chapter`
--
ALTER TABLE `chapter`
MODIFY `chapter_id` int(4) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `chapter_type`
--
ALTER TABLE `chapter_type`
MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `executive_setup`
--
ALTER TABLE `executive_setup`
MODIFY `exe_id` int(3) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `pre_chapter_type`
--
ALTER TABLE `pre_chapter_type`
MODIFY `pre_type_id` int(2) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `chapter_executive`
--
ALTER TABLE `chapter_executive`
ADD CONSTRAINT `fk_chapter_executive` FOREIGN KEY (`chapter_id`) REFERENCES `chapter` (`chapter_id`),
ADD CONSTRAINT `fk_chapter_executive1` FOREIGN KEY (`exe_id`) REFERENCES `executive_setup` (`exe_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
