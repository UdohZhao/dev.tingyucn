# 用户表:) 主键id，用户名{手机号码}，密码，类型{0>普通用户，1>签约用户}，状态{0>正常，1>冻结}
CREATE TABLE `user`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户表主键id',
  `username` char(11) NOT NULL COMMENT '用户名{手机号码}',
  `password` char(32) NOT NULL COMMENT '密码',
  `type` tinyint(1) UNSIGNED NOT NULL COMMENT '类型{0>普通用户，1>签约用户}',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态{0>正常，1>冻结}',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 身份认证表:) 主键id，关联用户表主键id，真实姓名，性别{0>女，1>男}，身份证号码，身份证正面图片路径，身份证背面图片路径，个人形象照图片路径，时间，状态{0>待审核，1>审核失败，2>审核成功}
CREATE TABLE `identity_authentication`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '身份认证表主键id',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
  `real_name` varchar(50) NOT NULL COMMENT '真实姓名',
  `sex` tinyint(1) UNSIGNED NOT NULL COMMENT '性别{0>女，1>男}',
  `age` int(11) UNSIGNED NOT NULL COMMENT '年龄',
  `id_card` varchar(20) NOT NULL COMMENT '身份证号码',
  `front_path` varchar(255) NOT NULL COMMENT '身份证正面图片路径',
  `back_path` varchar(255) NOT NULL COMMENT '身份证背面图片路径',
  `figure_path` varchar(255) NOT NULL COMMENT '个人形象照图片路径',
  `ctime` char(10) NOT NULL COMMENT '时间',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态{0>待审核，1>审核失败，2>审核成功}',
  PRIMARY KEY (`id`),
  KEY (`uid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 职业表:) 主键id，名称，排序
CREATE TABLE `profession`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '职业表主键id',
  `cname` varchar(255) NOT NULL COMMENT '名称',
  `sort` tinyint(3) UNSIGNED NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 兴趣表:) 主键id，名称，排序
CREATE TABLE `interest`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '兴趣表主键id',
  `cname` varchar(255) NOT NULL COMMENT '名称',
  `sort` tinyint(3) UNSIGNED NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 用户基本信息表:) 主键id，关联用户表主键id，关联职业表主键id，关联兴趣表主键id，头像，昵称，签名，黑名单用户id（序列化数组），关注用户id（序列化数组），余额，收入，所在城市，绑定支付宝，几几开
CREATE TABLE `userinfo`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户基本信息表主键id',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
  `pid` int(11) UNSIGNED NOT NULL COMMENT '关联职业表主键id',
  `iid` varchar(255)  NOT NULL COMMENT '关联兴趣表主键id(序列化字符串)',
  `head_portrait` varchar(255) NOT NULL COMMENT '头像路径',
  `nickname` varchar(50) NOT NULL COMMENT '昵称',
  `signature` varchar(255) NOT NULL COMMENT '签名',
  `blacklist` varchar(5000) NOT NULL COMMENT '黑名单用户id（序列化数组）',
  `attention` varchar(5000) NOT NULL COMMENT '关注用户id（序列化数组）',
  `balance` decimal(14,2) UNSIGNED NOT NULL COMMENT '余额',
  `earning` decimal(14,2) UNSIGNED NOT NULL COMMENT '收入',
  `city` varchar(20) NOT NULL COMMENT '所在城市',
  `alipay` varchar(255) NOT NULL COMMENT '绑定支付宝',
  `jjk` tinyint(1) UNSIGNED NOT NULL COMMENT '几几开,0为平台默认分成',
  PRIMARY KEY (`id`),
  KEY (`uid`),
  KEY (`pid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 意见反馈表:) 主键id，关联用户表主键id，意见，时间，状态{0>未读，1>已读}
