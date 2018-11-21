

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `hlhjlive_channel` */

DROP TABLE IF EXISTS `hlhjlive_channel`;

CREATE TABLE `hlhjlive_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel_name` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '频道名字',
  `channel_thumb` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '频道图片',
  `create_at` int(10) unsigned NOT NULL,
  `update_at` int(10) unsigned NOT NULL,
  `tv_source` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '直播、回访源',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='频道列表';

/*Data for the table `hlhjlive_channel` */

insert  into `hlhjlive_channel`(`id`,`channel_name`,`channel_thumb`,`create_at`,`update_at`,`tv_source`) values
(2,'四川广播电视台','/application/hlhjnews/source/upload/20180523/152704319095.jpg',1527043191,1527560440,'rtmp://live.hkstv.hk.lxdns.com/live/hks'),
(3,'成都电视台','/application/hlhjnews/source/upload/20180529/1527557524312.jpg',1527557525,1527560309,'http://service.inke.com/api/live/simpleall?&gender=1&gps_info=116.346844%2C40.090467&loc_info=CN%2C%E5%8C%97%E4%BA%AC%E5%B8%82%2C%E5%8C%97%E4%BA%AC%E5%B8%82&is_new_user=1&lc=0000000000000053&cc=TG0001&cv=IK4.0.30_Iphone&proto=7&idfa=D7D0D5A2-3073-4A74-A72'),
(4,'深圳电视台','/application/hlhjnews/source/upload/20180529/1527557933726.jpg',1527557935,1527560321,'http://service.inke.com/api/live/simpleall?&gender=1&gps_info=116.346844%2C40.090467&loc_info=CN%2C%E5%8C%97%E4%BA%AC%E5%B8%82%2C%E5%8C%97%E4%BA%AC%E5%B8%82&is_new_user=1&lc=0000000000000053&cc=TG0001&cv=IK4.0.30_Iphone&proto=7&idfa=D7D0D5A2-3073-4A74-A72');

/*Table structure for table `hlhjlive_comment` */

DROP TABLE IF EXISTS `hlhjlive_comment`;

CREATE TABLE `hlhjlive_comment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content` text CHARACTER SET utf8mb4 NOT NULL COMMENT '评论内容',
  `user_id` int(11) NOT NULL COMMENT '评论用户',
  `create_at` int(11) DEFAULT NULL,
  `live_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `hlhjlive_comment` */

insert  into `hlhjlive_comment`(`id`,`content`,`user_id`,`create_at`,`live_id`) values
(1,'dsfsdfafdaf',38,1527229649,2),
(2,'dsfsdfafdaf',38,1527232722,3),
(3,'是打发打发撒打发撒的发生的 ',38,1527232730,3),
(4,'是打发打发撒打发撒的发生的 ',38,1527232791,1),
(5,'是打发打发撒打发撒的发生的 ',38,1527232898,1),
(6,'需不下班下班',38,1527234538,1),
(7,'还不呢呢呢呢呢呢哼哼唧唧叫个哈哈健康天涯海角家个哈哈',38,1527234745,1),
(8,'hhhh',38,1527474498,2),
(9,'nsjs',38,1527475583,1),
(10,'nsjs',38,1527475584,1),
(11,'您您呢',38,1527477753,1),
(12,'vvv',38,1527478870,2),
(13,'  追追不追征',38,1527490550,2),
(14,'aa',38,1527492110,2),
(15,'表白斌难斤斤',38,1527495811,1),
(16,'您您您航航',38,1527496355,1),
(17,'？？？？',38,1527496733,1),
(18,'我哦哦脱离d',38,1527562578,4),
(19,'我哦哦脱离d',38,1527562579,4),
(20,'他小心翼rom',38,1527562589,4),
(21,'他小心翼rom',38,1527562590,4),
(22,'提醒我',38,1527562606,4),
(23,'提醒我',38,1527562606,4),
(24,'提醒我',38,1527562607,4),
(25,'承诺你才能承诺你内心呢',38,1527564336,1),
(26,'涂总',38,1527564764,3),
(27,'咯咯哦就如同ti',38,1527564775,3),
(28,'     健健康康来了句',38,1527565607,1),
(29,'     健健康康来了句',38,1527565609,1),
(30,'Hhhhh',38,1527565910,1),
(31,'兔兔兔兔呜呜魔女',38,1527569942,1);

/*Table structure for table `hlhjlive_online` */

DROP TABLE IF EXISTS `hlhjlive_online`;

CREATE TABLE `hlhjlive_online` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `online_content` text NOT NULL COMMENT '直播内容',
  `online_thumb` varchar(255) DEFAULT '' COMMENT '直播图片',
  `online_time` int(11) NOT NULL COMMENT '直播时间',
  `live_id` int(11) NOT NULL COMMENT '图文直播ID',
  `is_read` tinyint(1) NOT NULL DEFAULT '2' COMMENT '2-未读 1-已读',
  `update_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb4;

