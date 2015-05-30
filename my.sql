CREATE TABLE `user_master` (
  `um_id` int(11) NOT NULL AUTO_INCREMENT,
  `um_rm_id` int(11),
  `um_name` varchar(255) NOT NULL,
  `um_uuid` varchar(255),
  
  `um_rm_created` DATETIME, -- ルームを作成した場合最終作成日時が入る
  `um_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`um_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `room_master` (
  `rm_id` int(11) NOT NULL AUTO_INCREMENT,
  `rm_title` varchar(255),
  `rm_stat` varchar(20),
  `rm_ppl` INTEGER DEFAULT 0,
  `rm_max` INTEGER DEFAULT 8,
  
  `rm_creater_id` int(11),  -- ルーム作成者のid
  `rm_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rm_id`),
  KEY `index_rm_stat` (`rm_stat`(5))
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `user_active` (
  `am_id` int(11) NOT NULL AUTO_INCREMENT, -- 不必要？
  `am_um_id` int(11), -- NOT NULL
  `am_rm_id` int(11), -- NOT NULL
  
  `am_active` datetime, -- ユーザーのアクティブチェック
  `am_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`am_id`),
  UNIQUE KEY `ua_ru_id` (`ua_ru_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