CREATE TABLE `opinion`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '意见反馈表主键id',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
  `content` varchar(255) NOT NULL COMMENT '意见',
  `ctime` char(10) NOT NULL COMMENT '时间',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态{0>未读，1>已读}',
  PRIMARY KEY (`id`),
  KEY (`uid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 账户表:) 主键id，关联用户表主键id，金额，时间，类型{0>充值，1>提现}，状态{0>失败，1>成功}
CREATE TABLE `account`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '账户表主键id',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
  `money` decimal(14,2) UNSIGNED NOT NULL COMMENT '金额',
  `ctime` char(10) NOT NULL COMMENT '时间',
  `type` tinyint(1) UNSIGNED NOT NULL COMMENT '类型{0>充值，1>提现}',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态{0>失败，1>成功}',
  PRIMARY KEY (`id`),
  KEY (`uid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 服务类别表:) 主键id，名称，图标路径，排序，类型（0>线上娱乐，1>线上游戏，2>线下娱乐，3>线下游戏，4>旅游）
CREATE TABLE `service_category`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '服务类别表主键id',
  `cname` varchar(50) NOT NULL COMMENT '名称',
  `icon_path` varchar(255) NOT NULL COMMENT '图标路径',
  `sort` tinyint(3) UNSIGNED NOT NULL COMMENT '排序',
  `charge_mode` varchar(50) NOT NULL COMMENT '计费方式',
  `type` tinyint(1) UNSIGNED NOT NULL COMMENT '类型（0>线上娱乐，1>线上游戏，2>线下娱乐）',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 服务表:) 主键id，关联用户表主键id，关联服务类别表主键id，封面照路径，说明，标价，时间，类型（0>待审核，1>审核失败，2>审核成功），状态（0>上架，1>下架）;
CREATE TABLE `service`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '服务表主键id',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
  `scid` int(11) UNSIGNED NOT NULL COMMENT '关联服务类别表主键id',
  `cover_path` varchar(255) NOT NULL COMMENT '封面照路径',
  `explain` varchar(255) NOT NULL COMMENT '说明',
  `bid_price` decimal(14,2) UNSIGNED NOT NULL COMMENT '标价',
  `ctime` char(10) NOT NULL COMMENT '时间',
  `type` tinyint(1) UNSIGNED NOT NULL COMMENT '类型（0>待审核，1>审核失败，2>审核成功）',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态（0>上架，1>下架）',
  PRIMARY KEY (`id`),
  KEY (`uid`),
  KEY (`scid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 服务评价表:) 主键id，关联服务表主键id，关联用户表主键id，评价，打分，时间
CREATE TABLE `service_estimate`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '服务评价表主键id',
  `sid` int(11) UNSIGNED NOT NULL COMMENT '关联服务表主键id',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
  `order_id` int(11) UNSIGNED NOT NULL COMMENT '关联订单表主键id',
  `estimate` varchar(255) NOT NULL COMMENT '评价',
  `grade` tinyint(1) UNSIGNED NOT NULL COMMENT '评价状态(0>好评，1>中评，2>差评)',
  `ctime` char(10) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY (`sid`),
  KEY (`order_id`),
  KEY (`uid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 服务订单表:) 主键id，关联服务表主键id，关联用户表主键id，计费方式，支付金额，开始时间，结束时间，类型（0>待付款，1>已付款，2>待评价，3>售后），状态（0>暂未开始，1>进行中，2>已结束）
