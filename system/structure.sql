-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Host: vcdb.crwlsevgtlap.us-east-1.rds.amazonaws.com
-- Generation Time: Jan 08, 2013 at 05:20 PM
-- Server version: 5.5.20
-- PHP Version: 5.4.6-1ubuntu1.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `vcdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_calls`
--

DROP TABLE IF EXISTS `active_calls`;
CREATE TABLE IF NOT EXISTS `active_calls` (
  `id` int(11) NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `Incoming` tinyint(1) NOT NULL,
  `Duration` int(11) NOT NULL,
  `Number` varchar(20) NOT NULL,
  `Muted` tinyint(1) NOT NULL,
  `ATX_Pkts` int(11) NOT NULL,
  `ARX_Pkts` int(11) NOT NULL,
  `VTX_Pkts` int(11) NOT NULL,
  `VRX_Pkts` int(11) NOT NULL,
  `ATX_Pcnt` decimal(10,0) NOT NULL,
  `ARX_Pcnt` decimal(10,0) NOT NULL,
  `VTX_Pcnt` decimal(10,0) NOT NULL,
  `VRX_Pcnt` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id`,`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `alarms`
--

DROP TABLE IF EXISTS `alarms`;
CREATE TABLE IF NOT EXISTS `alarms` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

DROP TABLE IF EXISTS `api_keys`;
CREATE TABLE IF NOT EXISTS `api_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_key` varchar(255) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `company_id` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_key` (`api_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `id` varchar(255) NOT NULL,
  `created` int(11) NOT NULL,
  `last4` int(11) DEFAULT NULL,
  `updated` int(11) NOT NULL,
  `interval` int(11) NOT NULL,
  `plan_id` varchar(255) NOT NULL DEFAULT 'plan-sdioybs0',
  `active` tinyint(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(15) DEFAULT NULL,
  `state` varchar(10) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `partner` tinyint(4) NOT NULL,
  `customer_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `companies_devices`
--

DROP TABLE IF EXISTS `companies_devices`;
CREATE TABLE IF NOT EXISTS `companies_devices` (
  `id` varchar(100) NOT NULL,
  `company_id` varchar(100) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `hash` varchar(255) NOT NULL DEFAULT 'da39a3ee5e6b4b0d3255bfef95601890afd80709',
  `own` tinyint(1) NOT NULL,
  `verified` tinyint(1) NOT NULL,
  `verify_sent` tinyint(1) NOT NULL,
  `verify_code` varchar(25) DEFAULT NULL,
  `location` varchar(25) DEFAULT NULL,
  `checked` int(11) NOT NULL DEFAULT '0',
  `new_password` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
CREATE TABLE IF NOT EXISTS `devices` (
  `id` varchar(255) NOT NULL,
  `serial` varchar(255) DEFAULT NULL,
  `online` tinyint(4) DEFAULT NULL,
  `in_call` tinyint(4) DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT '0',
  `update` tinyint(4) DEFAULT NULL,
  `updated` int(11) NOT NULL DEFAULT '0',
  `added` int(11) NOT NULL,
  `name` varchar(25) DEFAULT NULL,
  `make` varchar(25) DEFAULT NULL,
  `model` varchar(25) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `type` varchar(25) DEFAULT NULL,
  `updating` int(11) NOT NULL DEFAULT '0',
  `licensekey` varchar(255) DEFAULT NULL,
  `outgoing_call_bandwidth` int(11) DEFAULT NULL,
  `incoming_call_bandwidth` int(11) DEFAULT NULL,
  `outgoing_total_bandwidth` int(11) DEFAULT NULL,
  `incoming_total_bandwidth` int(11) DEFAULT NULL,
  `auto_bandwidth` varchar(3) DEFAULT NULL,
  `max_calltime` decimal(11,0) DEFAULT NULL,
  `max_redials` int(11) DEFAULT NULL,
  `auto_answer` varchar(3) DEFAULT NULL,
  `auto_answer_mute` varchar(3) DEFAULT NULL,
  `auto_answer_multiway` varchar(3) DEFAULT NULL,
  `audio_codecs` text NOT NULL,
  `audio_active_microphone` varchar(255) NOT NULL,
  `telepresence` varchar(3) NOT NULL,
  `camera_lock` varchar(3) NOT NULL,
  `camera_far_control` varchar(25) NOT NULL,
  `camera_far_set_preset` varchar(25) NOT NULL,
  `camera_far_use_preset` varchar(25) NOT NULL,
  `active_microphone_volume` varchar(10) NOT NULL DEFAULT '0',
  `line_in_volume` varchar(10) NOT NULL DEFAULT '0',
  `audio_mute_device` varchar(10) NOT NULL DEFAULT 'all',
  `video_call_audio_output` varchar(10) NOT NULL DEFAULT '0',
  `voice_call_audio_output` varchar(10) NOT NULL DEFAULT '0',
  `line_out_treble` int(11) NOT NULL DEFAULT '0',
  `line_out_bass` int(11) NOT NULL DEFAULT '0',
  `ring_tone_volume` int(11) NOT NULL DEFAULT '0',
  `dtmf_tone_volume` int(11) NOT NULL DEFAULT '0',
  `status_tone_volume` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial` (`serial`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `devices_alarms`
--

DROP TABLE IF EXISTS `devices_alarms`;
CREATE TABLE IF NOT EXISTS `devices_alarms` (
  `device_id` varchar(255) NOT NULL,
  `alarm_id` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`device_id`,`alarm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `devices_history`
--

DROP TABLE IF EXISTS `devices_history`;
CREATE TABLE IF NOT EXISTS `devices_history` (
  `device_id` varchar(255) NOT NULL,
  `Id` int(255) NOT NULL,
  `Conf` int(255) DEFAULT NULL,
  `LocalName` varchar(255) DEFAULT NULL,
  `LocalNumber` varchar(255) DEFAULT NULL,
  `RemoteName` varchar(255) DEFAULT NULL,
  `RemoteNumber` varchar(255) DEFAULT NULL,
  `DialedDigits` varchar(255) DEFAULT NULL,
  `StartTime` datetime DEFAULT NULL,
  `EndTime` datetime DEFAULT NULL,
  `Duration` int(11) DEFAULT NULL,
  `Direction` varchar(255) DEFAULT NULL,
  `Protocol` varchar(255) DEFAULT NULL,
  `Security` varchar(255) DEFAULT NULL,
  `ReqKibps` varchar(255) DEFAULT NULL,
  `ActKibps` varchar(255) DEFAULT NULL,
  `TXVid` varchar(255) DEFAULT NULL,
  `TXAud` varchar(255) DEFAULT NULL,
  `TXRes` varchar(255) DEFAULT NULL,
  `RXVid` varchar(255) DEFAULT NULL,
  `RXAud` varchar(255) DEFAULT NULL,
  `RXRes` varchar(255) DEFAULT NULL,
  `TXPres` varchar(255) DEFAULT NULL,
  `RXPres` varchar(255) DEFAULT NULL,
  `PresFmt` varchar(255) DEFAULT NULL,
  `TermCode` varchar(255) DEFAULT NULL,
  `TxV1PctLoss` decimal(10,0) DEFAULT NULL,
  `RxV1PctLoss` decimal(10,0) DEFAULT NULL,
  `TxV1PktsLost` varchar(255) DEFAULT NULL,
  `RxV1PktsLost` varchar(255) DEFAULT NULL,
  `TxV1AvgJitter` varchar(255) DEFAULT NULL,
  `RxV1AvgJitter` varchar(255) DEFAULT NULL,
  `TxV1MaxJitter` varchar(255) DEFAULT NULL,
  `RxV1MaxJitter` varchar(255) DEFAULT NULL,
  `TxA1PctLoss` decimal(10,0) DEFAULT NULL,
  `RxA1PctLoss` decimal(10,0) DEFAULT NULL,
  `TxA1PktsLost` varchar(255) DEFAULT NULL,
  `RxA1PktsLost` varchar(255) DEFAULT NULL,
  `TxA1AvgJitter` varchar(255) DEFAULT NULL,
  `RxA1AvgJitter` varchar(255) DEFAULT NULL,
  `TxA1MaxJitter` varchar(255) DEFAULT NULL,
  `RxA1MaxJitter` varchar(255) DEFAULT NULL,
  `TxV2PctLoss` decimal(10,0) DEFAULT NULL,
  `RxV2PctLoss` decimal(10,0) DEFAULT NULL,
  `TxV2PktsLost` varchar(255) DEFAULT NULL,
  `RxV2PktsLost` varchar(255) DEFAULT NULL,
  `TxV2AvgJitter` varchar(255) DEFAULT NULL,
  `RxV2AvgJitter` varchar(255) DEFAULT NULL,
  `TxV2MaxJitter` varchar(255) DEFAULT NULL,
  `RxV2MaxJitter` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`device_id`,`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `devices_online`
--

DROP TABLE IF EXISTS `devices_online`;
CREATE TABLE IF NOT EXISTS `devices_online` (
  `device_id` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `online` tinyint(1) NOT NULL,
  PRIMARY KEY (`device_id`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `edits`
--

DROP TABLE IF EXISTS `edits`;
CREATE TABLE IF NOT EXISTS `edits` (
  `id` varchar(255) NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `verb` varchar(10) NOT NULL,
  `object` varchar(10) NOT NULL,
  `target` varchar(16) NOT NULL,
  `details` varchar(255) NOT NULL,
  `completed` tinyint(1) NOT NULL,
  `added` int(11) NOT NULL,
  `by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

DROP TABLE IF EXISTS `levels`;
CREATE TABLE IF NOT EXISTS `levels` (
  `id` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `levels_permissions`
--

DROP TABLE IF EXISTS `levels_permissions`;
CREATE TABLE IF NOT EXISTS `levels_permissions` (
  `level_id` varchar(255) NOT NULL,
  `permission` varchar(255) NOT NULL,
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `setting` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `updater_log`
--

DROP TABLE IF EXISTS `updater_log`;
CREATE TABLE IF NOT EXISTS `updater_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `worker_id` varchar(255) NOT NULL,
  `type` varchar(25) NOT NULL,
  `time` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `detail` text,
  `update_time` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1159961 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `level` varchar(255) NOT NULL,
  `sesshash` varchar(255) NOT NULL,
  `as` varchar(255) NOT NULL,
  `timezone` varchar(30) NOT NULL DEFAULT 'UTC',
  `created` int(11) NOT NULL,
  `last_login` int(11) NOT NULL DEFAULT '0',
  `registered` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_alarms`
--

DROP TABLE IF EXISTS `users_alarms`;
CREATE TABLE IF NOT EXISTS `users_alarms` (
  `user_id` varchar(255) NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `alarm_id` varchar(255) NOT NULL,
  `notified` int(11) NOT NULL,
  `enabled` tinyint(4) NOT NULL,
  PRIMARY KEY (`user_id`,`device_id`,`alarm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_companies`
--

DROP TABLE IF EXISTS `users_companies`;
CREATE TABLE IF NOT EXISTS `users_companies` (
  `user_id` varchar(255) NOT NULL,
  `company_id` varchar(255) NOT NULL,
  `added` int(11) NOT NULL,
  `own` tinyint(4) NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`,`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
