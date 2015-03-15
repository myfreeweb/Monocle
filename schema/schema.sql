/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channel_entries` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` bigint(11) DEFAULT NULL,
  `entry_id` bigint(11) DEFAULT NULL,
  `entry_published` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channel_sources` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` bigint(11) DEFAULT NULL,
  `feed_id` bigint(11) DEFAULT NULL,
  `filter` text,
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feeds` (`channel_id`,`feed_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channels` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('default','feeds') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entries` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `feed_id` bigint(11) DEFAULT NULL,
  `url` text,
  `name` text,
  `content` text NOT NULL,
  `like_of_url` text,
  `repost_of_url` text,
  `in_reply_to_url` text,
  `author_name` text NOT NULL,
  `author_url` text NOT NULL,
  `author_photo` text NOT NULL,
  `photo_url` text,
  `video_url` text,
  `audio_url` text,
  `event_start` datetime DEFAULT NULL,
  `event_start_tz_offset` int(11) DEFAULT NULL,
  `event_end` datetime DEFAULT NULL,
  `event_end_tz_offset` int(11) DEFAULT NULL,
  `date_published` datetime NOT NULL,
  `timezone_offset` int(11) NOT NULL,
  `num_likes` int(11) DEFAULT NULL,
  `num_reposts` int(11) DEFAULT NULL,
  `num_comments` int(11) DEFAULT NULL,
  `num_rsvps` int(11) DEFAULT NULL,
  `date_retrieved` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feed_id` (`feed_id`),
  KEY `url` (`feed_id`,`url`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entries_tags` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` bigint(11) DEFAULT NULL,
  `tag` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entry_id` (`entry_id`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feeds` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(190) DEFAULT NULL,
  `name` text NOT NULL,
  `feed_url` text NOT NULL,
  `feed_type` enum('mf2','atom','rss') DEFAULT NULL,
  `homepage_url` text,
  `push_hub_url` text,
  `push_topic_url` text,
  `push_subscribed` tinyint(1) NOT NULL DEFAULT '0',
  `push_last_ping_received` datetime DEFAULT NULL,
  `push_expiration` datetime DEFAULT NULL,
  `last_retrieved` datetime DEFAULT NULL,
  `public` tinyint(4) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `subscriptions_url` varchar(255) DEFAULT NULL,
  `default_timezone` varchar(255) DEFAULT 'America/Los_Angeles',
  `authorization_endpoint` varchar(255) DEFAULT NULL,
  `micropub_endpoint` varchar(255) DEFAULT NULL,
  `micropub_access_token` text,
  `micropub_scope` varchar(255) DEFAULT NULL,
  `token_response` text,
  `micropub_success` tinyint(4) DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
