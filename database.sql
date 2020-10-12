CREATE TABLE `Users` (
  `userid` int(11) NOT NULL auto_increment,
  `firstname` varchar(30) NOT NULL default '',
  `lastname` varchar(30) NOT NULL default '',
  `username` varchar(30) NOT NULL default '',
  `alternatemail` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `Users`
-- 

INSERT INTO `Users` VALUES (1, 'your', 'name', 'admin', 'yourothermail@google.com');