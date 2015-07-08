CREATE TABLE `user_master` (
`um_id` int(11) NOT NULL AUTO_INCREMENT,
`um_name` varchar(255) NOT NULL,
`um_uuid` varchar(255),

`um_rm_created` DATETIME,
`um_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`um_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `room_master` (
`rm_id` int(11) NOT NULL AUTO_INCREMENT,
`rm_title` varchar(255),
`rm_stat` varchar(20),
`rm_ppl` INTEGER DEFAULT 0,
`rm_max` INTEGER DEFAULT 8,

`rm_creater_id` int(11),
`rm_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`rm_id`),
KEY `index_rm_stat` (`rm_stat`(5))
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE  `room_user` (
`ru_id` int(11) NOT NULL AUTO_INCREMENT,
`ru_um_id` int(11),
`ru_rm_id` int(11),
`ru_money` int(11),

`am_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ru_id`),
UNIQUE KEY `ru_um_id` (`ru_um_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `user_active` (
`ua_id` int(11) NOT NULL AUTO_INCREMENT,
`ua_ru_id` int(11), -- NOT NULL

`ua_time` datetime, -- ユーザーのアクティブチェック
`ua_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ua_id`),
UNIQUE KEY `ua_ru_id` (`ua_ru_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `room_auction` (
`ra_id` int(11) NOT NULL AUTO_INCREMENT,
`ra_rm_id` int(11),
`ra_ma_id` int(11), -- 現在のオークション

`ra_time` datetime, -- オークション系各種処理の開始時間

`ra_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ra_id`),
UNIQUE KEY `ra_rm_id` (`ra_rm_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `monster_auction` (
`ma_id` int(11) NOT NULL AUTO_INCREMENT,
`ma_ra_id` int(11), -- このレコードを使用するroom_auctionレコードの指定

`ma_ru_id` int(11),

`ma_mm_id` int(11),
`ma_price` int(11),
`ma_closeflg` varchar(19),

`ma_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ma_id`),
UNIQUE KEY `ma_ra_id` (`ma_ra_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `monster_master` (
`mm_id` int(11) NOT NULL AUTO_INCREMENT,
`mm_name` varchar(255),
`mm_price` int(11), -- デフォルトの金額

-- その他能力値
PRIMARY KEY(`mm_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- 装備購入シーンテーブル
CREATE TABLE `room_equip` (
`re_id` int(11) NOT NULL AUTO_INCREMENT,
`re_rm_id` int(11),

--`re_time` datetime,

`re_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY(`re_id`),
UNIQUE KEY `re_rm_id` (`re_rm_id`)
);

-- 装備アイテム一覧
CREATE TABLE `item_master` (
`im_id` int(11) NOT NULL AUTO_INCREMENT,
`im_name` varchar(255) DEFAULT NULL,
`im_price` int(11) DEFAULT NULL,
`im_desc` text,
`im_site` int(19) DEFAULT NULL,

`im_type_hu` int(19) DEFAULT NULL, -- ヒューマンが装備可能か
`im_type_be` int(19) DEFAULT NULL, -- 野獣が装備可能か
`im_type_dr` int(19) DEFAULT NULL, -- ドラゴンが装備可能か
`im_type_gi` int(19) DEFAULT NULL, -- 巨大が装備可能か
PRIMARY KEY (`im_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8

-- ユーザー作成モンスターデータ
CREATE TABLE `monster_wrap` (
`mw_id` int(11) NOT NULL AUTO_INCREMENT,
`mw_mm_id` int(11),
`mw_ru_id` int(11),

`mw_wep_id` int(11),
`mw_gua_id` int(11),
`mw_acc_id` int(11),

PRIMARY KEY(`mw_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;


CREATE TABLE `room_tour` (
`rt_id` int(11) NOT NULL AUTO_INCREMENT

);
