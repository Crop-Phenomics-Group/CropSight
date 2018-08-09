DROP TABLE images;

CREATE TABLE `images` (
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(255) DEFAULT NULL,
  `image` longblob,
  `macaddress` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE permissions;

CREATE TABLE `permissions` (
  `email` varchar(255) NOT NULL,
  `projectid` varchar(255) NOT NULL,
  PRIMARY KEY (`email`,`projectid`),
  KEY `projectid` (`projectid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE pidata;

CREATE TABLE `pidata` (
  `macaddress` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datafile` varchar(255) NOT NULL,
  PRIMARY KEY (`macaddress`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE pisensor;

CREATE TABLE `pisensor` (
  `sensorid` varchar(255) NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `unit` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`sensorid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO pisensor VALUES("cputemp","Device Temperature","Device Temp","?");
INSERT INTO pisensor VALUES("humid","Relative Humidity","Humidity","%");
INSERT INTO pisensor VALUES("light","Relative Light Levels","Light Levels","%");
INSERT INTO pisensor VALUES("soilhumid","Relative Soil Moisture","Soil Moisture","%");
INSERT INTO pisensor VALUES("soiltemp","Soil Temperature","Soil Temp","?");
INSERT INTO pisensor VALUES("temp","Temperature","Temperature","?");

DROP TABLE pistatus;

CREATE TABLE `pistatus` (
  `macaddress` varchar(255) NOT NULL,
  `ipaddress` varchar(255) DEFAULT NULL,
  `internalipaddress` varchar(255) NOT NULL,
  `hostname` varchar(255) DEFAULT NULL,
  `uptime` varchar(255) DEFAULT NULL,
  `lastupdate` varchar(255) DEFAULT NULL,
  `duration` int(255) NOT NULL,
  `projectid` varchar(255) DEFAULT '',
  `cputemp` varchar(255) NOT NULL,
  `lastimage` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`macaddress`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE pistorage;

CREATE TABLE `pistorage` (
  `macaddress` varchar(255) NOT NULL,
  `mountpoint` varchar(255) NOT NULL,
  `total` int(255) DEFAULT NULL,
  `free` int(255) DEFAULT NULL,
  `online` tinyint(1) NOT NULL,
  PRIMARY KEY (`macaddress`,`mountpoint`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE projectgroup;

CREATE TABLE `projectgroup` (
  `groupid` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE projects;

CREATE TABLE `projects` (
  `projectid` varchar(255) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `map` text,
  `projectgroup` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`projectid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE sensorreading;

CREATE TABLE `sensorreading` (
  `macaddress` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sensor` varchar(255) NOT NULL,
  `reading` varchar(255) NOT NULL,
  PRIMARY KEY (`macaddress`,`timestamp`,`sensor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE users;

CREATE TABLE `users` (
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO users VALUES("admin","admin","","1");

