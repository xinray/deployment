-- MySQL dump 10.13  Distrib 5.5.47, for debian-linux-gnu (x86_64)
--
-- Host: 192.168.32.101    Database: artifactory
-- ------------------------------------------------------
-- Server version	5.5.47-0+deb7u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `artifactory`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `artifactory` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `artifactory`;

--
-- Table structure for table `buildinfo`
--

DROP TABLE IF EXISTS `buildinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buildinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productid` varchar(16) NOT NULL,
  `buildid` varchar(14) NOT NULL,
  `svnhost` varchar(200) NOT NULL,
  `svnrevision` varchar(30) NOT NULL,
  `checksum` varchar(50) DEFAULT NULL,
  `artifactoryhost` varchar(15) NOT NULL,
  `artifactorydir` varchar(200) NOT NULL,
  `archivetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) NOT NULL,
  `deploytime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=257 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deploymenthistory`
--

DROP TABLE IF EXISTS `deploymenthistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deploymenthistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` int(11) NOT NULL,
  `hostsdeployed` varchar(200) NOT NULL,
  `filedistributionresult` tinyint(1) NOT NULL,
  `filedistributionbuildtag` varchar(100) NOT NULL,
  `hostverificationresult` tinyint(1) NOT NULL,
  `hostverificationbuildtag` varchar(100) NOT NULL,
  `apitestresult` tinyint(1) NOT NULL,
  `apitestbuildtag` varchar(100) NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hosts`
--

DROP TABLE IF EXISTS `hosts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hosts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(20) NOT NULL,
  `root` varchar(300) NOT NULL,
  `productid` varchar(16) NOT NULL,
  `env` varchar(30) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testresults`
--

DROP TABLE IF EXISTS `testresults`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testresults` (
  `guid` int(11) NOT NULL,
  `smoketestresult` tinyint(1) DEFAULT NULL,
  `smoketestbuildtag` varchar(100) NOT NULL,
  `uatresult` tinyint(1) DEFAULT NULL,
  `uatbuildtag` varchar(100) NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-05-14 19:51:36