/*Data for the table `hlhjlive_online` */

insert  into `hlhjlive_online`(`id`,`online_content`,`online_thumb`,`online_time`,`live_id`,`is_read`,`update_at`) values
(6,'jfkdjfsdfjlsfjldffsf','',1527237494,2,2,1527237494),
(7,'dkfjakfjsdkfs','',1527238001,2,2,1527238001),
(8,'lflafjkldjflajflkdjflajfkldsfsfsdff','',1527238036,2,2,1527238036),
(9,'sdafadfasdf','',1527238342,2,2,1527238342),
(10,'111111111111111111111111111111','',1527238441,2,2,1527238441),
(11,'22222222222222222222','',1527238524,2,2,1527238524),
(13,'ffadfasdfasdfasfd','/application/hlhjnews/source/upload/20180525/1527238797250.png',1527238798,2,2,1527238798),
(14,'asdfasdfasdf','',1527239840,2,2,1527239840),
(15,'asdfasdfasdfasfd','',1527239910,2,2,1527239910),
(16,'sdfasdfasfwrr32323232','/application/hlhjnews/source/upload/20180525/1527239921250.jpg',1527239922,2,2,1527239922),
(17,'sdafasdfadf','/application/hlhjnews/source/upload/20180525/1527240174438.jpg',1527240175,2,2,1527240175),
(18,'你最帅你最帅你最帅你最帅你最帅你最帅你最帅你最帅你最帅你最帅你最帅你最帅你最帅','/application/hlhjnews/source/upload/20180525/1527240354481.jpg',1527240355,2,2,1527240355),
(19,'是的发送到发是发','/application/hlhjnews/source/upload/20180525/1527241285555.png',1527241286,2,2,1527241286),
(20,'撒打发撒打发撒打发撒打发撒打发撒打发撒打发撒打发撒打发撒打发撒打发撒打发撒打发撒打发撒打发','/application/hlhjnews/source/upload/20180525/15272413157.jpg',1527241316,2,2,1527241316),
(21,'是的发送到发撒打发3223323223','',1527241355,2,2,1527241355),
(22,'是的发送到发是的发送','',1527241405,2,2,1527241405),
(23,'是的发送到发是发','',1527241645,2,2,1527241645),
(24,'sad发送到发送到发送到发','/application/hlhjnews/source/upload/20180525/1527241655368.jpg',1527241655,2,2,1527241655),
(25,'发是的发送到发是大神','/application/hlhjnews/source/upload/20180525/1527241674724.jpg',1527241674,2,2,1527241674),
(26,'说的试试所所','/application/hlhjnews/source/upload/20180525/1527241686750.png',1527241688,2,2,1527241688),
(27,'是打发是打发斯蒂芬撒打算东方大厦','',1527241696,2,2,1527241696),
(28,'撒打发撒打发是的发送到发','',1527241702,2,2,1527241702),
(29,'234发送到发送到发送到发4柔肤水分手的发色人人撒打发说的','',1527241710,2,2,1527241710),
(30,'是打发打发343无法使用大神范儿发4人发斯蒂芬','',1527241717,2,2,1527241717),
(31,'是的发生昂发678太阳花功夫yujy877i大润发个说的','',1527241724,2,2,1527241724),
(32,'mmmmmmm','',1527241918,2,2,1527241918),
(33,'fdsfdfadfdfasdfd ','',1527241945,2,2,1527241945),
(34,'fadfasfsdfsaf','',1527241973,2,2,1527241973),
(35,'\'\r\n法福克斯的后方可恢复发货客服奥斯卡复活撒可富是客服是客服还是空方式代理费\r\n方法是否快速的发货时刻福克斯的粉红色看见对方是的粉红色的f发胜多负少咖啡色开发撒旦法拉萨的护肤老师的卡和富士康打飞机可是对方还是卡的复活卡萨电话费可接受的付款是否快速回复会计师电话费空间撒大黄蜂开始的恢复会计师电话费会计师电话费空间撒后方可第三方康师傅说的发','',1527242000,2,2,1527242000),
(36,'发的顺丰到付','/application/hlhjnews/source/upload/20180525/1527242029250.png',1527242035,2,2,1527242035),
(37,'发的方法','',1527242076,2,2,1527242076),
(38,'fdfasfds f','',1527242250,2,2,1527242250),
(39,'fdsfsfsdfd','/application/hlhjnews/source/upload/20180525/152724227411.jpg',1527242276,2,2,1527242276),
(40,'fdsfasfsdfsdfsfsdffsfsdfdsfda','',1527242285,2,2,1527242285),
(41,'fafafdfsdfdsfsdfdsfsfsdfdsfsadfafafsafdsfsfsfs','',1527242300,2,2,1527242300),
(42,'gdgdgsdgsadgcsfxdfagsgsdgegwew','',1527242312,2,2,1527242312),
(43,'fsfngbvnbvnvbnvbnvbnvnvbn','/application/hlhjnews/source/upload/20180525/1527242323104.png',1527242328,2,2,1527242328),
(44,'gdfgdfgd','',1527479484,2,2,1527479484),
(45,'fsdfasffsa\'f\'s\'d\'f','',1527479715,2,2,1527479715),
(46,'fsadfs','',1527479995,2,2,1527479995),
(47,'疯狂的首付款发的首付款放声大哭播放的说法是开发办贷款时间啊发布的说法叫师傅不上课备份数据库的发布开始加大风暴将咖啡杯卡萨举报发空间撒比福克斯保妇康栓发顺丰是否快速反馈健身房','',1527480028,2,2,1527480028),
(48,'蜂蜜水的烦恼撒可富就开始打飞机拉萨附近开始的飞机上课了房间里sad','',1527480054,2,2,1527480054),
(49,'雷锋精神了房间里水电费健康路附近','',1527486116,2,2,1527486116),
(50,'直播直播付电费卡放开手放开敬爱的福克斯大富科技撒大部分萨迪克发','',1527486148,2,2,1527486148),
(51,'方法所发生的发士大夫撒啊a','',1527486186,2,2,1527486186),
(52,'发发呆发生的发生发','',1527486192,2,2,1527486192),
(53,'asdfasdf a','',1527486273,2,2,1527486273),
(54,'sdfasdfasdfsadf','',1527486296,2,2,1527486296),
(55,'assaaaasdfsdfadf','',1527486304,2,2,1527486304),
(56,'dfasdfasdfasdfasfd','',1527486310,2,2,1527486310),
(57,'111111111111','',1527486319,2,2,1527486319),
(58,'fsadfasfafsaf疯掉了房间爱离开房间奥德赛仿佛你是卡立方劳动法南斯拉夫是否纳斯达克法兰克福男女分开付老师的奶粉是你发了你发了你发来烦恼你发了男方萨芬娜路口发哪里发哪里方式','/application/hlhjnews/source/upload/20180528/1527486413262.png',1527486414,2,2,1527486414),
(59,'发的撒发顺丰是否发顺丰舒服撒地方斯蒂芬是否是的方式发顺丰的说法','',1527486432,2,2,1527486432),
(60,'佛挡杀佛','',1527486492,2,2,1527486492),
(61,'是xv\'x\'c\'v','',1527486652,2,2,1527486652),
(62,'么么么么木木木木木发货方的','/application/hlhjnews/source/upload/20180528/152748666572.jpg',1527486695,2,2,1527486695),
(63,'法法师法士大夫撒 范德萨发第三方胜多负少的发送到方式','',1527486718,2,2,1527486718),
(64,'个个都是发给对方大哥大嫂','',1527486738,2,2,1527486738),
(65,'根深蒂固大概的 ','/application/hlhjnews/source/upload/20180528/1527486750769.png',1527486752,2,2,1527486752),
(66,'广东佛山给第三方刚刚好也太过分大概多少广东佛山供电所覆盖的发生过的法国大使馆的广东省分公司电饭锅的发送给对方是广东省个梵蒂冈电视广告','/application/hlhjnews/source/upload/20180528/1527486769456.png',1527486770,2,2,1527486770),
(67,'dfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdfdfasdf','/application/hlhjnews/source/upload/20180528/1527486802747.jpg',1527486803,2,2,1527486803),
(68,'dsaffdssdsdsd','',1527486821,2,2,1527486821),
(69,'asdfasdfasdfasdf','/application/hlhjnews/source/upload/20180528/1527486836137.jpg',1527486837,2,2,1527486837),
(70,'sdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdfsdfadfasdf','/application/hlhjnews/source/upload/20180528/1527486868999.jpg',1527486869,2,2,1527486869),
(71,'法法师的','',1527486911,2,2,1527486911),
(72,'法师法师打发发','',1527487698,2,2,1527487698),
(73,'发了房间卡士大夫见识到了发送到你烦死了快方法','/application/hlhjnews/source/upload/20180528/1527487712467.png',1527487713,2,2,1527487713),
(74,'发所发生的f','',1527487732,2,2,1527487732),
(75,'范德萨发发送啊','/application/hlhjnews/source/upload/20180528/1527487759521.jpg',1527487762,2,2,1527487762),
(76,'法拉盛解放路的什么飞洒，你仿佛是多么，','/application/hlhjnews/source/upload/20180528/1527487803507.png',1527487805,2,2,1527487805),
(77,'fdasfsafsfsafsf','',1527488743,2,2,1527488743),
(78,'fsfsfsffccscscscscscsdfsfsfsfsfs','/application/hlhjnews/source/upload/20180528/1527488755642.png',1527488759,2,2,1527488759),
(79,'爱迪生发盛大发售','/application/hlhjnews/source/upload/20180528/1527488792157.jpg',1527488793,2,2,1527488793),
(80,'爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	爱迪生发盛大发售	','/application/hlhjnews/source/upload/20180528/1527488808827.jpg',1527488810,2,2,1527488810),
(81,'爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售爱迪生发盛大发售','/application/hlhjnews/source/upload/20180528/1527488875823.jpg',1527488876,2,2,1527488876),
(82,'打发斯蒂芬法撒旦发','',1527489168,2,2,1527489168),
(83,'发送法师法师打发','/application/hlhjnews/source/upload/20180528/1527489192112.png',1527489195,2,2,1527489195),
(84,'fsadfs','',1527489558,2,2,1527489558),
(85,'dgsgsdgsdgdsgsgdsf','/application/hlhjnews/source/upload/20180528/1527489566948.png',1527489576,2,2,1527489576),
(86,'fadffaftejjggfhhfhdhghfghfhfhfh','/application/hlhjnews/source/upload/20180528/152748959262.png',1527489594,2,2,1527489594),
(87,'fafafsfdskfjsalfjsalkfjsklfjsldkfsdfsadfsafasf','/application/hlhjnews/source/upload/20180528/1527489662278.jpg',1527489663,2,2,1527489663),
(88,'mmmmmmmmmmlppofpsflsajfkfslkfskjflksfjlskfjslfjslkfjslkfjslfjlkmcxmahglnxahlgflkaufkfsanlhlsaklfnlfjlsakflksflfksajfksfslffaffas','/application/hlhjnews/source/upload/20180528/1527489694500.png',1527489695,2,2,1527489695),
(89,'fasfsadf saf','/application/hlhjnews/source/upload/20180528/1527489913626.png',1527489914,2,2,1527489914),
(90,'fasfsdfsdfsfsdfsdfsfs','/application/hlhjnews/source/upload/20180528/1527489925166.png',1527489926,2,2,1527489926),
(91,'fsafsfsfasfasfd','',1527489933,2,2,1527489933),
(92,'fasfsdfsfs','',1527489952,2,2,1527489952),
(93,'mnmsfnsd,mfnsd,fns,mfsdfsfsfsfsdf','/application/hlhjnews/source/upload/20180528/1527489964331.jpg',1527489965,2,2,1527489965),
(94,'fsfsafasfas','',1527490104,2,2,1527490104),
(95,'fsafsfsafsaf','/application/hlhjnews/source/upload/20180528/1527490112585.png',1527490114,2,2,1527490114),
(96,'fasfsafsaf','',1527490135,2,2,1527490135),
(97,'fafdsfafafsdafsdaf','/application/hlhjnews/source/upload/20180528/1527490307420.png',1527490310,2,2,1527490310),
(98,'fsfasf','',1527490327,2,2,1527490327),
(99,'fasfsdafsfsdfsafsadf','/application/hlhjnews/source/upload/20180528/1527490336449.png',1527490341,2,2,1527490341),
(100,'fsfsafffdsfsfsdaf','/application/hlhjnews/source/upload/20180528/1527490356790.png',1527490359,2,2,1527490359),
(101,'ffafdfnasdkfjsakfnsmfsdfsfasfsf','/application/hlhjnews/source/upload/20180528/1527490380136.png',1527490381,2,2,1527490381),
(102,'fasfsadfsfsdfsaf','/application/hlhjnews/source/upload/20180528/1527490397721.png',1527490398,2,2,1527490398),
(103,'梦想那么哪些卡还款的是你发卡号开电脑卡部分快递费说的','',1527490408,2,2,1527490408),
(104,'能行吗框架的说法卡上部分卡萨翻倍卡健身房会计师风科技萨福克举案说法看见爱上部分卡是部分可不舒服','',1527490424,2,2,1527490424),
(105,'防守反击爱咖啡开始的凤凰卡话费卡后方可撒谎方会计师电话费看是否快速的方式','/application/hlhjnews/source/upload/20180528/152749044360.png',1527490444,2,2,1527490444),
(106,'是冻死了房间爱垃圾分类四大法师打发','/application/hlhjnews/source/upload/20180528/1527490479700.png',1527490480,2,2,1527490480),
(107,'发放水电费合格合格后方法和法国恢复规划','',1527490507,2,2,1527490507),
(108,'是的发送到发的','',1527493971,2,2,1527493971),
(109,'s sa ','',1527494200,2,2,1527494200),
(110,'sadsadasd','',1527495716,2,2,1527495716),
(111,'dd打撒撒撒','',1527495825,2,2,1527495825),
(112,'撒大声地安上打撒安上安上','',1527495839,2,2,1527495839),
(113,'大萨达阿萨德阿萨德撒的撒','',1527495866,2,2,1527495866),
(114,'安上打撒多阿萨德安上','',1527495994,2,2,1527495994),
(115,'打撒安上打撒安上','',1527496007,2,2,1527496007),
(116,'是安上打撒打撒撒','',1527496124,2,2,1527496124),
(117,'打撒打撒多撒','',1527496132,2,2,1527496132),
(118,'撒安上','',1527496264,2,2,1527496264),
(119,'打撒打撒安上','',1527496273,2,2,1527496273),
(120,'撒安上安上安上','',1527496286,2,2,1527496286),
(121,'打撒打撒多子线程新注册','',1527496305,2,2,1527496305),
(122,'啊啊啊啊啊啊啊啊','',1527496429,2,2,1527496429),
(123,'啊啊是撒撒撒撒撒撒安上啊啊','',1527496453,2,2,1527496453),
(124,'从行政村自行车行政村现在从行政村这些','',1527496493,2,2,1527496493),
(125,'是打打打','',1527499736,2,2,1527499736),
(126,'的是撒','/application/hlhjnews/source/upload/20180528/1527499764441.png',1527499768,2,2,1527499768),
(127,'sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf sdfasdf ','',1527499817,3,2,1527499817),
(128,'打撒打撒打撒安上','',1527499852,3,2,1527499852),
(129,'撒安上阿萨德撒的撒','',1527499893,3,2,1527499893),
(130,'撒的阿萨德撒打撒','',1527499964,3,2,1527499964),
(131,'阿是打发是打发斯蒂芬','',1527500099,3,2,1527500099),
(132,'大萨达撒大萨达阿萨德安上安上','',1527500138,3,2,1527500138),
(133,'撒大大撒的撒所多所多','',1527500611,3,2,1527500611),
(134,'订单搜索多多所所多多所','',1527500731,3,2,1527500731),
(135,'是打发是打发斯蒂芬','',1527500773,3,2,1527500773),
(136,'是打发是打发斯蒂芬','',1527500988,3,2,1527500988),
(137,'打撒大萨达安上','',1527501010,3,2,1527501010),
(138,'大萨达撒大萨达撒','',1527501032,3,2,1527501032),
(139,'打撒打撒打撒打撒','',1527501055,3,2,1527501055),
(140,'大萨达安上打撒打撒是','',1527501425,3,2,1527501425),
(141,'打是的撒','',1527501444,3,2,1527501444),
(142,'打撒打打巷战初学者','',1527501449,3,2,1527501449),
(144,'啊啊啊啊啊啊','',1527501474,3,2,1527501474),
(145,'大斯达舒大萨达撒安上','/application/hlhjnews/source/upload/20180529/1527564621278.png',1527501714,3,2,1527564622),
(146,'但发生的','/application/hlhjnews/source/upload/20180529/1527564844384.png',1527564845,5,2,1527564845);

