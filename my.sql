CREATE TABLE `user_master` (
  `um_id` int(11) NOT NULL AUTO_INCREMENT,
  `um_name` varchar(255) NOT NULL,
  `um_uuid` varchar(255),
  `um_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`um_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `room_master` (
  `rm_id` int(11) NOT NULL AUTO_INCREMENT,
  `rm_title` varchar(255),
  `rm_stat` varchar(255),
  `rm_ppl` INTEGER,
  `rm_max` INTEGER,
  `rm_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rm_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