CREATE TABLE `service_indent`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '服务订单表主键id',
  `sid` int(11) UNSIGNED NOT NULL COMMENT '关联服务表主键id',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
  `charge_mode` varchar(50) NOT NULL COMMENT '计费方式',
  `payment_amount` decimal(14,2) UNSIGNED NOT NULL COMMENT '支付金额',
  `start_time` char(10) NOT NULL COMMENT '开始时间',
  `end_time` char(10) NOT NULL COMMENT '结束时间',
  `ctime` char(10) NOT NULL COMMENT '下单时间',
  `serial_number` varchar(50) NOT NULL COMMENT '订单编号',
  `address` varchar(255) COMMENT '服务地点，可为空',
  `reply_status` tinyint(1) UNSIGNED NOT NULL COMMENT '回复状态(0>待确认,1>已确认,2>取消)',
  `type` tinyint(1) UNSIGNED NOT NULL COMMENT '类型（0>待付款，1>已付款，2>待评价，3>售后）' ,
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态（0>暂未开始，1>进行中，2>已结束）' ,
  PRIMARY KEY (`id`),
  KEY (`sid`),
  KEY (`uid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 优惠券表:) 主键id，关联服务类别表主键id，关联用户表主键id（序列化数组），名称，价格，说明，截止时间，状态（0>使用中，1>弃用）
CREATE TABLE `discount_coupon`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '优惠券表主键id',
  `scid` int(11) UNSIGNED NOT NULL COMMENT '关联服务类别表主键id',
  `uids` varchar(5000) NOT NULL COMMENT '关联用户表主键id（序列化数组）' ,
  `cname` varchar(50) NOT NULL COMMENT '名称',
  `price` decimal(14,2) UNSIGNED NOT NULL COMMENT '价格',
  `explain` varchar(255) NOT NULL COMMENT '说明',
  `end_time` char(10) NOT NULL COMMENT '截止日期',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态（0>使用中，1>弃用）',
  PRIMARY KEY (`id`),
  KEY (`scid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 站点配置表:) 主键id，logo路径，名称，百度关键字，百度简介，版权所有，状态{0>开启，1>关闭}
CREATE TABLE `website_config`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '站点配置表主键id',
  `logo_path` varchar(255) NOT NULL COMMENT 'logo路径',
  `cname` varchar(255) NOT NULL COMMENT '名称',
  `baidu_keyword` varchar(255) NOT NULL COMMENT '百度关键字',
  `baidu_intro` varchar(255) NOT NULL COMMENT '百度简介',
  `email` varchar(255) NOT NULL COMMENT '客服邮箱',
  `qq` varchar(255) NOT NULL COMMENT '客服qq',
  `telphones` varchar(20) NOT NULL COMMENT '客服电话',
  `worktime` varchar(255) NOT NULL COMMENT '客服工作时间',
  `weblogo` varchar(255) NOT NULL COMMENT '微信二维码路径',
  `copyright` varchar(255) NOT NULL COMMENT '版权所有',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态{0>开启，1>关闭}',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# banner表:) 主键id，banner路径，排序，状态{显示，隐藏}
CREATE TABLE `banner`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'banner表主键id',
  `banner_path` varchar(255) NOT NULL COMMENT 'banner路径',
  `sort` tinyint(3) UNSIGNED NOT NULL COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态{显示，隐藏}',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 系统消息表:) 主键id，标题，内容，时间
CREATE TABLE `message`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '系统消息表主键id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` varchar(500) NOT NULL COMMENT '内容',
  `ctime` char(10) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 分成表:) 主键id，平台分成，签约用户分成
CREATE TABLE `divide_into`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分成表主键id',
  `platform` tinyint(1) UNSIGNED NOT NULL COMMENT '平台分成',
  `sign_user` tinyint(1) UNSIGNED NOT NULL COMMENT '签约用户分成',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 后台用户表:) 主键id，用户名，密码，时间，状态{0>正常，1>冻结}
CREATE TABLE `admin_user`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '后台用户表主键id',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `ctime` char(10) NOT NULL COMMENT '时间',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态{0>正常，1>冻结}',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 用户视频音频表:) 主键id，关联用户，视频路径，音频路径
CREATE TABLE `user_media`(
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户视频音频表主键id',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
  `video_path` varchar(255) NOT NULL COMMENT '视频路径',
  `audio_path` varchar(255) NOT NULL COMMENT '音频路径',
  PRIMARY KEY (`id`),
  KEY (`uid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;













