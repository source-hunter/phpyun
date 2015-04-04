-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 12 月 16 日 07:04
-- 服务器版本: 5.6.12-log
-- PHP 版本: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_ad`
--

CREATE TABLE IF NOT EXISTS `phpyun_ad` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `ad_name` varchar(100) NOT NULL,
  `did` varchar(100) NOT NULL DEFAULT '0',
  `time_start` varchar(100) NOT NULL,
  `time_end` varchar(100) NOT NULL,
  `ad_type` varchar(10) NOT NULL,
  `word_info` text NOT NULL,
  `word_url` varchar(100) NOT NULL,
  `pic_url` varchar(100) NOT NULL,
  `pic_src` varchar(100) NOT NULL,
  `pic_width` varchar(100) NOT NULL,
  `pic_height` varchar(100) NOT NULL,
  `flash_url` varchar(100) DEFAULT NULL,
  `flash_src` varchar(100) DEFAULT NULL,
  `flash_width` varchar(100) DEFAULT NULL,
  `flash_height` varchar(100) DEFAULT NULL,
  `class_id` int(20) DEFAULT NULL,
  `is_check` int(2) DEFAULT '0',
  `is_open` int(1) DEFAULT '0',
  `target` int(2) DEFAULT NULL,
  `hits` int(11) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_adclick`
--

CREATE TABLE IF NOT EXISTS `phpyun_adclick` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_announcement`
--

CREATE TABLE IF NOT EXISTS `phpyun_admin_announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `datetime` int(11) NOT NULL,
  `did` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_config`
--

CREATE TABLE IF NOT EXISTS `phpyun_admin_config` (
  `name` varchar(255) NOT NULL,
  `config` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_link`
--

CREATE TABLE IF NOT EXISTS `phpyun_admin_link` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `link_name` varchar(50) NOT NULL,
  `link_url` varchar(50) NOT NULL,
  `img_type` int(30) NOT NULL,
  `pic` varchar(50) NOT NULL,
  `link_type` varchar(1) NOT NULL,
  `link_state` int(1) NOT NULL DEFAULT '0',
  `link_sorting` int(8) NOT NULL DEFAULT '0',
  `link_time` varchar(20) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `tem_type` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_log`
--

CREATE TABLE IF NOT EXISTS `phpyun_admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `username` varchar(200) CHARACTER SET gbk  DEFAULT NULL,
  `content` text CHARACTER SET gbk ,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_navigation`
--

CREATE TABLE IF NOT EXISTS `phpyun_admin_navigation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `keyid` int(11) DEFAULT '0',
  `url` varchar(50) CHARACTER SET gb2312 COLLATE gb2312_bin DEFAULT NULL,
  `menu` int(1) DEFAULT NULL,
  `classname` varchar(100) DEFAULT '0',
  `sort` int(5) DEFAULT '0',
  `display` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_template`
--

CREATE TABLE IF NOT EXISTS `phpyun_admin_template` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `tp_name` varchar(50) NOT NULL,
  `update_time` int(32) NOT NULL,
  `dir` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_user`
--

