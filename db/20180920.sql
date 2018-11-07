
CREATE TABLE `tm_answer` (
  `answer_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL COMMENT '问题id',
  `flag` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'a，b，c，d',
  `answer_content` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '答案',
  `create_at` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_at` int(10) DEFAULT NULL COMMENT '修改时间',
  `is_answer` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '正确答案'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tm_answer`
--

INSERT INTO `tm_answer` (`answer_id`, `question_id`, `flag`, `answer_content`, `create_at`, `update_at`, `is_answer`) VALUES
(1, 0, NULL, '', NULL, NULL, NULL),
(2, 0, NULL, '', NULL, NULL, NULL),
(3, 0, NULL, '', NULL, NULL, NULL),
(4, 1, 'A', '1', NULL, NULL, NULL),
(5, 1, 'B', '32453123132', 1521275978, 0, '2'),
(6, 1, 'D', '爱仕达多', 1521276139, 1521276139, '2'),
(7, 2, 'A', '是的撒卡进度很看好', 1521276329, 1521276329, '2'),
(8, 2, 'B', '可是大家爱看好的开机动画看手机号付款后付款时间和发看见好看就好', 1521276329, 1521276329, '2'),
(9, 2, 'C', '撒大声地', 1521276329, 1521276329, '2'),
(10, 2, 'D', '爱仕达多', 1521276329, 1521276329, '2'),
(11, 3, 'A', '是的撒卡进度很看好', 1521276581, 1521276581, 'D'),
(12, 3, 'B', '可是大家爱看好的开机动画看手机号付款后付款时间和发看见好看就好', 1521276581, 1521276581, 'D'),
(13, 3, 'C', '撒大声地', 1521276581, 1521276581, 'D'),
(14, 3, 'D', '爱仕达多', 1521276581, 1521276581, 'D'),
(15, 4, 'A', '是的撒卡进度很看好22', 1521276667, 1521276667, 'D'),
(16, 4, 'B', '可是大家爱看好的开机动画看手机号付款后付款时间和发看见好看就好', 1521276667, 1521276667, 'D'),
(17, 4, 'C', '撒大声地2', 1521276667, 1521276667, 'D'),
(18, 4, 'D', '222222', 1521276667, 1521276667, 'D'),
(19, 5, 'A', '撒大声地', 1521276991, 1521276991, 'C'),
(20, 5, 'B', '硕大的', 1521276991, 1521276991, 'C'),
(21, 5, 'C', '硕大的sad', 1521276991, 1521276991, 'C'),
(22, 6, 'A', '撒大声地', 1521277061, 1521277061, 'C'),
(23, 6, 'B', '硕大的', 1521277061, 1521277061, 'C'),
(24, 6, 'C', '硕大的sad', 1521277061, 1521277061, 'C'),
(25, 7, 'A', '撒大声地', 1521277116, 1521277116, 'C'),
(26, 7, 'B', ' 23321', 1521277116, 1521277116, 'C'),
(27, 7, 'C', '硕大的sad', 1521277116, 1521277116, 'C'),
(28, 8, 'A', '撒大声地', 1521277172, 1521277172, 'C'),
(29, 8, 'B', '硕大的', 1521277172, 1521277172, 'C'),
(30, 8, 'C', '硕大的sad', 1521277172, 1521277172, 'C'),
(31, 9, 'A', '46465', 1521279815, 1521279815, 'D'),
(32, 9, 'B', '565456', 1521279815, 1521279815, 'D'),
(33, 9, 'C', '65465465', 1521279815, 1521279815, 'D'),
(34, 9, 'D', '212.1321', 1521279815, 1521279815, 'D'),
(35, 10, 'A', '46465', 1521279994, 1521279994, 'D'),
(36, 10, 'B', '565456', 1521279994, 1521279994, 'D'),
(37, 10, 'C', '65465465', 1521279994, 1521279994, 'D'),
(38, 10, 'D', '212.1321', 1521279994, 1521279994, 'D'),
(39, 11, 'A', '就开始等会撒', NULL, NULL, 'A'),
(40, 32, 'A', '结合国际化', 1521370570, 1521370570, 'C'),
(41, 32, 'B', '开机即可', 1521370570, 1521370570, 'C'),
(42, 32, 'C', '骷髅', 1521370570, 1521370570, 'C'),
(43, 32, 'A', '', 1521468030, 1521468030, 'B'),
(44, 32, 'B', '', 1521468030, 1521468030, 'B'),
(45, 40, 'A', 'A', 1522758020, 1522758020, 'B'),
(46, 40, 'S', 'S', 1522758020, 1522758020, 'B'),
(47, 41, 'A', '要先有钱', 1523244719, 1523244719, 'A'),
(48, 41, 'B', '无', 1523244719, 1523244719, 'A'),
(49, 41, 'C', '鸡', 1523244719, 1523244719, 'A'),
(50, 41, 'D', '鸡蛋', 1523244719, 1523244719, 'A');

-- --------------------------------------------------------

--
-- Table structure for table `tm_branch`
--

CREATE TABLE `tm_branch` (
  `branch_id` int(10) UNSIGNED NOT NULL COMMENT '分支机构ID',
  `parent_id` int(10) UNSIGNED DEFAULT NULL COMMENT '父节点ID',
  `branch_name` varchar(64) NOT NULL COMMENT '分支机构名称',
  `branch_tel` varchar(32) DEFAULT NULL COMMENT '分支机构电话',
  `branch_fax` varchar(32) DEFAULT NULL COMMENT '分支机构传真',
  `branch_code` varchar(64) NOT NULL COMMENT '分支机构代码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_branch`
--

INSERT INTO `tm_branch` (`branch_id`, `parent_id`, `branch_name`, `branch_tel`, `branch_fax`, `branch_code`) VALUES
(1, NULL, '全部', NULL, NULL, '0'),
(2, 1, '多媒体中心', NULL, NULL, 'DE0D265CCFED6B636B81A80A32725FBD'),
(3, 1, '技术中心', NULL, NULL, '843C92BF12CB73D0CDD3CEFE92286FD3'),
(4, 2, '运营部门', NULL, NULL, '24E25CA091ABFA80BF4EFBA9F9217D6B'),
(5, 2, '产品部门', NULL, NULL, '898385F43ADC759C4DBBC71AF63E67C5'),
(6, 3, '运维管理部', NULL, NULL, 'E1F3F0C9EFF3E2954BFE64557C8E8C07'),
(7, 3, '技术部', NULL, NULL, 'BD27DA35FAA6DC9350D6AD995B0E6FD7'),
(8, 1, '矩阵号', NULL, NULL, '890B217EF0EED8D1C0BC1BA29C8A5428'),
(9, 8, '南平', NULL, NULL, '19CE12797FAAD44C5E5FF5A606ACAAB3'),
(10, 8, '福州', NULL, NULL, 'CCFD3F08E70039E72A505EE19715381B'),
(11, 8, '龙岩', NULL, NULL, '207F74BBE7642C88FB47307F1B0DE049'),
(12, 8, '莆田', NULL, NULL, '61F146A4AA95CBC7919D2875FC3983E9'),
(13, 1, '测试', NULL, NULL, 'F63DFF7DF3752AFE5D80E1E7C504F696');

-- --------------------------------------------------------

--
-- Table structure for table `tm_classes`
--

CREATE TABLE `tm_classes` (
  `classes_id` int(10) UNSIGNED NOT NULL COMMENT '栏目ID',
  `classes_code` varchar(64) NOT NULL COMMENT '栏目代码',
  `classes_name` varchar(64) NOT NULL COMMENT '栏目名称',
  `classes_en_name` varchar(64) DEFAULT NULL COMMENT '栏目英文名',
  `classes_intro` varchar(128) DEFAULT NULL COMMENT '分类介绍',
  `site_code` varchar(64) NOT NULL COMMENT '站点ID',
  `index_template_name` varchar(64) DEFAULT NULL COMMENT '首页模板名称',
  `list_template_name` varchar(64) DEFAULT NULL COMMENT '列表页模板名称',
  `detail_template_name` varchar(64) DEFAULT NULL COMMENT '详细页模板名称',
  `list_amount` int(10) UNSIGNED DEFAULT NULL COMMENT '列表显示的记录数',
  `detail_page_title` varchar(64) DEFAULT NULL COMMENT '详细页命名规则',
  `parent_classes_id` int(10) UNSIGNED DEFAULT NULL COMMENT '父栏目ID',
  `sort` int(10) UNSIGNED DEFAULT NULL COMMENT '排序字段'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_classes`
--

INSERT INTO `tm_classes` (`classes_id`, `classes_code`, `classes_name`, `classes_en_name`, `classes_intro`, `site_code`, `index_template_name`, `list_template_name`, `detail_template_name`, `list_amount`, `detail_page_title`, `parent_classes_id`, `sort`) VALUES
(2, '568955E2999CA3BCA5AC185867BCA1EC', '123', NULL, '123', 'CA1DD4572C81AB750A7ED6C66F2492EA', NULL, NULL, NULL, NULL, NULL, 0, 3),
(3, '458B3421C40DF1BF845BD4BBD04A3A5E', '2', NULL, '123', 'CA1DD4572C81AB750A7ED6C66F2492EA', NULL, NULL, NULL, NULL, NULL, 0, 2),
(4, 'CBFFE3615311C389CE10490EAAC6C9BE', '23', NULL, '3', 'CA1DD4572C81AB750A7ED6C66F2492EA', NULL, NULL, NULL, NULL, NULL, 0, 4),
(5, '7CB2026BCDB27436E153FAC4FC2CD847', '问', NULL, '问', 'CA1DD4572C81AB750A7ED6C66F2492EA', NULL, NULL, NULL, NULL, NULL, 0, 5),
(6, '1EC072B06343715C83DD9E97E507EED4', 'qqqq', NULL, 'qqq', '0C2923197E2D955123E1C2EE11034DBB', NULL, NULL, NULL, NULL, NULL, 0, 6),
(7, 'C013B3A31921B03BB3B2919A74ABF9BD', 'ww', NULL, 'www', '0C2923197E2D955123E1C2EE11034DBB', NULL, NULL, NULL, NULL, NULL, 6, 7),
(8, '9200F9B3B45817EDEC6CE11101C24296', 'klj', NULL, '', 'CA1DD4572C81AB750A7ED6C66F2492EA', NULL, NULL, NULL, NULL, NULL, 2, 8),
(9, '2924704045D45F3ACCAB80B4F0E3F869', 'jkhkjh', NULL, '', 'CA1DD4572C81AB750A7ED6C66F2492EA', NULL, NULL, NULL, NULL, NULL, 2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `tm_component`
--

CREATE TABLE `tm_component` (
  `component_id` int(10) UNSIGNED NOT NULL COMMENT '产品组件id',
  `component_code` varchar(64) NOT NULL COMMENT '产品组件代码',
  `component_name` varchar(128) NOT NULL COMMENT '产品组件名称',
  `component_key` varchar(64) NOT NULL COMMENT '模块唯一key',
  `developer_code` varchar(64) NOT NULL COMMENT '开发者code',
  `access_key` varchar(128) NOT NULL COMMENT '认证key',
  `secret_key` varchar(128) DEFAULT NULL COMMENT '安全key',
  `index_url` text COMMENT '前端入口地址',
  `index_version` varchar(128) DEFAULT NULL COMMENT '前端版本号',
  `admin_url` text COMMENT '后端入口地址',
  `admin_version` varchar(128) DEFAULT NULL COMMENT '后端版本号',
  `app_code` varchar(64) NOT NULL COMMENT 'app代码',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `company_name` varchar(128) DEFAULT NULL COMMENT '公司名',
  `address` varchar(128) DEFAULT NULL COMMENT '公司地址',
  `tel` varchar(64) DEFAULT NULL COMMENT '公司电话',
  `description` varchar(128) DEFAULT NULL COMMENT '描述',
  `linkman` varchar(128) DEFAULT NULL,
  `note` varchar(128) DEFAULT NULL,
  `component_pic` varchar(128) DEFAULT NULL COMMENT '应用图标'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_component`
--

INSERT INTO `tm_component` (`component_id`, `component_code`, `component_name`, `component_key`, `developer_code`, `access_key`, `secret_key`, `index_url`, `index_version`, `admin_url`, `admin_version`, `app_code`, `create_time`, `company_name`, `address`, `tel`, `description`, `linkman`, `note`, `component_pic`) VALUES
(4, 'B2433C8761B4671553FE6A45C9DCC4CB', '会员管理', 'system', '0002670C1999235DE324746FF8AF2971', '', 'Video', NULL, '', '/#/Site/Member', '', 'member', 1545432123, '', '', NULL, NULL, '', NULL, '/uploads/icon/22.png');

-- --------------------------------------------------------

--
-- Table structure for table `tm_fix_item`
--

CREATE TABLE `tm_fix_item` (
  `fix_id` int(10) UNSIGNED NOT NULL COMMENT '受控词ID',
  `fix_name` varchar(128) NOT NULL COMMENT '受控词名称',
  `description` varchar(128) NOT NULL COMMENT '受控词描述',
  `parent_id` int(10) UNSIGNED DEFAULT NULL COMMENT '父节点ID',
  `site_code` varchar(64) NOT NULL COMMENT '站点代码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_kind`
--

CREATE TABLE `tm_kind` (
  `type_id` int(10) NOT NULL COMMENT '题库类id',
  `title` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '图库名称',
  `content` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '题库说明',
  `creattime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updtime` int(10) DEFAULT NULL COMMENT '修改时间',
  `types` varchar(10) CHARACTER SET utf8 DEFAULT NULL COMMENT '题库类型'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tm_kind`
--

INSERT INTO `tm_kind` (`type_id`, `title`, `content`, `creattime`, `updtime`, `types`) VALUES
(7, '65464561', '654d654111', 1521248346, 1521248346, '单选'),
(9, '客家话', '交换机', 1521249062, 1521292076, '单选'),
(11, '两会党建答题', '两会党建答题', 1522757134, 1522757134, '单选'),
(12, '测试数据，请勿操作！', '测试', 1523244437, 1523244437, '单选'),
(13, '测试1', '测测', 1523245715, 1523412172, '单选'),
(14, '测试2', '测的', 1523245725, 1523245725, '单选'),
(15, '测试3', '测试', 1523245737, 1523245737, '单选'),
(18, '测试12', 'c饿死', 1523245828, 1523245828, '单选'),
(20, '测试20', '测试', 1523245857, 1523245857, '单选'),
(21, '测试21', 'c饿死', 1523245866, 1523245866, '单选');

-- --------------------------------------------------------

--
-- Table structure for table `tm_member`
--

CREATE TABLE `tm_member` (
  `member_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `member_code` varchar(64) NOT NULL COMMENT '用户代码',
  `member_name` varchar(64) NOT NULL COMMENT '用户名称',
  `member_nickname` varchar(64) DEFAULT NULL COMMENT '昵称',
  `member_real_name` varchar(64) DEFAULT NULL COMMENT '用户真实姓名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `site_code` varchar(64) NOT NULL COMMENT '站点code',
  `email` varchar(64) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(32) NOT NULL COMMENT '手机',
  `head_pic` varchar(128) DEFAULT NULL COMMENT '头像',
  `birthday` varchar(20) DEFAULT NULL COMMENT '生日',
  `sex` tinyint(1) UNSIGNED DEFAULT NULL COMMENT '性别1 男 2 女',
  `access_key` varchar(128) DEFAULT NULL COMMENT '登录认证key',
  `access_key_create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '认证key创建时间',
  `secret_key` varchar(128) DEFAULT NULL COMMENT '安全key',
  `status` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '状态(0：正常，1：停用)',
  `deleted` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否删除(0：未删除，1：已删除)',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `wx` varchar(128) DEFAULT NULL COMMENT '微信账号',
  `qq` varchar(128) DEFAULT NULL COMMENT 'qq账号',
  `zfb` varchar(128) DEFAULT NULL COMMENT '支付宝账号',
  `wb` varchar(128) DEFAULT NULL COMMENT '微博账号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_member`
--

INSERT INTO `tm_member` (`member_id`, `member_code`, `member_name`, `member_nickname`, `member_real_name`, `password`, `site_code`, `email`, `mobile`, `head_pic`, `birthday`, `sex`, `access_key`, `access_key_create_time`, `secret_key`, `status`, `deleted`, `create_time`, `wx`, `qq`, `zfb`, `wb`) VALUES
(1, 'CCA13621965AE58771DED86A8FCA98DB', '187****4734', NULL, NULL, 'd0e809432f539348ef1eefa62551fa84', '00000000000000000000000000000000', NULL, '18728804734', NULL, NULL, NULL, '4C912AA522BF481CD531776656DDDDCC', 1525250846, NULL, 0, 0, 1518088581, NULL, NULL, NULL, NULL),
(5, 'B43EE08A91EFBB407A6D4C0062800953', '155****7009', NULL, NULL, 'fdf47d0a45e48b275ede9596d3493817', '00000000000000000000000000000000', NULL, '15528007009', NULL, NULL, NULL, '969F146BDF02B2CD60A4377A109DB13B', 1519632400, NULL, 0, 0, 1518075884, NULL, NULL, NULL, NULL),
(7, 'BC917C9B7C20CD35B36AB0DC5193BAFC', '180****0926', '盒子QQ', NULL, 'd9b1d7db4cd6e70935368a1efb10e377', '0C2923197E2D955123E1C2EE11034DBB', NULL, '18081000926', '/uploads/member/18081000926/15211844313716.jpeg', '2018-02-09', 1, 'FCDB1E0C33DFB28227A8BC10FA21FFD5', 1521688255, NULL, 0, 0, 1518086408, NULL, NULL, NULL, NULL),
(10, '984AFA53552795408A90F7B4C373C5D9', '刘彬1', '刘彬1', NULL, '14e1b600b1fd579f47433b88e8d85291', '00000000000000000000000000000000', NULL, '13908064279', '/uploads/member/13908064279/15211831798769.jpeg', '2018-03-16', 1, 'BFCDDACCAFB1F5D59070E9F55EC8D8F6', 1523608001, NULL, 0, 0, 1518417208, NULL, NULL, NULL, NULL),
(11, '3C6FEAEF1B4BF535BD9770AFD4B8D839', '187****0886', NULL, NULL, '74f25816d90c44708fba54b5e0eec363', '00000000000000000000000000000000', NULL, '18784440886', NULL, NULL, NULL, 'C4789C9D216E5504091427AD073FD834', 1522637488, NULL, 0, 0, 1518422019, NULL, NULL, NULL, NULL),
(12, 'ED993DBB240D55EFCAE8587E7512B923', '136****6144', '呵呵', NULL, 'c3ef41e6b2fd9aa7a44210804ec23175', '00000000000000000000000000000000', NULL, '13699476144', NULL, '2018-03-08', 1, 'E8C5B6C70A8269F63E8787B829FE9852', 1520513715, NULL, 0, 0, 1519456463, NULL, NULL, NULL, NULL),
(13, '9CB8973A284ECAD3A770716C9CBAF474', '138****2232', NULL, NULL, '0bb0513d4507950cc0f8029d8e0e15af', '00000000000000000000000000000000', NULL, '13880642232', NULL, NULL, NULL, '36687847F586195E3B130D88F4ED16A5', 1521721063, NULL, 0, 0, 1520167150, NULL, NULL, NULL, NULL),
(14, '9C406DB934BC350144FF64F5C581AAB7', '158****6063', NULL, NULL, 'ff92a240d11b05ebd392348c35f781b2', '', NULL, '15882416063', '/uploads/member/15882416063/15211886633631.png', NULL, NULL, 'EA8F169DEB60AC20390034FA436E0B0D', 1526551750, NULL, 0, 0, 1520585248, NULL, NULL, NULL, NULL),
(15, '5361F09AC1E224C004AC21399757DD1A', '156****7710', NULL, NULL, '8697cb6b53ca3621cfbacdd17469748b', '00000000000000000000000000000000', NULL, '15682077710', NULL, NULL, NULL, 'D7D597B7440BA2C54752DCA8B4DAA14E', 1526625389, NULL, 0, 0, 1520590929, NULL, NULL, NULL, NULL),
(16, '69C0E075F2D638E13EF59727154803DB', '185****6181', '飞飞虾', NULL, 'a428f002cc30b16c9d3492414722aa4a', '00000000000000000000000000000000', NULL, '18512816181', '/uploads/member/18512816181/15214509522793.png', '2007-03-09', 1, '751C66D71C9998820F48599CBF8536DD', 1522636814, NULL, 0, 0, 1520596365, NULL, NULL, NULL, NULL),
(17, 'D7336C755561544803AF9E137C4E2B53', '135****2250', NULL, NULL, '3b2bf3ff4d58c2ed8515c7c6a388e0b0', '00000000000000000000000000000000', NULL, '13568882250', NULL, NULL, NULL, '3463A3086CA3EAB6621F683B4852DD78', 1520835441, NULL, 0, 0, 1520835441, NULL, NULL, NULL, NULL),
(18, '5193406D63C9F8A3B1BF1FEE71D882F3', '182****7391', NULL, NULL, '30d30d20d5b29b35845eebdbf39789e4', '00000000000000000000000000000000', NULL, '18215607391', NULL, NULL, NULL, '2D03E10F6F9DA2D1C824FE3BAAC128E7', 1522047913, NULL, 0, 0, 1520921181, NULL, NULL, NULL, NULL),
(19, '97D228C05C0C59C520E89138B442CB47', '173****8925', NULL, NULL, '14e1b600b1fd579f47433b88e8d85291', '00000000000000000000000000000000', NULL, '17345738925', '/uploads/member/17345738925/15222266196586.png', NULL, 2, 'C196F0F9DB4CAB2F31B315980A453A52', 1522636074, NULL, 0, 0, 1520927167, NULL, NULL, NULL, NULL),
(20, 'AA66016672955A2B0C2EBDB476BDEAA7', '158****6063', NULL, NULL, '63456d9779c107c3f0a53756a84168b2', '0C2923197E2D955123E1C2EE11034DBB', NULL, '15882416063', '/uploads/member/15882416063/15211886633631.png', NULL, NULL, 'EA8F169DEB60AC20390034FA436E0B0D', 1526551750, NULL, 0, 0, 1520935767, NULL, NULL, NULL, NULL),
(21, '3511DFB3CA8E7E5C62891290A3EEE60C', '180****0926', '盒子', NULL, '551a378573647c88843300e225699a44', '00000000000000000000000000000000', NULL, '18081000926', '/uploads/member/18081000926/15211844313716.jpeg', NULL, 1, 'FCDB1E0C33DFB28227A8BC10FA21FFD5', 1521688255, NULL, 0, 0, 1521007147, NULL, NULL, NULL, NULL),
(22, '29CA8C53B38C4A2A0C778E682F8FF6DA', '测试人员', '测试', NULL, 'ff92a240d11b05ebd392348c35f781b2', '00000000000000000000000000000000', NULL, '15882416063', '/uploads/member/15882416063/15211886633631.png', '2018-03-16', 2, 'EA8F169DEB60AC20390034FA436E0B0D', 1526551750, NULL, 0, 0, 1521102642, NULL, NULL, NULL, NULL),
(23, '12CD994B48204A4AD266B0D72AB8E262', '181****0787', NULL, NULL, '4b26041782f8c33a37c7fb050380edfd', '0C2923197E2D955123E1C2EE11034DBB', NULL, '18190710787', NULL, NULL, NULL, 'D029F3A6443BA56C27FB3EE0536508BC', 1521196425, NULL, 0, 0, 1521195047, NULL, NULL, NULL, NULL),
(24, 'AAA540ED30A16A15CB217F1AFB521B56', '159****8455', NULL, NULL, 'b0833c2a21e5165d0cf2a525435334a6', '00000000000000000000000000000000', NULL, '15908148455', '/uploads/member/15908148455/15211961812463.png', NULL, NULL, '4F994BA3D6337C0BB6C2C96C4AFB51E1', 1521543121, NULL, 0, 0, 1521196158, NULL, NULL, NULL, NULL),
(25, '1D807B2FF33607926933630A574199B4', '158****4828', NULL, NULL, 'bc09046e16a462e691716b666232d283', '00000000000000000000000000000000', NULL, '15884594828', '/uploads/member/15884594828/15214544678868.jpeg', NULL, NULL, 'BC5B22B16AB6D2695FA9413B95A71665', 1521453915, NULL, 0, 0, 1521442457, NULL, NULL, NULL, NULL),
(26, 'C7E1C84E96E2BA07DFE916F31BC7BF56', '139****7223', NULL, NULL, 'df3f5467ddb682da295b30291409bac1', '00000000000000000000000000000000', NULL, '13981157223', '/uploads/member/13981157223/15214431586265.png', '2018-01-19', 2, 'BFAE79239FA94519BF62C51A6A34C4CF', 1521701582, NULL, 0, 0, 1521443123, NULL, NULL, NULL, NULL),
(27, 'BD8412618CE269573166149F62138572', '187****8953', NULL, NULL, '8c56bb4e4cad1fa231c471f32ac5fac9', '00000000000000000000000000000000', NULL, '18798798953', NULL, NULL, NULL, '94F3CB30B2E347DD452C18504D6DDCDB', 1521445922, NULL, 0, 0, 1521445922, NULL, NULL, NULL, NULL),
(28, 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', '188****3227', NULL, NULL, 'af977b5fec65354eeac4668e7cdf5d93', '00000000000000000000000000000000', NULL, '18880473227', NULL, NULL, NULL, '2D55C98267E2447155BF08A662FB47DB', 1524123516, NULL, 0, 0, 1521460300, NULL, NULL, NULL, NULL),
(29, '0F83D41582FD1392AA90C08CAC56A131', '133****8119', NULL, NULL, '287effc5b6b19803b520b84538a6cf61', '00000000000000000000000000000000', NULL, '13348988119', NULL, NULL, NULL, 'F5A89EE482444889BF8C896FD33D6C60', 1521779806, NULL, 0, 0, 1521526566, NULL, NULL, NULL, NULL),
(30, 'F616157D6E1F0A03D2882E256CA32158', '138****7238', NULL, NULL, 'f58f14ce55d6bd65dd70eab2ef751f00', '00000000000000000000000000000000', NULL, '13810797238', NULL, NULL, NULL, '80D721E4E9FFFF58CBB19FFCD231DC2F', 1521647190, NULL, 0, 0, 1521647190, NULL, NULL, NULL, NULL),
(31, '97A57A6A292EF8F4CD6BE9C2AB9F1212', '157****6882', NULL, NULL, 'b464c80963eb15ba381627bc0ca4e885', '00000000000000000000000000000000', NULL, '15708456882', NULL, NULL, NULL, '2E214A98E2FEAF00DF358020F0A60021', 1522290677, NULL, 0, 0, 1522048389, NULL, NULL, NULL, NULL),
(32, 'A1B65739171C61D7F35437EB11B4BE7C', '185****5307', '请叫我强哥', NULL, '97fdf379dc3f663a834a37fc1b8b787a', '00000000000000000000000000000000', NULL, '18583925307', '/uploads/member/18583925307/15220542649727.jpeg', '2018-03-28', 1, '0A55A376730945FBB52B87BC1667BBB5', 1522639219, NULL, 0, 0, 1522048826, NULL, NULL, NULL, NULL),
(33, 'D571572BB858B3D98990E0C64CF3D8B3', '182****7741', NULL, NULL, 'ce68e30184b915f5d23a8e5f0434dc69', '00000000000000000000000000000000', NULL, '18215677741', NULL, NULL, NULL, 'BD19B77751261D9A5323C4FBBAED4E97', 1522217717, NULL, 0, 0, 1522217717, NULL, NULL, NULL, NULL),
(34, 'B5DE8C0F6459A92B559E4F572DC49F63', '176****6293', NULL, NULL, 'd09ac3d4f38b4b7b6eba67779fefa28f', '00000000000000000000000000000000', NULL, '17602886293', NULL, NULL, NULL, '3022A74A870684A1E6349F3A84A9B800', 1522389991, NULL, 0, 0, 1522378526, NULL, NULL, NULL, NULL),
(35, 'CD3440C9A64BFAEEA96D020E6E0C5089', '182****3070', NULL, NULL, '0722250b26d086c97c3836fea4df6c7b', '0C2923197E2D955123E1C2EE11034DBB', NULL, '18284823070', NULL, NULL, NULL, 'C1623A7A1ABC044100E02EC6E83C0A71', 1526026300, NULL, 0, 0, 1522725789, NULL, NULL, NULL, NULL),
(36, '6A0D5F6FEF6B8E5CFA91B7C5BA64724C', '133****0539', NULL, NULL, '6127c58aa2fb66df1a23dfadb3119787', '0C2923197E2D955123E1C2EE11034DBB', NULL, '13350850539', NULL, NULL, NULL, '841A597CBBA516200024B8B252454A80', 1522744375, NULL, 0, 0, 1522744375, NULL, NULL, NULL, NULL),
(45, '60045C823694FA91BDAA5806E707DFF9', '流年浮夸了岁月', NULL, NULL, 'd9cb24df70c94060d312c8df9d4b414b', '00000000000000000000000000000000', NULL, '', 'http://tva4.sinaimg.cn/crop.310.0.789.789.1024/81174c87gw1eglx4sgj2bj21360m1tca.jpg', NULL, 1, '10D9AA9250B84BBE6EE46C7B79BA7245', 1524125410, NULL, 0, 0, 1523432114, NULL, NULL, NULL, '2165787783'),
(46, '1B1E7D74202915E0AC84CDC6779DC488', '六月风走街穿巷 ', NULL, NULL, '11432d8dc03879d65e9518f31a6b2552', '00000000000000000000000000000000', NULL, '', 'https://thirdqq.qlogo.cn/qqapp/1106729587/1D5E6358BA0306401120B3BCB601C022/100', NULL, 1, 'F9EC97569026E52606EB16553C323704', 1524020665, NULL, 0, 0, 1524020402, NULL, '1D5E6358BA0306401120B3BCB601C022', NULL, NULL),
(47, 'B021ACC49C98428DC7FC6825D787FE39', '笋芯', NULL, NULL, '6a2aad9b936019c10779ae0b9072327e', '00000000000000000000000000000000', NULL, '15280986043', '39.107.76.66/uploads/member/15280986043/15252295589007.jpeg', NULL, NULL, '98C5A87AA7B9DD9A594B8762FF8000A0', 1526283681, NULL, 0, 0, 1524886331, NULL, NULL, NULL, NULL),
(48, '9DC98ACD1245D43D5497F4D79A85C83F', '135****0714', NULL, NULL, '08b4a02cdbc3a710bea9600a51b9931f', '00000000000000000000000000000000', NULL, '13537570714', NULL, NULL, NULL, 'B56AA269A44478CCF41227B191CDF8CE', 1525251257, NULL, 0, 0, 1525251257, NULL, NULL, NULL, NULL),
(49, 'BA9939F2DFFBE6F6FC859C7389122717', '182****3070', NULL, NULL, '14e1b600b1fd579f47433b88e8d85291', '00000000000000000000000000000000', NULL, '18284823070', NULL, NULL, NULL, 'C1623A7A1ABC044100E02EC6E83C0A71', 1526026300, NULL, 0, 0, 1525253829, NULL, NULL, NULL, NULL),
(50, '417D1B065C145A9082DD2ACD89FAFA18', '会飞的鱼', NULL, NULL, '7c67fb192b1a1b7eef143065b2731d32', '00000000000000000000000000000000', NULL, '15928091115', '39.107.76.66/uploads/member/15928091115/15253358848289.jpg', NULL, NULL, '28F10E5BAD7C951AD29E6A386F66B4A6', 1526283201, NULL, 0, 0, 1525335660, NULL, NULL, NULL, NULL),
(51, '1A1BD18126AD47265950F7161DF54FCC', '177****1226', NULL, NULL, '7c7285ea27f8a978cdfe2fc064c729e0', '00000000000000000000000000000000', NULL, '17781481226', NULL, NULL, NULL, '95F4174631360FBD30C9C4FCFF1F3C65', 1526032276, NULL, 0, 0, 1525415440, NULL, NULL, NULL, NULL),
(52, '0D9932C783B3E90A5F9E28994664C471', '155****0925', NULL, NULL, '5f5879c1cd1d13d23d864a5b71edc4d3', '00000000000000000000000000000000', NULL, '15528330925', NULL, NULL, NULL, 'FDC6E52C6A5B476926889FFEB2D9EC49', 1526053710, NULL, 0, 0, 1526053710, NULL, NULL, NULL, NULL),
(53, '9C1BD7E1148877886D7157C196D0414A', '小王子', NULL, NULL, '5d03db65ca67c89e4d51b8fbc90265b6', '00000000000000000000000000000000', NULL, '18512850113', '39.107.76.66/uploads/member/18512850113/15262837975690.png', NULL, NULL, '448F9874317B8F0E4841FC7C4CF24DC8', 1526283741, NULL, 0, 0, 1526283741, NULL, NULL, NULL, NULL),
(54, '92F90BFA14741A9971539DE45EBC8216', '136****3086', 'dammer', NULL, 'c9b0dca65827418fe068298c2734b315', '00000000000000000000000000000000', NULL, '13699443086', NULL, '19921003', 1, 'CCE503E0C8367D3D6592FE193E60215F', 1528282544, NULL, 0, 0, 1528182007, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tm_member_comment`
--

CREATE TABLE `tm_member_comment` (
  `comment_id` int(10) UNSIGNED NOT NULL COMMENT '评论id',
  `app_id` varchar(128) NOT NULL COMMENT 'appid',
  `member_code` varchar(64) NOT NULL COMMENT '用户code',
  `article_id` int(10) UNSIGNED NOT NULL COMMENT '文章id',
  `article_content` varchar(255) NOT NULL COMMENT '文章节选内容',
  `comment_content` varchar(255) NOT NULL COMMENT '评论内容',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '评论时间',
  `extend` varchar(255) DEFAULT NULL COMMENT '扩展字段'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_member_comment`
--

INSERT INTO `tm_member_comment` (`comment_id`, `app_id`, `member_code`, `article_id`, `article_content`, `comment_content`, `create_time`, `extend`) VALUES
(2, 'test', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 1, '222222222222222222222222222222', '222222222222222222222222222222222222', 1656554443, ''),
(3, 'test', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 1, '333333333333333333333333333333', '33333333333333333333333333333333333333', 1656554443, ''),
(5, 'test', '60045C823694FA91BDAA5806E707DFF9', 1, '3444444444444444444444444444', '4444444444444444444444444444', 1656554443, ''),
(6, 'test', '60045C823694FA91BDAA5806E707DFF9', 1, '11111111111111111111111111111', '11111111111111111111111111', 1656554443, '');

-- --------------------------------------------------------

--
-- Table structure for table `tm_member_history`
--

CREATE TABLE `tm_member_history` (
  `history_id` int(10) UNSIGNED NOT NULL COMMENT '浏览历史id',
  `app_id` varchar(128) NOT NULL COMMENT 'appid',
  `member_code` varchar(64) NOT NULL COMMENT '用户code',
  `article_id` int(10) UNSIGNED NOT NULL COMMENT '文章id',
  `title` varchar(255) NOT NULL COMMENT '文章标题',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '浏览时间',
  `extend` varchar(255) DEFAULT NULL COMMENT '扩展字段'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_member_history`
--

INSERT INTO `tm_member_history` (`history_id`, `app_id`, `member_code`, `article_id`, `title`, `create_time`, `extend`) VALUES
(1, 'test', '60045C823694FA91BDAA5806E707DFF9', 4, 'article5', 1523847786, NULL),
(2, 'test', '60045C823694FA91BDAA5806E707DFF9', 2, 'article2', 1522893600, NULL),
(3, 'test', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 12, 'article12', 1522893600, NULL),
(4, 'test', '60045C823694FA91BDAA5806E707DFF9', 132, 'article132', 1522980000, NULL),
(5, 'test', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 132, 'articl1e132', 1522980000, NULL),
(6, 'test', '60045C823694FA91BDAA5806E707DFF9', 132, 'articl11e132', 1523066400, NULL),
(7, 'test', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 132, 'ar2ticl11e132', 1523066400, NULL),
(8, 'test', '60045C823694FA91BDAA5806E707DFF9', 132, 'ar2ticl311e132', 1523152800, NULL),
(9, 'test', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 132, 'ar2ticl311e5132', 1523152800, NULL),
(10, 'test', '60045C823694FA91BDAA5806E707DFF9', 132, 'ar2ticl3113e5132', 1523239200, NULL),
(11, 'tes1t', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 132, 'ar2ticl3113e5132', 1523239200, NULL),
(12, 'te2s1t', '60045C823694FA91BDAA5806E707DFF9', 132, 'ar2ticl3113e5132', 1523325600, NULL),
(13, 't4e2s1t', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 132, 'ar2ticl3113e5132', 1523325600, NULL),
(14, 't4e21s1t', '60045C823694FA91BDAA5806E707DFF9', 132, 'ar2ticl3113e5132', 1523412000, NULL),
(15, 't4e21s51t', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 132, 'ar2ticl3113e5132', 1523412000, NULL),
(16, 't4e21s51t', '60045C823694FA91BDAA5806E707DFF9', 132, 'ar2ticl31131e5132', 1523498400, NULL),
(17, 't4e21s51t', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 132, 'ar2ticl313131e5132', 1523498400, NULL),
(18, 't4e21s51t', '60045C823694FA91BDAA5806E707DFF9', 132, 'ar2ticl3413131e5132', 1523584800, NULL),
(19, 't4e21s51t', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 132, 'ar2ticl34131131e5132', 1523584800, NULL),
(21, 't4e21s51t', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 132, 'ar2ticl2341313131e5132', 1523671200, NULL),
(22, 't4e21s51t', '60045C823694FA91BDAA5806E707DFF9', 132, 'ar2ticl12341313131e5132', 1523757600, NULL),
(24, 't4e21s51t', '60045C823694FA91BDAA5806E707DFF9', 132, 'ar2ticl1223341313131e5132', 1523847786, NULL),
(25, 't4e21s51t', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 132, 'ar2ticl12243341313131e5132', 1523847786, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tm_member_star`
--

CREATE TABLE `tm_member_star` (
  `star_id` int(10) UNSIGNED NOT NULL COMMENT '收藏id',
  `member_code` varchar(64) NOT NULL COMMENT '收藏用户code',
  `app_id` varchar(128) NOT NULL COMMENT 'appid',
  `article_id` int(10) UNSIGNED NOT NULL COMMENT '文章id',
  `title` varchar(50) NOT NULL COMMENT '文章标题',
  `intro` varchar(100) DEFAULT NULL COMMENT '文章简介',
  `pic` varchar(255) DEFAULT NULL COMMENT '文章图片',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '收藏时间',
  `extend` varchar(255) DEFAULT NULL COMMENT '扩展字段'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_member_star`
--

INSERT INTO `tm_member_star` (`star_id`, `member_code`, `app_id`, `article_id`, `title`, `intro`, `pic`, `create_time`, `extend`) VALUES
(2, 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 'test', 2, 'article2', NULL, NULL, 1523514903, NULL),
(7, 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 'test', 7, 'article7', '', '', 1523514922, ''),
(10, '60045C823694FA91BDAA5806E707DFF9', 'test', 10, 'article10', '', '', 1523514922, ''),
(11, '60045C823694FA91BDAA5806E707DFF9', 'test', 11, 'article11', '', '', 1523514922, ''),
(12, '60045C823694FA91BDAA5806E707DFF9', 'test', 12, 'article12', '', '', 1523514922, ''),
(13, '60045C823694FA91BDAA5806E707DFF9', 'test', 13, 'article13', '', '', 1523514922, ''),
(14, '29CA8C53B38C4A2A0C778E682F8FF6DA', 'testid', 2, '成都日报', NULL, NULL, 1528353697, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tm_myprize`
--

CREATE TABLE `tm_myprize` (
  `que_id` int(10) NOT NULL COMMENT '主键',
  `user_id` int(10) DEFAULT NULL COMMENT '用户id',
  `mark` int(10) DEFAULT NULL COMMENT '用户答题分数',
  `prize_id` int(10) DEFAULT NULL COMMENT '用户奖品',
  `p_status` int(10) DEFAULT NULL COMMENT '奖品是否兑换0未兑换1已兑换',
  `create_at` int(10) DEFAULT NULL COMMENT '中奖时间',
  `update_at` int(10) NOT NULL COMMENT '兑换时间',
  `yes` int(10) DEFAULT NULL,
  `wrong` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tm_myprize`
--

INSERT INTO `tm_myprize` (`que_id`, `user_id`, `mark`, `prize_id`, `p_status`, `create_at`, `update_at`, `yes`, `wrong`) VALUES
(1, 55, 80, 2, 0, NULL, 0, NULL, NULL),
(2, 56, 30, 3, 1, NULL, 0, 32, 0),
(3, NULL, 39, NULL, NULL, 1521456267, 0, 39, -7),
(4, NULL, 43, NULL, NULL, 1521456324, 0, 32, 0),
(5, NULL, 43, NULL, NULL, 1521456325, 0, 32, 0),
(6, NULL, 43, NULL, NULL, 1521456326, 0, 32, 0),
(7, NULL, 43, NULL, NULL, 1521456326, 0, 32, 0),
(8, NULL, 43, NULL, NULL, 1521456326, 0, 32, 0),
(9, NULL, 43, NULL, NULL, 1521456427, 0, 32, 0),
(10, NULL, 43, NULL, NULL, 1521456428, 0, 32, 0),
(11, NULL, 43, NULL, NULL, 1521456428, 0, 32, 0),
(12, NULL, 43, NULL, NULL, 1521456469, 0, 39, -7),
(13, NULL, 43, NULL, NULL, 1521456470, 0, 39, -7),
(14, NULL, 43, NULL, NULL, 1521456471, 0, 39, -7),
(15, NULL, 43, NULL, NULL, 1521456471, 0, 39, -7),
(16, NULL, 43, NULL, NULL, 1521456493, 0, 39, 7),
(17, NULL, 43, NULL, NULL, 1521456494, 0, 39, 7),
(18, NULL, 43, NULL, NULL, 1521456494, 0, 39, 7),
(19, NULL, 0, NULL, NULL, 1521457121, 0, 0, -32),
(20, NULL, 0, NULL, NULL, 1521457868, 0, 0, -32),
(21, NULL, 0, NULL, NULL, 1521459149, 0, 0, -32),
(22, NULL, 0, NULL, NULL, 1521459159, 0, 0, -32),
(23, NULL, 0, NULL, NULL, 1521459193, 0, 0, -32),
(24, NULL, 0, NULL, NULL, 1521459202, 0, 0, -32),
(25, 50, 0, NULL, NULL, 1521459217, 0, 0, -32),
(26, 50, 0, NULL, NULL, 1521459231, 0, 0, -32),
(27, 50, 0, NULL, NULL, 1521459320, 0, 0, -32),
(28, 50, 0, NULL, NULL, 1521459327, 0, 0, -32),
(29, 50, 0, NULL, NULL, 1521459361, 0, 0, -32),
(30, 50, 0, NULL, NULL, 1521459401, 0, 0, -32),
(31, 50, 0, NULL, NULL, 1521459547, 0, 0, -32),
(32, 50, 0, NULL, NULL, 1521459599, 0, 0, -32),
(33, 50, 0, NULL, NULL, 1521459913, 0, 0, 32),
(34, 50, 0, NULL, NULL, 1521459998, 0, 0, 32),
(35, 50, 0, NULL, NULL, 1521460122, 0, 0, 32),
(36, NULL, 0, NULL, NULL, 1521460681, 0, 0, 32),
(37, NULL, 0, NULL, NULL, 1521460684, 0, 0, 32),
(38, NULL, 1, NULL, NULL, 1521460726, 0, 1, 31),
(39, NULL, 1, NULL, NULL, 1521460727, 0, 1, 31),
(40, NULL, 1, NULL, NULL, 1521460728, 0, 1, 31),
(41, 50, 0, NULL, NULL, 1521513190, 0, 0, 39),
(42, 50, 0, NULL, NULL, 1521517265, 0, 0, 39),
(43, 50, 0, NULL, NULL, 1521525198, 0, 0, 39),
(44, 50, 0, NULL, NULL, 1521525618, 0, 0, 39),
(45, 50, 0, NULL, NULL, 1521525784, 0, 0, 39),
(46, 50, 0, NULL, NULL, 1521526766, 0, 0, 39),
(47, 50, 0, NULL, NULL, 1521533073, 0, 0, 39),
(48, 50, 0, NULL, NULL, 1521536008, 0, 0, 39),
(49, 50, 0, NULL, NULL, 1521536155, 0, 0, 39),
(50, 0, 0, NULL, NULL, 1521631879, 0, 0, 39),
(51, 0, 0, NULL, NULL, 1522026092, 0, 0, 39),
(52, 0, 0, NULL, NULL, 1522113990, 0, 0, 39),
(53, 0, 0, NULL, NULL, 1522655644, 0, 0, 39),
(54, 0, 0, NULL, NULL, 1523350930, 0, 0, 41),
(55, 0, 0, NULL, NULL, 1523350957, 0, 0, 41);

-- --------------------------------------------------------

--
-- Table structure for table `tm_parameterex`
--

CREATE TABLE `tm_parameterex` (
  `source_id` int(10) UNSIGNED NOT NULL COMMENT '扩展数据id',
  `entity_type` varchar(64) NOT NULL COMMENT '实体对象的类型枚举“component””site““user”',
  `attribute_key` varchar(64) NOT NULL COMMENT '扩展属性名称',
  `attribute_value` varchar(64) NOT NULL COMMENT '扩展数据值',
  `description` varchar(64) NOT NULL COMMENT '描述',
  `create_code` varchar(64) DEFAULT NULL COMMENT '创建用户代码',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_portal`
--

CREATE TABLE `tm_portal` (
  `portal_key` varchar(64) NOT NULL COMMENT '应用列表key',
  `portal_value` text NOT NULL COMMENT '应用列表菜单值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_portal`
--

INSERT INTO `tm_portal` (`portal_key`, `portal_value`) VALUES
('27483D4C-1A45-2865-9B8C-701EA265ED92', '[{\"key\":\"0\",\"title\":\"资讯视频\",\"children\":[]},{\"key\":\"1\",\"title\":\"互动娱乐\",\"category\":\"1\",\"url\":\"\",\"children\":[],\"thunmb\":\"/uploads/default.png\"},{\"key\":\"2\",\"title\":\"管理\",\"category\":\"2\",\"url\":\"\",\"children\":[{\"key\":\"2-0\",\"title\":\"会员管理\",\"type\":\"module\",\"component_code\":\"B2433C8761B4671553FE6A45C9DCC4CB\",\"site_code\":\"00000000000000000000000000000000\",\"app_code\":\"member\",\"category\":\"2\",\"admin_url\":\"/#/Site/Member\",\"url\":\"member/Index/Index?token=3E6CAD130AAD3148FE8D1A25FAD0016E&site_id=00000000000000000000000000000000\",\"thumb\":\"/uploads/default/20180601/6b270aae57882c48982acae1e597db92.png\"}],\"thunmb\":\"/uploads/default.png\"}]'),
('2760AB599245A3B9C4CCBB023F08F151', '[{\"key\":\"0\",\"title\":\"所有应用\",\"children\":[{\"key\":\"0-0\",\"title\":\"新闻资讯\",\"type\":\"url\",\"app_code\":\"\",\"admin_url\":\"\",\"index_url\":\"\",\"category\":\"0\",\"url\":\"\",\"thumb\":\"uploads/icon/44.png\",\"webUrl\":\"/hlhj_news/index/index\",\"site_code\":\"00000000000000000000000000000000\"},{\"key\":\"0-1\",\"title\":\"投票\",\"type\":\"url\",\"app_code\":\"\",\"admin_url\":\"\",\"index_url\":\"\",\"category\":\"0\",\"url\":\"\",\"thumb\":\"uploads/icon/15.png\",\"webUrl\":\"/hlhjvote/activity/index\",\"site_code\":\"00000000000000000000000000000000\"},{\"key\":\"0-2\",\"title\":\"答题\",\"type\":\"url\",\"app_code\":\"\",\"admin_url\":\"\",\"index_url\":\"\",\"category\":\"0\",\"url\":\"\",\"thumb\":\"/uploads/default/20180511/e2fcb00b976f61dc6af34bffd175f5c9.png\",\"webUrl\":\"/hlhjanswer/activity/index\",\"site_code\":\"00000000000000000000000000000000\"}]}]'),
('364F56004D97FA67AE5FD107C7B5292B', '[{\"key\":\"0\",\"title\":\"所有应用\",\"children\":[{\"key\":\"0-0\",\"title\":\"rest\",\"type\":\"url\",\"app_code\":\"\",\"admin_url\":\"\",\"index_url\":\"\",\"category\":\"0\",\"url\":\"\",\"thumb\":\"uploads/icon/11.png\",\"webUrl\":\"/application/rest/resource/tianma-admin\",\"site_code\":\"00000000000000000000000000000000\"},{\"key\":\"0-1\",\"title\":\"我要看电视\",\"type\":\"url\",\"app_code\":\"\",\"admin_url\":\"\",\"index_url\":\"\",\"category\":\"0\",\"url\":\"\",\"thumb\":\"uploads/icon/25.png\",\"webUrl\":\"/application/rest_tv/tianma-admin\"},{\"key\":\"0-2\",\"title\":\"我要听广播\",\"type\":\"url\",\"app_code\":\"\",\"admin_url\":\"\",\"index_url\":\"\",\"category\":\"0\",\"url\":\"\",\"thumb\":\"uploads/icon/38.png\",\"webUrl\":\"/application/rest_gb/tianma-admin\"}]}]');

-- --------------------------------------------------------

--
-- Table structure for table `tm_privilege`
--

CREATE TABLE `tm_privilege` (
  `privilege_id` int(10) UNSIGNED NOT NULL COMMENT '权限ID',
  `privilege_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '权限类型(0：站点权限，1：菜单权限)',
  `privilege_name` varchar(64) NOT NULL COMMENT '权限名称',
  `privilege_code` varchar(64) NOT NULL COMMENT '权限代码',
  `privilege_intro` varchar(128) DEFAULT NULL COMMENT '权限说明',
  `parent_pri_id` varchar(32) DEFAULT NULL COMMENT '父权限代码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_privilege`
--

INSERT INTO `tm_privilege` (`privilege_id`, `privilege_type`, `privilege_name`, `privilege_code`, `privilege_intro`, `parent_pri_id`) VALUES
(6, 0, '基础数据>分类管理', 'ClassManage', '菜单权限', NULL),
(7, 0, '基础数据>权限管理', 'PrivilegeManage', '菜单权限', NULL),
(10, 0, '基础数据>角色管理', 'RoleManage', '菜单权限', NULL),
(11, 0, '基础数据>部门、用户管理', 'DepartmentUserManage', '菜单权限', NULL),
(12, 0, '组件>组件管理', 'ComponentManage', '菜单权限', NULL),
(13, 0, '系统>系统升级', 'SystemUpdate', '菜单权限', NULL),
(16, 0, '会员>会员管理', 'MemberManage', '菜单权限', NULL),
(17, 0, '站点>站点管理', 'SiteManage', '菜单权限', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tm_prize`
--

CREATE TABLE `tm_prize` (
  `prize_id` int(10) NOT NULL COMMENT '奖品id',
  `prize_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '奖品名称',
  `prize_content` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '奖品描述',
  `prize_status` int(10) DEFAULT NULL COMMENT '兑换方式',
  `prize_states` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '是否展示',
  `prize_sum` int(10) DEFAULT NULL COMMENT '奖品数量',
  `prize_mark` int(10) DEFAULT NULL COMMENT '单个奖品积分值',
  `prize_type` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '奖品类型',
  `create_at` int(10) DEFAULT NULL COMMENT '新增时间',
  `update_at` int(10) DEFAULT NULL COMMENT '修改时间',
  `prize_image` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '奖品图片'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tm_prize`
--

INSERT INTO `tm_prize` (`prize_id`, `prize_name`, `prize_content`, `prize_status`, `prize_states`, `prize_sum`, `prize_mark`, `prize_type`, `create_at`, `update_at`, `prize_image`) VALUES
(4, '我喜欢1', '；‘；离开’111', 0, '是', 1223111, 502311, '实物', 1521291291, 1523437181, 'http://39.107.76.66/application/prize/source/upload/20180411/1523419369726.jpg'),
(5, '我喜欢', '；‘；离开’', 1, '否', 12, 50, '虚拟', 1521291298, 1521291298, NULL),
(6, '收到', '十大', 1, '是', 12, 52, '虚拟', 1521291449, 1521291449, '20180319/c7c029722016b748b1207564b065e056.jpg'),
(7, '1212', '1哈哈', 1, '是', 12, 12, '虚拟', 1521291584, 1521352231, '20180319/cc274e738a4ae2f90e31b8c1d245a50e.jpg'),
(8, '加上', '几点回家熬枯受淡', 0, '是', 122, 22, '实物', 1521291705, 1523437224, '20180319/4dd17a4118ebf3314122877b85d0d973.jpg'),
(9, '阿萨', '撒大声地', 0, '是', 12, 552, '实物', 1521291914, 1521291914, '20180319/ea843fe5c7faf52fa95ffbd773c1d7f0.jpg'),
(10, '4545', '撒大声地', 1, '是', 52, 50, '实物', 1521334925, 1521334925, NULL),
(16, '委屈', '十大', 0, '是', 25, 50, '实物', 1521443653, 1521443653, NULL),
(17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1521443653, 1521443653, NULL),
(18, '将尽快尽快看看', '讲话稿集合集合管', NULL, NULL, 10, 20, '实物', 1521445044, 1521445044, '20180319/e58d23951448966995cfbb070048ba6b.jpg'),
(19, '小熊（测试奖品）', '测试奖品', 0, '是', 100, 10000, '实物', 1523252028, 1523252028, 'http://192.168.4.123/application/prize/source/upload/20180409/1523251989635.png'),
(20, '魔法棒', '魔法棒（测试）', 1, '是', 20000, 100, '虚拟', 1523252107, 1523252514, NULL),
(21, 'asdfasd', 'fasdfasdfas', NULL, NULL, 32, 23, '实物', 1523407809, 1523407809, 'http://39.107.76.66/application/prize/source/upload/20180411/1523407800796.jpg'),
(22, '2333', '2233232', 0, '否', 23, 23, '实物', 1523440206, 1523440392, 'http://39.107.76.66/application/prize/source/upload/20180411/1523440199551.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tm_push`
--

CREATE TABLE `tm_push` (
  `push_id` bigint(20) UNSIGNED NOT NULL COMMENT '推送id',
  `push_content` text NOT NULL COMMENT '推送内容',
  `member_code` varchar(255) DEFAULT '' COMMENT '用户极光pushCode',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '发送时间',
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:未读 1:已读'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_push`
--

INSERT INTO `tm_push` (`push_id`, `push_content`, `member_code`, `create_time`, `state`) VALUES
(4, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 1523242461, 0),
(5, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 1523242462, 0),
(6, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 1523242582, 0),
(8, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', '60045C823694FA91BDAA5806E707DFF9', 1523242596, 1),
(9, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', '60045C823694FA91BDAA5806E707DFF9', 1523242596, 1),
(10, '{\"content\":\"fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', '60045C823694FA91BDAA5806E707DFF9', 1523242596, 1),
(12, '{\"content\":\"fffffff\",\"title\":\"tiiiiiiiiiiiiiiitle\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', '60045C823694FA91BDAA5806E707DFF9', 1523242596, 0),
(13, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', 'B6DD59C5180F23DDAFB56A6F9BF6BBC8', 1523242596, 0),
(14, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', '60045C823694FA91BDAA5806E707DFF9', 1523242596, 1),
(15, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', '60045C823694FA91BDAA5806E707DFF9', 1523242596, 0),
(16, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', '60045C823694FA91BDAA5806E707DFF9', 1523242596, 0),
(17, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', '60045C823694FA91BDAA5806E707DFF9', 1523242596, 0),
(18, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', '60045C823694FA91BDAA5806E707DFF9', 1523242596, 0),
(19, '{\"content\":\"fffffff\",\"title\":\"title\",\"iosInfo\":{\"native\":true,\"src\":\"HViewController\",\"paramStr\":\"\",\"wwwFolder\":\"\"},\"androidInfo\":{\"native\":true,\"src\":\"com.higgses.news.mobile.newsmap.NewsMapFragment\",\"paramStr\":\"21e364890c730daff9e413660e04d924\",\"wwwFolder\":\"comp01\\/\"},\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', '60045C823694FA91BDAA5806E707DFF9', 1523242596, 0),
(20, '{\"title\":\"test\",\"content\":\"testttttttttttttttttttttttttttt\",\"member_code\":\"29CA8C53B38C4A2A0C778E682F8FF6DA\"}', '29CA8C53B38C4A2A0C778E682F8FF6DA', 1526552387, 0),
(21, '{\"title\":\"testtitle\",\"content\":\"testcontent\",\"member_code\":\"9C1BD7E1148877886D7157C196D0414A\"}', '', 1528259432, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tm_question`
--

CREATE TABLE `tm_question` (
  `id` int(10) UNSIGNED NOT NULL,
  `question_content` text CHARACTER SET utf8 COMMENT '问题',
  `create_at` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_at` int(10) DEFAULT NULL COMMENT '修改时间',
  `type` varchar(10) CHARACTER SET utf8 DEFAULT NULL COMMENT '题型',
  `question_resolve` text CHARACTER SET utf8 COMMENT '解析',
  `type_id` int(10) DEFAULT NULL COMMENT '所属哪个题库',
  `mark` int(10) DEFAULT NULL COMMENT '分数'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tm_question`
--

INSERT INTO `tm_question` (`id`, `question_content`, `create_at`, `update_at`, `type`, `question_resolve`, `type_id`, `mark`) VALUES
(1, '1', 1521268991, 1521268991, NULL, '计划的技术股份', 5, 1),
(2, '2', 1521269310, 1521269310, NULL, '2', 8, 1),
(3, '3', 1521269437, 1521269437, NULL, '65656', 8, 1),
(4, '4', 1521270430, 1521270430, NULL, '65656', 8, 1),
(5, '5', 1521270517, 1521270517, NULL, '安达市多', 5, 1),
(6, '54646', 1521270831, 1521270831, NULL, '65465465', 6, 1),
(7, '654645611', 1521270900, 1521270900, NULL, '654d654111', 7, 1),
(8, '21', 1521270964, 1521270964, NULL, '大趋势多', 8, 1),
(9, '999', 1521271224, 1521271224, NULL, '9999', 9, 1),
(10, '10', 1521272126, 1521272126, NULL, '10', 3, 1),
(11, '11', 1521272903, 1521272903, NULL, '11', 9, 1),
(12, '12', 1521273011, 1521273011, NULL, '水电费', 8, 1),
(13, '13', 1521274033, 1521274033, NULL, '13', 7, 1),
(14, '14', 1521274108, 1521274108, NULL, '14', 7, 12),
(15, '15', 1521274122, 1521274122, NULL, '15', 7, 1),
(16, '16', 1521274139, 1521274139, NULL, '16', 9, 1),
(17, '17', 1521274447, 1521274447, NULL, '17', 3, 1),
(18, '18', 1521275748, 1521275748, NULL, '18', 5, 1),
(19, '19', 1521275882, 1521275882, NULL, '大声道', 5, 1),
(20, '20', 1521275978, 1521275978, NULL, '20', 5, 1),
(21, '21', 1521276139, 1521276139, NULL, '开始的发货速度快解放后收款计划', 5, 1),
(22, '22', 1521276328, 1521276328, NULL, '开始的发货速度快解放后收款计划', 5, 1),
(23, '23', 1521276581, 1521276581, NULL, '开始的发货速度快解放后收款计划', 5, 1),
(24, '24', 1521276667, 1521276667, NULL, '开始的发货速度快解放后收款计划', 5, 1),
(25, '25', 1521276991, 1521276991, NULL, '决定国家', 7, 1),
(26, '安慰', 1521277061, 1521277061, NULL, '决定国家', 7, 1),
(27, '安慰', 1521277116, 1521277116, NULL, '决定国家', 7, 1),
(28, '安慰', 1521277172, 1521277172, NULL, '决定国家', 7, 1),
(29, '安慰sad', 1521277636, 1521277636, NULL, '决定国家', 7, 1),
(30, '大号', 1521279815, 1521279815, NULL, '疴看到回复可见', 7, 1),
(31, '大号', 1521279994, 1521279994, NULL, '疴看到回复可见', 7, 1),
(32, '曹尼玛', 1521370569, 1521370569, NULL, '呵呵', 7, 1),
(33, '美好承诺', 1521465226, 1521465226, NULL, '，空间可能就看见', 7, NULL),
(34, '美女', 1521465289, 1521465289, NULL, '6546456', 7, NULL),
(35, '美女', 1521465466, 1521465466, NULL, '看见好看就好', 7, NULL),
(36, '美女', 1521465553, 1521465553, NULL, '环境和钢结构', 6, NULL),
(37, '妹子', 1521467392, 1521467392, NULL, '颗粒剂', 7, NULL),
(38, '加不加', 1521467669, 1521467669, NULL, '。，看见', 5, NULL),
(39, '', 1521468030, 1521468030, NULL, '', 5, NULL),
(40, '搜索', 1522758020, 1522758020, '单选', '2', 9, 2),
(41, '先有鸡还是先有蛋？', 1523244719, 1523244719, '单选', '要先有钱', 12, 10);

-- --------------------------------------------------------

--
-- Table structure for table `tm_role`
--

CREATE TABLE `tm_role` (
  `role_id` int(10) UNSIGNED NOT NULL COMMENT '角色ID',
  `role_code` varchar(64) NOT NULL COMMENT '角色代码',
  `role_name` varchar(128) NOT NULL COMMENT '角色名称',
  `role_intro` varchar(128) DEFAULT NULL COMMENT '角色介绍',
  `branch_code` varchar(64) DEFAULT NULL COMMENT '分支机构代码',
  `externa_code` varchar(64) DEFAULT NULL COMMENT '扩展代码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_role`
--

INSERT INTO `tm_role` (`role_id`, `role_code`, `role_name`, `role_intro`, `branch_code`, `externa_code`) VALUES
(1, '1', '超级管理员', '', '', NULL),
(2, 'AC2E04794F49FC6DF05F6B78BA8CC4DB', '编辑', '', '', NULL),
(3, 'A11A60B4A075515AB09134B914D6E7FF', '记者', '', '', NULL),
(4, '6F52253DA3B9C3719DEF9C1F071A0698', '主任', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tm_role_component`
--

CREATE TABLE `tm_role_component` (
  `role_code` varchar(64) NOT NULL COMMENT '角色code',
  `component_code` varchar(64) NOT NULL COMMENT '应用插件code'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_role_component`
--

INSERT INTO `tm_role_component` (`role_code`, `component_code`) VALUES
('1', 'E5C56F2646F50B5EC5C613E9D430427E'),
('1', '18A88AB07872B12D767B3BD654167AC9'),
('1', 'D2774DD3BC69F6EF535073440170C61F'),
('1', 'B2433C8761B4671553FE6A45C9DCC4CB'),
('1', '5C13A6E742091B22A98A6F492EBC3740'),
('A8D7D4031B79C2676D432ADEA267A171', 'E5C56F2646F50B5EC5C613E9D430427E'),
('A8D7D4031B79C2676D432ADEA267A171', '18A88AB07872B12D767B3BD654167AC9'),
('A8D7D4031B79C2676D432ADEA267A171', 'D2774DD3BC69F6EF535073440170C61F'),
('A8D7D4031B79C2676D432ADEA267A171', 'B2433C8761B4671553FE6A45C9DCC4CB'),
('A8D7D4031B79C2676D432ADEA267A171', '5C13A6E742091B22A98A6F492EBC3740'),
('A11A60B4A075515AB09134B914D6E7FF', 'E5C56F2646F50B5EC5C613E9D430427E'),
('A11A60B4A075515AB09134B914D6E7FF', '18A88AB07872B12D767B3BD654167AC9'),
('A11A60B4A075515AB09134B914D6E7FF', 'D2774DD3BC69F6EF535073440170C61F'),
('A11A60B4A075515AB09134B914D6E7FF', 'B2433C8761B4671553FE6A45C9DCC4CB'),
('A11A60B4A075515AB09134B914D6E7FF', '5C13A6E742091B22A98A6F492EBC3740'),
('AC2E04794F49FC6DF05F6B78BA8CC4DB', 'E5C56F2646F50B5EC5C613E9D430427E'),
('AC2E04794F49FC6DF05F6B78BA8CC4DB', '18A88AB07872B12D767B3BD654167AC9'),
('AC2E04794F49FC6DF05F6B78BA8CC4DB', 'D2774DD3BC69F6EF535073440170C61F'),
('1', 'C5953A90CBA748A88DD3B276E6A2725B'),
('1', '85B8309028CD4A808DFE353B00C9A9AC'),
('1', 'C488967B8270400CBCD4CF6B0A599274'),
('1', 'BC49B0ED8C214EDCABA6E6F3C2D23058'),
('1', '50564A2E6B204F9AADE1A4A52C13BF68'),
('1', 'E29B1403654E42F499731ED3D23AA070');

-- --------------------------------------------------------

--
-- Table structure for table `tm_role_privilege`
--

CREATE TABLE `tm_role_privilege` (
  `privilege_code` varchar(64) NOT NULL COMMENT '权限CODE',
  `role_code` varchar(64) NOT NULL COMMENT '角色CODE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_role_privilege`
--

INSERT INTO `tm_role_privilege` (`privilege_code`, `role_code`) VALUES
('SiteManage', 'A8D7D4031B79C2676D432ADEA267A171'),
('MemberManage', 'A8D7D4031B79C2676D432ADEA267A171'),
('SystemUpdate', 'A8D7D4031B79C2676D432ADEA267A171'),
('ComponentManage', 'A8D7D4031B79C2676D432ADEA267A171'),
('DepartmentUserManage', 'A8D7D4031B79C2676D432ADEA267A171'),
('RoleManage', 'A8D7D4031B79C2676D432ADEA267A171'),
('PrivilegeManage', 'A8D7D4031B79C2676D432ADEA267A171'),
('ClassManage', 'A8D7D4031B79C2676D432ADEA267A171'),
('SiteManage', '1'),
('MemberManage', '1'),
('SystemUpdate', '1'),
('ComponentManage', '1'),
('DepartmentUserManage', '1'),
('RoleManage', '1'),
('PrivilegeManage', '1'),
('ClassManage', '1'),
('ComponentManage', 'A11A60B4A075515AB09134B914D6E7FF'),
('DepartmentUserManage', 'A11A60B4A075515AB09134B914D6E7FF'),
('MemberManage', 'AC2E04794F49FC6DF05F6B78BA8CC4DB'),
('DepartmentUserManage', 'AC2E04794F49FC6DF05F6B78BA8CC4DB'),
('RoleManage', 'AC2E04794F49FC6DF05F6B78BA8CC4DB'),
('PrivilegeManage', 'AC2E04794F49FC6DF05F6B78BA8CC4DB'),
('ClassManage', 'AC2E04794F49FC6DF05F6B78BA8CC4DB');

-- --------------------------------------------------------

--
-- Table structure for table `tm_role_site`
--

CREATE TABLE `tm_role_site` (
  `role_code` varchar(64) NOT NULL COMMENT '角色代码',
  `site_code` varchar(64) NOT NULL COMMENT '站点代码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_role_site`
--

INSERT INTO `tm_role_site` (`role_code`, `site_code`) VALUES
('1', '0C2923197E2D955123E1C2EE11034DBB'),
('AC2E04794F49FC6DF05F6B78BA8CC4DB', '0C2923197E2D955123E1C2EE11034DBB'),
('A11A60B4A075515AB09134B914D6E7FF', '00000000000000000000000000000000');

-- --------------------------------------------------------

--
-- Table structure for table `tm_role_user`
--

CREATE TABLE `tm_role_user` (
  `role_code` varchar(64) NOT NULL COMMENT '角色代码',
  `user_code` varchar(64) NOT NULL COMMENT '用户代码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_role_user`
--

INSERT INTO `tm_role_user` (`role_code`, `user_code`) VALUES
('1', '27483D4C-1A45-2865-9B8C-701EA265ED92'),
('1', 'B30762EB040355005B25CF0265168E98'),
('A11A60B4A075515AB09134B914D6E7FF', '877FE24147662AF72B38B593D672D62F'),
('A11A60B4A075515AB09134B914D6E7FF', '85517AEC63DA7792F0635CCD690CFC7E'),
('AC2E04794F49FC6DF05F6B78BA8CC4DB', '85517AEC63DA7792F0635CCD690CFC7E'),
('AC2E04794F49FC6DF05F6B78BA8CC4DB', 'EDC76357E2C2B0C9A0DF0B77B2C5D7DA'),
('A11A60B4A075515AB09134B914D6E7FF', 'A123AADA8F737D53EA9B74DA8E118F4C'),
('A11A60B4A075515AB09134B914D6E7FF', '0D431614D4D306DC655FD503CC2C9475'),
('AC2E04794F49FC6DF05F6B78BA8CC4DB', 'CD4003E875B7CD31F7D474F367504BFA'),
('A11A60B4A075515AB09134B914D6E7FF', '208FC72EED62175AC48816785464F595'),
('A11A60B4A075515AB09134B914D6E7FF', '00AD8317A5FC80F9F32E8A5F801C93C0'),
('A11A60B4A075515AB09134B914D6E7FF', '1E3982BAAE3459417B9AB6EBC85CAF11'),
('A11A60B4A075515AB09134B914D6E7FF', '3DB07C1C79EE6E94E93278CA0D1E4DF1'),
('A11A60B4A075515AB09134B914D6E7FF', 'A9182AC758E47A58415188DE1E352FE5'),
('AC2E04794F49FC6DF05F6B78BA8CC4DB', 'A9182AC758E47A58415188DE1E352FE5'),
('1', 'A9182AC758E47A58415188DE1E352FE5');

-- --------------------------------------------------------

--
-- Table structure for table `tm_ruleprize`
--

CREATE TABLE `tm_ruleprize` (
  `rule_id` int(10) NOT NULL COMMENT '规则id',
  `rule_content` text CHARACTER SET utf8 COMMENT '活动规则内容',
  `rule_title` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '规则介绍',
  `rule_news` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '知识竞答内容',
  `rule_style` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '活动方式',
  `rule_answer` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '答题方式',
  `rule_prize` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '奖品兑换方式',
  `rule_rules` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '注意事项',
  `rule_start` int(10) DEFAULT NULL COMMENT '活动开始时间',
  `rule_end` int(10) DEFAULT NULL COMMENT '活动结束'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tm_ruleprize`
--

INSERT INTO `tm_ruleprize` (`rule_id`, `rule_content`, `rule_title`, `rule_news`, `rule_style`, `rule_answer`, `rule_prize`, `rule_rules`, `rule_start`, `rule_end`) VALUES
(1, '活动规则内容：11122\n3422', '活动介绍：11134222', '知识竞答内容1143222', '活动方式113422', '答题方式：11134222', '奖品兑换方式：113422', '注意事项：34\n111222', 1519660800, 1522425600);

-- --------------------------------------------------------

--
-- Table structure for table `tm_site`
--

CREATE TABLE `tm_site` (
  `site_id` int(10) UNSIGNED NOT NULL COMMENT '站点id',
  `site_code` varchar(64) NOT NULL COMMENT '站点代码',
  `site_name` varchar(128) NOT NULL COMMENT '站点名称',
  `site_alias` varchar(128) DEFAULT NULL COMMENT '站点别名(或英文名)',
  `site_url` varchar(128) DEFAULT NULL COMMENT '站点url地址',
  `site_intro` varchar(128) DEFAULT NULL COMMENT '站点描述',
  `index_template_id` varchar(32) DEFAULT NULL COMMENT '首页模板ID',
  `add_user` varchar(64) NOT NULL COMMENT '添加站点的用户名称',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '添加站点日期',
  `modify_user` varchar(64) DEFAULT NULL COMMENT '修改站点的用户名称',
  `modify_time` int(10) UNSIGNED DEFAULT NULL COMMENT '修改站点的日期',
  `img_upload_address` varchar(128) DEFAULT NULL COMMENT '图片服务器上传地址',
  `img_access_url` varchar(128) DEFAULT NULL COMMENT '图片服务器域名',
  `static_web_upload_address` varchar(128) DEFAULT NULL COMMENT '静态网页服务器上传地址',
  `static_web_access_url` varchar(128) DEFAULT NULL COMMENT '静态网页域名',
  `cdn_upload_address` varchar(128) DEFAULT NULL COMMENT 'CDN视频上传地址',
  `cdn_access_url` varchar(128) DEFAULT NULL COMMENT 'CDN视频访问域名',
  `local_storage_path` varchar(128) DEFAULT NULL COMMENT '站点页面静态存储地址',
  `is_auto_published` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否自动发布（0表示否，1表示是）',
  `is_auto_approval` tinyint(1) UNSIGNED DEFAULT '1' COMMENT '是否需要审核（0表示否，1表示是）',
  `is_work` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否启用（0表示否，1表示是）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tm_site`
--

INSERT INTO `tm_site` (`site_id`, `site_code`, `site_name`, `site_alias`, `site_url`, `site_intro`, `index_template_id`, `add_user`, `add_time`, `modify_user`, `modify_time`, `img_upload_address`, `img_access_url`, `static_web_upload_address`, `static_web_access_url`, `cdn_upload_address`, `cdn_access_url`, `local_storage_path`, `is_auto_published`, `is_auto_approval`, `is_work`) VALUES
(1, '00000000000000000000000000000000', '初始化站点', NULL, NULL, '初始化站点', NULL, 'admin', 1514954967, 'admin', 1514959188, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tm_user`
--

CREATE TABLE `tm_user` (
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `user_code` varchar(64) NOT NULL COMMENT '用户代码',
  `user_name` varchar(64) NOT NULL COMMENT '用户名称',
  `real_name` varchar(64) DEFAULT NULL COMMENT '用户真实姓名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `branch_id` int(10) UNSIGNED NOT NULL COMMENT '分支机构ID',
  `is_branch_admin` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是或否分支机构管理员(0：普通用户，1：管理员)',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态(0：正常，1：停用)',
  `email` varchar(64) DEFAULT NULL COMMENT '邮箱',
  `tel` varchar(32) DEFAULT NULL COMMENT '电话',
  `mobile` varchar(32) DEFAULT NULL COMMENT '手机',
  `access_key` varchar(128) DEFAULT NULL COMMENT '访问key',
  `access_key_create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '访问key创建时间',
  `secret_key` varchar(128) DEFAULT NULL COMMENT '安全key',
  `deleted` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否删除(0：未删除，1：已删除)',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `head_pic` varchar(128) DEFAULT NULL COMMENT '头像',
  `extend` text COMMENT '扩展字段'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tm_user_log`
--

CREATE TABLE `tm_user_log` (
  `log_id` int(10) UNSIGNED NOT NULL COMMENT '日志ID',
  `user_code` varchar(64) NOT NULL COMMENT '用户代码',
  `ip` varchar(32) NOT NULL COMMENT 'IP地址',
  `log_type` varchar(32) NOT NULL COMMENT '登陆类型（''login'',''logout'',''delete'',''add'',''modify''）',
  `log_message` text NOT NULL COMMENT '登陆信息',
  `log_time` int(10) UNSIGNED NOT NULL COMMENT '日志记录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tm_answer`
--
ALTER TABLE `tm_answer`
  ADD PRIMARY KEY (`answer_id`);

--
-- Indexes for table `tm_branch`
--
ALTER TABLE `tm_branch`
  ADD PRIMARY KEY (`branch_id`);

--
-- Indexes for table `tm_classes`
--
ALTER TABLE `tm_classes`
  ADD PRIMARY KEY (`classes_id`);

--
-- Indexes for table `tm_component`
--
ALTER TABLE `tm_component`
  ADD PRIMARY KEY (`component_id`);

--
-- Indexes for table `tm_fix_item`
--
ALTER TABLE `tm_fix_item`
  ADD PRIMARY KEY (`fix_id`);

--
-- Indexes for table `tm_kind`
--
ALTER TABLE `tm_kind`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `tm_member`
--
ALTER TABLE `tm_member`
  ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `tm_member_comment`
--
ALTER TABLE `tm_member_comment`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `tm_member_history`
--
ALTER TABLE `tm_member_history`
  ADD PRIMARY KEY (`history_id`);

--
-- Indexes for table `tm_member_star`
--
ALTER TABLE `tm_member_star`
  ADD PRIMARY KEY (`star_id`);

--
-- Indexes for table `tm_myprize`
--
ALTER TABLE `tm_myprize`
  ADD PRIMARY KEY (`que_id`);

--
-- Indexes for table `tm_parameterex`
--
ALTER TABLE `tm_parameterex`
  ADD PRIMARY KEY (`source_id`);

--
-- Indexes for table `tm_portal`
--
ALTER TABLE `tm_portal`
  ADD UNIQUE KEY `portal_key` (`portal_key`) USING BTREE;

--
-- Indexes for table `tm_privilege`
--
ALTER TABLE `tm_privilege`
  ADD PRIMARY KEY (`privilege_id`);

--
-- Indexes for table `tm_prize`
--
ALTER TABLE `tm_prize`
  ADD PRIMARY KEY (`prize_id`);

--
-- Indexes for table `tm_push`
--
ALTER TABLE `tm_push`
  ADD PRIMARY KEY (`push_id`);

--
-- Indexes for table `tm_question`
--
ALTER TABLE `tm_question`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tm_role`
--
ALTER TABLE `tm_role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `tm_ruleprize`
--
ALTER TABLE `tm_ruleprize`
  ADD PRIMARY KEY (`rule_id`);

--
-- Indexes for table `tm_site`
--
ALTER TABLE `tm_site`
  ADD PRIMARY KEY (`site_id`);

--
-- Indexes for table `tm_user`
--
ALTER TABLE `tm_user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `tm_user_log`
--
ALTER TABLE `tm_user_log`
  ADD PRIMARY KEY (`log_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tm_answer`
--
ALTER TABLE `tm_answer`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
--
-- AUTO_INCREMENT for table `tm_branch`
--
ALTER TABLE `tm_branch`
  MODIFY `branch_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分支机构ID', AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `tm_classes`
--
ALTER TABLE `tm_classes`
  MODIFY `classes_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '栏目ID', AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `tm_component`
--
ALTER TABLE `tm_component`
  MODIFY `component_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '产品组件id', AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `tm_fix_item`
--
ALTER TABLE `tm_fix_item`
  MODIFY `fix_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '受控词ID';
--
-- AUTO_INCREMENT for table `tm_kind`
--
ALTER TABLE `tm_kind`
  MODIFY `type_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '题库类id', AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `tm_member`
--
ALTER TABLE `tm_member`
  MODIFY `member_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID', AUTO_INCREMENT=55;
--
-- AUTO_INCREMENT for table `tm_member_comment`
--
ALTER TABLE `tm_member_comment`
  MODIFY `comment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '评论id', AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `tm_member_history`
--
ALTER TABLE `tm_member_history`
  MODIFY `history_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '浏览历史id', AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `tm_member_star`
--
ALTER TABLE `tm_member_star`
  MODIFY `star_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '收藏id', AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `tm_myprize`
--
ALTER TABLE `tm_myprize`
  MODIFY `que_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=56;
--
-- AUTO_INCREMENT for table `tm_parameterex`
--
ALTER TABLE `tm_parameterex`
  MODIFY `source_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '扩展数据id';
--
-- AUTO_INCREMENT for table `tm_privilege`
--
ALTER TABLE `tm_privilege`
  MODIFY `privilege_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '权限ID', AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `tm_prize`
--
ALTER TABLE `tm_prize`
  MODIFY `prize_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '奖品id', AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `tm_push`
--
ALTER TABLE `tm_push`
  MODIFY `push_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '推送id', AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `tm_question`
--
ALTER TABLE `tm_question`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT for table `tm_role`
--
ALTER TABLE `tm_role`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色ID', AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `tm_ruleprize`
--
ALTER TABLE `tm_ruleprize`
  MODIFY `rule_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '规则id', AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tm_site`
--
ALTER TABLE `tm_site`
  MODIFY `site_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '站点id', AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tm_user`
--
ALTER TABLE `tm_user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID', AUTO_INCREMENT=61;
--
-- AUTO_INCREMENT for table `tm_user_log`
--
ALTER TABLE `tm_user_log`
  MODIFY `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '日志ID', AUTO_INCREMENT=8966;