/*Table structure for table `hlhjlive_program` */

DROP TABLE IF EXISTS `hlhjlive_program`;

CREATE TABLE `hlhjlive_program` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `program_date` varchar(255) NOT NULL DEFAULT '' COMMENT '节目单日期',
  `program_time` int(10) unsigned NOT NULL COMMENT '节目时间',
  `create_at` int(10) unsigned NOT NULL,
  `update_at` int(10) unsigned NOT NULL,
  `tv_id` int(10) unsigned NOT NULL COMMENT '频道ID',
  `program_list` text NOT NULL COMMENT '节目列表  json格式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='节目单';

/*Data for the table `hlhjlive_program` */

insert  into `hlhjlive_program`(`id`,`program_date`,`program_time`,`create_at`,`update_at`,`tv_id`,`program_list`) values
(8,'1970-01-01',0,1527574200,1527574200,2,'[{\"name\":\"2017-12-14 13:37:24\",\"type\":\"2017-12-14 13:35:55\",\"time\":\"08:00\"},{\"name\":\"2017-12-08 23:57:16\",\"type\":\"2017-12-08 23:56:19\",\"time\":\"08:00\"},{\"name\":\"2017-12-08 23:57:16\",\"type\":\"2017-12-08 23:56:19\",\"time\":\"08:00\"},{\"name\":\"2017-11-30 22:05:47\",\"type\":\"2017-11-30 22:04:54\",\"time\":\"08:00\"},{\"name\":\"2017-11-30 21:41:55\",\"type\":\"2017-11-30 21:41:29\",\"time\":\"08:00\"},{\"name\":\"2017-11-26 12:10:04\",\"type\":\"2017-11-26 12:09:00\",\"time\":\"08:00\"},{\"name\":\"2017-11-11 01:48:43\",\"type\":\"2017-11-05 13:49:46\",\"time\":\"08:00\"},{\"name\":\"2017-11-11 01:23:32\",\"type\":\"2017-11-05 13:49:46\",\"time\":\"08:00\"},{\"name\":\"2017-11-11 01:23:05\",\"type\":\"2017-11-05 13:49:46\",\"time\":\"08:00\"},{\"name\":\"2017-11-11 01:12:48\",\"type\":\"2017-11-05 13:49:46\",\"time\":\"08:00\"},{\"name\":\"2017-11-11 01:11:56\",\"type\":\"2017-11-05 13:49:46\",\"time\":\"08:00\"},{\"name\":\"2017-11-11 01:10:47\",\"type\":\"2017-11-05 13:49:46\",\"time\":\"08:00\"},{\"name\":\"2017-11-11 01:09:17\",\"type\":\"2017-11-05 13:49:46\",\"time\":\"08:00\"},{\"name\":\"2017-11-11 00:31:55\",\"type\":\"2017-10-27 07:17:24\",\"time\":\"08:00\"},{\"name\":\"2017-11-01 13:44:44\",\"type\":\"2017-11-01 13:44:16\",\"time\":\"08:00\"},{\"name\":\"2017-10-27 18:09:12\",\"type\":\"2017-10-27 18:09:02\",\"time\":\"08:00\"},{\"name\":\"2017-10-27 17:54:41\",\"type\":\"2017-10-27 17:54:26\",\"time\":\"08:00\"}]'),
(9,'2018-05-28',1527436800,1527574529,1527576798,2,'[{\"name\":\"新闻联播\",\"type\":1,\"time\":\"17:00\"},{\"name\":\"综艺节目\",\"type\":2,\"time\":\"17:00\"}]');

