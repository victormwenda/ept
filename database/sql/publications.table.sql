CREATE TABLE `publications` (
  `publication_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text,
  `file_name` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `added_on` datetime NOT NULL,
  `status` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`publication_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1