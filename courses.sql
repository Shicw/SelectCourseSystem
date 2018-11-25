#
# Structure for table "class"
#

CREATE TABLE `class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `major_id` int(11) NOT NULL DEFAULT '0' COMMENT '专业id',
  `name` varchar(10) NOT NULL DEFAULT '' COMMENT '班级名称',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间(时间戳)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='班级表';

#
# Data for table "class"
#

INSERT INTO `class` VALUES (1,1,'无',0);

#
# Structure for table "course"
#

CREATE TABLE `course` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '课程编号',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '课程名称',
  `teacher_id` int(11) NOT NULL DEFAULT '0' COMMENT '开课教师user_id',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '类别id',
  `day` varchar(6) NOT NULL DEFAULT '' COMMENT '上课时间(周几)',
  `classroom` varchar(20) NOT NULL DEFAULT '' COMMENT '上课地点(教室)',
  `begin_lesson` int(2) NOT NULL DEFAULT '0' COMMENT '开始节数',
  `end_lesson` int(2) NOT NULL DEFAULT '0' COMMENT '结束节数',
  `limit_student_count` int(3) NOT NULL DEFAULT '0' COMMENT '限选人数',
  `real_student_count` int(3) NOT NULL DEFAULT '0' COMMENT '实际已选人数',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间(时间戳)',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间(时间戳)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='课程表';


#
# Structure for table "course_type"
#

CREATE TABLE `course_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '类别名称',
  `description` varchar(255) DEFAULT NULL COMMENT '备注',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间(时间戳)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='课程类型';

#
# Data for table "course_type"
#

INSERT INTO `course_type` VALUES (1,'人文社科类','人文社科类',0),(2,'艺术类','艺术类',0),(3,'自然科学','自然科学',0),(4,'社会科学','社会科学',0);

#
# Structure for table "log"
#

CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL DEFAULT '' COMMENT '操作',
  `user_id` varchar(20) NOT NULL DEFAULT '0' COMMENT '操作者id',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间(时间戳)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8 COMMENT='系统日志表';

#
# Structure for table "major"
#

CREATE TABLE `major` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '专业名称',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间(时间戳)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='专业表';

#
# Data for table "major"
#

INSERT INTO `major` VALUES (1,'无','无',0);

#
# Structure for table "option"
#

CREATE TABLE `option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '配置名称',
  `value` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='配置表';

#
# Data for table "option"
#

INSERT INTO `option` VALUES (1,'select_start_and_stop_time','{\"start_time\":1543128000,\"stop_time\":1544011200}'),(2,'create_start_and_stop_time','{\"start_time\":1542693600,\"stop_time\":1543128000}');

#
# Structure for table "student_course"
#

CREATE TABLE `student_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT '0' COMMENT '课程编号',
  `user_id` varchar(20) NOT NULL DEFAULT '' COMMENT '学生编号',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间(时间戳)',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间(时间戳)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学生选课记录表';

#
# Structure for table "user"
#

CREATE TABLE `user` (
  `id` varchar(20) NOT NULL DEFAULT '' COMMENT '用户编号',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 0:未设置;1:男;2:女',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类别 0:admin;1:学生;2:教师',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `major_id` int(11) NOT NULL DEFAULT '1' COMMENT '专业id',
  `class_id` int(11) NOT NULL DEFAULT '1' COMMENT '班级id',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间(时间戳)',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间(时间戳)',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间(时间戳)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='读者用户表';

#
# Data for table "user"
#

INSERT INTO `user` VALUES ('admin','admin','e10adc3949ba59abbe56e057f20f883e','admin',0,0,'',0,0,1542615000,1543110035,0);