/*Table structure for table `hlhjlive_radio` */

DROP TABLE IF EXISTS `hlhjlive_radio`;

CREATE TABLE `hlhjlive_radio` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `radio_name` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '广播名字',
  `radio_thumb` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '广播图片',
  `radio_source` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '广播源',
  `create_at` int(11) DEFAULT NULL,
  `update_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `hlhjlive_radio` */

insert  into `hlhjlive_radio`(`id`,`radio_name`,`radio_thumb`,`radio_source`,`create_at`,`update_at`) values
(2,'成都广播','/application/hlhjnews/source/upload/20180529/1527560606296.png','rtmp://live.hkstv.hk.lxdns.com/live/hks',1527560607,1527560607),
(3,'上海广播','/application/hlhjnews/source/upload/20180529/1527560959501.png','http://devimages.apple.com.edgekey.net/streaming/examples/bipbop_16x9/gear5/prog_index.m3u8',1527560963,1527561801);

/*Table structure for table `hlhjlive_radio_program` */

DROP TABLE IF EXISTS `hlhjlive_radio_program`;

CREATE TABLE `hlhjlive_radio_program` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `program_date` varchar(255) NOT NULL DEFAULT '' COMMENT '节目单日期',
  `program_time` int(10) unsigned NOT NULL COMMENT '节目时间',
  `create_at` int(10) unsigned NOT NULL,
  `update_at` int(10) unsigned NOT NULL,
  `radio_id` int(10) unsigned NOT NULL COMMENT '频道ID',
  `program_list` text NOT NULL COMMENT '节目列表  json格式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='节目单';

/*Data for the table `hlhjlive_radio_program` */

insert  into `hlhjlive_radio_program`(`id`,`program_date`,`program_time`,`create_at`,`update_at`,`radio_id`,`program_list`) values
(2,'2018-05-25',1527177600,1527235246,1527235246,1,'{\"1\":{\"time\":\"10:07\",\"name\":\"是单独的发斯蒂芬是\",\"type\":\"1\"},\"index0\":{\"time\":\"11:06\",\"name\":\"是的发送到发\",\"type\":\"1\"},\"index1\":{\"time\":\"16:02\",\"name\":\"是的发送到发\",\"type\":\"1\"},\"index2\":{\"time\":\"16:17\",\"name\":\"是的发送到发\",\"type\":\"1\"}}'),
(3,'2018-05-28',1527436800,1527473608,1527576221,1,'[{\"name\":\"新闻联播\",\"type\":1,\"time\":\"17:00\"}]'),
(4,'2018-05-30',1527609600,1527560879,1527560879,1,'{\"1\":{\"time\":\"10:26\",\"name\":\"大声地\",\"type\":\"2\"}}'),
(5,'2018-05-28',1527436800,1527565576,1527576221,2,'[{\"name\":\"新闻联播\",\"type\":1,\"time\":\"17:00\"}]');

/*Table structure for table `hlhjlive_vote` */

DROP TABLE IF EXISTS `hlhjlive_vote`;

CREATE TABLE `hlhjlive_vote` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `live_id` int(10) unsigned NOT NULL COMMENT '直播ID',
  `content` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '弹幕的内容',
  `create_at` int(11) NOT NULL COMMENT '弹幕时间',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1-已显示 2-未显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `hlhjlive_vote` */

insert  into `hlhjlive_vote`(`id`,`live_id`,`content`,`create_at`,`user_id`,`status`) values
(1,1,'11111',1527146176,38,2),
(2,1,'呢呢呢呢呢呢',1527216667,38,2),
(3,1,'就急急忙忙',1527216801,38,2),
(4,1,'就急急忙忙',1527216809,38,2),
(5,1,'就急急忙忙vvvv',1527216819,38,2),
(6,1,'更换哈哈哈哈',1527217032,38,2),
(7,1,'哈哈哈你呢现场v',1527217039,38,2),
(8,1,'昆明妈妈',1527217046,38,2),
(9,1,'行家就行哈更厚您喊您贵宾你侯斌您狗比斌刚比斌更不搬家刚喊姐姐刚后半钢板耨不表白表白更表白版后表白吧刚',1527491279,38,2),
(10,1,'表白表白吧',1527491286,38,2),
(11,1,'仅仅您',1527491298,38,2),
(12,1,'表白表白吧崩追周边贵州刚表白吧贵州吧规表白斌峰追周边中',1527492061,38,2),
(13,1,'航班棒棒',1527492067,38,2),
(14,1,'叔叔',1527492080,38,2),
(15,1,'斤斤计较',1527492088,38,2),
(16,1,'喊姐姐',1527492126,38,2),
(17,1,'擅拒绝',1527492141,38,2),
(18,1,'斤斤计较姐姐',1527492157,38,2),
(19,1,'就表白表白',1527492169,38,2),
(20,1,'bbbbbw',1527492233,38,2),
(21,1,'表白表白',1527492589,38,2),
(22,1,'航航航',1527492638,38,2),
(23,1,'斤斤计较家',1527492648,38,2),
(24,1,'斤斤计较',1527492653,38,2),
(25,1,'刚哼',1527492661,38,2),
(26,1,'就看您',1527492666,38,2),
(27,1,'靠靠长辈您耨不必追',1527492945,38,2),
(28,1,'您您厚您刚斌呢刚斌航',1527493098,38,2),
(29,1,'喊就好',1527493345,38,2),
(30,1,'周边表白表白进耨',1527494249,38,2),
(31,1,'m',1527496197,38,2),
(32,1,'靠开',1527496223,38,2),
(33,1,'也一样哈',1527496233,38,2),
(34,1,'jjj',1527498752,38,2),
(35,1,'jjj',1527499466,38,2),
(36,1,'书',1527500811,38,2),
(37,1,'会周边',1527556882,38,2),
(38,1,'航航',1527556908,38,2),
(39,1,'更航航',1527557359,38,2),
(40,1,'擅拒绝家',1527557372,38,2),
(41,1,'吃烤靠开来来来',1527557392,38,2),
(42,1,'斤斤计较家',1527557421,38,2),
(43,1,'很喜欢吃何处合成愁',1527557905,38,2),
(44,1,'笑哈哈很喜欢?',1527557948,38,2),
(45,1,'新年继续进行',1527558199,38,2),
(46,4,' wowYY\n',1527562414,38,2),
(47,4,'他咯哦',1527562429,38,2),
(48,4,'应用',1527562516,38,2),
(49,4,'企业营业执照',1527562520,38,2),
(50,4,'我再看看',1527562535,38,2),
(51,4,'企业营业执照',1527562539,38,2),
(52,4,'去游泳',1527562543,38,2),
(53,4,'兔子找我',1527562548,38,2),
(54,4,'我们都',1527562554,38,2),
(55,4,'兔崽子',1527562558,38,2),
(56,4,'我哦o x x',1527562588,38,2),
(57,4,'唐呜呜呜',1527562605,38,2),
(58,4,'困',1527562739,38,2),
(59,4,'l l l',1527562743,38,2),
(60,4,'v你男女',1527562766,38,2),
(61,3,'破案',1527564747,38,2),
(62,3,'，同',1527564773,38,2),
(63,1,'送你',1527572544,38,2);

/*Table structure for table `hlhjlive_web` */

DROP TABLE IF EXISTS `hlhjlive_web`;

CREATE TABLE `hlhjlive_web` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `live_title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '网络直播',
  `live_thumb` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '封面图',
  `live_desc` text CHARACTER SET utf8mb4 COMMENT '直播介绍',
  `live_source` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '直播源',
  `live_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-直播 2-回放',
  `create_at` int(11) NOT NULL,
  `update_at` int(11) NOT NULL,
  `online` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-正在播放 2-暂停播放',
  `live_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-视频直播 2-图文直播',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
