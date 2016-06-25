-- MySQL dump 10.13  Distrib 5.7.12, for osx10.10 (x86_64)
--
-- Host: localhost    Database: artifactory
-- ------------------------------------------------------
-- Server version	5.7.12

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
-- Table structure for table `deployhistory`
--
use artifactory;

DROP TABLE IF EXISTS `deployhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deployhistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` int(11) DEFAULT NULL,
  `hostsdeployed` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `0result` tinyint(1) DEFAULT NULL,
  `0tag` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `1result` tinyint(1) DEFAULT NULL,
  `1tag` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `2result` tinyint(1) DEFAULT NULL,
  `2tag` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `3result` tinyint(1) DEFAULT NULL,
  `3tag` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `4result` tinyint(1) DEFAULT NULL,
  `4tag` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `5result` tinyint(1) DEFAULT NULL,
  `5tag` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `6result` tinyint(1) DEFAULT NULL,
  `6tag` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `7result` tinyint(1) DEFAULT NULL,
  `7tag` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `8result` tinyint(1) DEFAULT NULL,
  `8tag` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `9result` tinyint(1) DEFAULT NULL,
  `9tag` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deployhistory`
--

LOCK TABLES `deployhistory` WRITE;
/*!40000 ALTER TABLE `deployhistory` DISABLE KEYS */;
INSERT INTO `deployhistory` VALUES (67,293,'192.168.32.101',1,'jenkins-0-deploy-job-65',1,'jenkins-1-host-verify-65',1,'jenkins-2-api-test-39',1,'jenkins-3-api-test-37',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-22 09:44:54'),(68,293,'192.168.32.103',1,'jenkins-0-deploy-job-66',1,'jenkins-1-host-verify-66',1,'jenkins-2-api-test-39',1,'jenkins-3-api-test-37',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-22 09:44:54'),(69,293,'192.168.32.101',1,'jenkins-0-deploy-job-67',1,'jenkins-1-host-verify-67',1,'jenkins-2-api-test-40',1,'jenkins-3-api-test-38',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-23 09:36:20'),(70,293,'192.168.32.101',1,'jenkins-0-deploy-job-71',1,'jenkins-1-host-verify-71',1,'jenkins-2-api-test-44',1,'jenkins-3-api-test-42',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-24 03:21:44'),(71,293,'192.168.32.101',1,'jenkins-0-deploy-job-72',1,'jenkins-1-host-verify-72',1,'jenkins-2-api-test-45',1,'jenkins-3-api-test-43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-24 03:38:13'),(72,292,'192.168.32.101',1,'jenkins-0-deploy-job-73',1,'jenkins-1-host-verify-73',1,'jenkins-2-api-test-46',1,'jenkins-3-api-test-44',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-24 03:46:00'),(73,291,'192.168.32.101',1,'jenkins-0-deploy-job-74',1,'jenkins-1-host-verify-74',1,'jenkins-2-api-test-47',1,'jenkins-3-api-test-45',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-24 03:47:22'),(74,291,'192.168.32.101',1,'jenkins-0-deploy-job-75',1,'jenkins-1-host-verify-75',1,'jenkins-2-api-test-47',1,'jenkins-3-api-test-45',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-24 03:47:53'),(75,291,'192.168.32.101',1,'jenkins-0-deploy-job-76',1,'jenkins-1-host-verify-76',1,'jenkins-2-api-test-49',1,'jenkins-3-api-test-47',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-24 03:49:25'),(76,291,'192.168.32.101',1,'jenkins-0-deploy-job-77',1,'jenkins-1-host-verify-77',1,'jenkins-2-api-test-50',1,'jenkins-3-api-test-48',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-24 03:51:15'),(77,291,'192.168.32.101',1,'jenkins-0-deploy-job-78',1,'jenkins-1-host-verify-78',1,'jenkins-2-api-test-51',1,'jenkins-3-api-test-49',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-24 03:52:54'),(78,290,'192.168.32.101',1,'jenkins-0-Deploy-Mars-Production-Environment-1239',0,'jenkins-1-Mars-Deployment-HostVerify-671',0,'jenkins-2-Mars-ProductionTest-API-516',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-24 09:07:09'),(79,290,'192.168.32.103',0,'jenkins-0-Deploy-Mars-Production-Environment-1240',0,'jenkins-1-Mars-Deployment-HostVerify-672',0,'jenkins-2-Mars-ProductionTest-API-516',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-06-24 09:07:09');
/*!40000 ALTER TABLE `deployhistory` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-24 17:13:10
