-- MySQL dump 10.17  Distrib 10.3.15-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: lifelog
-- ------------------------------------------------------
-- Server version	10.3.15-MariaDB-1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `blog`
--

DROP TABLE IF EXISTS `blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog` (
  `blog_id` int(18) NOT NULL AUTO_INCREMENT,
  `created_by` int(13) NOT NULL,
  `title` varchar(300) NOT NULL,
  `blog_content` longblob NOT NULL,
  `blog_created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `blog_image` blob NOT NULL,
  `views` int(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`blog_id`),
  UNIQUE KEY `title` (`title`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog`
--

LOCK TABLES `blog` WRITE;
/*!40000 ALTER TABLE `blog` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `comment_id` int(20) NOT NULL AUTO_INCREMENT,
  `comment_by` int(13) NOT NULL,
  `for_blog` int(18) NOT NULL,
  `comment_data` varchar(3000) NOT NULL,
  `commented_on` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`comment_id`),
  KEY `comment_by` (`comment_by`),
  KEY `for_blog` (`for_blog`),
  CONSTRAINT `comment_by` FOREIGN KEY (`comment_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `for_blog` FOREIGN KEY (`for_blog`) REFERENCES `blog` (`blog_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dbaccesscontrollist`
--

DROP TABLE IF EXISTS `dbaccesscontrollist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dbaccesscontrollist` (
  `role` varchar(40) NOT NULL,
  `action` varchar(40) NOT NULL,
  `resource` varchar(40) NOT NULL,
  PRIMARY KEY (`role`,`action`,`resource`),
  KEY `resource` (`resource`,`action`),
  CONSTRAINT `dbaccesscontrollist_ibfk_1` FOREIGN KEY (`role`) REFERENCES `dbrole` (`role`),
  CONSTRAINT `dbaccesscontrollist_ibfk_2` FOREIGN KEY (`resource`, `action`) REFERENCES `dbaction` (`resource`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dbaccesscontrollist`
--

LOCK TABLES `dbaccesscontrollist` WRITE;
/*!40000 ALTER TABLE `dbaccesscontrollist` DISABLE KEYS */;
/*!40000 ALTER TABLE `dbaccesscontrollist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dbaction`
--

DROP TABLE IF EXISTS `dbaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dbaction` (
  `resource` varchar(40) NOT NULL,
  `action` varchar(40) NOT NULL,
  PRIMARY KEY (`resource`,`action`),
  CONSTRAINT `dbaction_ibfk_1` FOREIGN KEY (`resource`) REFERENCES `dbresource` (`resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dbaction`
--

LOCK TABLES `dbaction` WRITE;
/*!40000 ALTER TABLE `dbaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `dbaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dbresource`
--

DROP TABLE IF EXISTS `dbresource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dbresource` (
  `resource` varchar(40) NOT NULL,
  PRIMARY KEY (`resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dbresource`
--

LOCK TABLES `dbresource` WRITE;
/*!40000 ALTER TABLE `dbresource` DISABLE KEYS */;
/*!40000 ALTER TABLE `dbresource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dbrole`
--

DROP TABLE IF EXISTS `dbrole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dbrole` (
  `role` varchar(40) NOT NULL,
  `description` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dbrole`
--

LOCK TABLES `dbrole` WRITE;
/*!40000 ALTER TABLE `dbrole` DISABLE KEYS */;
INSERT INTO `dbrole` VALUES ('Admin','Have full Access'),('Guest','Have Limited Access'),('Registerd User','Have Granted Access');
/*!40000 ALTER TABLE `dbrole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tagblog`
--

DROP TABLE IF EXISTS `tagblog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tagblog` (
  `tag_i` varchar(50) NOT NULL,
  `blog_i` int(20) NOT NULL,
  KEY `blog` (`blog_i`),
  KEY `tag` (`tag_i`),
  CONSTRAINT `blog_i` FOREIGN KEY (`blog_i`) REFERENCES `blog` (`blog_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tagblog`
--

LOCK TABLES `tagblog` WRITE;
/*!40000 ALTER TABLE `tagblog` DISABLE KEYS */;
/*!40000 ALTER TABLE `tagblog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(13) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(75) NOT NULL,
  `verification` tinyint(1) NOT NULL DEFAULT 0,
  `role` varchar(20) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `profile_pic` longblob DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'lifelog'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-08-21 19:38:59
