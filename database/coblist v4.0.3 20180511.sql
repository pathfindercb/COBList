-- phpMyAdmin SQL Dump
-- version 4.4.15.9
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 11, 2018 at 01:31 PM
-- Server version: 5.6.37
-- PHP Version: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coblist`
--

-- --------------------------------------------------------

--
-- Table structure for table `RateMaster`
--

CREATE TABLE IF NOT EXISTS `RateMaster` (
  `class` varchar(3) NOT NULL,
  `rate` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `RateMaster`
--

INSERT INTO `RateMaster` (`class`, `rate`, `date`, `status`) VALUES
('A', 1650, '2018-04-01', 1),
('B', 1320, '2018-04-01', 1),
('C', 1100, '2018-04-01', 1),
('D', 880, '2018-04-01', 1),
('E', 330, '2018-04-01', 1),
('K', 250, '2018-04-01', 1),
('M', 0, '2016-12-04', 1),
('C**', 110, '2018-04-01', 1),
('A**', 110, '2018-04-01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `RunBlob`
--

CREATE TABLE IF NOT EXISTS `RunBlob` (
  `dataid` int(11) NOT NULL,
  `logid` int(11) NOT NULL,
  `userid` varchar(15) CHARACTER SET utf8 NOT NULL,
  `adminaccess` tinyint(1) NOT NULL DEFAULT '0',
  `userdata` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `RunLog`
--

CREATE TABLE IF NOT EXISTS `RunLog` (
  `logid` mediumint(9) NOT NULL,
  `logdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `runtime` int(11) DEFAULT NULL,
  `filetime` int(11) DEFAULT NULL,
  `ip` varchar(50) CHARACTER SET utf8 NOT NULL,
  `user` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `type` int(11) NOT NULL,
  `records` int(11) NOT NULL,
  `notes` text CHARACTER SET utf8
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SlipMaster`
--

CREATE TABLE IF NOT EXISTS `SlipMaster` (
  `slipid` varchar(4) NOT NULL,
  `type` text NOT NULL,
  `dock` text NOT NULL,
  `class` varchar(3) NOT NULL,
  `scondition` varchar(20) NOT NULL DEFAULT 'Normal',
  `width` float NOT NULL DEFAULT '14',
  `depth` float NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `SlipMaster`
--

INSERT INTO `SlipMaster` (`slipid`, `type`, `dock`, `class`, `scondition`, `width`, `depth`, `date`) VALUES
('914', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('918', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('904', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('916', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('922', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('926', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('N02a', 'Kayak', 'North Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('N02b', 'Kayak', 'North Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('N02c', 'Kayak', 'North Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('N02d', 'Kayak', 'North Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('N02e', 'Kayak', 'North Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('N02f', 'Kayak', 'North Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('N12b', 'Kayak', 'North Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('N12a', 'Kayak', 'North Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('N04a', 'Kayak', 'North Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('N04b', 'Kayak', 'North Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('N05a', 'Kayak', 'North Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('N06a', 'Kayak', 'North Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('N06b', 'Kayak', 'North Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('N07a', 'Kayak', 'North Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('N07b', 'Kayak', 'North Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('N08a', 'Kayak', 'North Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('N08b', 'Kayak', 'North Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('N10b', 'Kayak', 'North Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('N11', 'Slip', 'North Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('N13', 'Slip', 'North Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('N14', 'Slip', 'North Dock', 'C**', 'Sea Grass', 14, 0, '2016-12-08 08:33:13'),
('N15', 'Slip', 'North Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('N16', 'Slip', 'North Dock', 'C**', 'Sea Grass', 14, 0, '2016-12-08 08:33:13'),
('N17', 'Slip', 'North Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('N18', 'Slip', 'North Dock', 'A**', 'Sea Grass', 16, 0, '2016-12-08 08:33:13'),
('N19', 'Slip', 'North Dock', 'C', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('N20', 'Slip', 'North Dock', 'A**', 'Sea Grass', 16, 0, '2016-12-08 08:33:13'),
('N21', 'Slip', 'North Dock', 'B', 'Normal', 16, 0, '2016-12-08 08:33:13'),
('N22', 'Slip', 'North Dock', 'A', 'Normal', 16, 0, '2016-12-08 08:33:13'),
('N23', 'Slip', 'North Dock', 'C', 'for small boat', 14, 0, '2016-12-08 08:33:13'),
('N24', 'Slip', 'North Dock', 'E', 'No finger pier', 14, 0, '2016-12-08 08:33:13'),
('N25', 'Slip', 'North Dock', 'E', 'Tight turn', 14, 0, '2016-12-08 08:33:13'),
('N27', 'Slip', 'North Dock', 'E', 'No finger pier', 14, 0, '2016-12-08 08:33:13'),
('S04a', 'Kayak', 'South Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('S04b', 'Kayak', 'South Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('S02a', 'Kayak', 'South Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('S02b', 'Kayak', 'South Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('S02c', 'Kayak', 'South Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('S02d', 'Kayak', 'South Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('S02e', 'Kayak', 'South Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('S02f', 'Kayak', 'South Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('S03a', 'Kayak', 'South Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('S03b', 'Kayak', 'South Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('S06a', 'Kayak', 'South Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('S06b', 'Kayak', 'South Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('S09', 'Slip', 'South Dock', 'C', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('S11', 'Slip', 'South Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('S12', 'Slip', 'South Dock', 'C', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('S13', 'Slip', 'South Dock', 'C', 'Lift', 14, 4, '2016-12-08 08:33:13'),
('S14', 'Slip', 'South Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('S15', 'Slip', 'South Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('S16', 'Slip', 'South Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('S17', 'Slip', 'South Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('S18', 'Slip', 'South Dock', 'A', 'Normal', 16, 0, '2016-12-08 08:33:13'),
('S19', 'Slip', 'South Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('S20', 'Slip', 'South Dock', 'A', 'Lift', 16, 0, '2016-12-08 08:33:13'),
('S21', 'Slip', 'South Dock', 'B', 'Lift', 16, 0, '2016-12-08 08:33:13'),
('S22', 'Slip', 'South Dock', 'A', 'Normal', 16, 0, '2016-12-08 08:33:13'),
('S23', 'Slip', 'South Dock', 'C', 'for small boat', 14, 0, '2016-12-08 08:33:13'),
('S24', 'Slip', 'South Dock', 'E', 'No Finger Pier', 14, 0, '2016-12-08 08:33:13'),
('S25', 'Slip', 'South Dock', 'E', 'Tight turn', 14, 0, '2016-12-08 08:33:13'),
('S27', 'Slip', 'South Dock', 'E', 'No finger pier', 14, 0, '2016-12-08 08:33:13'),
('N10a', 'Kayak', 'North Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('N05b', 'Kayak', 'North Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('N09', 'Slip', 'North Dock', 'C', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('912', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('920', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('902', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('906', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('908', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('910', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('924', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('928', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('930', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('932', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('934', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('936', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('938', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('940', 'Slip', 'MS', 'M', 'Normal', 14, 0, '2016-12-08 08:33:13'),
('S07', 'Slip', 'South Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('S05', 'Slip', 'South Dock', 'C', 'Lift', 14, 0, '2016-12-08 08:33:13'),
('S08a', 'Kayak', 'South Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('S08b', 'Kayak', 'South Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('S10b', 'Kayak', 'South Dock', 'K', 'Lower', 14, 0, '2016-12-08 08:33:13'),
('S10a', 'Kayak', 'South Dock', 'K', 'Upper', 14, 0, '2016-12-08 08:33:13'),
('N03a', 'Kayak', 'North Dock', 'K', 'Upper', 14, 4, '2018-05-10 17:10:30'),
('N03b', 'Kayak', 'North Dock', 'K', 'Lower', 14, 4, '2018-05-10 17:11:05');

-- --------------------------------------------------------

--
-- Table structure for table `Slips`
--

CREATE TABLE IF NOT EXISTS `Slips` (
  `slid` mediumint(9) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `names` text NOT NULL,
  `slipid` varchar(4) NOT NULL,
  `lift` tinyint(1) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(40) NOT NULL,
  `userid` varchar(15) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2905 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `UnitMaster`
--

CREATE TABLE IF NOT EXISTS `UnitMaster` (
  `unit` varchar(19) DEFAULT NULL,
  `space` int(11) DEFAULT NULL,
  `model` varchar(10) NOT NULL,
  `sqft` int(11) NOT NULL,
  `decks` int(11) NOT NULL,
  `fee` float NOT NULL,
  `beds` int(11) NOT NULL,
  `baths` float NOT NULL,
  `propid` text,
  `bldg` varchar(13) DEFAULT NULL,
  `floor` int(2) DEFAULT NULL,
  `stack` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `UnitMaster`
--

INSERT INTO `UnitMaster` (`unit`, `space`, `model`, `sqft`, `decks`, `fee`, `beds`, `baths`, `propid`, `bldg`, `floor`, `stack`) VALUES
('Marina Suites # 902', 902, 'Chopin', 2849, 2, 5.2, 2, 2.5, '2009097001', 'MS 1', 1, 20),
('Marina Suites # 904', 904, 'Mozart', 2685, 2, 4.8, 2, 1.5, '2009097002', 'MS 1', 1, 20),
('Marina Suites # 906', 906, 'Mozart', 2685, 2, 4.8, 2, 1.5, '2009097003', 'MS 1', 1, 20),
('Marina Suites # 908', 908, 'Chopin', 2849, 2, 5.2, 2, 2.5, '2009097004', 'MS 1', 1, 20),
('Marina Suites # 910', 910, 'Chopin', 2849, 2, 5.2, 2, 2.5, '2009097005', 'MS 2', 1, 20),
('Marina Suites # 912', 912, 'Mozart', 2685, 2, 4.8, 2, 1.5, '2009097006', 'MS 2', 1, 20),
('Marina Suites # 914', 914, 'Mozart', 2685, 2, 4.8, 2, 1.5, '2009097007', 'MS 2', 1, 20),
('Marina Suites # 916', 916, 'Chopin', 2849, 2, 5.2, 2, 2.5, '2009097008', 'MS 2', 1, 20),
('Marina Suites # 918', 918, 'Chopin', 2849, 2, 5.2, 2, 2.5, '2009097009', 'MS 3', 1, 20),
('Marina Suites # 920', 920, 'Mozart', 2685, 2, 4.8, 2, 1.5, '2009097010', 'MS 3', 1, 20),
('Marina Suites # 922', 922, 'Mozart', 2685, 2, 4.8, 2, 1.5, '2009097011', 'MS 3', 1, 20),
('Marina Suites # 924', 924, 'Chopin', 2849, 2, 5.2, 2, 2.5, '2009097012', 'MS 3', 1, 20),
('Marina Suites # 926', 926, 'Chopin', 2849, 2, 5.2, 2, 2.5, '2009097014', 'MS 4', 1, 20),
('Marina Suites # 928', 928, 'Mozart', 2685, 2, 4.8, 2, 1.5, '2009097015', 'MS 4', 1, 20),
('Marina Suites # 930', 930, 'Mozart', 2685, 2, 4.8, 2, 1.5, '2009097016', 'MS 4', 1, 20),
('Marina Suites # 932', 932, 'Chopin', 2849, 2, 5.2, 2, 2.5, '2009097017', 'MS 4', 1, 20),
('Marina Suites # 934', 934, 'Chopin', 2849, 2, 5.2, 2, 2.5, '2009097018', 'MS 5', 1, 20),
('Marina Suites # 936', 936, 'Mozart', 2685, 2, 4.8, 2, 1.5, '2009097019', 'MS 5', 1, 20),
('Marina Suites # 938', 938, 'Mozart', 2685, 2, 4.8, 2, 1.5, '2009097020', 'MS 5', 1, 20),
('Marina Suites # 940', 940, 'Chopin', 2849, 2, 5.2, 2, 2.5, '2009097021', 'MS 5', 1, 20),
('Tower 1 # 101', 1012, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095001', 'Tower 1', 1, 1),
('Tower 1 # 102', 1011, 'Monet', 1288, 1, 0.65, 2, 2, '2009095002', 'Tower 1', 1, 2),
('Tower 1 # 103', 1010, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095003', 'Tower 1', 1, 3),
('Tower 1 # 104', 1009, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095004', 'Tower 1', 1, 4),
('Tower 1 # 201', 1046, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095005', 'Tower 1', 2, 1),
('Tower 1 # 202', 1045, 'Monet', 1288, 1, 0.65, 2, 2, '2009095006', 'Tower 1', 2, 2),
('Tower 1 # 203', 1044, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095007', 'Tower 1', 2, 3),
('Tower 1 # 204', 1043, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095008', 'Tower 1', 2, 4),
('Tower 1 # 205', 1052, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095009', 'Tower 1', 2, 5),
('Tower 1 # 206', 1051, 'Monet', 1288, 1, 0.67, 2, 2, '2009095010', 'Tower 1', 2, 6),
('Tower 1 # 207', 1042, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095011', 'Tower 1', 2, 7),
('Tower 1 # 208', 1041, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095012', 'Tower 1', 2, 8),
('Tower 1 # 301', 1050, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095013', 'Tower 1', 3, 1),
('Tower 1 # 302', 1049, 'Monet', 1288, 1, 0.65, 2, 2, '2009095014', 'Tower 1', 3, 2),
('Tower 1 # 303', 1048, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095015', 'Tower 1', 3, 3),
('Tower 1 # 304', 1047, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095016', 'Tower 1', 3, 4),
('Tower 1 # 305', 1056, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095017', 'Tower 1', 3, 5),
('Tower 1 # 306', 1055, 'Monet', 1288, 1, 0.67, 2, 2, '2009095018', 'Tower 1', 3, 6),
('Tower 1 # 307', 1054, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095019', 'Tower 1', 3, 7),
('Tower 1 # 308', 1053, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095020', 'Tower 1', 3, 8),
('Tower 1 # 401', 1037, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095021', 'Tower 1', 4, 1),
('Tower 1 # 402', 1038, 'Monet', 1288, 1, 0.65, 2, 2, '2009095022', 'Tower 1', 4, 2),
('Tower 1 # 403', 1039, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095023', 'Tower 1', 4, 3),
('Tower 1 # 404', 1040, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095024', 'Tower 1', 4, 4),
('Tower 1 # 405', 1060, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095025', 'Tower 1', 4, 5),
('Tower 1 # 406', 1059, 'Monet', 1288, 1, 0.67, 2, 2, '2009095026', 'Tower 1', 4, 6),
('Tower 1 # 407', 1058, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095027', 'Tower 1', 4, 7),
('Tower 1 # 408', 1057, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095028', 'Tower 1', 4, 8),
('Tower 1 # 501', 1033, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095029', 'Tower 1', 5, 1),
('Tower 1 # 502', 1034, 'Monet', 1288, 1, 0.65, 2, 2, '2009095030', 'Tower 1', 5, 2),
('Tower 1 # 503', 1035, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095031', 'Tower 1', 5, 3),
('Tower 1 # 504', 1036, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095032', 'Tower 1', 5, 4),
('Tower 1 # 505', 1064, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095033', 'Tower 1', 5, 5),
('Tower 1 # 506', 1063, 'Monet', 1288, 1, 0.67, 2, 2, '2009095034', 'Tower 1', 5, 6),
('Tower 1 # 507', 1062, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095035', 'Tower 1', 5, 7),
('Tower 1 # 508', 1061, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095036', 'Tower 1', 5, 8),
('Tower 1 # 601', 1029, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095037', 'Tower 1', 6, 1),
('Tower 1 # 602', 1030, 'Monet', 1288, 1, 0.65, 2, 2, '2009095038', 'Tower 1', 6, 2),
('Tower 1 # 603', 1031, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095039', 'Tower 1', 6, 3),
('Tower 1 # 604', 1032, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095040', 'Tower 1', 6, 4),
('Tower 1 # 605', 1068, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095041', 'Tower 1', 6, 5),
('Tower 1 # 606', 1067, 'Monet', 1288, 1, 0.67, 2, 2, '2009095042', 'Tower 1', 6, 6),
('Tower 1 # 607', 1066, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095043', 'Tower 1', 6, 7),
('Tower 1 # 608', 1065, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095044', 'Tower 1', 6, 8),
('Tower 1 # 701', 1130, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095045', 'Tower 1', 7, 1),
('Tower 1 # 702', 1026, 'Monet', 1288, 1, 0.65, 2, 2, '2009095046', 'Tower 1', 7, 2),
('Tower 1 # 703', 1027, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095047', 'Tower 1', 7, 3),
('Tower 1 # 704', 1028, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095048', 'Tower 1', 7, 4),
('Tower 1 # 705', 1072, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095049', 'Tower 1', 7, 5),
('Tower 1 # 706', 1071, 'Monet', 1288, 1, 0.67, 2, 2, '2009095050', 'Tower 1', 7, 6),
('Tower 1 # 707', 1070, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095051', 'Tower 1', 7, 7),
('Tower 1 # 708', 1069, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095052', 'Tower 1', 7, 8),
('Tower 1 # 801', 1134, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095053', 'Tower 1', 8, 1),
('Tower 1 # 802', 1133, 'Monet', 1288, 1, 0.65, 2, 2, '2009095054', 'Tower 1', 8, 2),
('Tower 1 # 803', 1132, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095055', 'Tower 1', 8, 3),
('Tower 1 # 804', 1131, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095056', 'Tower 1', 8, 4),
('Tower 1 # 805', 1076, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095057', 'Tower 1', 8, 5),
('Tower 1 # 806', 1075, 'Monet', 1288, 1, 0.67, 2, 2, '2009095058', 'Tower 1', 8, 6),
('Tower 1 # 807', 1074, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095059', 'Tower 1', 8, 7),
('Tower 1 # 808', 1073, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095060', 'Tower 1', 8, 8),
('Tower 1 # 901', 1138, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095061', 'Tower 1', 9, 1),
('Tower 1 # 902', 1137, 'Monet', 1288, 1, 0.65, 2, 2, '2009095062', 'Tower 1', 9, 2),
('Tower 1 # 903', 1136, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095063', 'Tower 1', 9, 3),
('Tower 1 # 904', 1135, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095064', 'Tower 1', 9, 4),
('Tower 1 # 905', 1121, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095065', 'Tower 1', 9, 5),
('Tower 1 # 906', 1120, 'Monet', 1288, 1, 0.67, 2, 2, '2009095066', 'Tower 1', 9, 6),
('Tower 1 # 907', 1078, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095067', 'Tower 1', 9, 7),
('Tower 1 # 908', 1077, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095068', 'Tower 1', 9, 8),
('Tower 1 #1001', 1002, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095069', 'Tower 1', 10, 1),
('Tower 1 #1002', 1095, 'Monet', 1288, 1, 0.65, 2, 2, '2009095070', 'Tower 1', 10, 2),
('Tower 1 #1003', 1140, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095071', 'Tower 1', 10, 3),
('Tower 1 #1004', 1139, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095072', 'Tower 1', 10, 4),
('Tower 1 #1005', 1125, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095073', 'Tower 1', 10, 5),
('Tower 1 #1006', 1124, 'Monet', 1288, 1, 0.67, 2, 2, '2009095074', 'Tower 1', 10, 6),
('Tower 1 #1007', 1123, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095075', 'Tower 1', 10, 7),
('Tower 1 #1008', 1122, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095076', 'Tower 1', 10, 8),
('Tower 1 #1101', 1099, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095077', 'Tower 1', 11, 1),
('Tower 1 #1102', 1098, 'Monet', 1288, 1, 0.65, 2, 2, '2009095078', 'Tower 1', 11, 2),
('Tower 1 #1103', 1097, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095079', 'Tower 1', 11, 3),
('Tower 1 #1104', 1096, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095080', 'Tower 1', 11, 4),
('Tower 1 #1105', 1129, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095081', 'Tower 1', 11, 5),
('Tower 1 #1106', 1128, 'Monet', 1288, 1, 0.67, 2, 2, '2009095082', 'Tower 1', 11, 6),
('Tower 1 #1107', 1127, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095083', 'Tower 1', 11, 7),
('Tower 1 #1108', 1126, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095084', 'Tower 1', 11, 8),
('Tower 1 #1201', 1103, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095085', 'Tower 1', 12, 1),
('Tower 1 #1202', 1102, 'Monet', 1288, 1, 0.65, 2, 2, '2009095086', 'Tower 1', 12, 2),
('Tower 1 #1203', 1101, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095087', 'Tower 1', 12, 3),
('Tower 1 #1204', 1100, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095088', 'Tower 1', 12, 4),
('Tower 1 #1205', 1111, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095089', 'Tower 1', 12, 5),
('Tower 1 #1206', 1110, 'Monet', 1288, 1, 0.67, 2, 2, '2009095090', 'Tower 1', 12, 6),
('Tower 1 #1207', 1109, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095091', 'Tower 1', 12, 7),
('Tower 1 #1208', 1108, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095092', 'Tower 1', 12, 8),
('Tower 1 #1401', 1107, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095093', 'Tower 1', 14, 1),
('Tower 1 #1402', 1106, 'Monet', 1288, 1, 0.65, 2, 2, '2009095094', 'Tower 1', 14, 2),
('Tower 1 #1403', 1105, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095095', 'Tower 1', 14, 3),
('Tower 1 #1404', 1104, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095096', 'Tower 1', 14, 4),
('Tower 1 #1405', 1115, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095097', 'Tower 1', 14, 5),
('Tower 1 #1406', 1114, 'Monet', 1288, 1, 0.67, 2, 2, '2009095098', 'Tower 1', 14, 6),
('Tower 1 #1407', 1113, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095099', 'Tower 1', 14, 7),
('Tower 1 #1408', 1112, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095100', 'Tower 1', 14, 8),
('Tower 1 #1501', 1021, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095101', 'Tower 1', 15, 1),
('Tower 1 #1502', 1020, 'Monet', 1288, 1, 0.65, 2, 2, '2009095102', 'Tower 1', 15, 2),
('Tower 1 #1503', 1019, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095103', 'Tower 1', 15, 3),
('Tower 1 #1504', 1018, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095104', 'Tower 1', 15, 4),
('Tower 1 #1505', 1119, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095105', 'Tower 1', 15, 5),
('Tower 1 #1506', 1118, 'Monet', 1288, 1, 0.67, 2, 2, '2009095106', 'Tower 1', 15, 6),
('Tower 1 #1507', 1117, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095107', 'Tower 1', 15, 7),
('Tower 1 #1508', 1116, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095108', 'Tower 1', 15, 8),
('Tower 1 #1601', 1025, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095109', 'Tower 1', 16, 1),
('Tower 1 #1602', 1024, 'Monet', 1288, 1, 0.65, 2, 2, '2009095109', 'Tower 1', 16, 2),
('Tower 1 #1603', 1023, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095111', 'Tower 1', 16, 3),
('Tower 1 #1604', 1022, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095112', 'Tower 1', 16, 4),
('Tower 1 #1605', 1082, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095113', 'Tower 1', 16, 5),
('Tower 1 #1606', 1081, 'Monet', 1288, 1, 0.67, 2, 2, '2009095114', 'Tower 1', 16, 6),
('Tower 1 #1607', 1080, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095115', 'Tower 1', 16, 7),
('Tower 1 #1608', 1079, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095116', 'Tower 1', 16, 8),
('Tower 1 #1701', 1004, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095117', 'Tower 1', 17, 1),
('Tower 1 #1702', 1003, 'Monet', 1288, 1, 0.65, 2, 2, '2009095118', 'Tower 1', 17, 2),
('Tower 1 #1703', 1017, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095119', 'Tower 1', 17, 3),
('Tower 1 #1704', 1001, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095120', 'Tower 1', 17, 4),
('Tower 1 #1705', 1091, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095121', 'Tower 1', 17, 5),
('Tower 1 #1706', 1092, 'Monet', 1288, 1, 0.67, 2, 2, '2009095122', 'Tower 1', 17, 6),
('Tower 1 #1707', 1084, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095123', 'Tower 1', 17, 7),
('Tower 1 #1708', 1083, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095124', 'Tower 1', 17, 8),
('Tower 1 #1801', 1008, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095125', 'Tower 1', 18, 1),
('Tower 1 #1802', 1007, 'Monet', 1288, 1, 0.65, 2, 2, '2009095125', 'Tower 1', 18, 2),
('Tower 1 #1803', 1006, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095126', 'Tower 1', 18, 3),
('Tower 1 #1804', 1005, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095127', 'Tower 1', 18, 4),
('Tower 1 #1805', 1090, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095128', 'Tower 1', 18, 5),
('Tower 1 #1806', 1089, 'Monet', 1288, 1, 0.67, 2, 2, '2009095129', 'Tower 1', 18, 6),
('Tower 1 #1807', 1088, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095130', 'Tower 1', 18, 7),
('Tower 1 #1808', 1087, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095131', 'Tower 1', 18, 8),
('Tower 1 #1901', 1016, 'Rembrandt', 2315, 2, 0.88, 3, 2.5, '2009095132', 'Tower 1', 19, 1),
('Tower 1 #1902', 1015, 'Monet', 1288, 1, 0.65, 2, 2, '2009095132', 'Tower 1', 19, 2),
('Tower 1 #1903', 1014, 'Renoir', 1555, 1, 0.67, 2, 2, '2009095132', 'Tower 1', 19, 3),
('Tower 1 #1904', 1013, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095134', 'Tower 1', 19, 4),
('Tower 1 #1905', 1094, 'Van Gogh', 1729, 2, 0.73, 2, 2, '2009095135', 'Tower 1', 19, 5),
('Tower 1 #1906', 1093, 'Monet', 1288, 1, 0.67, 2, 2, '2009095136', 'Tower 1', 19, 6),
('Tower 1 #1907', 1085, 'Renoir', 1555, 1, 0.65, 2, 2, '2009095137', 'Tower 1', 19, 7),
('Tower 1 #1908', 1086, 'Cezanne', 1642, 2, 0.73, 2, 2, '2009095137', 'Tower 1', 19, 8),
('Tower 2 # 109', 2020, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096001', 'Tower 2', 1, 9),
('Tower 2 # 110', 2113, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096002', 'Tower 2', 1, 10),
('Tower 2 # 111', 2111, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096003', 'Tower 2', 1, 11),
('Tower 2 # 112', 2112, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096004', 'Tower 2', 1, 12),
('Tower 2 # 115', 0, 'Renoir', 1555, 1, 0, 2, 2, '', 'Tower 2', 1, 15),
('Tower 2 # 209', 2009, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096005', 'Tower 2', 2, 9),
('Tower 2 # 210', 2005, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096006', 'Tower 2', 2, 10),
('Tower 2 # 211', 2006, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096007', 'Tower 2', 2, 11),
('Tower 2 # 212', 2095, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096008', 'Tower 2', 2, 12),
('Tower 2 # 214', 2050, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096009', 'Tower 2', 2, 14),
('Tower 2 # 215', 2096, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096010', 'Tower 2', 2, 15),
('Tower 2 # 216', 2097, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096011', 'Tower 2', 2, 16),
('Tower 2 # 217', 2068, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096012', 'Tower 2', 2, 17),
('Tower 2 # 309', 2018, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096013', 'Tower 2', 3, 9),
('Tower 2 # 310', 2003, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096014', 'Tower 2', 3, 10),
('Tower 2 # 311', 2004, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096015', 'Tower 2', 3, 11),
('Tower 2 # 312', 2125, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096016', 'Tower 2', 3, 12),
('Tower 2 # 314', 2049, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096017', 'Tower 2', 3, 14),
('Tower 2 # 315', 2092, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096018', 'Tower 2', 3, 15),
('Tower 2 # 316', 2093, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096019', 'Tower 2', 3, 16),
('Tower 2 # 317', 2069, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096020', 'Tower 2', 3, 17),
('Tower 2 # 409', 2017, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096021', 'Tower 2', 4, 9),
('Tower 2 # 410', 2001, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096022', 'Tower 2', 4, 10),
('Tower 2 # 411', 2002, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096023', 'Tower 2', 4, 11),
('Tower 2 # 412', 2126, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096024', 'Tower 2', 4, 12),
('Tower 2 # 414', 2048, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096025', 'Tower 2', 4, 14),
('Tower 2 # 415', 2090, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096026', 'Tower 2', 4, 15),
('Tower 2 # 416', 2091, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096027', 'Tower 2', 4, 16),
('Tower 2 # 417', 2070, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096028', 'Tower 2', 4, 17),
('Tower 2 # 509', 2016, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096029', 'Tower 2', 5, 9),
('Tower 2 # 510', 2108, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096030', 'Tower 2', 5, 10),
('Tower 2 # 511', 2107, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096031', 'Tower 2', 5, 11),
('Tower 2 # 512', 2127, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096032', 'Tower 2', 5, 12),
('Tower 2 # 514', 2047, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096033', 'Tower 2', 5, 14),
('Tower 2 # 515', 2088, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096034', 'Tower 2', 5, 15),
('Tower 2 # 516', 2089, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096035', 'Tower 2', 5, 16),
('Tower 2 # 517', 2071, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096036', 'Tower 2', 5, 17),
('Tower 2 # 609', 2015, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096037', 'Tower 2', 6, 9),
('Tower 2 # 610', 2110, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096038', 'Tower 2', 6, 10),
('Tower 2 # 611', 2109, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096039', 'Tower 2', 6, 11),
('Tower 2 # 612', 2128, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096040', 'Tower 2', 6, 12),
('Tower 2 # 614', 2046, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096041', 'Tower 2', 6, 14),
('Tower 2 # 615', 2086, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096042', 'Tower 2', 6, 15),
('Tower 2 # 616', 2087, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096043', 'Tower 2', 6, 16),
('Tower 2 # 617', 2072, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096044', 'Tower 2', 6, 17),
('Tower 2 # 709', 2014, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096045', 'Tower 2', 7, 9),
('Tower 2 # 710', 2105, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096046', 'Tower 2', 7, 10),
('Tower 2 # 711', 2106, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096047', 'Tower 2', 7, 11),
('Tower 2 # 712', 2129, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096048', 'Tower 2', 7, 12),
('Tower 2 # 714', 2094, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096049', 'Tower 2', 7, 14),
('Tower 2 # 715', 2084, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096050', 'Tower 2', 7, 15),
('Tower 2 # 716', 2085, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096051', 'Tower 2', 7, 16),
('Tower 2 # 717', 2073, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096052', 'Tower 2', 7, 17),
('Tower 2 # 809', 2013, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096053', 'Tower 2', 8, 9),
('Tower 2 # 810', 2136, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096054', 'Tower 2', 8, 10),
('Tower 2 # 811', 2104, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096055', 'Tower 2', 8, 11),
('Tower 2 # 812', 2130, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096056', 'Tower 2', 8, 12),
('Tower 2 # 814', 2044, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096057', 'Tower 2', 8, 14),
('Tower 2 # 815', 2082, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096058', 'Tower 2', 8, 15),
('Tower 2 # 816', 2083, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096059', 'Tower 2', 8, 16),
('Tower 2 # 817', 2074, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096060', 'Tower 2', 8, 17),
('Tower 2 # 909', 2012, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096061', 'Tower 2', 9, 9),
('Tower 2 # 910', 2138, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096062', 'Tower 2', 9, 10),
('Tower 2 # 911', 2137, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096063', 'Tower 2', 9, 11),
('Tower 2 # 912', 2131, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096064', 'Tower 2', 9, 12),
('Tower 2 # 914', 2043, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096065', 'Tower 2', 9, 14),
('Tower 2 # 915', 2080, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096066', 'Tower 2', 9, 15),
('Tower 2 # 916', 2081, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096067', 'Tower 2', 9, 16),
('Tower 2 # 917', 2075, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096068', 'Tower 2', 9, 17),
('Tower 2 #1009', 2011, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096069', 'Tower 2', 10, 9),
('Tower 2 #1010', 2140, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096070', 'Tower 2', 10, 10),
('Tower 2 #1011', 2139, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096071', 'Tower 2', 10, 11),
('Tower 2 #1012', 2132, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096072', 'Tower 2', 10, 12),
('Tower 2 #1014', 2042, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096073', 'Tower 2', 10, 14),
('Tower 2 #1015', 2078, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096074', 'Tower 2', 10, 15),
('Tower 2 #1016', 2079, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096075', 'Tower 2', 10, 16),
('Tower 2 #1017', 2029, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096076', 'Tower 2', 10, 17),
('Tower 2 #1109', 2010, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096077', 'Tower 2', 11, 9),
('Tower 2 #1110', 2142, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096078', 'Tower 2', 11, 10),
('Tower 2 #1111', 2141, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096079', 'Tower 2', 11, 11),
('Tower 2 #1112', 2133, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096080', 'Tower 2', 11, 12),
('Tower 2 #1114', 2041, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096081', 'Tower 2', 11, 14),
('Tower 2 #1115', 2076, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096082', 'Tower 2', 11, 15),
('Tower 2 #1116', 2077, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096083', 'Tower 2', 11, 16),
('Tower 2 #1117', 2030, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096084', 'Tower 2', 11, 17),
('Tower 2 #1209', 2009, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096085', 'Tower 2', 12, 9),
('Tower 2 #1210', 2144, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096086', 'Tower 2', 12, 10),
('Tower 2 #1211', 2143, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096087', 'Tower 2', 12, 11),
('Tower 2 #1212', 2134, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096088', 'Tower 2', 12, 12),
('Tower 2 #1214', 2045, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096089', 'Tower 2', 12, 14),
('Tower 2 #1215', 2061, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096090', 'Tower 2', 12, 15),
('Tower 2 #1216', 2062, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096091', 'Tower 2', 12, 16),
('Tower 2 #1217', 2031, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096092', 'Tower 2', 12, 17),
('Tower 2 #1409', 2008, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096093', 'Tower 2', 14, 9),
('Tower 2 #1410', 2007, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096094', 'Tower 2', 14, 10),
('Tower 2 #1411', 2145, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096095', 'Tower 2', 14, 11),
('Tower 2 #1412', 2135, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096096', 'Tower 2', 14, 12),
('Tower 2 #1414', 2067, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096097', 'Tower 2', 14, 14),
('Tower 2 #1415', 2059, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096098', 'Tower 2', 14, 15),
('Tower 2 #1416', 2060, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096099', 'Tower 2', 14, 16),
('Tower 2 #1417', 2032, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096100', 'Tower 2', 14, 17),
('Tower 2 #1509', 2117, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096101', 'Tower 2', 15, 9),
('Tower 2 #1510', 2118, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096102', 'Tower 2', 15, 10),
('Tower 2 #1511', 2103, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096103', 'Tower 2', 15, 11),
('Tower 2 #1512', 2028, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096104', 'Tower 2', 15, 12),
('Tower 2 #1514', 2066, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096105', 'Tower 2', 15, 14),
('Tower 2 #1515', 2057, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096106', 'Tower 2', 15, 15),
('Tower 2 #1516', 2058, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096107', 'Tower 2', 15, 16),
('Tower 2 #1517', 2033, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096108', 'Tower 2', 15, 17),
('Tower 2 #1609', 2116, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096109', 'Tower 2', 16, 9),
('Tower 2 #1610', 2123, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096110', 'Tower 2', 16, 10),
('Tower 2 #1611', 2124, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096111', 'Tower 2', 16, 11),
('Tower 2 #1612', 2027, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096112', 'Tower 2', 16, 12),
('Tower 2 #1614', 2065, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096113', 'Tower 2', 16, 14),
('Tower 2 #1615', 2055, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096114', 'Tower 2', 16, 15),
('Tower 2 #1616', 2056, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096115', 'Tower 2', 16, 16),
('Tower 2 #1617', 2034, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096116', 'Tower 2', 16, 17),
('Tower 2 #1709', 2115, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096117', 'Tower 2', 17, 9),
('Tower 2 #1710', 2121, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096118', 'Tower 2', 17, 10),
('Tower 2 #1711', 2122, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096119', 'Tower 2', 17, 11),
('Tower 2 #1712', 2026, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096120', 'Tower 2', 17, 12),
('Tower 2 #1714', 2064, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096121', 'Tower 2', 17, 14),
('Tower 2 #1715', 2053, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096122', 'Tower 2', 17, 15),
('Tower 2 #1716', 2054, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096123', 'Tower 2', 17, 16),
('Tower 2 #1717', 2035, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096124', 'Tower 2', 17, 17),
('Tower 2 #1809', 2114, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096125', 'Tower 2', 18, 9),
('Tower 2 #1810', 2119, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096126', 'Tower 2', 18, 10),
('Tower 2 #1811', 2120, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096127', 'Tower 2', 18, 11),
('Tower 2 #1812', 2025, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096128', 'Tower 2', 18, 12),
('Tower 2 #1814', 2063, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096129', 'Tower 2', 18, 14),
('Tower 2 #1815', 2051, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096129', 'Tower 2', 18, 15),
('Tower 2 #1816', 2052, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096131', 'Tower 2', 18, 16),
('Tower 2 #1817', 2036, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096132', 'Tower 2', 18, 17),
('Tower 2 #1909', 2021, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096133', 'Tower 2', 19, 9),
('Tower 2 #1910', 2022, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096133', 'Tower 2', 19, 10),
('Tower 2 #1911', 2023, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096133', 'Tower 2', 19, 11),
('Tower 2 #1912', 2024, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096135', 'Tower 2', 19, 12),
('Tower 2 #1914', 2037, 'Van Gogh', 1729, 2, 0.725, 2, 2, '2009096136', 'Tower 2', 19, 14),
('Tower 2 #1915', 2038, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096137', 'Tower 2', 19, 15),
('Tower 2 #1916', 2039, 'Renoir', 1555, 1, 0.604, 2, 2, '2009096138', 'Tower 2', 19, 16),
('Tower 2 #1917', 2040, 'Rembrandt', 2315, 2, 0.924, 3, 2.5, '2009096139', 'Tower 2', 19, 17);

-- --------------------------------------------------------

--
-- Table structure for table `UserUnit`
--

CREATE TABLE IF NOT EXISTS `UserUnit` (
  `userunitid` mediumint(9) NOT NULL,
  `unit` varchar(19) DEFAULT NULL,
  `owner` varchar(5) DEFAULT NULL,
  `voter` varchar(5) DEFAULT NULL,
  `lastname` varchar(30) DEFAULT NULL,
  `firstname` varchar(30) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `cellphone` varchar(50) DEFAULT NULL,
  `homephone` varchar(50) DEFAULT NULL,
  `emergency` varchar(80) DEFAULT NULL,
  `unitwatcher` varchar(80) NOT NULL,
  `address` varchar(50) DEFAULT NULL,
  `citystatezip` varchar(50) DEFAULT NULL,
  `userid` varchar(15) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=27520 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `WaitList`
--

CREATE TABLE IF NOT EXISTS `WaitList` (
  `wlid` mediumint(9) NOT NULL,
  `type` text NOT NULL,
  `unit` varchar(30) NOT NULL,
  `names` text NOT NULL,
  `date` date DEFAULT NULL,
  `slipid` varchar(4) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `userid` varchar(15) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=335 DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `RateMaster`
--
ALTER TABLE `RateMaster`
  ADD PRIMARY KEY (`class`),
  ADD UNIQUE KEY `class` (`class`);

--
-- Indexes for table `RunBlob`
--
ALTER TABLE `RunBlob`
  ADD PRIMARY KEY (`dataid`),
  ADD KEY `logid` (`logid`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `RunLog`
--
ALTER TABLE `RunLog`
  ADD PRIMARY KEY (`logid`),
  ADD UNIQUE KEY `logid` (`logid`);

--
-- Indexes for table `SlipMaster`
--
ALTER TABLE `SlipMaster`
  ADD PRIMARY KEY (`slipid`),
  ADD UNIQUE KEY `slipid` (`slipid`);

--
-- Indexes for table `Slips`
--
ALTER TABLE `Slips`
  ADD PRIMARY KEY (`slid`),
  ADD KEY `slipid` (`slipid`),
  ADD KEY `unit` (`unit`);

--
-- Indexes for table `UnitMaster`
--
ALTER TABLE `UnitMaster`
  ADD UNIQUE KEY `Unit` (`unit`);

--
-- Indexes for table `UserUnit`
--
ALTER TABLE `UserUnit`
  ADD PRIMARY KEY (`userunitid`);

--
-- Indexes for table `WaitList`
--
ALTER TABLE `WaitList`
  ADD PRIMARY KEY (`wlid`),
  ADD KEY `unit` (`unit`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `RunBlob`
--
ALTER TABLE `RunBlob`
  MODIFY `dataid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `RunLog`
--
ALTER TABLE `RunLog`
  MODIFY `logid` mediumint(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Slips`
--
ALTER TABLE `Slips`
  MODIFY `slid` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2905;
--
-- AUTO_INCREMENT for table `UserUnit`
--
ALTER TABLE `UserUnit`
  MODIFY `userunitid` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27520;
--
-- AUTO_INCREMENT for table `WaitList`
--
ALTER TABLE `WaitList`
  MODIFY `wlid` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=335;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