CREATE TABLE IF NOT EXISTS `phpyun_admin_user` (
  `uid` int(3) NOT NULL AUTO_INCREMENT,
  `m_id` int(2) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `domain` varchar(100) DEFAULT NULL,
  `lasttime` int(11) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_user_group`
--

CREATE TABLE IF NOT EXISTS `phpyun_admin_user_group` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(100) NOT NULL,
  `group_power` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_ad_class`
--

CREATE TABLE IF NOT EXISTS `phpyun_ad_class` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(100) NOT NULL,
  `orders` int(20) NOT NULL,
  `href` varchar(100) NOT NULL,
  `integral_buy` varchar(100) DEFAULT '0',
  `type` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- 表的结构 `phpyun_answer`
--

CREATE TABLE IF NOT EXISTS `phpyun_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `comment` int(11) NOT NULL DEFAULT '0',
  `support` int(11) NOT NULL DEFAULT '0',
  `oppose` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `add_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_answer_review`
--

CREATE TABLE IF NOT EXISTS `phpyun_answer_review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL,
  `qid` int(11) DEFAULT NULL,
  `uid` int(11) NOT NULL,
  `content` text NOT NULL,
  `add_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_atn`
--

CREATE TABLE IF NOT EXISTS `phpyun_atn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `sc_uid` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `usertype` int(11) DEFAULT NULL,
  `sc_usertype` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_attention`
--

CREATE TABLE IF NOT EXISTS `phpyun_attention` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ids` text NOT NULL,
  `type` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_bank`
--

CREATE TABLE IF NOT EXISTS `phpyun_bank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `bank_number` varchar(200) DEFAULT NULL,
  `bank_address` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_banner`
--

CREATE TABLE IF NOT EXISTS `phpyun_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_blacklist`
--

CREATE TABLE IF NOT EXISTS `phpyun_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `p_uid` int(11) DEFAULT NULL,
  `c_uid` int(11) DEFAULT NULL,
  `inputtime` int(11) DEFAULT NULL,
  `usertype` int(1) DEFAULT NULL,
  `com_name` varchar(100) CHARACTER SET gb2312 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_change`
--

CREATE TABLE IF NOT EXISTS `phpyun_change` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `usertype` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `gid` int(11) DEFAULT NULL,
  `integral` int(11) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `linktel` varchar(100)  DEFAULT '',
  `linkman` varchar(200)  DEFAULT '',
  `body` varchar(255)  DEFAULT '',
  `status` int(11) DEFAULT '0',
  `statusbody` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk  AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_city_class`
--

CREATE TABLE IF NOT EXISTS `phpyun_city_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `letter` varchar(1) NOT NULL,
  `display` int(1) NOT NULL,
  `sitetype` int(2) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `phpyun_comclass`
--

CREATE TABLE IF NOT EXISTS `phpyun_comclass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `variable` varchar(50) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company`
--

CREATE TABLE IF NOT EXISTS `phpyun_company` (
  `uid` int(11) NOT NULL,
  `name` varchar(25) DEFAULT NULL,
  `hy` int(5) DEFAULT NULL,
  `pr` int(5) DEFAULT NULL,
  `provinceid` int(5) DEFAULT NULL,
  `cityid` int(5) DEFAULT NULL,
  `mun` int(3) DEFAULT NULL,
  `sdate` varchar(20) DEFAULT NULL,
  `money` int(11) DEFAULT NULL,
  `content` text,
  `address` varchar(100) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `linkman` varchar(50) DEFAULT NULL,
  `linkjob` varchar(50) DEFAULT NULL,
  `linkqq` varchar(20) DEFAULT NULL,
  `linkphone` varchar(20) DEFAULT NULL,
  `linktel` varchar(50) DEFAULT NULL,
  `linkmail` varchar(150) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `x` varchar(100) DEFAULT NULL,
  `y` varchar(100) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `payd` int(8) DEFAULT '0',
  `integral` int(10) DEFAULT '0',
  `lastupdate` varchar(10) DEFAULT NULL,
  `cloudtype` int(2) DEFAULT NULL,
  `jobtime` int(11) DEFAULT NULL,
  `r_status` int(2) DEFAULT '0',
  `firmpic` varchar(100) DEFAULT NULL,
  `rec` int(11) DEFAULT '0',
  `hits` int(11) DEFAULT '0',
  `ant_num` int(11) DEFAULT '0',
  `pl_time` int(11) DEFAULT NULL,
  `pl_status` int(11) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `admin_remark` varchar(255) DEFAULT NULL,
  `email_dy` int(11) DEFAULT '0',
  `msg_dy` int(11) DEFAULT '0',
  `sync` int(11) unsigned DEFAULT '0',
  `hy_dy` varchar(100) DEFAULT NULL,
  `moblie_status` int(1) DEFAULT '0',
  `email_status` int(1) DEFAULT '0',
  `yyzz_status` int(1) DEFAULT '0',
  `hottime` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_cert`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_cert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` varchar(200) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `step` int(11) DEFAULT NULL,
  `check` varchar(200) DEFAULT NULL,
  `check2` varchar(200) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `statusbody` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_job`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `hy` int(5) DEFAULT NULL,
  `job1` int(5) DEFAULT NULL,
  `job1_son` int(5) DEFAULT NULL,
  `job_post` int(5) DEFAULT NULL,
  `provinceid` int(5) DEFAULT NULL,
  `cityid` int(5) DEFAULT NULL,
  `three_cityid` int(5) DEFAULT NULL,
  `cert` varchar(50) DEFAULT NULL,
  `salary` int(5) DEFAULT NULL,
  `type` int(5) NOT NULL,
  `number` int(2) NOT NULL,
  `exp` int(5) NOT NULL,
  `report` int(5) NOT NULL,
  `sex` int(5) NOT NULL,
  `edu` int(5) NOT NULL,
  `marriage` int(5) NOT NULL,
  `description` text NOT NULL,
  `xuanshang` int(11) NOT NULL DEFAULT '0',
  `xsdate` int(11) DEFAULT NULL,
  `sdate` int(11) NOT NULL,
  `edate` int(11) NOT NULL,
  `jobhits` int(10) NOT NULL DEFAULT '0',
  `lastupdate` varchar(10) NOT NULL,
  `rec` int(2) DEFAULT '0',
  `cloudtype` int(2) DEFAULT NULL,
  `state` int(2) DEFAULT '0',
  `statusbody` varchar(200) DEFAULT '0',
  `age` int(11) DEFAULT NULL,
  `lang` text,
  `welfare` text,
  `com_name` varchar(50) NOT NULL DEFAULT '',
  `pr` int(5) DEFAULT NULL,
  `mun` int(5) DEFAULT NULL,
  `com_provinceid` int(5) DEFAULT NULL,
  `rating` int(5) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `urgent` int(1) DEFAULT NULL,
  `r_status` int(1) DEFAULT '0',
  `end_email` int(1) DEFAULT '0',
  `urgent_time` int(11) DEFAULT NULL,
  `com_logo` varchar(100) DEFAULT NULL,
  `autotype` int(11) DEFAULT '0',
  `autotime` int(11) DEFAULT '0',
  `is_link` int(1) DEFAULT '1',
  `link_type` int(1) DEFAULT '1',
  `source` int(1) DEFAULT '1',
  `rec_time` int(11) DEFAULT '0',
  `snum` int(11) DEFAULT '0',
  PRIMARY KEY (`id`,`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_job_link`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_job_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `jobid` int(11) DEFAULT NULL,
  `link_man` varchar(100) DEFAULT NULL,
  `link_moblie` varchar(20) DEFAULT NULL,
  `email_type` int(5) DEFAULT NULL,
  `is_email` int(2) DEFAULT '0',
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gb2312 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_msg`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `cuid` int(11) DEFAULT NULL,
  `content` text,
  `ctime` varchar(100) DEFAULT NULL,
  `status` int(2) DEFAULT NULL,
  `reply` text,
  `reply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_news`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `title` varchar(200) DEFAULT '0',
  `ctime` int(11) DEFAULT '0',
  `body` text,
  `status` int(2) DEFAULT '0',
  `statusbody` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_order`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `order_id` varchar(18) DEFAULT NULL,
  `order_type` varchar(25) DEFAULT NULL,
  `order_price` double(18,2) NOT NULL,
  `order_time` int(10) NOT NULL,
  `order_state` int(2) NOT NULL,
  `order_remark` text,
  `order_bank` varchar(150) NOT NULL DEFAULT '0',
  `type` int(1) DEFAULT NULL,
  `rating` int(10) DEFAULT NULL,
  `integral` int(11) DEFAULT NULL,
  `is_invoice` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_pay`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(18) DEFAULT NULL,
  `order_price` decimal(10,2) DEFAULT NULL,
  `pay_time` int(11) DEFAULT NULL,
  `pay_state` int(2) DEFAULT NULL,
  `com_id` int(10) DEFAULT NULL,
  `pay_remark` varchar(255) DEFAULT NULL,
  `type` int(1) DEFAULT NULL,
  `pay_type` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_product`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `title` varchar(200) DEFAULT '0',
  `pic` varchar(200) DEFAULT '0',
  `body` text,
  `ctime` int(11) DEFAULT '0',
  `status` int(2) DEFAULT '0',
  `statusbody` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_rating`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_rating` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `service_price` varchar(100) DEFAULT NULL,
  `integral_buy` varchar(100) DEFAULT NULL,
  `yh_price` varchar(100) DEFAULT NULL,
  `yh_integral` varchar(100) DEFAULT NULL,
  `time_start` int(11) DEFAULT NULL,
  `time_end` int(11) DEFAULT NULL,
  `resume` int(5) DEFAULT NULL,
  `job_num` int(11) DEFAULT NULL,
  `interview` int(11) DEFAULT NULL,
  `editjob_num` int(11) DEFAULT NULL,
  `breakjob_num` int(11) DEFAULT NULL,
  `sort` int(10) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  `explains` varchar(255) DEFAULT NULL,
  `com_pic` varchar(100) DEFAULT NULL,
  `com_color` varchar(100) DEFAULT NULL,
  `type` int(2) DEFAULT NULL,
  `category` int(2) DEFAULT NULL,
  `msg_num` int(11) DEFAULT '0',
  `service_time` int(11) DEFAULT NULL,
  `coupon` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_show`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_show` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET gbk  DEFAULT NULL,
  `picurl` varchar(200) CHARACTER SET gbk  DEFAULT NULL,
  `body` varchar(200) CHARACTER SET gbk  DEFAULT NULL,
  `ctime` varchar(200) CHARACTER SET gbk  DEFAULT NULL,
  `uid` varchar(200) CHARACTER SET gbk  DEFAULT NULL,
  `sort` int(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_statis`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_statis` (
  `uid` int(11) NOT NULL,
  `pay` double(10,2) NOT NULL DEFAULT '0.00',
  `integral` varchar(10) NOT NULL DEFAULT '0',
  `sq_job` int(6) unsigned NOT NULL,
  `fav_job` int(6) unsigned NOT NULL,
  `rating` int(5) unsigned DEFAULT NULL,
  `rating_name` varchar(100) DEFAULT NULL,
  `vip_etime` varchar(100) DEFAULT '0',
  `all_pay` double(10,2) NOT NULL,
  `consum_pay` double(10,2) NOT NULL,
  `rating_type` int(11) DEFAULT NULL,
  `invite_resume` int(10) DEFAULT NULL,
  `comtpl` varchar(100) DEFAULT '0',
  `comtpl_all` varchar(100) DEFAULT NULL,
  `job_num` int(11) DEFAULT '0',
  `editjob_num` int(11) DEFAULT '0',
  `breakjob_num` int(11) DEFAULT '0',
  `down_resume` int(10) DEFAULT '0',
  `qqshow` int(11) DEFAULT '0',
  `qqcomment` int(11) DEFAULT '0',
  `sinashare` int(11) DEFAULT '0',
  `sinashow` int(11) DEFAULT '0',
  `sinacomment` int(11) DEFAULT '0',
  `qqwname` varchar(100) DEFAULT NULL,
  `sinawname` varchar(100) DEFAULT NULL,
  `qqshare` int(11) DEFAULT '0',
  `msg_num` int(11) DEFAULT '0',
  `autotime` int(11) DEFAULT '0',
  `vip_stime` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_tpl`
--

CREATE TABLE IF NOT EXISTS `phpyun_company_tpl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET gbk  DEFAULT '0',
  `url` varchar(100) CHARACTER SET gbk  DEFAULT '0',
  `pic` varchar(200) CHARACTER SET gbk  DEFAULT '0',
  `type` int(10) DEFAULT '0',
  `price` varchar(100) CHARACTER SET gbk  DEFAULT '0',
  `status` int(10) DEFAULT NULL,
  `service_uid` varchar(225) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_cron`
--

CREATE TABLE IF NOT EXISTS `phpyun_cron` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `dir` varchar(200) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `week` int(11) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `hour` int(10) DEFAULT NULL,
  `minute` int(10) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `nowtime` int(11) DEFAULT '0',
  `nexttime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_description`
--

CREATE TABLE IF NOT EXISTS `phpyun_description` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `descs` text,
  `top_tpl` int(2) DEFAULT NULL,
  `top_tpl_dir` varchar(255) DEFAULT NULL,
  `footer_tpl` int(2) DEFAULT NULL,
  `footer_tpl_dir` varchar(255) DEFAULT NULL,
  `content` mediumtext,
  `sort` int(11) DEFAULT NULL,
  `is_nav` int(1) DEFAULT '0',
  `ctime` int(11) DEFAULT NULL,
  `is_menu` int(11) DEFAULT '0',
  `is_type` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_desc_class`
--

CREATE TABLE IF NOT EXISTS `phpyun_desc_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `sort` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_domain`
--

CREATE TABLE IF NOT EXISTS `phpyun_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `domain` varchar(200) NOT NULL,
  `province` int(11) DEFAULT NULL,
  `cityid` int(11) DEFAULT NULL,
  `three_cityid` int(11) DEFAULT NULL,
  `type` int(2) NOT NULL,
  `style` varchar(100) CHARACTER SET gbk  NOT NULL,
  `tpl` varchar(20) DEFAULT NULL,
  `hy` int(11) DEFAULT NULL,
  `fz_type` int(11) NOT NULL,
  `webtitle` text,
  `webkeyword` text,
  `webmeta` text,
  `weblogo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_down_resume`
--

CREATE TABLE IF NOT EXISTS `phpyun_down_resume` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `eid` int(11) DEFAULT NULL,
  `comid` int(11) DEFAULT NULL,
  `downtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_email_msg`
--

CREATE TABLE IF NOT EXISTS `phpyun_email_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `cuid` int(11) DEFAULT NULL,
  `cname` varchar(255) DEFAULT '',
  `email` varchar(200) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `content` text,
  `ctime` int(11) DEFAULT NULL,
  `state` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- 表的结构 `phpyun_fav_job`
--

CREATE TABLE IF NOT EXISTS `phpyun_fav_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `com_id` int(11) NOT NULL,
  `com_name` varchar(150) NOT NULL,
  `datetime` int(10) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `job_name` varchar(150) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_finder`
--

CREATE TABLE IF NOT EXISTS `phpyun_finder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `usertype` int(1) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `para` varchar(255) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend`
--

CREATE TABLE IF NOT EXISTS `phpyun_friend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `nid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `uidtype` int(2) DEFAULT NULL,
  `nidtype` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend_foot`
--

CREATE TABLE IF NOT EXISTS `phpyun_friend_foot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `fid` int(11) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend_info`
--

CREATE TABLE IF NOT EXISTS `phpyun_friend_info` (
  `uid` int(11) DEFAULT NULL,
  `nickname` varchar(100) CHARACTER SET gbk  DEFAULT NULL,
  `sex` int(1) DEFAULT '3',
  `pic` varchar(100) CHARACTER SET gbk  DEFAULT NULL,
  `pic_big` varchar(100) DEFAULT NULL,
  `description` varchar(100) CHARACTER SET gbk  DEFAULT NULL,
  `birthday` varchar(100) CHARACTER SET gbk  DEFAULT NULL,
  `usertype` int(2) DEFAULT NULL,
  `iscert` int(2) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend_message`
--

CREATE TABLE IF NOT EXISTS `phpyun_friend_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `u_name` varchar(100) CHARACTER SET gbk  DEFAULT NULL,
  `fid` int(11) DEFAULT NULL,
  `f_name` varchar(100) CHARACTER SET gbk  DEFAULT NULL,
  `nid` int(11) DEFAULT '0',
  `content` varchar(225) CHARACTER SET gbk  DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `remind_status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend_reply`
--

CREATE TABLE IF NOT EXISTS `phpyun_friend_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` int(11) DEFAULT NULL,
  `fid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `reply` varchar(225) CHARACTER SET gbk  DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend_state`
--

CREATE TABLE IF NOT EXISTS `phpyun_friend_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `content` varchar(225) CHARACTER SET gbk  DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT '1',
  `msg_pic` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_hotjob`
--

CREATE TABLE IF NOT EXISTS `phpyun_hotjob` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `username` varchar(200) DEFAULT NULL,
  `rating` varchar(20) DEFAULT NULL,
  `hot_pic` varchar(100) DEFAULT NULL,
  `service_price` int(11) DEFAULT NULL,
  `time_start` int(11) DEFAULT NULL,
  `time_end` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT '0',
  `beizhu` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_hot_key`
--

CREATE TABLE IF NOT EXISTS `phpyun_hot_key` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(100) NOT NULL,
  `num` int(20) NOT NULL DEFAULT '0',
  `type` int(2) NOT NULL,
  `size` varchar(10) DEFAULT NULL,
  `check` int(1) DEFAULT '0',
  `color` varchar(10) DEFAULT NULL,
  `bold` int(11) DEFAULT NULL,
  `tuijian` int(11) DEFAULT '0',
  `wxtime` int(11) DEFAULT '0',
  `wxuser` varchar(100) DEFAULT NULL,
  `wxid` varchar(100) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_industry`
--

CREATE TABLE IF NOT EXISTS `phpyun_industry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `sort` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `phpyun_job_class`
--

CREATE TABLE IF NOT EXISTS `phpyun_job_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sort` int(11) NOT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_look_job`
--

CREATE TABLE IF NOT EXISTS `phpyun_look_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `jobid` int(11) DEFAULT NULL,
  `com_id` int(11) DEFAULT NULL,
  `datetime` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  `com_status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk  AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_look_resume`
--

CREATE TABLE IF NOT EXISTS `phpyun_look_resume` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `com_id` int(11) DEFAULT NULL,
  `resume_id` int(11) DEFAULT NULL,
  `datetime` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  `com_status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_member`
--

CREATE TABLE IF NOT EXISTS `phpyun_member` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `moblie` varchar(20) DEFAULT NULL,
  `reg_ip` varchar(20) DEFAULT NULL,
  `reg_date` int(11) DEFAULT NULL,
  `login_ip` varchar(20) DEFAULT NULL,
  `login_date` int(11) DEFAULT NULL,
  `usertype` int(1) NOT NULL DEFAULT '1',
  `login_hits` int(11) DEFAULT '0',
  `salt` varchar(6) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `name_repeat` int(2) DEFAULT '0',
  `qqid` varchar(200) DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  `pwuid` int(11) DEFAULT '0',
  `pw_repeat` int(1) DEFAULT '0',
  `lock_info` varchar(200) CHARACTER SET gb2312 DEFAULT NULL,
  `email_status` int(1) DEFAULT NULL,
  `signature` varchar(100) DEFAULT NULL,
  `sinaid` varchar(100) DEFAULT NULL,
  `wxid` varchar(100) DEFAULT '0',
  `wxname` varchar(100) DEFAULT NULL,
  `wxbindtime` int(11) DEFAULT '0',
  `passtext` varchar(100) DEFAULT NULL,
  `source` int(1) DEFAULT '1',
  `regcode` int(10) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_member_log`
--

CREATE TABLE IF NOT EXISTS `phpyun_member_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `opera` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `usertype` int(11) DEFAULT NULL,
  `content` text CHARACTER SET gbk,
  `ip` varchar(20) CHARACTER SET gbk DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk  AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_member_statis`
--

CREATE TABLE IF NOT EXISTS `phpyun_member_statis` (
  `uid` int(11) NOT NULL,
  `integral` varchar(10) NOT NULL DEFAULT '0',
  `pay` double(10,2) NOT NULL DEFAULT '0.00',
  `resume_num` int(10) NOT NULL,
  `fav_jobnum` int(10) NOT NULL,
  `sq_jobnum` int(10) NOT NULL,
  `message_num` int(10) NOT NULL,
  `down_num` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_message`
--

CREATE TABLE IF NOT EXISTS `phpyun_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL,
  `username` varchar(20) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  `ctime` int(11) DEFAULT NULL,
  `reply` varchar(200) DEFAULT '',
  `reply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_moblie_msg`
--

CREATE TABLE IF NOT EXISTS `phpyun_moblie_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `cuid` int(11) DEFAULT NULL,
  `cname` varchar(255) DEFAULT NULL,
  `moblie` varchar(200) DEFAULT NULL,
  `content` varchar(200) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_msg`
--

CREATE TABLE IF NOT EXISTS `phpyun_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `jobid` int(11) DEFAULT NULL,
  `job_uid` int(11) DEFAULT NULL,
  `datetime` int(11) DEFAULT NULL,
  `reply` text,
  `content` text,
  `reply_time` int(11) DEFAULT NULL,
  `com_name` varchar(100) DEFAULT NULL,
  `job_name` varchar(100) DEFAULT NULL,
  `del_status` int(11) DEFAULT '0',
  `type` int(11) DEFAULT '1',
  `user_remind_status` int(1) DEFAULT '1',
  `com_remind_status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_navigation`
--

CREATE TABLE IF NOT EXISTS `phpyun_navigation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` varchar(100) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `display` int(1) NOT NULL,
  `eject` int(1) NOT NULL,
  `type` int(1) DEFAULT '1',
  `furl` varchar(100) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `model` varchar(20) DEFAULT NULL,
  `bold` int(1) DEFAULT NULL,
  `desc` int(11) DEFAULT NULL,
  `news` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_navigation_type`
--

CREATE TABLE IF NOT EXISTS `phpyun_navigation_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typename` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_navmap`
--

CREATE TABLE IF NOT EXISTS `phpyun_navmap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` varchar(100) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `display` int(1) NOT NULL DEFAULT '0',
  `eject` int(1) NOT NULL,
  `type` int(1) DEFAULT '1',
  `furl` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_news_base`
--

CREATE TABLE IF NOT EXISTS `phpyun_news_base` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` int(11) NOT NULL,
  `did` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `keyword` varchar(200) NOT NULL,
  `author` varchar(200) NOT NULL,
  `datetime` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `describe` varchar(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `newsphoto` varchar(100) DEFAULT NULL,
  `s_thumb` varchar(100) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `lastupdate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_news_content`
--

CREATE TABLE IF NOT EXISTS `phpyun_news_content` (
  `nbid` int(11) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`nbid`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_news_group`
--

CREATE TABLE IF NOT EXISTS `phpyun_news_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort` int(11) DEFAULT '0',
  `rec` int(1) DEFAULT '0',
  `is_menu` int(1) DEFAULT '0',
  `rec_news` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_once_job`
--

CREATE TABLE IF NOT EXISTS `phpyun_once_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `mans` varchar(100) NOT NULL,
  `require` varchar(255) NOT NULL,
  `companyname` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `hits` int(11) DEFAULT '0',
  `linkman` varchar(50) NOT NULL,
  `address` varchar(200) NOT NULL,
  `ctime` int(11) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '0',
  `password` varchar(100) NOT NULL,
  `qq` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `edate` int(11) DEFAULT NULL,
  `login_ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_outside`
--

CREATE TABLE IF NOT EXISTS `phpyun_outside` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `titlelen` int(10) DEFAULT NULL,
  `infolen` int(10) DEFAULT NULL,
  `byorder` varchar(200) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `code` text,
  `edittime` int(10) DEFAULT NULL,
  `lasttime` int(11) DEFAULT NULL,
  `urltype` varchar(200) DEFAULT NULL,
  `timetype` varchar(200) DEFAULT NULL,
  `where` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- 表的结构 `phpyun_property`
--

CREATE TABLE IF NOT EXISTS `phpyun_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `value` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `phpyun_question`
--

CREATE TABLE IF NOT EXISTS `phpyun_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `answer_num` int(11) NOT NULL DEFAULT '0',
  `visit` int(11) NOT NULL DEFAULT '0',
  `is_recom` int(1) NOT NULL DEFAULT '0',
  `lastupdate` int(11) DEFAULT NULL,
  `add_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_q_class`
--

CREATE TABLE IF NOT EXISTS `phpyun_q_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `pid` int(11) NOT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `sort` int(11) NOT NULL,
  `intro` text,
  `add_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_reason`
--

CREATE TABLE IF NOT EXISTS `phpyun_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- 表的结构 `phpyun_report`
--

CREATE TABLE IF NOT EXISTS `phpyun_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `p_uid` int(11) DEFAULT NULL,
  `c_uid` int(11) DEFAULT NULL,
  `eid` int(11) DEFAULT NULL,
  `usertype` int(1) DEFAULT NULL,
  `inputtime` int(11) DEFAULT NULL,
  `username` varchar(100) CHARACTER SET gb2312 DEFAULT NULL,
  `r_name` varchar(100) CHARACTER SET gb2312 DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  `r_reason` varchar(200) CHARACTER SET gb2312 DEFAULT NULL,
  `type` int(11) DEFAULT '0',
  `r_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume` (
  `uid` int(11) NOT NULL,
  `name` varchar(25) DEFAULT NULL,
  `sex` int(2) DEFAULT NULL,
  `birthday` varchar(10) DEFAULT NULL,
  `marriage` varchar(2) DEFAULT NULL,
  `height` varchar(4) DEFAULT NULL,
  `nationality` varchar(20) DEFAULT NULL,
  `weight` varchar(4) DEFAULT NULL,
  `idcard` varchar(20) DEFAULT NULL,
  `telphone` varchar(20) DEFAULT NULL,
  `telhome` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `edu` int(2) DEFAULT NULL,
  `homepage` varchar(50) DEFAULT NULL,
  `address` varchar(80) DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL,
  `resume_photo` varchar(100) DEFAULT NULL,
  `photo` varchar(100) DEFAULT NULL,
  `expect` int(2) DEFAULT '0',
  `def_job` int(11) DEFAULT '0',
  `exp` int(11) DEFAULT NULL,
  `status` int(2) DEFAULT '1',
  `idcard_pic` varchar(100) DEFAULT NULL,
  `email_status` int(1) DEFAULT '0',
  `moblie_status` int(1) DEFAULT '0',
  `idcard_status` int(1) DEFAULT '0',
  `statusbody` varchar(200) DEFAULT NULL,
  `cert_time` int(11) DEFAULT NULL,
  `r_status` int(1) DEFAULT '0',
  `ant_num` int(11) DEFAULT '0',
  `email_dy` int(1) DEFAULT '0',
  `msg_dy` int(1) DEFAULT '0',
  `living` varchar(100) DEFAULT NULL,
  `domicile` varchar(100) DEFAULT NULL,
  `basic_info` int(11) DEFAULT '1',
  `hy_dy` varchar(100) DEFAULT NULL,
  `info_status` int(1) DEFAULT '1',
  KEY `默认简历` (`def_job`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_cert`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume_cert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `sdate` int(10) DEFAULT NULL,
  `edate` int(10) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_doc`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume_doc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `doc` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_edu`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume_edu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `sdate` int(10) DEFAULT NULL,
  `edate` int(10) DEFAULT NULL,
  `specialty` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_expect`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume_expect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `name` varchar(25) DEFAULT NULL,
  `hy` int(5) DEFAULT NULL,
  `job_classid` varchar(100) DEFAULT NULL,
  `provinceid` int(5) DEFAULT NULL,
  `cityid` int(5) DEFAULT NULL,
  `three_cityid` int(5) DEFAULT NULL,
  `salary` int(3) DEFAULT NULL,
  `type` int(3) DEFAULT NULL,
  `report` int(3) DEFAULT NULL,
  `defaults` int(1) NOT NULL DEFAULT '0',
  `open` int(1) DEFAULT '1',
  `is_entrust` int(1) DEFAULT '0',
  `full` int(3) DEFAULT '0',
  `doc` int(1) DEFAULT '0',
  `hits` int(6) DEFAULT '0',
  `lastupdate` int(10) NOT NULL,
  `def_job` int(11) DEFAULT NULL,
  `cloudtype` int(2) DEFAULT NULL,
  `olduid` int(11) DEFAULT NULL,
  `integrity` int(11) DEFAULT NULL,
  `height_status` int(11) DEFAULT '0',
  `statusbody` varchar(200) DEFAULT NULL,
  `status_time` int(11) DEFAULT NULL,
  `rec` int(11) DEFAULT '0',
  `top` int(11) DEFAULT NULL,
  `topdate` int(11) DEFAULT '0',
  `rec_resume` int(11) DEFAULT NULL,
  `dom_sort` varchar(255) DEFAULT NULL,
  `resume_diy` text,
  `source` int(1) DEFAULT '1',
  `tmpid` int(5) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `dnum` int(11) DEFAULT '0',
  PRIMARY KEY (`id`,`defaults`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_other`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume_other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_project`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `sdate` int(10) DEFAULT NULL,
  `edate` int(10) DEFAULT NULL,
  `sys` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_show`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume_show` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET gbk  DEFAULT NULL,
  `picurl` varchar(200) CHARACTER SET gbk  DEFAULT NULL,
  `ctime` varchar(200) CHARACTER SET gbk  DEFAULT NULL,
  `uid` varchar(200) CHARACTER SET gbk  DEFAULT NULL,
  `sort` int(4) DEFAULT '0',
  `eid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_skill`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume_skill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `skill` int(5) NOT NULL,
  `ing` int(5) NOT NULL,
  `longtime` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_tiny`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume_tiny` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `password` varchar(50) NOT NULL,
  `sex` int(11) NOT NULL,
  `exp` int(11) NOT NULL,
  `hits` int(11) DEFAULT '0',
  `job` varchar(25) NOT NULL,
  `mobile` varchar(25) NOT NULL,
  `qq` varchar(25) NOT NULL,
  `production` text NOT NULL,
  `time` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `login_ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_training`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume_training` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `sdate` int(10) DEFAULT NULL,
  `edate` int(10) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_work`
--

CREATE TABLE IF NOT EXISTS `phpyun_resume_work` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `sdate` int(10) DEFAULT NULL,
  `edate` int(10) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_reward`
--

CREATE TABLE IF NOT EXISTS `phpyun_reward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET gbk DEFAULT NULL,
  `nid` int(11) DEFAULT NULL,
  `integral` int(11) DEFAULT NULL,
  `num` int(11) DEFAULT '0',
  `restriction` int(11) DEFAULT '0',
  `stock` int(11) DEFAULT '0',
  `pic` varchar(100) CHARACTER SET gbk DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `content` text CHARACTER SET gbk,
  `status` int(1) DEFAULT NULL,
  `sdate` int(11) DEFAULT NULL,
  `rec` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk  AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_reward_class`
--

CREATE TABLE IF NOT EXISTS `phpyun_reward_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET gbk DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk  AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_seo`
--

CREATE TABLE IF NOT EXISTS `phpyun_seo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seoname` varchar(100) DEFAULT NULL,
  `ident` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` text,
  `time` int(11) DEFAULT NULL,
  `affiliation` varchar(100) DEFAULT NULL,
  `php_url` varchar(100) DEFAULT NULL,
  `rewrite_url` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_subscribe`
--

CREATE TABLE IF NOT EXISTS `phpyun_subscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `job1` int(11) DEFAULT NULL,
  `job1_son` int(11) DEFAULT NULL,
  `job_post` int(11) DEFAULT NULL,
  `provinceid` int(11) DEFAULT NULL,
  `cityid` int(11) DEFAULT NULL,
  `three_cityid` int(11) DEFAULT NULL,
  `salary` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  `code` varchar(10) DEFAULT NULL,
  `cycle_time` int(11) DEFAULT NULL,
  `time` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_sysmsg`
--

CREATE TABLE IF NOT EXISTS `phpyun_sysmsg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) CHARACTER SET gbk  NOT NULL,
  `fa_uid` int(11) NOT NULL,
  `username` varchar(20) CHARACTER SET gbk  NOT NULL ,
  `ctime` int(11) NOT NULL,
  `remind_status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_talent_pool`
--

CREATE TABLE IF NOT EXISTS `phpyun_talent_pool` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `eid` int(11) DEFAULT NULL,
  `cuid` int(11) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `remark` varchar(200) CHARACTER SET gbk DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk  AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_templates`
--

CREATE TABLE IF NOT EXISTS `phpyun_templates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_toolbox_class`
--

CREATE TABLE IF NOT EXISTS `phpyun_toolbox_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `content` text CHARACTER SET gbk,
  `pic` varchar(100) CHARACTER SET gbk DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk  AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_toolbox_doc`
--

CREATE TABLE IF NOT EXISTS `phpyun_toolbox_doc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT NULL,
  `name` varchar(100) CHARACTER SET gbk DEFAULT NULL,
  `url` varchar(100) CHARACTER SET gbk DEFAULT NULL,
  `is_show` int(1) DEFAULT '0',
  `add_time` int(11) DEFAULT NULL,
  `downnum` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk  AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_userclass`
--

CREATE TABLE IF NOT EXISTS `phpyun_userclass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `variable` varchar(100) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_userid_job`
--

CREATE TABLE IF NOT EXISTS `phpyun_userid_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `job_name` varchar(150) NOT NULL,
  `com_id` int(11) NOT NULL,
  `com_name` varchar(150) NOT NULL,
  `eid` int(10) unsigned NOT NULL,
  `datetime` int(10) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '1',
  `is_browse` int(1) NOT NULL DEFAULT '1',
  `body` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_userid_msg`
--

CREATE TABLE IF NOT EXISTS `phpyun_userid_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `fid` int(11) NOT NULL,
  `fname` varchar(150) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `datetime` int(10) NOT NULL,
  `default` int(1) DEFAULT '0',
  `is_browse` int(1) DEFAULT '1',
  `address` varchar(255) DEFAULT NULL,
  `intertime` varchar(255) DEFAULT NULL,
  `linkman` varchar(50) DEFAULT NULL,
  `linktel` varchar(50) DEFAULT NULL,
  `jobid` int(11) DEFAULT NULL,
  `jobname` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `phpyun_user_resume`
--

CREATE TABLE IF NOT EXISTS `phpyun_user_resume` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `eid` int(10) NOT NULL,
  `expect` int(1) DEFAULT '0',
  `skill` int(1) DEFAULT '0',
  `work` int(1) DEFAULT '0',
  `project` int(1) DEFAULT '0',
  `edu` int(1) DEFAULT '0',
  `training` int(1) DEFAULT '0',
  `cert` int(1) DEFAULT '0',
  `other` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_warning`
--

CREATE TABLE IF NOT EXISTS `phpyun_warning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` int(1) DEFAULT NULL,
  `status` int(1) DEFAULT '1',
  `ctime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk  AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `phpyun_website`
--

CREATE TABLE IF NOT EXISTS `phpyun_website` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL DEFAULT '0',
  `smallday` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_wxlog`
--

CREATE TABLE IF NOT EXISTS `phpyun_wxlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxid` varchar(100) CHARACTER SET gbk  NOT NULL DEFAULT '0',
  `wxname` varchar(100) DEFAULT NULL,
  `wxuid` int(11) DEFAULT '0',
  `wxuser` varchar(100) DEFAULT NULL,
  `content` text,
  `reply` text,
  `nav` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_wxnav`
--

CREATE TABLE IF NOT EXISTS `phpyun_wxnav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `keyid` int(11) DEFAULT NULL,
  `key` varchar(100) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_zhaopinhui`
--

CREATE TABLE IF NOT EXISTS `phpyun_zhaopinhui` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT '0',
  `pic` varchar(200) DEFAULT '0',
  `starttime` varchar(100) DEFAULT '0',
  `endtime` varchar(100) DEFAULT '0',
  `provinceid` int(11) DEFAULT '0',
  `cityid` int(11) DEFAULT '0',
  `address` varchar(200) DEFAULT NULL,
  `traffic` text,
  `phone` varchar(100) DEFAULT '0',
  `organizers` varchar(200) DEFAULT '0',
  `user` varchar(200) DEFAULT NULL,
  `weburl` varchar(100) DEFAULT '0',
  `body` text,
  `media` text,
  `packages` text,
  `booth` text,
  `participate` text,
  `status` int(11) DEFAULT '0',
  `ctime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_zhaopinhui_com`
--

CREATE TABLE IF NOT EXISTS `phpyun_zhaopinhui_com` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `zid` int(11) DEFAULT '0',
  `jobid` varchar(255) DEFAULT '0',
  `ctime` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `statusbody` varchar(100) DEFAULT NULL,
  `inadd` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_zhaopinhui_pic`
--

CREATE TABLE IF NOT EXISTS `phpyun_zhaopinhui_pic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT '0',
  `pic` varchar(200) DEFAULT '0',
  `sort` int(11) DEFAULT '0',
  `zid` int(11) DEFAULT '0',
  `is_themb` int(5) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
